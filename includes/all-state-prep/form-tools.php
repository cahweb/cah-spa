<?php
namespace UCF\CAH\SPA\Music\AllStatePrep\FormTools;

include_once THEME_DIR . "/includes/_mailauth.php";

/**
 * Adds the enqueue action for a the Google reCAPTCHA script.
 */
function setup_captcha_wp()
{
    add_action( 'wp_enqueue_scripts', "UCF\\CAH\\SPA\\Music\\AllStatePrep\\FormTools\\enqueue_recaptcha", 0, 10);
}

/**
 * Callback to enqueue the Google reCAPTCHA script.
 */
function enqueue_recaptcha()
{
    wp_enqueue_script(
        'recaptcha-script',
        'https://www.google.com/recaptcha/api.js',
        [],
        null,
        false
    );
}

/**
 * Custom Exception class to show what's going wrong with the database connection
 */
class DatabaseException extends \Exception
{
    /**
     * Overrides Exception::__construct() to narrow down specific database errors. 42 is an arbitrary error code. Also passes in any previous errors, for Exception chaining.
     * 
     * @param mysqli $connection  The existing database connection, if applicable.
     * @param string $sql  The original SQL query, if applicable.
     * @param Throwable $previous  The Exception thrown before this one.
     * 
     * @return void
     */
    public function __construct( \mysqli $connection = null, string $sql = "", \Throwable $previous = null )
    {
        $msg = "";

        if( is_null( $connection ) )
        {
            $msg = "Error connecting to database: " . mysqli_connect_errno() . ": " . mysqli_connect_error();
        }
        else
        {
            $msg = "MySQL query error: (" . mysqli_errno( $connection ) . ") " . mysqli_error( $connection );

            if( !empty( $sql ) )
            {
                $msg .= "\n\n\tSQL: $sql";
            }
        }
        
        parent::__construct( $msg, 42, $previous );
    }
}

/**
 * Returns the music database connection, and creates one if it doesn't yet exist.
 * Throws a DatabaseException (defined above) if it experiences a connection error.
 * 
 * @return mysqli
 */
function get_db()
{
    global $musicdb_connection, $db_server;
    if( is_null( $musicdb_connection ) )
    {
        $musicdb_connection = mysqli_connect( $db_server, \MusicAllStateCreds::USER, \MusicAllStateCreds::PASS, \MusicAllStateCreds::DB );

        if( $musicdb_connection === false ) {
            throw new DatabaseException();
        }
    }
    return $musicdb_connection;
}

/**
 * Closes the Music database connection
 */
function close_db()
{
    global $musicdb_connection;
    if( !is_null( $musicdb_connection ) )
    {
        mysqli_close( $musicdb_connection );
    }
}

function scrub( string $value ) : string
{
    if( $db = get_db() )
    {
        return mysqli_real_escape_string( $db, htmlentities( trim( $value ) ) );
    }
}

/**
 * Checks to see if a given field is in $_POST, and returns it. Returns empty string if not.
 * 
 * @param string $name  Field name to check for
 * 
 * @return string
 */
function get_field( string $name ) : string
{
    if( isset( $_POST[$name] ) && !empty( $_POST[$name] ) )
    {
        return $_POST[$name];
    }
    
    return "";
}

/**
 * Gets the email body, with text updated to reflect the return to in-person clinics.
 * 
 * @param string    $name  Recipient name
 * @param string    $emailstring  HTML list of selected clinics
 * @param int       $agree  Whether they agree to allow their photo to be used
 * 
 * @return string
 */
