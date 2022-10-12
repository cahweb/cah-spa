<?php
namespace UCF\CAH\SPA\EnsembleInterestForm;

require_once 'db-helper.php';

use mysqli_result;

use UCF\CAH\DB_Helper as db;

// List of Errors
const ENSEMBLE__ERROR_EMAIL = ['msg' => "You must use a valid email address.", 'type' => 'danger'];
const ENSEMBLE__ERROR_DB = [ 'msg' => 'There was a problem inserting your response in the database. Please try again later. If the problem persists, contact the CAH Web Team at <a href="mailto:cahweb@ucf.edu">cahweb@ucf.edu</a>.', 'type' => 'danger' ];

const ENSEMBLE__STATUS_SUCCESS = [ 'msg' => "Thank you for your interest! Your form has been successfully submitted.", 'type' => 'success' ];
const ENSEMBLE__STATUS_FAIL = [ 'msg' => 'There was a problem when submitting your form. Please try again later. If the problem persists, contact the CAH Web Team at <a href="mailto:cahweb@ucf.edu">cahweb@ucf.edu</a>.', 'type' => 'danger' ];

const ENSEMBLE__NO_RECAPTCHA = [ 'msg' => 'You need to fill out the reCAPTCHA validation.', 'type' => 'danger' ];

const ENSEMBLE__BLANK_INSTRUMENTS = [ 'msg' => 'Please select at least one instrument or discipline.', 'type' => 'danger' ];
const ENSEMBLE__BLANK_ENSEMBLES = [ 'msg' => 'Please select at least one ensemble of interest.', 'type' => 'danger' ];
const ENSEMBLE__BLANK_OTHER_YEAR = [ 'msg' => 'You selected "Other" for your year. Please provide your year classification.', 'type' => 'danger' ];
const ENSEMBLE__BLANK_OTHER_INSTRUMENT = [ 
    'msg' => 'You selected "Other" for one of your instruments. Please provide an entry for any instrument(s) not listed.',
    'type' => 'danger'
];

const SPA__BASE_URL = 'https://performingarts.cah.ucf.edu/';

/**
 * Gets a value from $_POST if it's set, or returns an empty string if not.
 * Used for remembering form values on submission (in case of error).
 * 
 * @author Mike W. Leavitt
 * 
 * @param string $name - The name of the $_POST field you want to check.
 * 
 * @return string
 */
function get_val( string $name ) : string {
    if( isset( $_POST[$name] ) ) return $_POST[$name];
    else return '';
}

/**
 * Does essentially the same thing as above, only it allows the form
 * to remember which options were checked.
 * 
 * @author Mike W. Leavitt
 * 
 * @param string $field - The $_POST value to check
 * @param string $value - The value to check against.
 * 
 * @return string
 */
function check_box(string $field, string $value ) : string {
    if( isset( $_POST[$field] ) && in_array( $value, $_POST[$field] ) ) {
        return " checked";
    }
    else return "";
}


/**
 * Generates the outgoing message for the Chorus
 * 
 * @author Mike W. Leavitt
 * 
 * @return string
 */
