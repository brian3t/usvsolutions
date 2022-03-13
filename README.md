WP code of usvsolutions.com

Other utils:
###wapi
- i.php : send email
- j.php: send email using Google Oauth2  
    + In order to get new refresh token from Google: hit either http://localhost/wapi/get_oauth_token.php or https://usvsolutions.com/wapi/get_oauth_token.php  
After getting the token, put it to wapi/conf.php, under GMREFRESH_TOKEN
