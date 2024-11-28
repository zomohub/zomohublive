<?php

if (!empty($_POST['new_password']) &&  !empty($_POST['code'])) {
	
    $user_id_data  = explode("_", $_POST['code']);
    $user_id = $user_id_data[0]; 
	$update = true;

    // Validate the reset token
    if (Wo_isValidPasswordResetToken($_POST['code']) === false && Wo_isValidPasswordResetToken2($_POST['code']) === false) {
        $update = false;
        $error_code = 9;
        $error_message = 'Invalid or expired reset code.';
    }

    // If token is valid, proceed to update the password
    if ($update === true) {
        // Check if the new password meets the length requirement
        if (strlen($_POST['new_password']) >= 6) {
            $new_password = $_POST['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Get user information based on the reset code
            //$getUser = $db->where('email_code', $_POST['code'])->getOne(T_USERS);
            
            if ($user_id) {
                //$user_id = $getUser->user_id;
                
                // Update the user's password and clear the email_code
                $db->where('user_id', $user_id)->update(T_USERS, [
                    'password' => $hashed_password,
                    'email_code' => ''
                ]);

                // Clear the user's sessions to log them out of other devices
                $db->where('user_id', $user_id)->delete(T_APP_SESSIONS);

                // Clear the cache for the user data
                cache($user_id, 'users', 'delete');

                // Send a success response
                $response_data = [
                    'api_status' => 200,
                    'message' => 'Your password has been successfully updated.'
                ];
            } else {
                $error_code = 9;
                $error_message = 'User not found or invalid code.';
            }
        } else {
            $error_code = 10;
            $error_message = 'Password is too short. It must be at least 6 characters.';
        }
    }
} else {
    $error_code = 8;
    $error_message = 'new_password and code cannot be empty.';
}

// Output the response in JSON format
header("Content-type: application/json");
if (isset($error_message)) {
    echo json_encode([
        'api_status' => $error_code,
        'message' => $error_message
    ]);
} else {
    echo json_encode($response_data);
}

exit();