function message_chorus() : string {
    $url = SPA__BASE_URL . 'ensemble/choirs/';
    ob_start();
    ?>
    <table style="margin-bottom: 3em;">
        <tr>
            <td><p style="margin-top: 1.5em;">Greetings, fellow Knights!</p></td>
        </tr>
        <tr>
            <td><p style="margin-top: 2em;">It is my honor and privilege to invite you to join the Choral Program here at UCF. This year we welcome the 2019 GRAMMY Music Educator Award Recipient, Dr. Jeffery Redding, as the new Director of Choral Activities. Dr. Redding is looking forward to building upon the strong choral foundation of UCF, creating an even more vibrant choral/vocal culture of musically passionate, diverse students. He believes the power of music brings us all together, building bridges and honoring our cultures and history. The UCF Choral Program is not only about the creation of outstanding music; we also strive to create community.</p></td>
        </tr>
        <tr>
            <td><p style="margin-top: 2em;">There is a place for everyone, regardless of your major! Transfer professional life skills and creativity to any and every major! We perform all styles of choir literature, from classical to contemporary. We have both auditioned and un-auditioned choirs; all ensembles have the topmost expectations and are held to the highest of standards. Learn more at <a href="<?= $url ?>"><?= $url ?></a>.</p></td>
        </tr>
        <tr>
            <td><p style="margin-top: 2em;">Do not hesitate to contact us if you have any questions! We can&apos;t wait to meet you!</p></td>
        </tr>
        <tr>
            <td><p style="margin-top: 2em;">Best regards,<br />Dr. Miller</p></td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 3em;">
                    <span style="font-size: 1.25rem;"><strong>Dr. Kelly A. Miller</strong></span><br />
                    Coordinator of Music Education<br />
                    Conductor of SoA1 and TeBa Choirs<br />
                    President-Elect, Florida ACDA<br />
                    University of Central Florida<br />
                    24688 Centaurus Blvd.<br />
                    Orlando, FL &nbsp;32816<br />
                    <a href="mailto:kelly.miller@ucf.edu">Kelly.Miller@ucf.edu</a><br />
                    <a href="tel:+14078234545">407.823.4545</a>
                </p>
            </td>
        </tr>
    </table>
    <?php
    return ob_get_clean();
}


/**
 * Generates the outgoing message for the Opera
 * 
 * @author Mike W. Leavitt
 * 
 * @return string
 */
function message_opera() : string {
    $url = SPA__BASE_URL . 'ensemble/opera/';
    ob_start();
    ?>
    <table style="margin-bottom: 3em;">
        <tr>
            <td><p style="margin-top: 1.5em;">Dear future Knight!</p></td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">Welcome to UCF! This is a great big place with a great big heart, and you&apos;re going to love being here! I wanted to let you know about an opportunity that you may want to pursue, whether you are majoring in music or not (every year, we have several non-majors in the program). If you love acting and singing, learning about character study and stage movement, <strong>you are welcomed to audition for the UCF Opera program</strong>.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">&ldquo;Op-Shoppers&rdquo; are a close-knit community of students who are always supportive of one another and who come together with the goal of learning more about opera and how to make opera performances entertaining and educational to their audience. We (UCF Opera) also have a great relationship with the local professional opera company, Opera Orlando, and through the years, several of our opera students have been cast to sing in the chorus and smaller parts in their productions. When Opera Orlando produced <em>Hansel and Gretel</em> a couple of years ago, nearly all the cast was UCF Opera alums. One of our alumni is currently one of Opera Orlando&apos;s &ldquo;Young Artist&rdquo; apprentices&mdash;a paid position.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">We do a big opera scenes program every Fall Semester, produce a full opera with orchestra accompaniment every spring (except for 2020, of course, unfortunately), and make a trip (as a group) to see a live performance of an opera at the Sarasota Opera Company each semester, which is really inspiring and educational for all. Learn more at <a href="<?= $url ?>"><?= $url ?></a>.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">Here&apos;s a list of operas we have done in the past several years:</p>
            </td>
        </tr>
        <tr>
            <td>
                <ul style="list-style-type: none; margin-top: 2em;">
                    <li>2020: Rossini's &ldquo;L&apos;italiana in Algeri&rdquo; (<em>postponed to 2021</em>)</li>
                    <li>2019: Copland's &ldquo;The Tender Land&rdquo;</li>
                    <li>2018: Puccini's &ldquo;Gianni Schicchi&rdquo; &amp; Michael Ching's &ldquo;Buoso&apos;s Ghost&rdquo; (<em>double header</em>)</li>
                    <li>2017: Strauss&apos; &ldquo;Die Fledermaus&rdquo;</li>
                    <li>2016: Donizetti&apos;s &ldquo;L&apos;elisir d&apos;amore&rdquo; (<em>Elixir of Love</em>)</li>
                    <li>2015: Purcell&apos;s &ldquo;Dido and Aeneas&rdquo;</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">The Opera Workshop class [MUO 3503L] meets on Mondays and Fridays from 3&ndash;5 p.m. If you&apos;re interested in auditioning for a spot in the class, go ahead and enroll in that course, and we&apos;ll contact you about specific audition times. Those auditions will take place on the Sunday afternoon prior to the first week of school.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">And, if you don't think singing in the operas is your thing, but you still want to learn more about opera, consider joining the Student Opera Club at UCF.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">So, once again, welcome to UCF! And please write me with any questions you might have about UCF Opera&mdash;the address to use is <a href="mailto:opera@ucf.edu">opera@ucf.edu</a>.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">Best to you!<br />~Thomas</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 3em;">
                    <strong><span style="font-size: 1.25rem;">Thomas Potter</span> (he/him/his)</strong><br />
                    Associate Professor of Music [Voice and Opera]<br />
                    UCF Opera Executive Director<br />
                    Applied Voice Teacher<br />
                    Website: <a href="https://www.ThomasPotterOnline.com/">www.ThomasPotterOnline.com</a><br />
                    Office: <a href="tel:+14078234680">407.823.4680</a><br />
                    Email: <a href="mailto:opera@ucf.edu">opera@ucf.edu</a>
                </p>
            </td>
        </tr>
    </table>
    <?php
    return ob_get_clean();
}


