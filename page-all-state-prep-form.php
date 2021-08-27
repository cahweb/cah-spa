<?php
/*
 * Template Name: All-State Prep Clinic Form
 * Description: Form for students to register for the All-State Prep Clinic
 * Author: Mike W. Leavitt
 */
//ini_set( 'display_errors', 1 );

include_once 'includes/phpmailer-helper.php';
include_once 'includes/all-state-prep/form-tools.php';

use UCF\CAH\MailTools\PHPMailerHelper as Mailer;
use UCF\CAH\SPA\Music\AllStatePrep\FormTools;

require_once 'recaptcha.php';

FormTools\setup_captcha_wp();

$dateFormat = "Y-m-d H:i T";
$startDate = date_create_from_format($dateFormat, "2021-05-01 12:00 EDT");
$endDate   = date_create_from_format($dateFormat, "2021-08-27 17:00 EDT");

$messages = [];
$has_error = false;

$resp = null;

if( isset( $_POST['submitform'] ) )
{
    // Verify reCAPTCHA field
    if( isset( $_POST['g-recaptcha-response'] ) )
    {
        $recaptcha = new \ReCaptcha\ReCaptcha( $secret );
        $resp = $recaptcha->verify( $_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'] );

        if( !$resp->isSuccess() )
        {
            $messages[] = [
                'type' => 'danger',
                'text' => 'Invalid CAPTCHA.',
            ];
            $has_error = true;
        }
    }
    else
    {
        $messages[] = [
            'type' => 'danger',
            'text' => 'The reCAPTCHA field is required.',
        ];
        $has_error = true;
    }

    // Send the confirmation email and enter student data in database
    if( $resp->isSuccess() && ( !empty( $_POST['classins'] ) || !empty( $_POST['jazzins'] ) ) )
    {
        $fname = FormTools\scrub( FormTools\get_field( 'fname' ) );
        $lname = FormTools\scrub( FormTools\get_field( 'lname' ) );
        $name = "$fname $lname";
        $address = FormTools\scrub( FormTools\get_field( 'address' ) );
        $city = FormTools\scrub( FormTools\get_field( 'city' ) );
        $state = "Florida";
        $zip = FormTools\scrub( FormTools\get_field( 'zip' ) );
        $email = FormTools\scrub( FormTools\get_field( 'email' ) );
        $school = FormTools\scrub( FormTools\get_field( 'school' ) );
        $grade = FormTools\scrub( FormTools\get_field( 'grade' ) );
        $gradenum = intval( str_replace( "Grade ", "", $grade ) );
        $jazzclinic = FormTools\scrub( intval( FormTools\get_field( 'jazzclinic' ) ) );
        $classins = FormTools\scrub( FormTools\get_field( 'classins' ) );
        $jazzins = "";
        
        // We'll only pay attention to Jazz instrument selections for students in 9th grade or higher
        if( $gradenum >= 9 )
        {
            $jazzins = FormTools\scrub( FormTools\get_field( 'jazzins' ) );
        }

        $agree = 0;
        if( isset( $_POST['fldAgree'] ) ) {
            $agree = intval( $_POST['fldAgree'] );
        }

        $emailstring = "<p>$grade<br />";
        if( !empty( $classins ) )
        {
            $emailstring .= "Classical: $classins<br />";
        }
        if( !empty( $jazzins ) )
        {
            $emailstring .= "Jazz: $jazzins";
        }
        $emailstring .= "</p>";


        // Create and send the email
        $email_subject = "All-State Preparation Clinic Submission";
        $email_body = FormTools\get_email_body( $name, $emailstring, $agree );

        // Set up all the addresses
        $to = [
            'addr' => $email,
            'name' => $name,
        ];

        $from = [
            'addr' => 'allstate@ucf.edu',
            'name' => 'UCF Music - All-State Prep Clinic',
        ];

        $bcc = [];

        if( !empty( $classins ) )
        {
            if( "Percussion" == $classins )
            {
                $bcc = FormTools\get_classical_recipient( $classins );
            }
            else
            {
                $bcc[] = FormTools\get_classical_recipient( $classins );
            }
        }

        if( !empty( $jazzins ) )
        {
            // For Testing/Debug
            $duplicate = false;
            /*
            foreach( $bcc as $entry )
            {
                if( $entry['addr'] = 'michael.leavitt@ucf.edu' )
                {
                    $duplicate = true;
                    break;
                }
            }
            */
            
            if( !$duplicate )
            {
                $bcc[] = FormTools\get_jazz_recipient( $jazzins );
            }
        }

        $bcc[] = [
            'addr' => 'allstate@ucf.edu',
            'name' => 'UCF Music - All-State Prep Clinic',
        ];

        // Create the PHPMailerHelper object (which is mostly just a wrapper for the PHPMailer functionality)
        $mail = new Mailer( $to, $from, $email_subject, $email_body, [], $bcc );

        // Try to send the email, and generate an alert message depending on the outcome
        if( !$mail->send() )
        {
            $messages[] = [
                'type' => 'danger',
                'text' => 'The registration confirmation email failed to send. Please contact the <a href="mailto:cahweb@ucf.edu">CAH Web Team</a> for assistance.',
            ];
            $has_error = true;
        }
        else
        {
            $messages[] = [
                'type' => 'success',
                'text' => 'Registration confirmation email successfully sent! Please read through the email for additional details about the event.',
            ];
        }

        // SQL query to add the student data to the database.
        $sql = "INSERT INTO allstateprep ( fname, lname, `address`, city, zip, email, school, grade, classins, jazzins, `time`, jazzclinic, agree) VALUES ( '$fname', '$lname', '$address', '$city', '$zip', '$email', '$school', '$gradenum', '$classins', '$jazzins', NOW(), $jazzclinic, $agree )";

        $result = mysqli_query( FormTools\get_db(), $sql );

        // Generate an alert depending on success or failure of database insertion.
        if( $result )
        {
            $messages[] = [
                'type' => 'success',
                'text' => 'Your application has been received! Thank you!',
            ];
        }
        else
        {
            $messages[] = [
                'type' => 'danger',
                'text' => 'There was a problem entering your application information into the database. Please contact the <a href="mailto:cahweb@ucf.edu">CAH Web Team</a> for assistance.',
            ];
            $has_error = true;

            throw new FormTools\DatabaseException( FormTools\get_db(), $sql );
        }
    }
}

get_header();
?>

<?php
$now = time();

if ($startDate->format('U') <= $now && $endDate->format('U') >= $now) :
    ?>
<div class="container mt-5 mb-4">
    <?php if( !empty( $messages ) ) : ?>
        <?php foreach( $messages as $message ) : ?>
            <div class="alert alert-<?= $message['type'] ?> alert-dismissable fade show mb-3" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p class="mb-0"><?= $message['text'] ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <form id="all-state-reg-form" method="post" action="">
        <div class="container-fluid">
            <p class="form-text">For your request to be processed as quickly as possible, all information must be spelled correctly. Proper capitalization and punctuation should also be used.</p>
            <h3>Personal Information</h3>
            <div class="row mb-3">
                <div class="form-group col-md-6">
                    <label for="fname">First Name:</label>
                    <input type="text" name="fname" id="fname" class="form-control" value="<?= FormTools\get_field('fname') ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="lname">Last Name:</label>
                    <input type="text" name="lname" id="lname" class="form-control" value="<?= FormTools\get_field('lname') ?>" required>
                </div>
                <div class="form-group col-12">
                    <label for="address">Street Address:</label>
                    <input type="text" name="address" id="address" class="form-control" value="<?= FormTools\get_field('address') ?>" required>
                </div>
                <div class="form-group col-md-8">
                    <label for="city">City:</label>
                    <input type="text" name="city" id="city" class="form-control" value="<?= FormTools\get_field('city') ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="zip">ZIP Code:</label>
                    <input type="text" name="zip" id="zip" class="form-control" value="<?= FormTools\get_field('zip') ?>" maxlength="5" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= FormTools\get_field('email') ?>" required>
                </div>
                <div class="form-group col-md-12">
                    <label for="school">School Name:</label>
                    <input type="text" name="school" id="school" class="form-control" value="<?= FormTools\get_field('school') ?>" required>
                </div>
            </div>
            <h3>Program</h3>
            <p class="form-text">Select your Grade and Instrument. <em>Note: Students in Grade 7 or 8 will only be able to choose a classical instrument.</em></p>
            <div class="row">
                <div class="col-12">
                    <h4>Grade:</h4>
                    <?php for( $i = 7; $i <= 12; $i++ ) : ?>
                    <div class="form-check form-check-inline">
                        <label for="grade-<?= $i ?>" class="form-check-label">
                            <input type="radio" name="grade" class="form-check-input" id="grade-<?= $i ?>" value="Grade <?= $i ?>"<?= "Grade $i" === FormTools\get_field('grade') ? " checked" : "" ?> required>
                            <?= $i ?>
                        </label>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php $instruments = FormTools\get_instrument_list();
                foreach( $instruments as $class => $list ) :
                    $style = "Classical" === $class ? "class" : "jazz";
                    $current = FormTools\get_field("${style}ins");
                ?>
                <div class="form-group col-md-6 mb-3">
                    <label for="ins<?= $style ?>"><?= $class ?></label>
                    <select id="ins<?= $style ?>" class="form-control" name="<?= $style ?>ins">
                        <option value="">None</option>
                        <?php foreach( $list as $ins ) : ?>
                        <option value="<?= $ins['value'] ?>"<?= $ins['value'] === $current ? " selected" : "" ?>><?= $ins['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>
                <div class="col-12">
                    <p class="h4 mt-2">Jazz Improvisation/Jazztet Clinic</p>
                    <p class="form-text">Do you wish to participate in the High School Jazz Improvisation Clinic?</p>
                    <?php foreach( [1, 0] as $value ) : ?>
                    <?php $text = $value ? "yes" : "no"; ?>
                    <div class="form-check form-check-inline">
                        <label for="clinic-<?= $text ?>" class="form-check-label">
                            <input type="radio" name="jazzclinic" id="clinic-<?= $text ?>" class="form-check-input" value="<?= $value ?>">
                            <?= ucfirst( $text ) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-12 mt-4 mb-4">
                    <div class="form-check">
                        <label for="agree" class="form-check-label">
                            <input type="checkbox" name="fldAgree" id="agree" value="1" checked>
                            I acknowledge that my photo may be taken during this event, and I grant permission to the University of Central Florida to use these images for promotional purposes.
                        </label>
                    </div>
                </div>
            </div>
            <p class="form-text">You will receive an email notification of your registration.</p>
            <div class="g-recaptcha mb-3" data-sitekey="<?= $siteKey ?>"></div>
            <button type="submit" name="submitform" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>
<?php else : ?>
<div class="container mt-5 mb-4">
    <h3>All-State Prep Clinic Pre-Registration is now closed.</h3>
    <p>Walk-in registration is welcome and will occur in the Visual Arts Building lobby.</p>
</div>
<?php endif; ?>

<?php
get_footer();
?>