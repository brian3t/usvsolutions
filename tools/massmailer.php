<?php

$name = "Name";
$company_name = "Comp name";
$road_name = "Dandridge Ln";
$note = "PS Brian is cool";

$headers = 'From:support@leadfollowapp.com' . "\r\n" .

$message = "Hello $name,

My name is Brian and I am Chief Technology Officer of LeadFollow App Pte. Ltd. I found out about $company_name via YellowPage.

We are a startup built by MIT students in 2015. Most of our clients come from REMax. Our main office is in Boston, and we just expanded to beautiful San Diego. Our app helps you follow up with house buyers 24/7, using real people with the help of artificial intelligence. You can find out more at https://leadfollowapp.com and on San Diego Tribune magazine, Sunday March 13, 2017 issue. LeadFollowApp is free with basic features, and you can upgrade to premium subscription when you see fit.

If you find that you need to boost sales using artificial intelligence, please reply to me, or use the contact form on our website, or call us up.

Me or my colleague can also visit you at $company_name down $road_name with the time of your choosing. We are new to San Diego and we are eager to meet you in person. Your office is only 20 minutes from our San Diego office. 

Mobile apps, artificial intelligence, machine learning and cloud computing is hot, let us help you make the best use of it.

$note

With best regards,
Brian Nguyen
CTO, Lead Follow App Pte. Ltd.
https://leadfollowapp.com 
559 347 4767";

echo mail('ngxtri@gmail.com', 'Hello from LeadFollow App', $message);