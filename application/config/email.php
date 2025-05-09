<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']     = 'smtp';
$config['smtp_host']    = 'smtp.hostinger.com';  // <— tanpa "ssl://"
$config['smtp_port']    = 465;
$config['smtp_crypto']  = 'ssl';                  // <— tambahkan ini
$config['smtp_timeout'] = 30;
$config['smtp_user']    = 'syahrul@c-tech.id';
$config['smtp_pass']    = 'Syahrulramadhan#123';
$config['mailtype']     = 'html';
$config['charset']      = 'utf-8';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";
$config['wordwrap']     = TRUE;