/**
 * Generates the outgoing message for the Orchestra
 * 
 * @author Mike W. Leavitt
 * 
 * @return string
 */
function message_orchestra() : string {
    $url = SPA__BASE_URL . 'ensemble/orchestra/';
    ob_start();
    ?>
    <table>
        <tr>
            <td><p style="margin-top: 1.5em;">Thank you for your interest in the UCF Orchestra.</p></td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">The University of Central Florida Orchestra program consists of two ensembles serving nearly 200 students. The program is designed to give a compelling and rigorous musical experience for string, brass, woodwind and percussion players in the university community as well as pre-professional training for music, music education and performance majors.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">The <strong>UCF Symphony Orchestra</strong> is a full orchestra with strings, brass, woodwinds and percussion, performing literature by master composers such as Bach, Dvorak and Bernstein, while performing new works by some of today's leading composers. The UCF Symphony Orchestra appears in venues throughout Central Florida, including the renowned Dr. Phillips Center for the Performing Arts. The ensemble is open to all students, regardless of major. Auditions are required for orchestra membership. The ensemble rehearses twice per week and presents two to three concerts each semester. Symphony Orchestra serves as a major ensemble for music majors, and members receive one hour of academic credit for each semester of successful participation.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">The <strong>UCF Chamber Orchestra</strong> performs the highest caliber literature by master composers. The ensemble is highly selective and a successful audition or director invitation are required for entry. The ensemble strives for professional levels of performance, and incorporates the most rigorous methods in use by the pre-eminent ensembles of today, including historically informed performance practice, just intonation and performing from memory. Any UCF student may audition, regardless of major.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">The rehearsal schedule will vary from year to year to accommodate members' schedules. The Chamber Orchestra generally performs two to three concerts in the spring semester and may meet on an <em>ad hoc</em> basis in the fall if performance opportunities arise. The Chamber Orchestra serves as a minor ensemble for music majors, and members receive one hour of academic credit for each semester of successful participation.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">To learn more, visit:</p>
                    <ul>
                        <li><a href="<?= $url ?>"><?= $url ?></a></li>
                        <li><a href="https://performingarts.cah.ucf.edu/music/ensembles/ensemble-auditions-band-and-orchestra/">https://performingarts.cah.ucf.edu/music/ensembles/ensemble-auditions-band-and-orchestra/</a></li>
                    </ul>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 3em;">
                    <span style="font-size: 1.25rem;"><strong>Chung Park</strong></span><br />
                    Director of Orchestras<br />
                    <a href="mailto:chung.park@ucf.edu">chung.park@ucf.edu</a>
                </p>
            </td>
        </tr>
    </table>
    <?php
    return ob_get_clean();
}


/**
 * Generates the outgoing message for both the Concert and Marching Bands
 * 
 * @author Mike W. Leavitt
 * 
 * @return string
 */
