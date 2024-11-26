
<?php

$user_id_start_with = $_GET['user_id_start_with'];
$user_id_end_with = $_GET['user_id_end_with'];

$users = $db->get(T_USERS);


$start = $_GET['start'];
$end = $_GET['end'];

$email_prefix = "zomo_user_";
$email_domain = "@gmail.com";

$existing_emails = [];

foreach($users as $user)
{
    $existing_emails[] = $user->email;
}


$missing_emails = [];
for ($i = $start; $i <= $end; $i++) {
    $expected_email = $email_prefix . $i . $email_domain;
    if (!in_array($expected_email, $existing_emails)) {
        $missing_emails[] = $expected_email;
    }
}

// Output the missing emails
if (!empty($missing_emails)) {
    echo "Missing emails in the sequence:<br>";
    foreach ($missing_emails as $missing_email) {
        echo $missing_email . "--------";
    }
} else {
    echo "No missing emails in the sequence.";
}



?>