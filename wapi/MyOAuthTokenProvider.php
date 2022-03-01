<?php

class MyOAuthTokenProvider implements \PHPMailer\PHPMailer\OAuthTokenProvider
{

    /**
     * @inheritDoc
     */
    public function getOauth64() {
        return 'dXNlcj1zb21laWRzQGdtYWlsLmNvbVwwMDFhdXRoPUJlYXJlciBhY2Nlc3N0b2tlblwwMDFcMDAx';//base64 of user=someids@gmail.com\001auth=Bearer accesstoken\001\001
    }
}
