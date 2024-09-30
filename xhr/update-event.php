<?php 
if ($f == "update-event") {
    if (true) {
        if (empty($_POST['event-name']) || empty($_POST['event-locat']) || empty($_POST['event-description'])) {
            $error = $error_icon . $wo['lang']['please_check_details'];
        } else {
            if (strlen($_POST['event-name']) < 10) {
                $error = $error_icon . $wo['lang']['title_more_than10'];
            }
            if (strlen($_POST['event-description']) < 32) {
                $error = $error_icon . $wo['lang']['desc_more_than32'];
            }
            if (empty($_POST['event-start-date'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($_POST['event-end-date'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($_POST['event-start-time'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            if (empty($_POST['event-end-time'])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
            // Simplified date comparison logic
            $date_start = explode('-', $_POST['event-start-date']);
            $date_end = explode('-', $_POST['event-end-date']);
            if ($date_start[0] > $date_end[0] || ($date_start[0] == $date_end[0] && $date_start[1] > $date_end[1]) || ($date_start[0] == $date_end[0] && $date_start[1] == $date_end[1] && $date_start[2] > $date_end[2])) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            }
        }

        if (empty($error) && isset($_GET['eid']) && is_numeric($_GET['eid'])) {
            // Update event data
            $registration_data = array(
                'name' => Wo_Secure($_POST['event-name']),
                'location' => Wo_Secure($_POST['event-locat']),
                'description' => Wo_Secure($_POST['event-description']),
                'start_date' => Wo_Secure($_POST['event-start-date']),
                'start_time' => Wo_Secure($_POST['event-start-time']),
                'end_date' => Wo_Secure($_POST['event-end-date']),
                'end_time' => Wo_Secure($_POST['event-end-time'])
            );
            $result = Wo_UpdateEvent($_GET['eid'], $registration_data);

            if ($result) {
                // Handle cropped image if it is provided
                if (!empty($_POST['cropped_image'])) {
                    $cropped_image_data = $_POST['cropped_image'];

                    // Extract the base64 data
                    if (preg_match('/^data:image\/(\w+);base64,/', $cropped_image_data, $type)) {
                        $cropped_image_data = substr($cropped_image_data, strpos($cropped_image_data, ',') + 1);
                        $type = strtolower($type[1]); // jpg, png, gif

                        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                            throw new \Exception('Invalid image type');
                        }

                        $cropped_image_data = base64_decode($cropped_image_data);

                        if ($cropped_image_data === false) {
                            throw new \Exception('Base64 decode failed');
                        }
                    } else {
                        throw new \Exception('Did not match data URI with image data');
                    }

                    // Generate a unique name for the image
                    $cropped_image_name = 'cropped_' . time() . '.' . $type;

                    // Define the file path
                    $file_path = 'uploads/events/' . $cropped_image_name;

                    // Save the image
                    if (file_put_contents($file_path, $cropped_image_data) === false) {
                        throw new \Exception('Failed to save the cropped image file');
                    }

                    // Update the event cover
                    Wo_UploadImage($file_path, $cropped_image_name, 'cover', 'image/' . $type, $_GET['eid'], 'event');
                } 
                // Handle the regular image file upload if no cropped image
                else if (!empty($_FILES["event-cover"]["tmp_name"])) {
                    $temp_name = $_FILES["event-cover"]["tmp_name"];
                    $file_name = $_FILES["event-cover"]["name"];
                    $file_type = $_FILES['event-cover']['type'];
                    $file_size = $_FILES["event-cover"]["size"];
                    Wo_UploadImage($temp_name, $file_name, 'cover', $file_type, $_GET['eid'], 'event');
                }

                // Send success response
                $data = array(
                    'message' => $success_icon . $wo['lang']['event_saved'],
                    'status' => 200,
                    'url' => Wo_SeoLink("index.php?link1=show-event&eid=" . $_GET['eid'])
                );
            } else {
                $data = array(
                    'status' => 500,
                    'message' => $error_icon . $wo['lang']['event_save_error']
                );
            }
        } else {
            $data = array(
                'status' => 500,
                'message' => $error
            );
        }
    }

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
