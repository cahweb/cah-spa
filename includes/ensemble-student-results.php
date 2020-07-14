<?php
/**
 * Sets up a Dashboard page for viewing/interacting with the response data from the music Ensemble Interest Form
 * 
 * @author Mike W. Leavitt
 * @version 1.0.0
 */

namespace UCF\CAH\SPA\Music;

defined( 'ABSPATH' ) or die( 'No direct access plzthx' );

// Bring in our database helper class.
require_once 'db-helper.php';
use UCF\CAH\DB_Helper as db;

// Bring mysqli_result into the current namespace
use mysqli_result;

class EnsembleAdminPage
{
    private function __construct() {} // Prevents instantiation


    /**
     * Sets up the actions that will create our menu and load our CSS on the page.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @return void
     */
    public static function setup() {
        if( !is_admin() ) return;

        add_action( 'admin_menu', [ __CLASS__, 'register_menu' ], 10, 0 );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'maybe_enqueue_style' ] );
    }


    /**
     * Registers the top-level menu page.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @return void
     */
    public static function register_menu() {
        // I did them as different variables to make things slightly easier to
        // tweak, if necessary
        $title = 'Manage Ensemble Interest Form Submissions';
        $label = 'Ensemble Interest';
        $user_can = 'publish_pages'; // Should grab Editor role and above
        $slug = 'cah-spa-ensemble-form-manage';
        $callback = [ __CLASS__, 'build' ];
        $icon = 'dashicons-portfolio';
        $position = 26;

        // Save return value to a variable for debug purposes
        $hook = add_menu_page( $title, $label, $user_can, $slug, $callback, $icon, $position );

        $subhook = add_submenu_page( 
            $slug, 
            "Download CSV", 
            "Download CSV", 
            'publish_pages', 
            'cah-spa-ensemble-csv', 
            function() {}
        );

        add_action( "load-$subhook", [ __CLASS__, 'download' ], 10, 0 );

        // For debug
        //error_log( $hook );
        //error_log( "Submenu hook: $subhook" );
    }


    /**
     * Builds the HTML for the menu page.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     */
    public static function build() {
        
        // Get the info from the database. Will return either
        // a mysqli_result or NULL
        $student_info = self::_get_student_info();
        ?>
        <div clas="wrap">
            <h1>Ensemble Interest Form Respondents</h1>
            <div class="button-bar">
                <a href="<?= admin_url( 'admin.php' ) . '?page=cah-spa-ensemble-csv' ?>" class="button button-primary">Download CSV</a>
            </div>
            <table id="ensemble-results">
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Year</th>
                    <th>Major</th>
                    <th>Instrument(s)</th>
                    <th>Ensembles</th>
                    <th>Notes</th>
                </tr>
                <?php if( !is_null( $student_info ) ) : // If the value isn't null, we know we can use it
                    
                    // Keep track of the current ID number, so we don't list a student's name twice
                    $current_id = 0;

                    // Loop through the results
                    while( $entry = mysqli_fetch_assoc( $student_info ) ) : 

                        // Storing this value, since we'll refer to it a lot.
                        $new = $entry['id'] !== $current_id;

                        // Make it the new $current_id, if it's new
                        if( $new ) {
                            $current_id = $entry['id'];
                        }

                        // Unserialize the list of instruments and interests, since they're serialized
                        // in the database.
                        $instruments = unserialize( $entry['instruments'] );
                        $interests = unserialize( $entry['interests'] );

                        // If they put "Other" for their year, replace it with the alternate value.
                        $year = 'Other' === $entry['year'] ? $entry['year-other'] : $entry['year'];
                ?>
                <tr>
                    <td class="lname"><?= $new ? $entry['lname'] : '' ?></td>
                    <td class="fname"><?= $new ? $entry['fname'] : '' ?></td>
                    <td class="email"><?= $new ? '<a href="mailto:' . $entry['email'] . '">' . $entry['email'] . '</a>' : '' ?></td>
                    <td class="phone"><?= '<a href="tel:+1' . $entry['phone'] . '">' . self::_format_phone( $entry['phone'] ) . '</a>' ?></td>
                    <td class="year"><?= $year ?></td>
                    <td class="major"><?= $entry['major'] ?></td>
                    <td><?= is_array( $instruments ) ? implode( ', ', $instruments ) : $instruments ?></td>
                    <td><?= is_array( $interests ) ? implode( ', ', $interests ) : $interests ?></td>
                    <td><?= $entry['notes'] ?></td>
                </tr>
                <?php endwhile; else : ?>
                <tr>
                    <td class="no-result" colspan="8"><strong><em>No entries to display.</em></strong></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <?php
    }


    public static function build_csv() {
        ?>
        <div class="wrap">
            <h4>[NO DATA]</h4>
        </div>
        <?php
    }


    public static function download() {

        if( !is_user_logged_in() || !current_user_can( 'publish_pages' ) ) wp_die( "You are not authorized to view this page. " );

        if( strpos( $_SERVER['REQUEST_URI'], 'admin.php' ) === false || $_GET['page'] !== 'cah-spa-ensemble-csv' ) return;

        $student_info = self::_get_student_info();

        if( $student_info ) {

            $filename = "Ensemble-Interest_" . date( 'Ymd' ) . ".csv";

            header( "Content-Type: text/csv; charset=utf-8" );
            header( "Content-Description: File Transfer" );
            header( "Content-Disposition: attachment; filename=$filename" );
            header( "Pragma: no-cache" );

            $output = fopen( 'php://output', 'w' );

            $headers = [
                'Last Name',
                'First Name',
                'Email',
                'Phone',
                'Year',
                'Major',
                'Instrument(s)',
                'Ensembles',
                'Notes',
            ];
        
            fputcsv( $output, $headers );
        
            while( $row = mysqli_fetch_assoc( $student_info ) ) {
                
                $instruments = unserialize( $row['instruments'] );
                $instruments = is_array( $instruments ) ? implode(',', $instruments ) : $instruments;
        
                $interests = unserialize( $row['interests'] );
                $interests = is_array( $interests ) ? implode( ',', $interests ) : $interests;
        
                $year = 'Other' === $row['year'] ? $row['year-other'] : $row['year'];
        
                $line = [
                    $row['lname'],
                    $row['fname'],
                    $row['email'],
                    $row['phone'],
                    $year,
                    $row['major'],
                    $instruments,
                    $interests,
                    $row['notes'],
                ];
        
                fputcsv( $output, $line );
            }
        
            fclose( $output );
        }
        else {
            return;
        }

        exit();
    }


    /**
     * Loads our CSS if we're on the right page.
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @param string $hook - passed from WordPress, the unique page hook it provides
     *                          when you register the menu page.
     * 
     * @return void
     */
    public static function maybe_enqueue_style( $hook ) {
        
        if( 'toplevel_page_cah-spa-ensemble-form-manage' !== $hook ) return;

        wp_enqueue_style( 'ensemble-form-style', get_stylesheet_directory_uri() . '/dist/css/ensemble-student-results.css' );
    }


    /**
     * Gets the student information from the database. Login and authorization
     * is handled in the DB_Helper class from db-helper.php
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @return mysqli_result|null - Only returns a result if we get a useable
     *                                  answer.
     */
    private static function _get_student_info() : ?mysqli_result {
        $db = new db();

        $sql = "SELECT * FROM students LEFT JOIN entries ON students.id = entries.student_id ORDER BY students.lname, students.fname, entries.id";

        $result = $db->query( $sql );

        if( $result instanceof mysqli_result && $result->num_rows > 0 ) {
            return $result;
        }
        else return null;
    }


    /**
     * Formats a 10-digit phone number to add the parentheses, spacing, and
     * hyphen. Doesn't format numbers without area code OR international numbers
     * 
     * @author Mike W. Leavitt
     * @since 1.0.0
     * 
     * @param string $phone - The input phone number
     * 
     * @return string - The formatted (or not) phone number
     */
    private static function _format_phone( string $phone ) : string {
        if( 10 == strlen( $phone ) ) {
            $area = substr( $phone, 0, 3 );
            $first = substr( $phone, 3, 3 );
            $last = substr( $phone, 6 );

            return "($area) $first-$last";
        }
        else return $phone;
    }
}
?>