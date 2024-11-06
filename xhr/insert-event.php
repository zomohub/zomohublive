<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($f == "insert-event") {
    if (Wo_CheckSession($hash_id) === true) {
        // Check for required fields and validate
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

            // Date validation logic
            if (empty($error)) {
                $date_start = explode('-', $_POST['event-start-date']);
                $date_end = explode('-', $_POST['event-end-date']);
                if ($date_start[0] > $date_end[0] || 
                    ($date_start[0] == $date_end[0] && $date_start[1] > $date_end[1]) || 
                    ($date_start[0] == $date_end[0] && $date_start[1] == $date_end[1] && $date_start[2] > $date_end[2])) {
                    $error = $error_icon . $wo['lang']['please_check_details'];
                }
            }
        }

        if (empty($error)) {
            // Insert the event data into the database
            $registration_data = array(
                'name' => Wo_Secure($_POST['event-name'], 1),
                'location' => Wo_Secure($_POST['event-locat']),
                'description' => Wo_Secure($_POST['event-description'], 1),
                'start_date' => Wo_Secure($_POST['event-start-date']),
                'start_time' => Wo_Secure($_POST['event-start-time']),
                'end_date' => Wo_Secure($_POST['event-end-date']),
                'end_time' => Wo_Secure($_POST['event-end-time']),
                'poster_id' => $wo['user']['id']
            );

            $last_id = Wo_InsertEvent($registration_data);
        
            if ($last_id && is_numeric($last_id)) {
                $image_uploaded = false;

                // Handle cropped image if available
                if (!empty($_POST['cropped_image'])) {
                    $cropped_image_data = $_POST['cropped_image'];
                    error_log('Cropped image data received.');

                    // Extract base64 data and decode it
                    if (preg_match('/^data:image\/(\w+);base64,/', $cropped_image_data, $type)) {
                        $cropped_image_data = substr($cropped_image_data, strpos($cropped_image_data, ',') + 1);
                        $type = strtolower($type[1]);

                        if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                            $data = array(
                                'status' => 500,
                                'message' => $error_icon . 'Invalid image type. Only JPG and PNG are supported.'
                            );
                            header("Content-type: application/json");
                            echo json_encode($data);
                            exit();
                        }

                        $cropped_image_data = base64_decode($cropped_image_data);
                        if ($cropped_image_data === false) {
                            $data = array(
                                'status' => 500,
                                'message' => $error_icon . 'Image decoding failed. Please try again.'
                            );
                            header("Content-type: application/json");
                            echo json_encode($data);
                            exit();
                        }

                        // Save the cropped image
                        $year = date('Y');
                        $month = date('m');
                        $upload_dir = "upload/photos/{$year}/{$month}/";
                        
                        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                            $data = array(
                                'status' => 500,
                                'message' => $error_icon . 'Failed to create upload directory.'
                            );
                            header("Content-type: application/json");
                            echo json_encode($data);
                            exit();
                        }

                        $cropped_image_name = 'cropped_' . time() . '_' . uniqid() . '.' . $type;
                        $file_path = $upload_dir . $cropped_image_name;

                        if (file_put_contents($file_path, $cropped_image_data) !== false) {
                            error_log('Cropped image successfully saved at: ' . $file_path);

                            // Update the event's cover column with the image path
                            $db_update_query = "UPDATE wo_events SET cover = '" . Wo_Secure($file_path) . "' WHERE id = " . Wo_Secure($last_id);
                            $db_result = mysqli_query($sqlConnect, $db_update_query);
                            if ($db_result) {
                                error_log('Database updated successfully with the cropped image path.');
                                $image_uploaded = true;
                            } else {
                                error_log('Failed to update the database with the new image path.');
                            }
                        } else {
                            $data = array(
                                'status' => 500,
                                'message' => $error_icon . 'Unable to save cropped image. Please try again.'
                            );
                            header("Content-type: application/json");
                            echo json_encode($data);
                            exit();
                        }
                    } else {
                        $data = array(
                            'status' => 500,
                            'message' => $error_icon . 'Invalid cropped image data.'
                        );
                        header("Content-type: application/json");
                        echo json_encode($data);
                        exit();
                    }
                } else {
                    error_log('No cropped image data found.');
                }

                // If no cropped image, use normal image upload if available
                if (!$image_uploaded && !empty($_FILES["event-cover"]["tmp_name"])) {
                    error_log('Uploading normal image...');
                    $temp_name = $_FILES["event-cover"]["tmp_name"];
                    $file_name = $_FILES["event-cover"]["name"];
                    $file_type = $_FILES['event-cover']['type'];

                    $year = date('Y');
                    $month = date('m');
                    $upload_dir = "upload/photos/{$year}/{$month}/";

                    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                        $data = array(
                            'status' => 500,
                            'message' => $error_icon . 'Failed to create upload directory.'
                        );
                        header("Content-type: application/json");
                        echo json_encode($data);
                        exit();
                    }

                    $image_name = 'event_' . time() . '_' . uniqid() . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
                    $file_path = $upload_dir . $image_name;

                    if (move_uploaded_file($temp_name, $file_path)) {
                        // Update the event's cover column with the image path
                        $db_update_query = "UPDATE wo_events SET cover = '" . Wo_Secure($file_path) . "' WHERE id = " . Wo_Secure($last_id);
                        $db_result = mysqli_query($sqlConnect, $db_update_query);
                        if ($db_result) {
                            error_log('Database updated successfully with the normal image path.');
                            $image_uploaded = true;
                        } else {
                            error_log('Failed to update the database with the new image path.');
                        }
                    } else {
                        error_log('Failed to upload the normal image to the server.');
                    }
                }

                // Success response if event was added and image uploaded
                $data = array(
                    'message' => $success_icon . $wo['lang']['event_added'],
                    'status' => 200,
                    'url' => Wo_SeoLink("index.php?link1=show-event&eid=" . $last_id)
                );
            } else {
                // Handle event insertion error
                $data = array(
                    'status' => 500,
                    'message' => $error_icon . 'Failed to insert event. Please try again.'
                );
            }
        } else {
            // Error response for form validation errors
            $data = array(
                'status' => 500,
                'message' => $error
            );
        }
    } else {
        // Handle invalid session
        $data = array(
            'status' => 403,
            'message' => $error_icon . 'Session check failed. Please refresh and try again.'
        );
    }

    // Return JSON response
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