function message_bands() : string {
    $url = 'https://ucfbands.com/';
    ob_start();
    ?>
    <table style="margin-bottom: 3em;">
        <tr>
            <td>
                <p style="margin-top: 1.5em;">Dear Incoming Knight,</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">On behalf of the UCF Bands, congratulations on your outstanding decision to attend the University of Central Florida, and WELCOME! You&apos;re joining a community that&apos;s focused on your interests and successes. I&apos;m writing you today to encourage you to explore your opportunities with the UCF Bands. On your Ensemble Interest Form, you indicated interest in concert and/or athletic bands, and there is absolutely a home for you in our program! In addition to the exciting opportunities and encounters with our athletic bands, the <strong>Marching Knights</strong> and <strong>Jammin&apos; Knights</strong>, we also offer four outstanding concert bands and chamber winds&mdash;some auditioned and some non-auditioned&mdash;so truly, there&apos;s a place for everyone.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">Our <a href="<?= $url ?>">Ensembles Information Page</a>, which contains everything you&apos;ll need to know about trying out for our auditioned ensembles (Orchestra, Wind Ensemble, and Symphonic Band), as well as information about our two non-auditioned concert bands (Concert Band and University Band) can be found online at <a href="<?= $url ?>"><?= $url ?></a>. Questions specific to our athletic bands can be directed to UCF Associate Director of Bands, Dr. Tremon Kizer(<a href="mailto:tkizer@ucf.edu">tkizer@ucf.edu</a>) or our Assistant Director of Bands, Mr. Dave Schreier (<a href="mailto:dave.schreier@ucf.edu">dave.schreier@ucf.edu</a>).</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">As UCF Director of Bands, I know that any of us on our bands staff will be happy to share any and all information with you, and know that we would all be very pleased to welcome you into our program in whatever way or ways fit your vision for your collegiate experience. Certainly, if you have friends or other acquaintances that you think might also benefit from information about the many ensemble experiences at UCF&mdash;which also include our outstanding jazz ensembles, vocal ensembles, and more&mdash;I hope you&apos;ll pass this message along to them.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">Please always feel free to contact us at any time, should you have questions. Additional program information can also be found online at <a href="https://www.ucfbands.com/">www.UCFBands.com</a> and <a href="https://performingarts.cah.ucf.edu/music/ensembles/ensemble-auditions-band-and-orchestra/">https://performingarts.cah.ucf.edu/music/ensembles/ensemble-auditions-band-and-orchestra/</a>. We&apos;re excited about the year to come and would be very happy to share it with you.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">Best wishes as you complete your summer, and <strong>Go Knights!</strong></p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top:3em;">
                    <span style="font-size: 1.25rem;"><strong>Scott Lubaroff, DMA</strong></span><br />
                    Professor of Music<br />
                    Director of Bands<br />
                    University of Central Florida<br />
                    School of Performing Arts - Music<br />
                    PACM206<br />
                    Orlando, FL &nbsp;32816<br />
                    Office: <a href="tel:+14078230887">(407) 823-0887</a><br />
                    Fax: <a href="tel:+14078233378">(407) 823-3378</a><br />
                    <a href="mailto:slubaroff@ucf.edu">slubaroff@ucf.edu</a><br />
                    <a href="https://www.ucfbands.com/">www.UCFBands.com</a>
                </p>
            </td>
        </tr>
    </table>
    <?php
    return ob_get_clean();
}


/**
 * Generates the outgoing message for the Jazz Ensembles
 * 
 * @author Mike W. Leavitt
 * 
 * @return string
 */
