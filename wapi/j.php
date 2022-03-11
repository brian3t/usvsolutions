<?php
/**
 * Web api
 * Send email using Google Oauth
 * j.php?act=mail&to=sendto&cont=content
 */
header('Content-Type: application/json');

$http_origin = $_SERVER['HTTP_ORIGIN'] ?? 'missing_server_http_orig';
$http_referrer = $_SERVER['HTTP_REFERER'] ?? 'missing_server_http_referrer';
$remote_addr = $_SERVER['REMOTE_ADDR'] ?? 'missing_server_remoteaddr';
if (str_contains($http_origin, 'jslib') || str_contains($http_origin, 'craftbelly') || str_contains($http_origin, 'socal')
    || str_contains($_SERVER['HTTP_USER_AGENT'], 'PostmanRuntime/') || $remote_addr === '72.220.10.214') {
    header("Access-Control-Allow-Origin: *");
} else {
    echo 'Not allowed. Origin: ' . $http_origin. " Referer: $http_referrer Remoteaddr: $remote_addr";
    return 'Not allowed';
}

require_once('../vendor/autoload.php');

use League\OAuth2\Client\Provider\Google;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

const SMTP_DEBUG_LVL = SMTP::DEBUG_OFF;

$act = $_REQUEST['act'] ?? ''; //action
if (empty($act)) {
    echo '{Action required}';
    die();
}
$res = [];

/**
 * Action mail
 * Needs: to, subj, cont
 * todo_future: Add a rate limit per IP
 */
if ($act === 'mail') {
    require_once('conf.php');
    $now = (new \DateTime())->format('Y-m-d h:i:s');

    $to = $_REQUEST['to'] ?? '';
    if (empty($to)) {
        err('Missing `to`');
        die();
    }
    $subj = $_REQUEST['subj'] ?? '';
    if (empty($subj)) {
        $subj = DEFAULT_SUB . " $now";
    }
    $cont = $_REQUEST['cont'] ?? '';
    if (empty($cont)) {
        $cont = "Email sent on $now via " . $_SERVER['SERVER_ADDR'] . " ; requested from " . $_SERVER['REMOTE_ADDR'];
    }

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";

    $mail->SMTPDebug = SMTP_DEBUG_LVL;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->SMTPAuth = TRUE;
    $mail->AuthType = 'XOAUTH2';
    $mail->Port = 465;
    $mail->Host = "smtp.gmail.com";
    $mail->Username = FROM;
    $mail->Password = PW;

//Start Option 1: Use league/oauth2-client as OAuth2 token provider
//Fill in authentication details here
//Either the gmail account owner, or the user that gave consent
    $email = 'someids@gmail.com';
    $clientId = '861024413633-ika1uldd5q12fih8cr0vjr4gufer88oe.apps.googleusercontent.com';
    $clientSecret = GMCLIENT_SECRET;

//Obtained by configuring and running get_oauth_token.php
//after setting up an app in Google Developer Console.
    $refreshToken = GMREFRESH_TOKEN;

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

    $mail->IsHTML(true);
    $mail->AddAddress($to, $to);
    $mail->SetFrom(FROM, FROM_NAME);
//    $mail->AddBcc("someids@gmail.com", "admin monitor");//asdf
    $mail->Subject = $subj;

// Send the message
    try {
        $mail->MsgHTML($cont);
        if (! $mail->Send()) {
            err($mail);
        } else {
            succ("Email sent successfully");
        }
    } catch (\Exception $e) {
        err($e->getMessage());
    }
}

function err($err_msg) {
    http_response_code(400);
    if (! is_string($err_msg)) $err_msg = json_encode($err_msg);
    echo json_encode(['msg' => $err_msg, 'stat' => -1]);
}

function succ($msg) {
    http_response_code(200);
    if (! is_string($msg)) $msg = json_encode($msg);
    echo json_encode(['msg' => $msg, 'stat' => 1]);
}
