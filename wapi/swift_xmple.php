<?php
/**
 * Web api
 * Send email
 */
header('Content-Type: application/json');
require_once('../../vendor/autoload.php');

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

if ($act === 'mail'){
    $to = $_REQUEST['to'] ?? '';
    if (empty($to)) {
        echo 'To required';
        die();
    }
    require_once('conf.php');
    /** @var $FROM string */
    /** @var $FROM_NAME string */
    /** @var $PW string */

    // Create the Transport
    $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587))
        ->setUsername($FROM)
        ->setPassword($PW)
    ;

// Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

// Create a message
    $message = (new Swift_Message('Wonderful Subject'))
        ->setFrom([$FROM => $FROM_NAME])
        ->setTo([$to])
        ->setBody('Here is the message itself')
    ;

// Send the message
    try {
        $result = $mailer->send($message);
    }
    catch (\Exception $e){
        echo $e->getMessage();
    }
    $a=1;

}