function message_jazz() : string {
    $url = SPA__BASE_URL . 'ensemble/jazz/';
    ob_start();
    ?>
    <table style="margin-bottom: 3em;">
        <tr>
            <td>
                <p style="margin-top: 1.5em;">Hello and welcome to UCF! As Director of Jazz Studies, I&apos;d like to personally invite all of you to audition for one of our 7 jazz bands, which include 2 big bands and at least 5 jazz chamber groups. These courses are taught by faculty (not grad students). These bands are open to <strong>all UCF students</strong>. Auditions will happen the Sunday before school starts (August 23, 2020).</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;"><a href="<?= $url ?>"><?= $url ?></a></p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">If you'd like more information, <a href="https://performingarts.cah.ucf.edu/program/jazz-studies/">visit here</a>.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">We also have a quite active record label, <a href="https://flyinghorserecords.com">Flying Horse Records</a>. Check out some of our albums (over 10)!</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 2em;">If you have questions about auditions, you can contact one of the faculty:</p>
            </td>
        </tr>
        <tr>
            <td>
                <ul style="list-style-type: none; padding-left: 0; margin-top: 2em;">
                    <li>Jeff Rupert, Director of Jazz Studies, saxophone <a href="mailto:jeffrupert@ucf.edu">jeffrupert@ucf.edu</a></li>
                    <li>Dan Miller, jazz trumpet, Jazz Chamber Group <a href="mailto:daniel.miller@ucf.edu">daniel.miller@ucf.edu</a></li>
                    <li>Per Danielsson, jazz piano, Jazz Ensemble II <a href="mailto:perdanielsson@ucf.edu">perdanielsson@ucf.edu</a></li>
                    <li>Bobby Koelble, jazz guitar, Jazz Chamber Group <a href="mailto:robert.koelble@ucf.edu">Robert.Koelble@ucf.edu</a></li>
                    <li>Richard Drexler, jazz bass, Jazz Workshop, Jazz Chamber Group <a href="mailto:richard.drexler@ucf.edu">Richard.Drexler@ucf.edu</a></li>
                    <li>Marty Morell, jazz drums, Jazz Chamber Group <a href="mailto:martin.morell@ucf.edu">martin.morell@ucf.edu</a></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <p>Thank you very much for your interest in Jazz Studies at UCF, and we look forward to seeing you August 23.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin-top: 3em;">
                    <span style="font-size: 1.25rem;"><strong>Jeff Rupert</strong></span><br />
                    Tenor Sax<br />
                    Trustee Chair<br />
                    Pegasus Professor<br />
                    Director of Jazz Studies<br />
                    Phone: <a href="tel:+14078235411">(407) 823-5411</a><br />
                    <a href="https://performingarts.cah.ucf.edu/program/jazz-studies/">UCF Jazz Studies</a><br />
                    <a href="https://flyinghorserecords.com">Flying Horse Records</a><br />
                    <a href="http://www.yamaha.com/artists/artistdetailb.html?CNTID=5103049&CTID=5070060">Yamaha Performing Artist</a><br />
                    Booking and Information: <a href="https://www.nightisalive.com/jeffrupert">Night Is Alive</a>
                </p>
            </td>
        </tr>
    </table>
    <?php
    return ob_get_clean();
}

// Outgoing email messages
$messages = [
    'Chorus'         => message_chorus(),
    'Opera'          => message_opera(),
    'Orchestra'      => message_orchestra(),
    'Concert Bands'  => message_bands(),
    'Marching Band'  => message_bands(),
    'Jazz Ensembles' => message_jazz(),
    //'Something Else' => "This is the outgoing Something Else message.",
];


/**
 * Generates the email that is sent to the respondent.
 * 
 * @author Mike W. Leavitt
 * 
 * @param array $post - A copy of the $_POST superglobal from the page.
 * 
 * @return string
 */
function build_student_email( array $post ) : string {
    global $messages;
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
        <body>
            <table style="width: 100%; background: url(http://devspa.cah.ucf.edu/wp-content/uploads/sites/20/2015/07/band_brass.jpg) no-repeat center center fixed; background-size: cover; background-color: #111;">
                <tr>
                    <td>
                        <table style="width: 600px; margin: 1.5em auto; background-color: #fc0; padding: 1.5em;">
                            <tr>
                                <td>
                                    <img src="cid:music_logo">
                                </td>
                            </tr>
                            <tr>
                                <td><p>Thank you for your interest in the UCF Music Ensembles. The major music ensembles at UCF are open to all students on campus. All ensembles are courses at UCF and you must be registered in order to participate. Some ensembles only require course registration while others require an audition. To learn more about ensembles and auditions, please see the note below and visit <a href="https://performingarts.cah.ucf.edu/performance-opportunities/">https://performingarts.cah.ucf.edu/performance-opportunities/</a>.</p></td>
                            </tr>

                            <?php foreach( $post['ensembles'] as $ensemble ) : ?>
                                <?php if( 'Marching Band' === $ensemble && in_array( 'Concert Bands', $post['ensembles'] ) ) continue; ?>
                            <tr>
                                <td><h2 style="margin-top: 2em; font-family: serif;"><?= 'Concert Bands' === $ensemble || 'Marching Band' === $ensemble ? 'Bands' : $ensemble ?></h2></td>
                            </tr>
                            <tr>
                                <td style="padding: 1em;">
                                    <?= $messages[$ensemble] ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <tr>
                                <td><p style="margin-top: 3em;">You will receive further communication from the individual ensemble(s) you selected soon!</p></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
    </html>
    <?php

    return ob_get_clean();
}


