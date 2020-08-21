<?php
namespace UCF\CAH\SPA\Theatre\AuditionForm;

require_once get_stylesheet_directory() . "/includes/db-helper.php";
use UCF\CAH\DB_Helper as DBHelper;

use mysqli as mysqli;
use mysqli_result as mysqli_result;

final class AuditionFormAdmin
{

    private static $text_domain = "cah-spa-audition-theatre";

    // For DEV
    private static $application_file_path = "D:\\wamp64\\application-files\\spa\\theatre";

    // For PROD
    //private static $application_file_path = "D:\\inetpub\\store\\spa\\theatre\\audition-files";

    private function __construct() { /*Prevents instantiation */ }

    public static function setup() {
        if( !is_admin() ) return;

        add_action( 'admin_menu', [ __CLASS__, 'register_admin_page' ], 10, 0 );

        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'register_scripts' ], 5, 0 );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'maybe_enqueue_scripts' ] );
    }


    public static function register_admin_page() {
        add_menu_page(
            self::_wp_text( 'Theatre Audition Applications' ),
            'Theatre Auditions',
            'manage_options',
            'theatre-audition-menu',
            [ __CLASS__, 'build' ],
            'dashicons-clipboard',
            30
        );

        $hook = add_submenu_page(
            'theatre-audition-menu',
            'Theatre Audition Downloads',
            'Download',
            'manage_options',
            'theatre-audition-downloads',
            function() {}
        );
        add_action( "load-$hook", [ __CLASS__, 'download' ], 10, 0 );
    }


    public static function register_scripts() {
        // Put admin page styles here
        $uri = get_stylesheet_directory_uri() . "/audition-forms/theatre-audition-form/includes";
        $path = get_stylesheet_directory() . "/audition-forms/theatre-audition-form/includes";
        wp_register_style(
            'theatre-audition-admin-style',
            "$uri/theatre-audition-form-admin.css",
            [],
            filemtime( "$path/theatre-audition-form-admin.css" ),
            'all'
        );
    }


    public static function maybe_enqueue_scripts( $hook ) {
        // Check if we're on the right page, and load style if so

        error_log( "Page Hook: $hook" );
        if( 'toplevel_page_theatre-audition-menu' !== $hook ) return;

        wp_enqueue_style( 'theatre-audition-admin-style' );
    }


    public static function build() {
        // Build the page -- will involve querying the DB
        $db = new DBHelper( 'spa_auditions_theatre' );

        $applications = self::_get_applications( $db->get_db() );

        // TODO: Retrieve/list the attached files, and allow the user to download them
        ?>
        <div class="wrap">
            <h1>Theatre Audition Applicants</h1>
            <table>
                <tr>
                    <th>Application #</th>
                    <th>Status</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Program</th>
                    <th>First Choice</th>
                    <th>Second Choice</th>
                    <th>Resume</th>
                    <th>Other Documents</th>
                </tr>
                <?php if( !empty( $applications ) ) : ?>
                    <?php 
                    foreach ( $applications as $row ) : 
                        $docs = unserialize( $row['document_attachments'] );

                        $resume = $docs['resume']['filename'];
                        $extra = [];
                        if( isset( $docs['extra'] ) ) {
                            foreach( $docs['extra'] as $doc ) {
                                $extra[] = $doc['filename'];
                            }
                        }
                    ?>
                <tr>
                    <td><?= $row['app_id'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['last_name'] ?></td>
                    <td><?= $row['first_name'] ?></td>
                    <td><a href="mailto:<?= $row['email'] ?>"><?= $row['email'] ?></a></td>
                    <td><?= preg_replace( "/,/", "<br />", $row['address'], substr_count( $row['address'], ',') - 1 ) ?></td>
                    <td><a href="tel:+1<?= $row['phone'] ?>"><?= self::_format_phone( $row['phone'] ) ?></a></td>
                    <td><?= $row['program'] ?></td>
                    <td><?= $row['first_choice_date'] ?></td>
                    <td><?= $row['second_choice_date'] ?></td>
                    <td>
                        <a href="<?= admin_url( "admin.php" ) . "?page=theatre-audition-downloads&app_id={$row['app_id']}&document=" . htmlentities( $resume ) ?>">
                            <span class="dashicons dashicons-media-text" style="text-decoration: none;"></span><?= $resume ?>
                        </a>
                    </td>
                    <td><?= !empty( $extra ) ? implode( ", ", $extra ) : "" ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php
        else:
        ?>
        <p><strong>No results.</strong></p>
        <?php endif; ?>
        </div>
        <?php
    }


    public static function download() {

        if( strpos( $_SERVER['REQUEST_URI'], 'admin.php' ) === false || 'theatre-audition-downloads' !== $_GET['page'] || !isset( $_GET['app_id'] ) || !isset( $_GET['document'] ) ) {
            wp_redirect( admin_url( 'admin.php' ) . "?page=theatre-audition-menu", 302 );
            exit();
        }

        if( !is_user_logged_in() || !current_user_can( 'manage_options' ) ) {
            wp_die( "You are not authorized to be access this page." );
        }

        $db = new DBHelper( 'spa_auditions_theatre' );

        $sql = "SELECT document_attachments FROM applications WHERE id = {$_GET['app_id']} LIMIT 1";

        $result = mysqli_query( $db->get_db(), $sql );

        if( $result instanceof mysqli_result && $result->num_rows > 0 ) {

            $requested_doc = html_entity_decode( $_GET['document'] );

            $row = mysqli_fetch_assoc( $result );
            $docs = unserialize( $row['document_attachments'] );

            $doclist = [
                $docs['resume'],
            ];

            if( isset( $docs['extra'] ) && !empty( $docs['extra'] ) ) {
                foreach( $docs['extra'] as $doc ) {
                    $doclist[] = $doc;
                }
            }

            $have_file = false;
            $target = null;
            foreach( $doclist as $doc ) {
                if( $doc['filename'] === $requested_doc ) {
                    $have_file = true;
                    $target = $doc;
                    break;
                }
            }

            if( $have_file ) {
                
                header( "Content-Type: {$target['filetype']}; charset=utf-8" );
                header( "Content-Description: File Transfer" );
                header( "Content-Disposition: attachment; filename={$target['filename']}" );
                header( "Pragma: no-cache" );

                $path = self::$application_file_path . "\\{$_GET['app_id']}";

                readfile( "$path\\{$target['filename']}" );

                exit();
            }
        }
    }


    private static function _get_applications( mysqli $db, int $amount = 25, int $offset = 0 ) : array {

        $sql = "SELECT a.id AS app_id, ac.status_desc AS `status`, a.last_name, a.first_name, a.email, a.`address`, a.phone, p.description AS program, a.first_choice_date, a.second_choice_date, a.document_attachments FROM applications as a LEFT JOIN application_status as s ON s.application_id = a.id LEFT JOIN application_codes as ac ON s.status_code = ac.id LEFT JOIN program_codes as p ON a.program = p.id ORDER BY last_name, first_name LIMIT $offset, $amount";

        $result = mysqli_query( $db, $sql );

        if( $result instanceof mysqli_result && $result->num_rows > 0 ) {
            return mysqli_fetch_all( $result, MYSQLI_ASSOC );
        }

        return [];
    }


    private static function _format_phone( string $phone ): string {
        return "(" . substr( $phone, 0, 3 ) . ") " . substr( $phone, 3, 3 ) . "-" . substr( $phone, 6 );
    }


    private static function _wp_text( string $text, string $context = null ) : string {
        
        if( !is_null( $context ) ) {
            return _x( $text, $context, self::$text_domain );
        }

        return __( $text, self::$text_domain );
    }
}
?>