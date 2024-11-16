
<?php
    
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    echo $login_url = $_POST['site_url']."auth";
    
    $post_url = $_POST['site_url']."new_post";

    $post_per_user = $_POST['post_per_user'];

    $lorem_text = [
        "Lorem ipsum dolor", "Training hard", "Achieving new goals", "Teamwork in action",
        "Pushing boundaries", "Striving for excellence", "Inspiring moves", "Passion for the game",
        "Building endurance", "Mastering skills", "Victory moments", "Focused on the goal",
        "Challenge accepted", "Unstoppable energy", "Chasing dreams", "Determined to win",
        "Strength and discipline", "Play with heart", "Rise to the challenge", "Unbreakable spirit"
    ];



    //     $post_url_token = $post_url.'?access_token=17521c139aabd8ca9eb926218798066ad430d267dea286c5ba0edf2cc66944d387434f27858844093b777b775721dfa8d36de2a320a03e53';



    //     $post_curl = curl_init($post_url_token);

    //     curl_setopt($post_curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($post_curl, CURLOPT_POST, true);
    //     curl_setopt($post_curl, CURLOPT_ENCODING, '');
    //     curl_setopt($post_curl, CURLOPT_MAXREDIRS, 10);
    //     curl_setopt($post_curl, CURLOPT_TIMEOUT, 0);
    //     curl_setopt($post_curl, CURLOPT_FOLLOWLOCATION, true);
    //     curl_setopt($post_curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    //     curl_setopt($post_curl, CURLOPT_CUSTOMREQUEST, 'POST');

    //     curl_setopt($post_curl, CURLOPT_POSTFIELDS, array(
    //         'server_key' => $server_key,
    //         'postText' => $lorem_text[array_rand($lorem_text)],
    //         'post_color' => 33,
    //         'postMusic' => '',
    //         'event_id' => 1,
    //         'postPrivacy' => 4,
    //         'postFile' => '',
    //         'postPhotos[0]' => new CURLFile('F:/xampp/htdocs/zomohublive/upload/photo1.png'),
    //         'postPhotos[1]' => new CURLFile('F:/xampp/htdocs/zomohublive/upload/photo2.png'),
    //         'postPhotos[2]' => new CURLFile('F:/xampp/htdocs/zomohublive/upload/photo3.png'),
    //         'postVideo' => '',
    //         'device_type' => 'windows')
    //     );

    //     $response = curl_exec($post_curl);

    //     curl_close($post_curl);

    //     echo "<br>";


   

    // exit();



    if (isset($_FILES['json_data_file']) && $_FILES['json_data_file']['error'] === UPLOAD_ERR_OK) {
  
        $tempFilePath = $_FILES['json_data_file']['tmp_name'];

      
        
        $jsonData = file_get_contents($tempFilePath);
        
        $data = json_decode($jsonData, true);
   

        $loop_count = $_POST['record_add_count'];

        $user_id_start_with = $_POST['user_id_start_with'];

        

        for ($j = 0; $j < count($data); $j++) 
        {
            if ($j >= $loop_count) {

                break;
            }
    
            $code = md5(rand(1111, 9999) . time());
            $account_data = array(
                'first_name'    => $data[$j]['first_name'],
                'last_name'     => $data[$j]['last_name'],
                'email'         => Wo_Secure('zomo_user_'.$user_id_start_with.'@gmail.com', 0),
                'username'      => Wo_Secure('zomo_user_'.$user_id_start_with, 0),
                'password'      => 'zomouserpass123',
                'email_code'    => $code,
                'src'           => 'Phone',
                'timezone'      => 'UTC',
                'gender'        => 'male',
                'active'        => '1',
                'lastseen'      => time(),
                'avatar'        => 'upload/photos/f-avatar.jpg',
                // 'active' => Wo_Secure($activate)
            );
    
            Wo_RegisterUser($account_data);

            $user_id_start_with++;

        
            echo "======================================================================<br>User Count :".$j.'<br>======================================================================<br>';
    
            echo "Name: ". $account_data['first_name'] ." ". $account_data['last_name'] ."<br>";
            echo "Email : ". $account_data['email'] ."<br>";
            echo "Username : ". $account_data['username'] ."<br>";
            echo "Password : ". $account_data['password'] ."<br>";
            echo "Email Code : ". $account_data['email_code'] ."<br>";
            echo "SRC : ". $account_data['src'] ."<br>";
            echo "Timezone : ". $account_data['timezone'] ."<br>";
            echo "Gender : ". $account_data['gender'] ."<br>";
            echo "Active : ". $account_data['gender'] ."<br>";
            echo "<br>";

            $login_data = [
                'server_key' => $server_key,
                'username' => $account_data['username'],
                'password' => $account_data['password'],
                'device_type' => 'windows'
            ];


            $ch = curl_init($login_url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($login_data));

            $response = curl_exec($ch);

            $login_response_data = json_decode($response, true);

            curl_close($ch);

            $post_url_token = $post_url.'?access_token='.$login_response_data['access_token'];
    
            
            for ($post_loop = 0; $post_loop < $post_per_user; $post_loop++) 
            {

                $numbers = range(1, 10);

                shuffle($numbers);

                $var1 = $numbers[0];
                $var2 = $numbers[1];
                $var3 = $numbers[2];

                $post_curl = curl_init($post_url_token);

                curl_setopt($post_curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($post_curl, CURLOPT_POST, true);
                curl_setopt($post_curl, CURLOPT_ENCODING, '');
                curl_setopt($post_curl, CURLOPT_MAXREDIRS, 10);
                curl_setopt($post_curl, CURLOPT_TIMEOUT, 0);
                curl_setopt($post_curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($post_curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($post_curl, CURLOPT_CUSTOMREQUEST, 'POST');

                curl_setopt($post_curl, CURLOPT_POSTFIELDS, array(
                    'server_key' => $server_key,
                    'postText' => $lorem_text[array_rand($lorem_text)],
                    'post_color' => 33,
                    'postMusic' => '',
                    'event_id' => 1,
                    'postPrivacy' => 4,
                    'postFile' => '',
                    'postPhotos[0]' => new CURLFile('F:/xampp/htdocs/zomohublive/upload/photo'.$var1.'.png'),
                    'postPhotos[1]' => new CURLFile('F:/xampp/htdocs/zomohublive/upload/photo'.$var2.'.png'),
                    'postPhotos[2]' => new CURLFile('F:/xampp/htdocs/zomohublive/upload/photo'.$var3.'.png'),
                    'postVideo' => '',
                    'device_type' => 'windows')
                );

                $response = curl_exec($post_curl);

                curl_close($post_curl);

                echo "<pre>";
                    print_r($response);
                echo "</pre>";

 
            }

    
        }

    
        if (json_last_error() === JSON_ERROR_NONE) {

            // echo "Uploaded JSON data:\n";
            // print_r($data); // or process $data as needed
        } else {
            echo "Error decoding JSON: " . json_last_error_msg();
        }
    } else {
        echo "File upload failed or no file uploaded.";
    }



 






?>