/**
 * Check to see if a particular student exists in the database, based on email.
 * This keeps us from listing any particular student twice.
 * 
 * @author Mike W. Leavitt
 * 
 * @param mysqli $db - A mysqli connection object
 * @param string $email - The email address to check.
 * 
 * @return int - The student row's ID, or 0 if the student isn't present.
 */
function student_exists( $db, string $email ) : int {
    // Build SQL
    $sql = "SELECT id FROM students WHERE email = '$email' LIMIT 1";

    // Make the query
    $result = mysqli_query( $db, $sql );

    // If we have a result, return the ID field, or return 0 if we didn't
    // find it
    if( $result instanceof mysqli_result && $result->num_rows > 0 ) {
        $row = mysqli_fetch_assoc( $result );
        return intval( $row['id'] );
    }
    else return 0;
}


/**
 * Add a student to the database and return their new row ID.
 * 
 * @author Mike W. Leavitt
 * 
 * @param mysqli $db - A mysqli connection object.
 * @param array $data - The data to be inserted.
 * 
 * @return int - The newly-created student's number, or 0 on failure.
 */
function add_student( $db, array $data ) : int {

    // Extract the array for easy reference.
    extract( $data );

    // Build the SQL
    $sql = "INSERT INTO students (email, fname, lname, phone) VALUES ('$email', '$fname', '$lname', '$phone')";

    // Make the query
    $result = mysqli_query( $db, $sql );

    // If it returns TRUE, we retrieve the ID of the newly-created
    // student. If it returns FALSE, we return 0.
    if( $result ) {
        return student_exists( $db, $email );
    }
    else return 0;
}


/**
 * Adds a form entry for a particular student.
 * 
 * @author Mike W. Leavitt
 * 
 * @param mysqli $db - A mysqli connection object.
 * @param array $data - The data to be inserted.
 * 
 * @return bool - Success or Failure
 */
function add_entry( $db, array $data ) : bool {
    
    // Extract the array for easy reference.
    extract( $data );
    
    // Build the SQL. The ternaries are checking for fields that are allowed
    // to be NULL.
    $sql = "INSERT INTO entries (student_id, major, `year`, year_other, instruments, interests, notes) VALUES ($student_id, '$major', '$year', " . ( !is_null( $year_other ) ? "'$year_other'" : "NULL" ) . ", '$instruments', '$interests', " . ( !is_null( $notes ) ? "'$notes'" : "NULL" ) . ")";

    // Make the query and return the result (since it's an INSERT,
    // the return value will be either TRUE or FALSE)
    $result = mysqli_query( $db, $sql );

    if( !$result && mysqli_errno( $db->get_db() ) !== 0 ) {
        error_log( "Ensemble Form MySQL Error: " . mysqli_errno( $db->get_db() ) . " - " . mysqli_error( $db->get_db() ) . "\n\tSQL: $sql" );
    }
    else if( !$result ) {
        error_log( "Ensemble Form MySQL Error: unspecified\n\tSQL: $sql" );
    }

    return $result;
}


/**
 * This puts all the previous functions together, so we only have to call one thing
 * from the front-end page.
 * 
 * @author Mike W. Leavitt
 * 
 * @param array $post - A copy of the $_POST superglobal.
 * 
 * @return bool - Success or Failure of the whole operation.
 */
