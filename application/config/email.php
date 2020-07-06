<?php

/*
|--------------------------------------------------------------------------
| Mail Configuration
|--------------------------------------------------------------------------
|
| Mail Configuration for send emails using Mail services like Mailgun and Mailchimp
|
*/
$config['email']['protocol']     = 'smtp';
$config['email']['smtp_host']    = 'smtp.mailgun.org';
$config['email']['smtp_port']    = '587';
$config['email']['smtp_timeout'] = '30';
$config['email']['smtp_user']    = 'postmaster@trackit.iblinfotech.com';
$config['email']['smtp_pass']    = '502153a7906b0e7cd46aa2959fbe260d-0470a1f7-f7b80605';
$config['email']['smtp_crypto']  = '';
$config['email']['charset']      = 'utf-8';
$config['email']['mailtype']     = 'html';
$config['email']['wordwrap']     = TRUE;
$config['email']['newline']      = "\r\n";
