<?php
if ($f == "insert-event") {
    if (Wo_CheckSession($hash_id) === true) {
        // Validate required fields
        if (empty($_POST['event-name']) || empty($_POST['event-locat']) || empty($_POST['event-description'])) {
            $error = $error_icon . $wo['lang']['please_check_details'];
        } else {
            if (strlen($_POST['event-name']) < 10) {
                $error = $error_icon . $wo['lang']['title_more_than10'];
            }
            if (strlen($_POST['event-description']) < 32) {
                $error = $error_icon . $wo['lang']['desc_more_than32'];
            }
            if (empty($_POST['event-start-date']) || empty($_POST['event-end-date']) || empty($_POST['event-start-time']) || empty($_POST['event-end-time'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }

            // Simplified Date Comparison
            $startDate = new DateTime($_POST['event-start-date']);
            $endDate = new DateTime($_POST['event-end-date']);
            
            if ($startDate > $endDate) {
                $error = $error_icon . $wo['lang']['invalid_date_range'];
            }
        }

        // If no error, insert the event into the database
        if (empty($error)) {
            $registration_data = array(
                'name'        => Wo_Secure($_POST['event-name'],1),
                'location'    => Wo_Secure($_POST['event-locat']),
                'description' => Wo_Secure($_POST['event-description'],1),
                'start_date'  => Wo_Secure($_POST['event-start-date']),
                'start_time'  => Wo_Secure($_POST['event-start-time']),
                'end_date'    => Wo_Secure($_POST['event-end-date']),
                'end_time'    => Wo_Secure($_POST['event-end-time']),
                'poster_id'   => $wo['user']['id']
            );
            $last_id = Wo_InsertEvent($registration_data);

            if ($last_id && is_numeric($last_id)) {
                // Check for cropped image (base64 data)
                if (!empty($_POST['cropped_image'])) {
                    $cropped_image_data = $_POST['cropped_image'];

                    // Extract the base64 data from the string (e.g., data:image/png;base64,...)
                    list($type, $cropped_image_data) = explode(';', $cropped_image_data);
                    list(, $cropped_image_data) = explode(',', $cropped_image_data);

                    // Decode the base64 data
                    $cropped_image_data = base64_decode($cropped_image_data);

                    // Generate a unique name for the cropped image
                    $cropped_image_name = 'cropped_' . time() . '.png'; // or use .jpg/.jpeg depending on the file type

                    // Define the file path where the cropped image will be saved
                    $file_path = 'uploads/events/' . $cropped_image_name;

                    // Save the cropped image to the server
                    file_put_contents($file_path, $cropped_image_data);

                    // Update the database with the cropped image name
                    Wo_UploadImage($file_path, $cropped_image_name, 'cover', 'image/png', $last_id, 'event');
                } 
                // Check for regular image upload (original image)
                else if (!empty($_FILES["event-cover"]["tmp_name"])) {
                    $temp_name = $_FILES["event-cover"]["tmp_name"];
                    $file_name = $_FILES["event-cover"]["name"];
                    $file_type = $_FILES['event-cover']['type'];
                    $file_size = $_FILES["event-cover"]["size"];
                    Wo_UploadImage($temp_name, $file_name, 'cover', $file_type, $last_id, 'event');
                }

                // Success response
                $data = array(
                    'message' => $success_icon . $wo['lang']['event_added'],
                    'status' => 200,
                    'url' => Wo_SeoLink("index.php?link1=show-event&eid=" . $last_id)
                );
            }
        } else {
            // Error response
            $data = array(
                'status' => 500,
                'message' => $error
            );
        }
    }
    // Return the response as JSON
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
