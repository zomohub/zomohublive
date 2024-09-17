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

// Determine if the environment is local or live
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Local server settings
    $sql_db_host = "localhost";
    $sql_db_user = "root";
    $sql_db_pass = "1234";
    $sql_db_name = "zomolive";
    $site_url = "http://localhost/zomolive"; // e.g. (http://example.com)
} else {
    // Live server settings
    $sql_db_host = "localhost";
    $sql_db_user = "zomohubc_werey";
    $sql_db_pass = "]Iwo6c5mkKru";
    $sql_db_name = "zomohubc_zomohub_social_media";
    $site_url = "https://phase1.zomohub.com"; // e.g. (http://example.com)
}


$auto_redirect = true;

// Purchase code
$purchase_code = "d610ddb3-9b75-473f-a017-7009baeeae74"; // Your purchase code, don't give it to anyone.

$siteEncryptKey = "bcffb441ab0d5bf9f6d42fbc648cb3022463da3d"; // Your site encrypt key, don't give it to anyone.
?>