function new_entry( array $post ) : bool {

    // Create a new DB_Helper object that will do most of the heavy lifting with
    // the database for us.
    $db = new db();

    // Check if the student entry exists, and set $student to their row ID.
    $student = student_exists( $db->get_db(), $post['email'] );

    // If they don't exist ($student === 0), create a new one.
    if( !$student ) {

        // Populate the array with the data we need.
        $student_data = [
            'email' => $post['email'],
            'fname' => $post['fname'],
            'lname' => $post['lname'],
            'phone' => $post['phone'],
        ];

        // Add the student. This will also return the student number if
        // successful, so we shouldn't have to do anything else.
        $student = add_student( $db->get_db(), $student_data );
    }

    // For debug.
    //var_dump( $student );

    // If we still don't have a student number, there's some kind of problem
    // and we won't be able to continue.
    if( $student == 0 ) return false;

    // Check to see if they selected "Other" for one of their instruments, and add
    // it into the array of them, rather than maintaining it as a separate value.
    if( in_array( "Other", $post['instruments'] ) && isset( $post['instrument-other'] ) && !empty( $post['instrument-other'] ) ) {
        $post['instruments'][count( $post['instruments'] ) - 1] = "Other: " . $post['instrument-other'];
    }

    // Prepare the data for the entry, including the student's row ID for foreign key reference.
    $entry_data = [
        'student_id' => $student,
        'major' => $post['major'],
        'year' => $post['year'],
        'year_other' => isset( $post['year-other'] ) ? $post['year-other'] : null,
        'instruments' => serialize( $post['instruments'] ),
        'interests' => serialize( $post['ensembles'] ),
        'notes' => isset( $post['notes'] ) ? $post['notes'] : null,
    ];

    // Add the entry. This will return true or false.
    $entry = add_entry( $db->get_db(), $entry_data );

    // For debug
    /*
    if( !$entry ) {
        $dump = "MSQL Error " . mysqli_errno( $db->get_db() ) . ": " . mysqli_error( $db->get_db() );
        var_dump( $dump );
    }
    */

    return $entry;
}


/**
 * Static helper class to allow namespaced access to our various variables,
 * without clogging up the page template.
 * 
 * @author Mike W. Leavitt
 */
class Vars
{
    private function __construct() {} // Prevents instantiation

    // List of instruments/musical disciplines
    public static $instruments = [
        "Flute",
        "Oboe",
        "Clarinet",
        "Bass Clarinet",
        "Bassoon",
        "Alto Sax",
        "Tenor Sax",
        "Bari Sax",
        "Trumpet",
        "French Horn",
        "Trombone",
        "Euphonium/Baritone",
        "Tuba",
        "Percussion",
        "Violin",
        "Viola",
        "Cello",
        "Double Bass (Classical)",
        "Double Bass (Jazz)",
        "Guitar (Classical)",
        "Guitar (Jazz)",
        "Drum Set (Jazz)",
        "Piano (Classical)",
        "Piano (Jazz)",
        "Voice (Soprano)",
        "Voice (Alto)",
        "Voice (Tenor)",
        "Voice (Bass)",
        "Colorguard (Marching Band)",
        //"Dance (Marching Band)",
        "Majorette (Marching Band)",
    ];

    // List of available Ensembles (and any supplementary subtitles they may have)
    public static $ensembles = [
        ['name' => 'Chorus',         'subtitle' => "(Chamber Singers, University Choir, Women's Choir, Men's Choir)"],
        ['name' => 'Opera',          'subtitle' => ''],
        ['name' => 'Orchestra',      'subtitle' => ''],
        ['name' => 'Concert Bands',  'subtitle' => "(Wind Ensemble, Symphonic Band, Concert Band, University Band)"],
        ['name' => 'Marching Band',  'subtitle' => "(Marching Knights, Jammin' Knights Pep Band)"],
        ['name' => 'Jazz Ensembles', 'subtitle' => "(Flying Horse Big Band, Big Band II, Jazz Chamber Groups)"],
        //['name' => 'Something Else', 'subtitle' => "(Piano, Classical Guitar, etc.)" ],
    ];

    // List of possible student years, for the Select box.
    public static $years = [
        ['name' => 'Freshman (First time in college)', 'value' => 'Freshman'],
        ['name' => 'Transfer (Sophomore)', 'value' => 'Sophomore'],
        ['name' => 'Transfer (Junior)', 'value' => 'Junior'],
        ['name' => 'Transfer (Senior)', 'value' => 'Senior'],
        ['name' => 'Other', 'value' => 'Other'],
    ];
}
?>