<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate PHP Social Networking Platform
// | Copyright (c) 2016 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
// // MySQL Hostname
// $sql_db_host = "localhost";
// // MySQL Database User
// $sql_db_user = "root";
// // MySQL Database Password
// $sql_db_pass = "";
// // MySQL Database Name
// $sql_db_name = "zomolive";

// // Site URL
// $site_url = "http://localhost/zomohub"; // e.g (http://example.com)


ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Determine if the environment is local or live
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Local server settings
    $sql_db_host = "localhost";
    $sql_db_user = "root";
    $sql_db_pass = "";
    $sql_db_name = "zomolive";
    $site_url = "http://localhost/zomohublive"; // e.g. (http://example.com)
} else {
    // Live server settings
    $sql_db_host = "database-1.c3iyeo2a6nzb.us-east-1.rds.amazonaws.com";
    $sql_db_user = "zomohub";
    $sql_db_pass = "sIBuO1rvnK8TU1fhVnLY";
    $sql_db_name = "zomohub";
    $site_url = "https://www.zomohub.com"; // e.g. (http://example.com)
}
// echo $sql_db_name;
// die();

$auto_redirect = true;

// Purchase code
$purchase_code = "d610ddb3-9b75-473f-a017-7009baeeae74"; // Your purchase code, don't give it to anyone.

$siteEncryptKey = "bcffb441ab0d5bf9f6d42fbc648cb3022463da3d"; // Your site encrypt key, don't give it to anyone.
?>