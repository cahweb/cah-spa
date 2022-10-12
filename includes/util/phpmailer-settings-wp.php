<?php
/*
 * PHP Mailer utility functions for the WordPress integration of PHP Mailer
 */
namespace UCF\CAH\WordPress\Mail;

function phpmailer_init()
{
    add_action( 'phpmailer_init', "UCF\\CAH\\WordPress\\Mail\\set_smtp" );
    add_filter( 'wp_mail_content_type', "UCF\\CAH\\WordPress\\Mail\\set_html" );
}

function set_smtp( $phpmailer )
{
    $phpmailer->isSMTP();
    $phpmailer->Host        = 'ucfsmtp1.mail.ucf.edu';
    $phpmailer->Port        = 25;
    $phpmailer->SMTPAuth    = false;
    $phpmailer->From        = "cahweb@ucf.edu";
    $phpmailer->FromName    = "CAH Web Team";
}

function set_html()
{
    return "text/html";
}

function format_name_addr( string $addr, string $name ) : string
{
    return "$name <$addr>";
}

?>