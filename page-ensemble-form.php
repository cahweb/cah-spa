<?php

// Calling in our page-specific function/variable library.
require_once 'includes/ensemble-form-helper.php';
use UCF\CAH\SPA\EnsembleInterestForm as ens;

// Load our Mail helper class
require_once 'includes/phpmailer-helper.php';
use UCF\CAH\MailTools\PHPMailerHelper as mailer;

// Load reCAPTCHA
require_once 'recaptcha.php';

$year = ens\get_val( 'year' );

get_header();
global $post;

$errors = [];
$danger_flags = [];
$success = false;

if( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
    //var_dump( $_POST );

    // Input validation
    if( !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
        $errors[] = ens\ENSEMBLE__ERROR_EMAIL;
    }

    if( "Other" === $_POST['year'] && empty( $_POST['year-other'] ) ) {
        $errors[] = ens\ENSEMBLE__BLANK_OTHER_YEAR;
        $danger_flags[] = 'year-other';
    }

    if( isset( $_POST['instruments'] ) && in_array( "Other", $_POST['instruments'] ) && empty( $_POST['instrument-other'] ) ) {
        $errors[] = ens\ENSEMBLE__BLANK_OTHER_INSTRUMENT;
        $danger_flags[] = 'instrument-other';
    }

    if( !isset( $_POST['instruments'] ) || empty( $_POST['instruments'] ) ) {
        $errors[] = ens\ENSEMBLE__BLANK_INSTRUMENTS;
    }

    if( !isset( $_POST['ensembles'] ) || empty( $_POST['ensembles'] ) ) {
        $errors[] = ens\ENSEMBLE__BLANK_ENSEMBLES;
    }

    if( isset( $_POST['g-recaptcha-response'] ) ) {
        $recaptcha = new \ReCaptcha\ReCaptcha( $secret );
        $resp = $recaptcha->verify( $_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'] );
    }
    else {
        $errors[] = ens\ENSEMBLE__NO_RECAPTCHA;
    }

    // If no errors, go ahead and build/send emails.
    if( empty( $errors ) ) {

        if( ens\new_entry( $_POST ) ) { 
            // Send the student a confirmation email
            $subject = "Ensemble Interest Form Submission";

            $to = [ [ 'addr' => $_POST['email'], 'name' => $_POST['fname'] . " " . $_POST['lname'] ] ];
            $from = [ 'addr' => 'cahweb@ucf.edu', 'name' => 'CAH Web Team' ];
            
            $body = ens\build_student_email( $_POST );

            $mail = new mailer( $to, $from, $subject, $body );

            $mail->embed( 'wp-content/uploads/sites/20/2020/05/UILinternal_K_School-of-Performing-Arts.png', 'music_logo' );

            /*
            // Send the faculty email with student information
            $to = [ ['addr' => 'michael.leavitt@ucf.edu', 'name' => 'Michael Leavitt'] ];
            $from = ['addr' => 'cahweb@ucf.edu', 'name' => 'CAH Web Team'];
            
            $body = ens\build_dept_email( $_POST );

            $dept_mail = new mailer( $to, $from, $subject, $body );
            */

            if( $mail->send() ) {
                $errors[] = ens\ENSEMBLE__STATUS_SUCCESS;
                $success = true;
            }
            else {
                $errors[] = ens\ENSEMBLE__STATUS_FAIL;
                $errors[] = [ 'msg' => "PHPMailer Error: " . $mail->getError(), 'type' => 'warning' ];
            }
        }
        else {
            $errors[] = ens\ENSEMBLE__ERROR_DB;
        }
    }
}
?>

<div class="container mt-5 mb-4">

    <?php if( !empty( $errors ) ) : ?>
        <?php foreach( $errors as $error ) : ?>
            <div class="alert alert-<?= $error['type'] ?> alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><strong><?= ( 'danger' === $error['type'] ? "Error" : ucfirst( $error['type'] ) ) . ":" ?></strong> <?= $error['msg'] ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if( !$success ) : ?>
    <form method="post" action="">

    
        <h4 class="mt-5">Personal Information</h4>
        <div class="row">
            <div class="form-group col-lg-6">
                <label for="fname" id="fname-label">First Name: </label>
                <input type="text" id="fname" name="fname" class="form-control" required value="<?= ens\get_val( 'fname' ); ?>" aria-labelledby="fname-label">
            </div>
            <div class="form-group col-lg-6">
                <label for="lname" id="lname-label">Last Name: </label>
                <input type="text" id="lname" name="lname" class="form-control" required value="<?= ens\get_val( 'lname' ); ?>" aria-labelledby="lname-label">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-6">
                <label for="email" id="email-label">Email Address: </label>
                <input type="email" id="email" name="email" class="form-control" required value="<?= ens\get_val( 'email' ); ?>" aria-labelledby="email-label">
            </div>
            <div class="form-group col-lg-6">
                <label for="phone" id="phone-label">Phone Number: </label>
                <input type="tel" id="phone" name="phone" class="form-control" required value="<?= ens\get_val( 'phone' ) ?>" aria-labelledby="phone-label">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-6">
                <label for="major" id="major-label">Major: </label>
                <input type="text" id="major" name="major" class="form-control" required value="<?= ens\get_val( 'major' ); ?>" aria-labelledby="major-label">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 form-group mt-3">
                <label for="year" id="year-label" class="font-weight-bold">I am a&hellip;</label>
                <select class="form-control" id="year" name="year" required aria-labelledby="year-label">
                    <option value=""> -- Please Select -- </option>
                <?php foreach( ens\Vars::$years as $y ) : ?>
                    <option value="<?= $y['value'] ?>"<?= $year == $y['value'] ? " selected" : "" ?>><?= $y['name'] ?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 form-group<?= in_array( 'year-other', $danger_flags ) ? ' has-danger' : '' ?>">
                <p id="year-other-help" class="form-text text-muted mb-1"><small>If Other, please describe:</small></p>
                <input type="text" id="year-other" name="year-other" class="form-control<?= in_array( 'year-other', $danger_flags ) ? ' form-control-danger' : '' ?>" value="<?= ens\get_val( 'year-other' ); ?>" aria-describedby="year-other-help" aria-label="Other Year Description">
            </div>
        </div>


        <h4 class="mt-5">Instrument or Discipline</h4>
        <p id="instruments-help" class="form-text text-muted"><small>Check all that apply</small></p>
        <div class="row">
        <?php foreach( ens\Vars::$instruments as $instrument ) : ?>
            <?php $label_id = strtolower( str_replace( "()", "", str_replace( " ", "-", $instrument ) ) ) . "-label"; ?>
            <div class="col-md-6 col-lg-4 form-check">
                <label class="form-check-label" id="<?= $label_id ?>">
                    <input class="form-check-input" type="checkbox" name="instruments[]" aria-describedby="instruments-help" aria-labelledby="<?= $label_id ?>" value="<?= $instrument ?>"<?= ens\check_box( 'instruments', $instrument ) ?>>
                    <?= $instrument ?>
                </label>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-6 col-lg-4 form-check">
                <label for="instrument-other" class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="instruments[]" id="instrument-other" aria-describedby="instruments-help" aria-label="Other" value="Other" <?= ens\check_box( 'instruments', "Other" ) ?>>
                    Other <p id="instruments-other-help" class="form-text text-muted d-inline"><small>(If checked, please enter below)</small></p>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-lg-4<?= in_array( 'instrument-other', $danger_flags ) ? ' has-danger' : '' ?>">
                <input class="form-control<?= in_array( 'instrument-other', $danger_flags ) ? ' form-control-danger' : '' ?>" type="text" id="instrument-color" name="instrument-other" aria-describedby="instruments-other-help" aria-label="Other Instrument" value="<?= ens\get_val( 'instrument-other' ); ?>">
            </div>
        </div>


        <h4 class="mt-5">I am interested in...</h4>
        <p id="ensembles-help" class="form-text text-muted"><small>Check all that apply</small></p>
        <div class="row">
        <?php foreach( ens\Vars::$ensembles as $ensemble ) : ?>
            <?php $label_id = strtolower( str_replace( " ", "-", $ensemble['name'] ) ); ?>
            <div class="col-12 form-check">
                <label class="form-check-label" id="<?= $label_id ?>">
                    <input class="form-check-input" type="checkbox" name="ensembles[]" aria-describedby="ensembles-help" aria-labelledby="<?= $label_id ?>" value="<?= $ensemble['name'] ?>"<?= ens\check_box( 'ensembles', $ensemble['name'] ) ?>>
                    <?= $ensemble['name'] . ( !empty( $ensemble['subtitle'] ) ? " <small><em>{$ensemble['subtitle']}</em></small>" : '' ) ?>
                </label>
            </div>
        <?php endforeach; ?>
        </div>


        <h4 class="mt-5">Additional Notes</h4>
        <p id="notes-help" class="form-text text-muted"><small>Anything you want/need us to know?</small></p>
        <div class="row">
            <div class="col-lg-6 form-group">
                <textarea class="form-control" rows="4" id="notes" name="notes" aria-describedby="notes-help" aria-label="Additional Notes"><?= ens\get_val( 'notes' ); ?></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="g-recaptcha" data-sitekey="<?= $siteKey ?>"></div>
                <script src="https://www.google.com/recaptcha/api.js?h1=<?= $lang ?>"></script>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-5" aria-label="Submit">Submit</button>
    </form>

    <?php else : ?>
    <a href="/" class="btn btn-primary btn-lg">Home</a>
    <?php endif; ?>
</div>

<?php
get_footer();
?>