<?php

/**
 * This example shows how to send via Google's Gmail servers using XOAUTH2 authentication
 * using the league/oauth2-client to provide the OAuth2 token.
 * To use a different OAuth2 library create a wrapper class that implements OAuthTokenProvider and
 * pass that wrapper class to PHPMailer::setOAuth().
 */

//Import PHPMailer classes into the global namespace
use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

//Alias the League Google OAuth2 provider class

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('America/Los_Angeles');

//Load dependencies from composer
//If this causes an error, run 'composer install'
require dirname(__DIR__) . '/vendor/autoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer();

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_SERVER;

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';

//Set the SMTP port number:
// - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
// - 587 for SMTP+STARTTLS
$mail->Port = 465;

//Set the encryption mechanism to use:
// - SMTPS (implicit TLS on port 465) or
// - STARTTLS (explicit TLS on port 587)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Set AuthType to use XOAUTH2
$mail->AuthType = 'XOAUTH2';

//Start Option 1: Use league/oauth2-client as OAuth2 token provider
//Fill in authentication details here
//Either the gmail account owner, or the user that gave consent
$email = 'someids@gmail.com';
$clientId = '861024413633-ika1uldd5q12fih8cr0vjr4gufer88oe.apps.googleusercontent.com';
$clientSecret = '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';

//Obtained by configuring and running get_oauth_token.php
//after setting up an app in Google Developer Console.
$refreshToken = '1//06lJ5RS03QcnKCgYIARAAGAYSNwF-L9IryM96yricmSdv-6YyJx8lOdPFI6fpMSMVST6DH89jA5CA2Mz_x1GxqxJLtI_f_b_sdJM';

//Create a new OAuth2 provider instance
$provider = new Google(
    [
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
    ]
);

//Pass the OAuth provider instance to PHPMailer
$mail->setOAuth(
    new OAuth(
        [
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $email,
        ]
    )
);
//End Option 1

//Set who the message is to be sent from
//For gmail, this generally needs to be the same as the user you logged in as
$mail->setFrom($email, 'wapi gmail oauth2');

//Set who the message is to be sent to
$mail->addAddress('ngxtri@gmail.com', 'Tri Nguyen');

//Set the subject line
$mail->Subject = 'PHPMailer GMail XOAUTH2 SMTP test';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->CharSet = PHPMailer::CHARSET_UTF8;
$mail->msgHTML(file_get_contents( __DIR__ . '/contentsutf8.html'), __DIR__);

//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//Attach an image file
$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
}
