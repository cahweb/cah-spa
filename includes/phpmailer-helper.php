<?php
namespace UCF\CAH\MailTools;

// Load PHPMailer
/* For Local Dev */
/*
require_once 'D:\\wamp64\\composer\\vendor\\autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
//*/

/* -- For PROD -- */
///*
require_once 'class.phpmailer.php';
use PHPMailer as PHPMailer;
//*/

// SMTP info and authorization credentials
require_once '_mailauth.php';

/**
 * == PHPMailer Helper Class ==
 * Wrapper for PHPMailer that auto-sets most common settings, in order to
 * cut down on boilerplate in a given app.
 * 
 * @author Mike W. Leavitt
 * @version 1.0.0
 */
if( !class_exists( 'PHPMailerHelper' ) ) {
    class PHPMailerHelper
    {
        private $mail;

        /**
         * Constructor
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @param array $to - an array of email address arrays, each containing a `name` and `addr` field
         * @param array $from - an email array, with a `name` and `addr` field
         * @param string $subj - email subject
         * @param string $body - email body
         * @param array $cc - an array of email address arrays, for sending CCs. Default empty array
         * @param array $bcc - an array of email address arrays, for sending BCCs. Default empty array
         * 
         * @return void
         */
        public function __construct(array $to, array $from, string $subj, string $body, array $cc = [], array $bcc = []) {
            $this->mail = new PHPMailer();

            $this->mail->isHTML(true);

            $this->mail->setFrom( $from['addr'], $from['name'] );

            if( empty( $to ) ) return false;

            $this->_addAddr( $to, 'to' );

            if( !empty( $cc ) ) $this->_addAddr( $cc, 'cc' );
            if( !empty( $bcc ) ) $this->_addAddr( $bcc, 'bcc' );

            $this->mail->Subject = $subj;
            $this->mail->Body = $body;
            $this->mail->AltBody = "Make sure your email client is set to recieve HTML emails.";

            $this->mail->isSMTP();

            global $mail_server, $mail_username, $mail_password;

            $this->mail->Host = $mail_server;

            /* For PROD */
            $this->mail->SMTPAuth = false;

            /* In case the SMTP server requires authentication.
            $this->mail->SMTPAuth = true;

            $this->mail->Username = $mail_username;
            $this->mail->Password = $mail_password;
            */

            $this->mail->Port = 25;
        }


        /**
         * Wrapper for PHPMailer::send()
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return bool
         */
        public function send() : bool {
            if( $this->mail->send() ) return true;
            else return false;
        }


        /**
         * Wrapper that returns PHPMailer::$ErrorInfo
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @return string
         */
        public function getError() : string {
            return $this->mail->ErrorInfo;
        }


        /**
         * Wrapper for PHPMailer::AddEmbeddedImage()
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @param string $uri - The URI of the image resource you want to embed.
         * @param string $label - The label to use to refer to the image in the email body.
         * 
         * @return void
         */
        public function embed( string $uri, string $label ) {
            $this->mail->AddEmbeddedImage( $uri, $label );
        }


        /**
         * Syntactic sugar for adding addresses, CCs, and BCCs
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         * 
         * @param array $to - The array of address arrays that we want to loop through
         * @param string $field - The type of address to add. Defaults to "to"
         * 
         * @return void
         */
        private function _addAddr( array $to, string $field = 'to' ) {
            foreach( $to as $recipient ) {
                switch( $field ) {

                    case 'to':
                        $this->mail->addAddress( $recipient['addr'], $recipient['name'] );
                        break;
                    
                    case 'cc':
                        $this->mail->addCc( $recipient['addr'], $recipient['name'] );
                        break;

                    case 'bcc':
                        $this->mail->addBcc( $recipient['addr'], $recipient['name'] );
                        break;
                }
            }
        }
    }
}
?>