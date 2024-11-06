<?php 
if ($f == "update-event") {
    if (checkUserSession()) { // Placeholder for actual session check
        $error = validateEventInput($_POST);
        if (!$error && isset($_GET['eid']) && is_numeric($_GET['eid'])) {
            // Assuming Wo_Secure sanitizes the inputs
            $registration_data = prepareEventData($_POST);
            $result = Wo_UpdateEvent($_GET['eid'], $registration_data);

            if ($result) {
                handleImageUploads($_GET['eid'], $_POST, $_FILES);
                $data = [
                    'message' => $success_icon . $wo['lang']['event_saved'],
                    'status' => 200,
                    'url' => Wo_SeoLink("index.php?link1=show-event&eid=" . $_GET['eid'])
                ];
            } else {
                $data = [
                    'status' => 500,
                    'message' => $error_icon . $wo['lang']['event_save_error']
                ];
            }
        } else {
            $data = [
                'status' => 500,
                'message' => $error
            ];
        }
    }

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

function checkUserSession() {
    // Implement actual session validation logic
    return true;
}

function validateEventInput($inputs) {
    $required_fields = ['event-name', 'event-locat', 'event-description', 'event-start-date', 'event-end-date', 'event-start-time', 'event-end-time'];
    foreach ($required_fields as $field) {
        if (empty($inputs[$field])) {
            return $error_icon . $wo['lang']['please_check_details'];
        }
    }
    if (strlen($inputs['event-name']) < 10) {
        return $error_icon . $wo['lang']['title_more_than10'];
    }
    if (strlen($inputs['event-description']) < 32) {
        return $error_icon . $wo['lang']['desc_more_than32'];
    }
    $date_start = new DateTime($inputs['event-start-date']);
    $date_end = new DateTime($inputs['event-end-date']);
    if ($date_start > $date_end) {
        return $error_icon . $wo['lang']['please_check_details'];
    }
    return null;
}

function prepareEventData($inputs) {
    return array(
        'name' => Wo_Secure($inputs['event-name']),
        'location' => Wo_Secure($inputs['event-locat']),
        'description' => Wo_Secure($inputs['event-description']),
        'start_date' => Wo_Secure($inputs['event-start-date']),
        'start_time' => Wo_Secure($inputs['event-start-time']),
        'end_date' => Wo_Secure($inputs['event-end-date']),
        'end_time' => Wo_Secure($inputs['event-end-time'])
    );
}

function handleImageUploads($eventId, $postData, $fileData) {
    if (!empty($postData['cropped_image'])) {
        uploadCroppedImage($postData['cropped_image'], $eventId);
    } elseif (!empty($fileData["event-cover"]["tmp_name"])) {
        uploadRegularImage($fileData["event-cover"], $eventId);
    }
}

function uploadCroppedImage($croppedImageData, $eventId) {
    global $sqlConnect, $wo;

    // Check if the Base64 string format is correct
    if (preg_match('/^data:image\/(\w+);base64,/', $croppedImageData, $type)) {
        $type = strtolower($type[1]); // Extract image type (e.g., jpg, png)

        // Validate allowed image types
        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception('Invalid image type. Only JPG, PNG, and GIF are supported.');
        }

        // Decode the Base64 image data
        $croppedImageData = base64_decode(substr($croppedImageData, strpos($croppedImageData, ',') + 1));
        if ($croppedImageData === false) {
            throw new Exception('Base64 decode failed. Please check the image data.');
        }

        // Create upload directory based on year and month
        $year = date('Y');
        $month = date('m');
        $upload_dir = "upload/photos/{$year}/{$month}/";
        
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
            throw new Exception('Failed to create upload directory.');
        }

        // Generate a unique file name and define the file path
        $unique_file_name = 'cropped_' . time() . '_' . uniqid() . '.' . $type;
        $file_path = $upload_dir . $unique_file_name;

        // Save the decoded image data to the file
        if (file_put_contents($file_path, $croppedImageData) === false) {
            throw new Exception('Unable to save cropped image. Please try again.');
        }

        // Verify the file is a valid image
        $check_file = getimagesize($file_path);
        if (!$check_file) {
            unlink($file_path); // Remove invalid file
            throw new Exception('Invalid image file.');
        }

        // Resize the image if required
        $resize_width = 918;  // Example width for resizing
        $resize_height = 332; // Example height for resizing
        $quality = $wo['config']['images_quality'];
        Wo_Resize_Crop_Image($resize_width, $resize_height, $file_path, $file_path, $quality);

        // Check if S3 upload is enabled
        if ($wo['config']['s3_upload'] == 1) {
            if (!Wo_UploadToS3($file_path)) {
                error_log("S3 upload failed for: " . $file_path); // Log if S3 upload fails
                throw new Exception('Failed to upload image to S3.');
            }
        }

        // Update the event's cover column with the image path in the database
        $db_update_query = "UPDATE wo_events SET cover = '" . Wo_Secure($file_path) . "' WHERE id = " . Wo_Secure($eventId);
        $db_result = mysqli_query($sqlConnect, $db_update_query);
        
        if (!$db_result) {
            unlink($file_path); // Remove the file if DB update fails
            error_log('Database update failed: ' . mysqli_error($sqlConnect));
            throw new Exception('Failed to update the event cover image in the database.');
        }

        return true; // Successfully uploaded, resized, and updated
    } else {
        throw new Exception('Invalid image data format. Please ensure the data is in Base64 format.');
    }
}






function uploadRegularImage($fileInfo, $eventId) {
    $temp_name = $fileInfo["tmp_name"];
    $file_name = $fileInfo["name"];
    $file_type = $fileInfo['type'];
    $file_size = $fileInfo["size"];
    Wo_UploadImage($temp_name, $file_name, 'cover', $file_type, $eventId, 'event');
}
?>
