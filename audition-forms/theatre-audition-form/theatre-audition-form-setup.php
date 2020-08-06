<?php
namespace UCF\CAH\SPA\Theatre\AuditionForm;

// Figures out if we're on my local server; if not, assumes we're on PROD
if( !defined( 'IS_DEV' ) ) {
    define( 'IS_DEV', file_exists( "D:\\wamp64" ) );
}

// This will handle DB Access
require_once get_stylesheet_directory() . "/includes/db-helper.php";

use DirectoryIterator;
use UCF\CAH\DB_Helper as DB;

// This will make it easier to interface with PHPMailer
require_once get_stylesheet_directory() . "/includes/phpmailer-helper.php";
use UCF\CAH\MailTools\PHPMailerHelper as Mailer;

// Bring the mysqli connection object into the current namespace
use mysqli as mysqli;

/**
 * Registers and enqueues the JavaScript and CSS for the Theatre Audition Form.
 * Also handles AJAX calls and back-end processing from the form.
 * 
 * @author Mike W. Leavitt
 * @version 1.0.0
 */
final class AuditionFormSetup
{
    // The slug of our target page
    private static $target_slug = "theatre/apply";

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

    private function __construct() { /* Prevents instantiation */ }


    /**
     * Registers all our actions: registering/enqueuing scripts and styles,
     * and sets up the AJAX handlers. This is a public form, so we want the
     * same action for both standard and nopriv.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @return void
     */
    public static function setup() {

        // Priority set to 5 to ensure the scripts are registered before they
        // can be enqueued
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_scripts' ], 5, 0);
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'maybe_enqueue_scripts' ], 10, 0 );

        add_action( 'wp_ajax_theatre_form_submit', [ __CLASS__, 'handle_ajax' ], 10, 0 );
        add_action( 'wp_ajax_nopriv_theatre_form_submit', [ __CLASS__, 'handle_ajax' ], 10, 0 );
    }


    /**
     * Register our scripts and styles, for later enqueueing.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @return void
     */
    public static function register_scripts() {
        // Storing the URI and Path to the dist directory to make the function
        // calls cleaner.
        $uri = get_stylesheet_directory_uri() . "/audition-forms/theatre-audition-form/dist";
        $path = get_stylesheet_directory() . "/audition-forms/theatre-audition-form/dist";

        // Register the Chunk JS file, which is a dependency of the main script
        wp_register_script(
            'theatre-form-chunk',
            "$uri/js/chunk-theatre-audition-form.js",
            [],
            filemtime( "$path/js/chunk-theatre-audition-form.js" ),
            true 
        );

        // Register the main script
        wp_register_script(
            'theatre-form-script',
            "$uri/js/theatre-audition-form.js",
            [ 'theatre-form-chunk' ],
            filemtime( "$path/js/theatre-audition-form.js" ),
            true
        );

        // Register the CSS
        wp_register_style(
            'theatre-form-style',
            "$uri/css/theatre-audition-form.css",
            [],
            filemtime( "$path/css/theatre-audition-form.css" ),
            'all'
        );
    }


    /**
     * Check and see if we need to enqueue the script. We're doing it by slug,
     * here, but we could also check the page content for shortcodes, if we
     * were doing this as a plugin and not part of the theme.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @return void
     */
    public static function maybe_enqueue_scripts() {
        
        // Get the post, and return if there isn't one (prevents a certain
        // error from popping up about $post not having a valid index)
        global $post;
        if( !isset( $post ) || !is_object( $post ) ) return;

        // Get the page slug
        $slug = $post->post_name;
        $parent_slug = get_post( wp_get_post_parent_id( $post->ID ) )->post_name;

        // Test against the slug we defined as a class member, above.
        if( "$parent_slug/$slug" === self::$target_slug ) {

            // We only need to enqueue the main script, since we registered it
            // with the chunk script as a dependency.
            wp_enqueue_script( 'theatre-form-script' );

            // Require reCAPTCHA file

            $rc_path = "";
            if( IS_DEV ) {
                $rc_path = "D:\\wamp64\\php-helpers\\lib\\";
            }

            require_once $rc_path . "recaptcha.php";

            // Send some variables that Vue will need to the front-end.
            wp_localize_script(
                'theatre-form-script',
                'wpVars',
                [
                    'baseUrl' => get_stylesheet_directory_uri(),
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'reCAPTCHA' => $siteKey,
                    'lang' => $lang,
                ]
            );

            // Enqueue the style, too.
            wp_enqueue_style( 'theatre-form-style' );
        }
    }


    /**
     * Handles our AJAX calls from the form, since we won't be doing a standard
     * POST submission and reloading the page.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @return void
     */
    public static function handle_ajax() {

        // Validate nonce from front-end
        if( !check_ajax_referer( 'theatre_form_submit', 'theatre-form-nonce' ) ) {
            wp_die( 'Invalid nonce.' );
        }

        // Pull in reCAPTCHA file
        $rc_path = "";
        if( IS_DEV ) {
            $rc_path = "D:\\wamp64\\php-helpers\\lib\\";
        }

        require_once $rc_path . "recaptcha.php";

        if( isset( $_POST['g-recaptcha-response'] ) ) {
            $recaptcha = new \ReCaptcha\ReCaptcha( $secret );
            $resp = $recaptcha->verify( $_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'] );

            if( !$resp ) {
                wp_die( "reCAPTCHA validation failed." );
            }
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
        $sql_base = "INSERT INTO applications (last_name, first_name, email, `address`, phone, last_school, preferred_name, pronouns, pronoun_other, program, first_choice_date, second_choice_date, audition_is_zoom) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s', '%s', %d)";

        // Build the SQL string
        $sql = sprintf( $sql_base, $_POST['lname'], $_POST['fname'], $_POST['email'], $_POST['address'], $_POST['phone'], $_POST['lastSchool'], $_POST['preferredName'], $_POST['pronouns'], $_POST['pronounOther'], $program_code, $_POST['firstChoiceDate'], $_POST['secondChoiceDate'], $_POST['auditionIsZoom'] );

        // Create a new DB_Helper object
        $db = new DB('spa_auditions_theatre');

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

        if( IS_DEV ) {
            $application_file_path = "D:\\wamp64\\application-files\\spa\\theatre";
        }
        else {
            $application_file_path = "D:\\inetpub\\store\\spa\\theatre\\audition-files";
        }

        $files = [];
        if( !empty( $_FILES ) ) {
            // Create the new application directory path based on the user's
            // application ID in the database.
            $path = $application_file_path . "\\$id";
            // Make the directory
            if( mkdir( $path ) ) {
                $files_to_scan = [];
                // Loop through the files
                foreach( $_FILES as $name => $info ) {
                    // Some cursory validation checks.
                    if( self::_validate_file( $info ) ) {
                        // If it seems legit, add it to the scan queue
                        $files_to_scan[$name] = $info['tmp_name'];
                    }
                    else {
                        error_log( "Problem validating file: " . $info['name'] . "\n\t" . print_r( $info, true ) );
                        wp_die( "Problem validating file." );
                    }
                }

                // Scan the files and get the clean ones. On my dev comp, this
                // takes a while for the AV to spin up its definition db; not
                // sure if it'll be better on a dedicated server.
                $scan_results = self::_scan_files( $files_to_scan );

                // Keep the good files, ditch the bad ones.
                foreach( $scan_results['clean'] as $name => $tmp_name ) {
                    $file = $_FILES[$name];
                    $filename = $file['name'];

                    // Move clean files to the application directory.
                    $moved = move_uploaded_file( $tmp_name, "$path\\$filename" );

                    // Throw an error if there's a problem.
                    if( !$moved ) {
                        error_log( "Could not move file $filename to target directory." );
                        wp_die( "Problem uploading file $filename." );
                    }

                    // Store the file info for adding to the db
                    $files[] = [
                        'filename' => $filename,
                        'filetype' => $file['type'],
                        'filesize' => intval( $file['size'] ),
                    ];
                }

                // Give the user a list of infected files that weren't
                // uploaded, so they know.
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

        // Create the email body. This is a bland placeholder for now.
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
        <?php
        $body = ob_get_clean();

        // Create the email object. For source and usage, see
        // phpmailer-helper.php
        $mail = new Mailer( [$to], $from, $subject, $body );

        // Try to send it, and send back an error message if it fails.
        if( !$mail->send() ) {
            error_log( $mail->getError() );
            $return_msg = "Your application was successfully submitted, but there was a problem generating your confirmation email. Your application number is: $id. A representative from Theatre UCF will contact you soon, but if you have any questions, please contact them at [Contact Info].";
        }

        // Make sure we kill the AJAX process on the back-end, just to be safe,
        // and return whatever our status message is.
        wp_die( $return_msg );
    }


    /**
     * Performs some cursory file validation (pre-virus-scan).
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @param array $file  The information on that file from $_FILES
     * 
     * @return bool  Whether the file is valid
     */
    private static function _validate_file( array $file ) : bool {

        // Break up the array for cleaner code
        extract( $file );

        // If there was an error of some kind, it's not valid
        if( $error ) return false;

        // Check to make sure the file is actually there
        $filename_good = is_uploaded_file( $tmp_name );

        // Check for valid filename
        $filename_content_good = ( preg_match( "/^[-0-9A-Z_\.]+$/i", $name ) ? true : false );

        // Check length of filename
        $filename_length_good = ( mb_strlen( $name, 'UTF-8' ) <= 255 ? true : false );

        // Check that the MIME type is one we allow
        $filetype_good = in_array( $type, self::$allowed_file_types );

        // All of those must be true for the file to be okay to continue
        return $filename_good && $filename_content_good && $filename_length_good && $filetype_good;
    }


    /**
     * Perform the virus scanning
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @param array $files  The array of files to scan, only the tmp_names
     * 
     * @return array  The files, organized into 'clean' and 'infected'
     */
    private static function _scan_files( array $files ) : array {

        // The path to the Microsoft virus scanner application
        /* -- For DEV -- */
        if( IS_DEV ) {
            $path = "C:\\ProgramData\\Microsoft\\Windows Defender\\Platform";
            $dir = new DirectoryIterator( $path );

            // On Windows 10 systems, you have to do all this crap to find
            // the right version and it's stupid
            $latest = null;
            $v = '';
            foreach( $dir as $file ) {
                $pattern = '/(\d+\.\d+\.\d+)(\.\d-\d)/';
                $matches = [];
                preg_match( $pattern, $file->getFilename(), $matches );
                
                if( !empty( $matches ) ) {
                    $date = date_create_from_format( 'n.j.Y', $matches[1] );

                    if( is_null( $latest ) || $latest <= $date ) {
                        $latest = $date;
                        $v = $matches[2];
                    }
                }
            }

            if( is_null( $latest ) ) {
                error_log( "Could not parse Windows Defender version folder" );
                wp_die( "Could not parse Windows Defender version folder" );
            }
            $win_def_version = date_format( $latest, 'n.j.Y' ) . $v;

            $cmdpath = "C:\\ProgramData\\Microsoft\\\"Windows Defender\"\\Platform\\$win_def_version\\MpCmdRun.exe -Scan -ScanType 3 -ReturnHR -DisableRemediation -File";
        }
        /* -- FOR PROD -- */
        else {
            // Much simpler, because we don't need to check for a dated version folder
            $cmdpath = "C:\\\"Program Files\"\\\"Microsoft Security Client\"\\MpCmdRun.exe -Scan -ScanType 3 -ReturnHR -File";    
        }

        // Create containers
        $infected_files = [];
        $results = [
            'clean' => [],
            'infected' => [],
        ];

        // We're going to run the scans one at a time to avoid having to scan
        // the whole PHP temp directory every time.
        foreach( $files as $file ) {
            // Run the scan
            $output = shell_exec( "$cmdpath $file" );

            error_log( "Scan result for $file: $output" );

            $threat_pattern = '/found (\d+) threats/';
            $matches = [];
            preg_match( $threat_pattern, $output, $matches );

            if( !empty( $matches ) && intval( $matches[1] ) > 0 ) {
                $filename_pattern = '/file\s+:\s+([A-Z]:\\\([-_\w\.\\\]+)\.tmp)/';
                $matches = [];
                preg_match( $filename_pattern, $output, $matches );

                if( !empty( $matches ) ) {
                    $infected_files[] = $matches[1];
                }
            }
        }

        // Check to see if anything is infected
        if( !empty( $infected_files ) ) {
            // We have to get a bit creative here because we've already used
            // foreach on $files and PHP iterators are garbage
            foreach( array_keys( $files ) as $key ) {
                // Sort the files
                if( in_array( $files[$key], $infected_files ) ) {
                    $results['infected'][$key] = $files[$key];
                }
                else {
                    $results['clean'][$key] = $files[$key];
                }
            }
        }
        // If $infected_files is empty, all the files are clean
        else {
            $results['clean'] = $files;
        }

        return $results;
    }


    /**
     * Logs a MySQL error. Just a DRY way to keep from typing the same
     * couple of lines of code over and over.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @param mysqli $connection  The database connection
     * @param string $sql  The SQL that generated the issue.
     * 
     * @return void
     */
    private static function _log_mysql_error( mysqli $connection, string $sql ) {
        // Well tack this onto the end of whatever-it-is
        $msg = "\n\tSQL: $sql";

        // If we have an error number, we'll print the specific error, as well as the SQL
        if( mysqli_errno( $connection ) ) {
            $msg = "MySQL Error " . mysqli_errno( $connection ) . ": " . mysqli_error( $connection ) . $msg;
        }
        else {
            $msg = "Unspecified error with database insert:$msg";
        }
        
        // Log the error.
        error_log( $msg );
    }
}
?>