function get_email_body( string $name, string $emailstring, int $agree ) : string
{
    ob_start();
    ?>

    <body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000; text-align: left;">
        <table style="width: 600px; border:none; border-collapse: collapse;">
            <tr>
                <td>
                    <table style="width: 100%; border: inherit; border-collapse: inherit;">
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <p style="margin-top: 0;">Dear <?= $name ?>,</p>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <p style="margin-top: 0;">Thank you for your interest in attending the UCF All-State Preparation Clinic. We have received your Student Registration Form and are looking forward to having you participate in the following clinic(s):</p>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <?= $emailstring ?>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <p style="margin-top: 0;">Click on the following links for the <a href="https://performingarts.cah.ucf.edu/all-state/">full schedule</a>, <a href="https://map.ucf.edu/directions/">directions to campus</a>, and a <a href="https://parking.ucf.edu/maps/">map of the campus</a>. You may park for free in lots H3, H4, and garage PGI.</p>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <p style="margin-top: 0;">For additional questions about the UCF All-State Prep Clinic sessions, please contact <a href="mailto:allstate@ucf.edu">allstate@ucf.edu</a>.</p>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <p style="margin-top: 0;">You have acknowledged that your photo may be taken during this event, and you have <?= $agree ? "granted" : "not granted" ?> permission to the University of Central Florida to use these images for promotional purposes.</p>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <p style="margin-top: 0;">
                                    Regards,<br /><br />
                                    Dr. Benjamin Lieser<br />
                                    Assistant Professor of Music &ndash; Horn Studies<br />
                                    University of Central Florida<br/>
                                    School of Performing Arts
                                </p>
                            </td>
                        </tr>
                        <tr style="padding-bottom: 1.5em;">
                            <td>
                                <p style="margin-top: 0;">
                                    Dr. Ayako Yonetani<br />
                                    Professor of Music &ndash; Violin/Viola Studies<br />
                                    University of Central Florida<br />
                                    School of Performing Arts
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

    <?php
    return ob_get_clean();
}

/**
 * Gets the list of instruments by style, to populate the selection box.
 * 
 * @return array
 */
function get_instrument_list() : array
{
    return [
        "Classical" => [
            ['name' => "Bassoon", 				'value' => "Bassoon"],
            ['name' => "Cello", 				'value' => "Cello"],
            ['name' => "Clarinet", 				'value' => "Clarinet"],
            ['name' => "Clarinet (Bass)", 		'value' => "Bass Clarinet"],
            ['name' => "English Horn", 			'value' => "English Horn"],
            ['name' => "Euphonium/Baritone", 	'value' => "Euphonium/Baritone"],
            ['name' => "Flute", 				'value' => "Flute"],
            ['name' => "French Horn", 			'value' => "French Horn"],
            ['name' => "Oboe", 					'value' => "Oboe"],
            ['name' => "Percussion", 			'value' => "Percussion"],
            ['name' => "Piccolo", 				'value' => "Piccolo"],
            ['name' => "Saxophone (Alto)", 		'value' => "Saxophone-Alto"],
            ['name' => "Saxophone (Baritone)", 	'value' => "Saxophone-Baritone"],
            ['name' => "Saxophone (Tenor)", 	'value' => "Saxophone-Tenor"],
            ['name' => "String Bass", 			'value' => "String Bass"],
            ['name' => "Trombone (Bass)", 		'value' => "Trombone Bass"],
            ['name' => "Trombone (Tenor)", 		'value' => "Trombone Tenor"],
            ['name' => "Trumpet", 				'value' => "Trumpet"],
            ['name' => "Tuba", 					'value' => "Tuba"],
            ['name' => "Viola", 				'value' => "Viola"],
            ['name' => "Violin", 				'value' => "Violin"],
            //['name' => "TEST",                  'value' => "TEST"],
        ],
        "Jazz" => [
            ['name' => "Drums", 								'value' => "Drums"],
            ['name' => "Electric Bass (Jazz)", 					'value' => "Electric Bass (Jazz)"],
            ['name' => "Guitar", 								'value' => "Guitar"],
            ['name' => "Percussion", 							'value' => "Percussion"],
            ['name' => "Piano", 								'value' => "Piano"],
            ['name' => "Saxophone", 							'value' => "Saxophone"],
            ['name' => "String Bass (Upright Bass) (Jazz)", 	'value' => "String Bass (Upright Bass) (Jazz)"],
            ['name' => "Trombone (Bass)", 						'value' => "Trombone Bass"],
            ['name' => "Trombone (Tenor)", 						'value' => "Trombone Tenor"],
            ['name' => "Trumpet", 								'value' => "Trumpet"],
            //['name' => 'TEST',                                  'value' => 'TEST'],
        ],
    ];
}

