<?php
namespace UCF\CAH\SPA\Music\AuditionForm;

if( !defined( 'IS_DEV' ) ) {
    define( 'IS_DEV', file_exists( "D:\\wamp64" ) );
}

require_once get_stylesheet_directory() . "/includes/db-helper.php";

use UCF\CAH\DB_Helper as DB;

require_once get_stylesheet_directory() . "/includes/phpmailer-helper.php";
use UCF\CAH\MailTools\PHPMailerHelper as Mailer;

use mysqli as mysqli;

final class AuditionFormSetup
{
    private static $target_slug = "music/apply";

    private function __construct() {}

    public static function setup() {

        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_scripts' ], 5, 0 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'maybe_enqueue_scripts' ], 10, 0 );

        add_action( 'wp_ajax_music_form_submit', [ __CLASS__, 'handle_ajax' ], 10, 0 );
        add_action( 'wp_ajax_nopriv_music_form_submit', [ __CLASS__, 'handle_ajax' ], 10, 0 );
    }


    public static function register_scripts() {

        $uri = get_stylesheet_directory_uri() . "/audition-forms/music-audition-form/dist";
        $path = get_stylesheet_directory() . "/audition-forms/music-audition-form/dist";

        wp_register_script(
            "music-form-chunk",
            "$uri/js/chunk-music-audition-form.js",
            [],
            filemtime( "$path/js/chunk-music-audition-form.js" ),
            true
        );

        wp_register_script(
            "music-form-script",
            "$uri/js/music-audition-form.js",
            [ "music-form-chunk" ],
            filemtime( "$path/js/music-audition-form.js" ),
            true
        );

        wp_register_style(
            "music-form-style",
            "$uri/css/music-audition-form.css",
            [],
            filemtime( "$path/css/music-audition-form.css" ),
            'all'
        );
    }


    public static function maybe_enqueue_scripts() {

        global $post;
        if( !isset( $post ) || !is_object( $post ) ) return;

        $slug = $post->post_name;
        $parent_slug = get_post( wp_get_post_parent_id( $post->ID ) )->post_name;

        if( "$parent_slug/$slug" === self::$target_slug ) {

            wp_enqueue_script( "music-form-script" );

            $rc_path = "";
            if( IS_DEV ) {
                $rc_path = "D:\\wamp64\\php-helpers\\lib\\";
            }

            require_once $rc_path . "recaptcha.php";

            wp_localize_script(
                "music-form-script",
                "wpVars",
                [
                    "baseUrl" => get_stylesheet_directory_uri(),
                    "ajaxUrl" => admin_url( 'admin-ajax.php' ),
                    "reCAPTCHA" => $siteKey,
                    "lang" => $lang,
                ]
            );

            wp_enqueue_style( "music-form-style" );
        }
    }


    public static function handle_ajax() {

        if( !check_ajax_referer( "music_form_submit", "music-form-nonce" ) ) {
            wp_die( "Invalid nonce." );
        }

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

        $return_msg = "Your application has been successfully submitted! You should receive a confirmation email shortly.";

        $program_lookup = [
            null,
            'ba-music',
            'bm-performance',
            'bm-jazz',
            'bm-composition',
            'bme',
            'ma-music',
            'ma-conducting',
        ];

        $program_code = array_search( $_POST['program'], $program_lookup );

        if( !$program_code ) {
            wp_die( "Invalid Program selection." );
        }

        // TODO: Build SQL string

        $db = new DB( 'spa_auditions_music' );

        $fields = [
            "firstName",
            "lastName",
            "email",
            "phone",
            "address",
            "parentName",
            "preferredName",
            "pronouns",
            "pronounOther",
            "program",
            "year",
            "schoolName",
            "schoolCounty",
            "instrument",
            "instrumentYears",
            "date",
        ];

        $column_arr = [];
        $value_arr = [];

        foreach( $fields as $field ) {
            if( isset( $_POST[$field] ) && !empty( $_POST[$field] ) ) {
                $column_arr[] = $field;

                if( $field === "instrumentYears" ) {
                    $value_arr[] = intval( $_POST[$field] );
                }
                else if( $field === "program" ) {
                    $value_arr[] = $program_code;
                }
                else {
                    $value_arr[] = "'" . htmlentities( mysqli_real_escape_string( $db->get_db(), $_POST[$field] ) ) . "'";
                }
            }
        }

        $columns = implode( ", ", $column_arr );
        $values = implode( ", ", $value_arr );

        $sql = "INSERT INTO applications ($columns) VALUES ($values)";

        $id = 0;
        if( mysqli_query( $db->get_db(), $sql ) ) {
            $id = $db->get_db()->insert_id;
        }

        if( !$id ) {
            self::_log_mysql_error( $db->get_db(), $sql );
            wp_die( "Problem inserting data to Database\n\tSQL: " . $sql );
        }

        // TODO: Build SQL string for status insert.
        $sql_status = "INSERT INTO application_status (student_id, `status`) VALUES ($id, 1)";

        if( !mysqli_query( $db->get_db(), $sql_status ) ) {
            self::_log_mysql_error( $db->get_db(), $sql_status );
            wp_die( "Problem updating application status.\n\tSQL: " . $sql_status );
        }

        // Send application a confirmation email.
        $to = [
            'addr' => $_POST['email'],
            'name' => $_POST['fname'] . " " . $_POST['lname'],
        ];

        $from = [
            'addr' => 'music@ucf.edu',
            'name' => 'UCF Music',
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
                                <h2>Thank you for your interest in UCF Music!</h2>
                                <p>Your audition application has been successfully submitted. Your application number is <strong><?= $id ?></strong>. A representative from UCF Music will contact you soon to confirm your final audition date and time.</p>
                                <p>We're excited to meet you and to see what you can bring to UCF!</p>
                                <p style="margin-top: 3em; font-size: 16px;"><strong>&ndash; UCF Music</strong></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php
        $body = ob_get_clean();

        $mail = new Mailer( [$to], $from, $subject, $body );

        if( !$mail->send() ) {
            error_log( $mail->getError() );
            $return_msg = "Your application was successfully submitted, but there was a problem generating your confirmation email. Your application number is: $id. A representative from UCF Music will contact you soon, but if you have any questions, please contact them at [Contact Info].";
        }

        wp_die( $return_msg );
    }


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