<?php

header("Content-type: application/json");

$response_data = [
    'api_status' => 0,
    'message' => ''
];

if (!empty($_POST['new_password']) && !empty($_POST['code'])) {
    $user_id_data = explode("_", $_POST['code']);
    $user_id = $user_id_data[0];
    $update = true;

    // Validate the reset token
    if (Wo_isValidPasswordResetToken($_POST['code']) === false && Wo_isValidPasswordResetToken2($_POST['code']) === false) {
        $update = false;
        $response_data['api_status'] = 9;
        $response_data['message'] = 'Invalid or expired reset code.';
    }

    // If token is valid, proceed to update the password
    if ($update === true) {
        // Check if the new password meets the length requirement
        if (strlen($_POST['new_password']) >= 6) {
            $new_password = $_POST['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Check if the user exists
            if ($user_id) {
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
                $response_data['api_status'] = 200;
                $response_data['message'] = 'Your password has been successfully updated.';
            } else {
                $response_data['api_status'] = 9;
                $response_data['message'] = 'User not found or invalid code.';
            }
        } else {
            $response_data['api_status'] = 10;
            $response_data['message'] = 'Password is too short. It must be at least 6 characters.';
        }
    }
} else {
    $response_data['api_status'] = 8;
    $response_data['message'] = 'new_password and code cannot be empty.';
}

// Output the final response
echo json_encode($response_data);
exit();