/**
 * Get email information for faculty by classical instrument type.
 * 
 * @param string $instrument  The instrument the applicant selected
 * 
 * @return array
 */
function get_classical_recipient( string $instrument ) : array
{
    switch( $instrument )
    {
        case "Bassoon":
            return [ 
                'addr' => "yoon.hwang@ucf.edu", 
                'name' => "Yoon Joo Hwang" 
            ];

        case "Cello":
            return [ 
                'addr' => "david.bjella@ucf.edu", 
                'name' => "David Bjella" 
            ];

        case "Clarinet":
            return [ 
                'addr' => "Keith.Koons@ucf.edu", 
                'name' => "Keith Koons" 
            ];

        case "English Horn":
            return [ 
                'addr' => "jamie.strefeler@ucf.edu", 
                'name' => "Jamie Strefeler" 
            ];

        case "French Horn":
            return [ 
                'addr' => "Benjamin.Lieser@ucf.edu", 
                'name' => "Ben Lieser" 
            ];

        case "Euphonium/Baritone":
            return [ 
                'addr' => "alexander.burtzos@ucf.edu", 
                'name' => "Alexander Burtzos" 
            ];

        case "Flute":
            return [ 
                'addr' => "noraleegarcia@ucf.edu", 
                'name' => "Nora Lee Garcia" 
            ];

        case "Oboe":
            return [ 
                'addr' => "jamie.strefeler@ucf.edu", 
                'name' => "Jamie Strefeler" 
            ];

        case "Percussion":
            return [ 
                [ 
                    'addr' => "tra@ucf.edu", 
                    'name' => "Thad Anderson" 
                ], 
                [ 
                    'addr' => "kirk.gay@ucf.edu", 
                    'name' => "Kirk Gay" 
                ] 
            ];

        case "Saxophone-Alto":
        case "Saxophone-Baritone":
        case "Saxophone-Tenor":
            return [ 
                'addr' => "george.weremchuk@ucf.edu", 
                'name' => "George Weremchuk" 
            ];

        case "Trombone Bass":
        case "Trombone Tenor":
            return [ 
                'addr' => "luis.fred@ucf.edu", 
                'name' => "Luis Fred" 
            ];

        case "Trumpet":
            return [ 
                'addr' => "jesse.cook@ucf.edu", 
                'name' => "Jesse Cook" 
            ];

        case "Viola":
            return [ 
                'addr' => "chung.park@ucf.edu", 
                'name' => "Chung Park" 
            ];

        case "Violin":
            return [ 
                'addr' => "ross.winter@ucf.edu", 
                'name' => "Ross Monroe Winter" 
            ];

        default:
            return [
                'addr' => '', //'michael.leavitt@ucf.edu', 
                'name' => '' //'Michael Leavitt'
            ];
    }
}

/**
 * Get email information for faculty by jazz instrument type.
 * 
 * @param string $instrument  The instrument the applicant selected
 * 
 * @return array
 */
function get_jazz_recipient( string $instrument )
{
    switch( $instrument )
    {
        case "Percussion":
        case "Drums":
            return [
                'addr' => "martin.morell@ucf.edu",
                'name' => "Marty Morell",
            ];
            
        case "Guitar":
            return [
                'addr' => "robert.koelble@ucf.edu",
                'name' => 'Bobby Koelble',
            ];

        case "Piano":
            return [ 
                'addr' => "perdanielsson@ucf.edu", 
                'name' => "Per Danielsson" 
            ];

        case "Saxophone":
            return [ 
                'addr' => "jeffrupert@ucf.edu", 
                'name' => "Jeff Rupert" 
            ];

        case "String Bass (Upright Bass) (Jazz)":
            return [ 
                'addr' => "richard.drexler@ucf.edu", 
                'name' => "Richard Drexler" 
            ];

        case "Trombone (Bass)":
        case "Trombone (Tenor)":
            return [
                'addr' => "luis.fred@ucf.edu",
                'name' => "Luis Fred",
            ];

        // We can fill these in as we get them.
        case "Electric Bass (Jazz)":
        case "Trumpet":
        default:
            return [
                'addr' => '', //'michael.leavitt@ucf.edu' 
                'name' => '' //'Michael Leavitt'
            ];
    }
}
?>