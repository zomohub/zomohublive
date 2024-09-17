<?php

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

// Handle Resend Email Request
if ($f == 'resend_email') {
    $email = isset($_POST['email']) ? Wo_Secure($_POST['email']) : '';
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array(
            'errors' => $error_icon . 'Invalid email address'
        ));
        exit();
    }
    
    $user = Wo_UserData(Wo_UserIdFromEmail($email));
    if ($user && $user['active'] == 0) {
        // Resend activation email
        $code = $user['email_code'];
        $wo['code'] = $code;
        $wo['user'] = $user;
        $body = Wo_LoadPage('emails/activate');
        $send_message_data = array(
            'from_email' => $wo['config']['siteEmail'],
            'from_name' => $wo['config']['siteName'],
            'to_email' => $user['email'],
            'to_name' => $user['username'],
            'subject' => $wo['lang']['account_activation'],
            'charSet' => 'utf-8',
            'message_body' => $body,
            'is_html' => true
        );
        $send = Wo_SendMessage($send_message_data);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Activation email resent successfully'
        ));
    } else {
        echo json_encode(array(
            'errors' => $error_icon . 'No inactive account found with that email'
        ));
    }
    exit();
}

if ($f == 'register') {
    if (!empty($_SESSION['user_id'])) {
        $_SESSION['user_id'] = '';
        unset($_SESSION['user_id']);
    }
    if (!empty($_COOKIE['user_id'])) {
        $_COOKIE['user_id'] = '';
        unset($_COOKIE['user_id']);
        setcookie('user_id', '', -1);
        setcookie('user_id', '', -1, '/');
    }
    if ($wo['config']['auto_username'] == 1) {
        $_POST['username'] = time() . rand(111111, 999999);
        if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
            $errors = $error_icon . $wo['lang']['first_name_last_name_empty'];
            header("Content-type: application/json");
            echo json_encode(array(
                'errors' => $errors
            ));
            exit();
        }
        if (preg_match('/[^\w\s]+/u', $_POST['first_name']) || preg_match('/[^\w\s]+/u', $_POST['last_name'])) {
            $errors = $error_icon . $wo['lang']['username_invalid_characters'];
        }
    }
    $fields = Wo_GetWelcomeFileds();
    if (empty($_POST['email']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $errors = $error_icon . $wo['lang']['please_check_details'];
    } else {
        $is_exist = Wo_IsNameExist($_POST['username'], 0);
        // zomo customization update start

        if (empty($_POST['gender'])) {
            $errors = $error_icon . 'Select Gender';
        }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors = $error_icon . 'Invalid email format';
        } elseif (!preg_match("/^[a-zA-Z0-9._]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/", $_POST['email'])) {
            $errors = $error_icon . 'Email contains invalid characters';
        }
        
        // zomo customization end
        if (empty($_POST['phone_num']) && $wo['config']['sms_or_email'] == 'sms') {
            $errors = $error_icon . $wo['lang']['worng_phone_number'];
        }
        if (in_array(true, $is_exist)) {
            $errors = $error_icon . $wo['lang']['username_exists'];
        }
        if (Wo_IsBanned($_POST['username'])) {
            $errors = $error_icon . $wo['lang']['username_is_banned'];
        }
        if (Wo_IsBanned($_POST['email'])) {
            $errors = $error_icon . $wo['lang']['email_is_banned'];
        }
        if (preg_match_all('~@(.*?)(.*)~', $_POST['email'], $matches) && !empty($matches[2]) && !empty($matches[2][0]) && Wo_IsBanned($matches[2][0])) {
            $errors = $error_icon . $wo['lang']['email_provider_banned'];
        }
        if (Wo_CheckIfUserCanRegister($wo['config']['user_limit']) === false) {
            $errors = $error_icon . $wo['lang']['limit_exceeded'];
        }
        if (in_array($_POST['username'], $wo['site_pages'])) {
            $errors = $error_icon . $wo['lang']['username_invalid_characters'];
        }
        if (strlen($_POST['username']) < 5 OR strlen($_POST['username']) > 32) {
            $errors = $error_icon . $wo['lang']['username_characters_length'];
        }
        if (!preg_match('/^[\w]+$/', $_POST['username'])) {
            $errors = $error_icon . $wo['lang']['username_invalid_characters'];
        }
        if ($wo['config']['reserved_usernames_system'] == 1 && in_array($_POST["username"], $wo['reserved_usernames'])) {
            $errors = $error_icon . $wo['lang']['username_is_disallowed'];
        }
        if (!empty($_POST['phone_num'])) {
            if (!preg_match('/^\+?\d+$/', $_POST['phone_num'])) {
                $errors = $error_icon . $wo['lang']['worng_phone_number'];
            } else {
                if (Wo_PhoneExists($_POST['phone_num']) === true) {
                    $errors = $error_icon . $wo['lang']['phone_already_used'];
                }
            }
        }
        if (Wo_EmailExists($_POST['email']) === true) {
            $errors = $error_icon . $wo['lang']['email_exists'];
        }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors = $error_icon . $wo['lang']['email_invalid_characters'];
        }
        if (strlen($_POST['password']) < 6) {
            $errors = $error_icon . $wo['lang']['password_short'];
        }
        if ($_POST['password'] != $_POST['confirm_password']) {
            $errors = $error_icon . $wo['lang']['password_mismatch'];
        }
        if ($config['reCaptcha'] == 1) {
            if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
                $errors = $error_icon . $wo['lang']['reCaptcha_error'];
            }
        }
        $birthday  = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
        $gender = 'male';
        if (in_array($_POST['gender'], array_keys($wo['genders']))) {
            $gender = $_POST['gender'];
        }
        if (!empty($fields) && count($fields) > 0) {
            foreach ($fields as $key => $field) {
                if (empty($_POST[$field['fid']])) {
                    $errors = $error_icon . $field['name'] . ' is required';
                }
                if (mb_strlen($_POST[$field['fid']]) > $field['length']) {
                    $errors = $error_icon . $field['name'] . ' field max characters is ' . $field['length'];
                }
            }
        }
    }
    $field_data = array();
    if (empty($errors)) {
        if (!empty($fields) && count($fields) > 0) {
            foreach ($fields as $key => $field) {
                if (!empty($_POST[$field['fid']])) {
                    $name = $field['fid'];
                    if (!empty($_POST[$name])) {
                        $field_data[] = array(
                            $name => $_POST[$name]
                        );
                    }
                }
            }
        }
        $activate = ($wo['config']['emailValidation'] == '1') ? '0' : '1';
        $code     = md5(rand(1111, 9999) . time());
        $re_data  = array(
            'email' => Wo_Secure($_POST['email'], 0),
            'username' => Wo_Secure($_POST['username'], 0),
            'password' => $_POST['password'],
            'email_code' => Wo_Secure($code, 0),
            'src' => 'site',
            'gender' => Wo_Secure($gender),
            'lastseen' => time(),
            'active' => Wo_Secure($activate),
            'birthday' => $birthday
        );
        if ($wo['config']['disable_start_up'] == '1') {
            $re_data['start_up'] = '1';
            $re_data['start_up_info'] = '1';
            $re_data['startup_follow'] = '1';
            $re_data['startup_image'] = '1';
        }
        if ($wo['config']['website_mode'] == 'linkedin' && !empty($_POST['currently_working']) && in_array($_POST['currently_working'], array(
            'yes',
            'am_looking_to_work',
            'am_looking_for_employees'
        ))) {
            $re_data['currently_working'] = Wo_Secure($_POST['currently_working'], 0);
        }
        if ($wo['config']['auto_username'] == 1) {
            if (!empty($_POST['first_name'])) {
                $re_data['first_name'] = Wo_Secure($_POST['first_name'],1);
            }
            if (!empty($_POST['last_name'])) {
                $re_data['last_name'] = Wo_Secure($_POST['last_name'],1);
            }
        }
        if ($gender == 'female') {
            $re_data['avatar'] = "upload/photos/f-avatar.jpg";
        }
        if (!empty($_SESSION['ref']) && $wo['config']['affiliate_type'] == 0) {
            $ref_user_id = Wo_UserIdFromUsername($_SESSION['ref']);
            if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                $re_data['referrer'] = Wo_Secure($ref_user_id);
                $re_data['src']      = Wo_Secure('Referrer');
                if ($wo['config']['affiliate_level'] < 2) {
                    $update_balance = Wo_UpdateBalance($ref_user_id, $wo['config']['amount_ref']);
                }
                unset($_SESSION['ref']);
            }
        } elseif (!empty($_SESSION['ref']) && $wo['config']['affiliate_type'] == 1) {
            $ref_user_id = Wo_UserIdFromUsername($_SESSION['ref']);
            if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                $re_data['ref_user_id'] = Wo_Secure($ref_user_id);
            }
        }
        if (!empty($_POST['phone_num'])) {
            $re_data['phone_number'] = Wo_Secure($_POST['phone_num']);
        }
        $in_code = (isset($_POST['invited'])) ? Wo_Secure($_POST['invited']) : false;
        if (empty($_POST['phone_num'])) {
            $register = Wo_RegisterUser($re_data, $in_code);
        } else {
            if ($activate == 1) {
                $register = Wo_RegisterUser($re_data, $in_code);
            } else {
                $register = true;
            }
        }
        if ($register === true) {
            if ($activate == 1) {
                // Direct success case (account activated without email validation)
                $data  = array(
                    'status' => 200,
                    'message' => $success_icon . $wo['lang']['successfully_joined_label']
                );
                $login = Wo_Login($_POST['username'], $_POST['password']);
                if ($login === true) {
                    $session = Wo_CreateLoginSession(Wo_UserIdFromUsername($_POST['username']));
                    $_SESSION['user_id'] = $session;
                    setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
                }
                $data['location'] = Wo_SeoLink('index.php?link1=start-up');
                if ($wo['config']['membership_system'] == 1) {
                    $data['location'] = Wo_SeoLink('index.php?link1=go-pro');
                }
            } else if ($wo['config']['sms_or_email'] == 'mail') {
                // Case for email validation

                $user = Wo_UserData(Wo_UserIdFromEmail($_POST['email']));
                $wo['code']        = $code;
                // $wo['user']['email'] = $_POST['email'];
                $wo['user'] = $user;
                $body              = Wo_LoadPage('emails/activate');
                $send_message_data = array(
                    'from_email' => $wo['config']['siteEmail'],
                    'from_name' => $wo['config']['siteName'],
                    'to_email' => $_POST['email'],
                    'to_name' => $_POST['username'],
                    'subject' => $wo['lang']['account_activation'],
                    'charSet' => 'utf-8',
                    'message_body' => $body,
                    'is_html' => true
                );
                $send              = Wo_SendMessage($send_message_data);
        
                // Response for frontend with resend/retry options
                $data = array(
                    'status' => 202, // Custom status code indicating that verification is pending
                    'message' => $success_icon . $wo['lang']['successfully_joined_verify_label']
                );
            } else if ($wo['config']['sms_or_email'] == 'sms' && !empty($_POST['phone_num'])) {
                // SMS validation case
                $random_activation = Wo_Secure(rand(11111, 99999));
                $message           = "Your confirmation code is: {$random_activation}";
                if (Wo_SendSMSMessage($_POST['phone_num'], $message) === true) {
                    $register = Wo_RegisterUser($re_data, $in_code);
                    $user_id  = Wo_UserIdFromUsername($_POST['username']);
                    $query    = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `sms_code` = '{$random_activation}' WHERE `user_id` = {$user_id}");
                    cache($user_id, 'users', 'delete');
                    $data     = array(
                        'status' => 300,
                        'location' => Wo_SeoLink('index.php?link1=confirm-sms?code=' . $code)
                    );
                } else {
                    $errors = $error_icon . $wo['lang']['failed_to_send_code_email'];
                }
            }
        }
        
        if (!empty($field_data)) {
            $user_id = Wo_UserIdFromUsername($_POST['username']);
            $insert  = Wo_UpdateUserCustomData($user_id, $field_data, false);
        }
    }
    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}
