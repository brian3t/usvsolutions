<?php
/**
 * Web api
 * Send email
 */
header('Content-Type: application/json');

$http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (str_contains($http_origin, 'jslib') || str_contains($http_origin, 'craftbelly'))
{
    header("Access-Control-Allow-Origin: *");
}
//$remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
//echo ("server is " . json_encode($_SERVER));

require_once('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

const SMTP_DEBUG_LVL = SMTP::DEBUG_OFF;

$act = $_REQUEST['act'] ?? ''; //action
if (empty($act)) {
    echo '{Action required}';
    die();
}
$res = [];

/* probing
$res['stat'] = 1;
$res['act'] = $act;
$res['msg'] = 'testing';
echo json_encode($res);
*/

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
    $mail->SMTPAuth = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    $mail->Host = "smtp.gmail.com";
    $mail->Username = FROM;
    $mail->Password = PW;

    $mail->IsHTML(true);
    $mail->AddAddress($to, $to);
    $mail->SetFrom(FROM, FROM_NAME);
    $mail->AddBcc("someids@gmail.com", "admin monitor");//asdf
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
    $a = 1;

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
