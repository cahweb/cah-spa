<?php
/**
 * Registers and enqueues the JavaScript and CSS for the Theatre Audition Form
 * 
 * @author Mike W. Leavitt
 * @version 1.0.0
 */

namespace UCF\CAH\SPA\Theatre\AuditionForm;

require_once get_stylesheet_directory() . "/includes/db-helper.php";
use UCF\CAH\DB_Helper as DB;

require_once get_stylesheet_directory() . "/includes/phpmailer-helper.php";
use UCF\CAH\MailTools\PHPMailerHelper as Mailer;

use mysqli as mysqli;

final class AuditionFormSetup
{
    // The slug of our target page
    private static $target_slug = "theatre-audition-form";

    // The MIME types we'll accept for uploaded files
    private static $allowed_file_types = [
        "application/pdf",
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/rtf",
        "image/jpeg",
        "image/png",
        "text/plain",
    ];

    private static $application_file_path = "D:\\wamp64\\application-files\\spa\\theatre";

    private function __construct() { /* Prevents instantiation */ }

    public static function setup() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_scripts' ], 5, 0);
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'maybe_enqueue_scripts' ], 10, 0 );

        add_action( 'wp_ajax_theatre_form_submit', [ __CLASS__, 'handle_ajax' ], 10, 0 );
        add_action( 'wp_ajax_nopriv_theatre_form_submit', [ __CLASS__, 'handle_ajax' ], 10, 0 );
    }


    public static function register_scripts() {
        $uri = get_stylesheet_directory_uri() . "/audition-forms/theatre-audition-form/dist";

        $path = get_stylesheet_directory() . "/audition-forms/theatre-audition-form/dist";

        wp_register_script(
            'theatre-form-chunk',
            "$uri/js/chunk-theatre-audition-form.js",
            [],
            filemtime( "$path/js/chunk-theatre-audition-form.js" ),
            true 
        );

        wp_register_script(
            'theatre-form-script',
            "$uri/js/theatre-audition-form.js",
            [ 'theatre-form-chunk' ],
            filemtime( "$path/js/theatre-audition-form.js" ),
            true
        );

        wp_register_style(
            'theatre-form-style',
            "$uri/css/theatre-audition-form.css",
            [],
            filemtime( "$path/css/theatre-audition-form.css" ),
            'all'
        );
    }


    public static function maybe_enqueue_scripts() {
        
        global $post;
        if( !isset( $post ) || !is_object( $post ) ) return;

        $slug = $post->post_name;

        if( $slug === self::$target_slug ) {
            wp_enqueue_script( 'theatre-form-script' );
            wp_localize_script(
                'theatre-form-script',
                'wpVars',
                [
                    'baseUrl' => get_stylesheet_directory_uri(),
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                ]
            );

            wp_enqueue_style( 'theatre-form-style' );
        }
    }


    public static function handle_ajax() {

        // Validate nonce from front-end
        if( !check_ajax_referer( 'theatre_form_submit', 'theatre-form-nonce' ) ) {
            wp_die( 'Invalid nonce.' );
        }

        // We're going to assume the best from the beginning, and change it
        // only if necessary.
        $return_msg = "Your application has been successfully submitted! You should receive a confirmation email shortly.";

        // So we can reverse lookup program number for db entry.
        $program_lookup = [
            null,
            'ba-theatre',
            'bfa-acting',
            'bfa-design-tech',
            'bfa-musical-theatre',
            'bfa-stage-mgmt',
            'ma-theatre',
            'ma-music-theatre',
            'mfa-acting',
            'mfa-young-theatre',
            'mfa-themed-exp',
        ];

        // Lookup the program code
        $program_code = array_search( $_POST['program'], $program_lookup );

        // Will fire if program code is FALSE (not found) or 0 (the first
        // index, which in this case is NULL).
        if( !$program_code ) {
            wp_die( "Invalid Program selection." );
        }

        // Create the SQL scaffold
        $sql_base = "INSERT INTO applications (last_name, first_name, email, `address`, phone, program, first_choice_date, second_choice_date) VALUES ('%s', '%s', '%s', '%s', '%s', %d, '%s', '%s')";

        // Build the SQL string
        $sql = sprintf( $sql_base, $_POST['lname'], $_POST['fname'], $_POST['email'], $_POST['address'], $_POST['phone'], $program_code, $_POST['firstChoiceDate'], $_POST['secondChoiceDate']);

        // Create a new DB_Helper object
        $db = new DB();

        // Insert the new application entry and retrieve the ID.
        $id = 0;
        if( mysqli_query( $db->get_db(), $sql ) ) {
            $id = $db->get_db()->insert_id;
        }

        // If we don't have an ID, there was a problem.
        if( !$id ) {
            self::_log_mysql_error( $db->get_db(), $sql );
            wp_die( "Problem inserting data to Database.\n\tSQL: " . $sql );
        }

        // SQL for updating status.
        $sql_status = "INSERT INTO application_status (application_id, status_code) VALUES ($id, 1)";

        // If this returns FALSE, then there was a problem.
        if( !mysqli_query( $db->get_db(), $sql_status ) ) {
            self::_log_mysql_error( $db->get_db(), $sql_status );
            wp_die( "Problem updating application status.\n\tSQL: " . $sql_status );
        }

        // Now do the files. The resume is required, so it shouldn't be
        // empty.
        $files = [];
        if( !empty( $_FILES ) ) {
            // Create the new application directory path.
            $path = self::$application_file_path . "\\$id";
            // Make the directory
            if( mkdir( $path ) ) {
                $files_to_scan = [];
                // Loop through the files
                foreach( $_FILES as $name => $info ) {
                    // Some cursory validation checks.
                    if( self::_validate_file( $info ) ) {
                        $files_to_scan[$name] = $info['tmp_name'];
                    }
                    else {
                        error_log( "Problem validating file: " . $info['name'] . "\n\t" . print_r( $info, true ) );
                        wp_die( "Problem validating file." );
                    }
                }

                // Scan the files and get the clean ones.
                $scan_results = self::_scan_files( $files_to_scan );
                foreach( $scan_results['clean'] as $name => $tmp_name ) {
                    $file = $_FILES[$name];
                    $filename = $file['name'];

                    $moved = move_uploaded_file( $tmp_name, "$path\\$filename" );

                    if( !$moved ) {
                        error_log( "Could not move file $filename to target directory." );
                        wp_die( "Problem uploading file $filename." );
                    }

                    $files[] = [
                        'filename' => $filename,
                        'filetype' => $file['type'],
                        'filesize' => $file['size'],
                    ];
                }

                if( !empty( $scan_results['infected'] ) ) {
                    $infected_file_list = "";
                    foreach( $scan_results['infected'] as $name => $not_used ) {
                        $infected_file_list .= $_FILES[$name]['name'] . " ";
                    }
                    $return_msg = "Some files contained malicious code and were not uploaded: $infected_file_list";
                }
            }
            else {
                // Log errors.
                error_log( "Problem creating application directory." );
                wp_die( "Problem uploading files." );
            }
        }

        // Serialize the file array.
        $file_str = !empty( $files ) ? serialize( $files ) : '';

        // Insert into database.
        if( !empty( $file_str ) ) {
            $sql_files = "UPDATE applications SET document_attachments = '$file_str' WHERE id = $id";

            if ( !mysqli_query( $db->get_db(), $sql_files ) ) {
                self::_log_mysql_error( $db->get_db(), $sql_files );
            }
        }

        // Send applicant a confirmation email
        $to = [
            'addr' => $_POST['email'],
            'name' => $_POST['fname'] . " " . $_POST['lname'],
        ];

        $from = [
            'addr' => 'music@example.com',
            'name' => 'UCF Theatre',
        ];

        $subject = "Confirmation of Theatre Audition";

        ob_start();
        ?>
        <table style="width: 100%;">
            <tr>
                <td>
                    <table style="width: 600px; margin: 1.5em auto;">
                        <tr>
                            <td>
                                <h2>Thank you for your interest in Theatre UCF!</h2>
                                <p>Your audition application has been successfully submitted. Your application number is <strong><?= $id ?></strong>. A representative from Theatre UCF will contact you soon to confirm your final audition date and time.</p>
                                <p>We're excited to meet you and to see what you can bring to UCF!</p>
                                <p style="margin-top: 3em; font-size: 16px;"><strong>&ndash; Theatre UCF</strong></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?
        $body = ob_get_clean();

        $mail = new Mailer( [$to], $from, $subject, $body );
        if( !$mail->send() ) {
            error_log( $mail->getError() );
            $return_msg = "Your application was successfully submitted, but there was a problem generating your confirmation email. Your application number is: $id. A representative from Theatre UCF will contact you soon, but if you have any questions, please contact them at [Contact Info].";
        }

        wp_die( $return_msg );
    }


    private static function _validate_file( array $file ) : bool {
        extract( $file );

        if( $error ) return false;

        $filename_good = is_uploaded_file( $tmp_name );
        $filename_content_good = ( preg_match( "/^[-0-9A-Z_\.]+$/i", $name ) ? true : false );
        $filename_length_good = ( mb_strlen( $name, 'UTF-8' ) <= 255 ? true : false );

        $filetype_good = in_array( $type, self::$allowed_file_types );

        return $filename_good && $filename_content_good && $filename_length_good && $filetype_good;
    }


    private static function _scan_files( array $files ) : array {
        $db = "C:\\ProgramData\\.clamwin\\db\\";

        $file_list = implode( " ", $files );

        $output = shell_exec( "C:\\\"Program Files (x86)\"\\ClamWin\\bin\\clamscan.exe --database=$db $file_list" );

        $results = [];

        // We need an array to hold RegEx matches.
        $matches = [];
        // Check to see if we have any infected files.
        if( preg_match( "/Infected\sfiles:\s(\d+)/", $output, $matches ) && intval( $matches[1] ) > 0 ) {

            // Setting up some containers.
            $clean_files = [];
            $infected = [];
            $infected_tmp = [];

            // Find the names of the infected files.
            // (This pattern is for Windows)
            preg_match_all( '/([A-Z]:(\\\[-_\w\.]+)+\.tmp):\s[-_\w\.]+\sFOUND/', $output, $matches );

            // Add any matches to the list.
            for( $i = 0; $i < count( $matches[1] ); $i++ ) {
                $infected_tmp[] = $matches[1][$i];
            }

            // Add the rest of the files to the clean files list.
            foreach( $files as $name => $file ) {
                if( !in_array( $file, $infected_tmp ) ) {
                    $clean_files[$name] = $file;
                }
                else {
                    $infected[$name] = $file;
                }
            }

            $results = [
                'clean' => $clean_files,
                'infected' => $infected,
            ];
        }
        else {
            $results = [
                'clean' => $files,
                'infected' => [],
            ];
        }

        return $results;
    }


    private static function _log_mysql_error( mysqli $connection, string $sql ) {
        if( mysqli_errno( $connection ) ) {
            error_log( "MySQL Error " . mysqli_errno( $connection ) . ": " . mysqli_error( $connection ) . "\n\tSQL: " . $sql );
        }
        else {
            error_log( "Unspecified error with database insert:\n\tSQL: " . $sql );
        }
    }
}
?>