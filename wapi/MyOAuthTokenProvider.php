<?php
class MyOAuthTokenProvider implements \PHPMailer\PHPMailer\OAuthTokenProvider
{

    /**
     * @inheritDoc
     */
    public function getOauth64() {
        return 'dXNlcj1tYXJjdXMuYm9pbnRvbkBnbWFpbC5jb20BYXV0aD1CZWFyZXIgeWEyOS5lZ0h5NGpXbkZaZFpMaEctV3g1ZUVtbGZiTUhqaG00Yk9BVzZETVVVamVSZDN0ZG5LOTV1bzd6ekFQcHhva3VWNjdJdEhpaWxKVnBROFEBAQ';//base64 of user=someids@gmail.com\001auth=Bearer accesstoken\001\001
    }
}
