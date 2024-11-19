
<?php

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    if ($_SERVER['SERVER_NAME'] == 'localhost') {
        
        $filePath = 'F:/xampp/htdocs/zomohublive/data-seeing-images/data-seeding.json';
        $_POST['server_key']='7c06ec12d931b721ecf2c66fb556b169';
    
    } 

    else 
    {
        $filePath = '/var/www/html/data-seeing-images/data-seeding.json';
        $_POST['server_key']='5df02baa4b9705bfa29589f69fc064a0';
    }




    $server_key = $_POST['server_key'];

    $login_url = $_GET['site_url']."auth";
    
    $post_url = $_GET['site_url']."new_post";

    $post_per_user = $_GET['post_per_user'];

    $get_post_per_user = $_GET['record_add_count'];

    $user_id_start_with = $_GET['user_id_start_with'];

    $lorem_text = [
        "Lorem ipsum dolor", "Training hard", "Achieving new goals", "Teamwork in action",
        "Pushing boundaries", "Striving for excellence", "Inspiring moves", "Passion for the game",
        "Building endurance", "Mastering skills", "Victory moments", "Focused on the goal",
        "Challenge accepted", "Unstoppable energy", "Chasing dreams", "Determined to win",
        "Strength and discipline", "Play with heart", "Rise to the challenge", "Unbreakable spirit"
    ];


    if (file_exists($filePath)) {


        // $tempFilePath = $_FILES['json_data_file']['tmp_name'];
  
        $jsonData = file_get_contents($filePath);

        // print_r($jsonData);
        
        $data = json_decode($jsonData, true);

        $loop_count = $get_post_per_user;

        $user_id_start_with = $user_id_start_with;


        for ($j = 0; $j < $loop_count; $j++) 
        {
            // if ($j >= $loop_count) {

            //     break;
            // }
            $numbers = range(1, 150);

            shuffle($numbers);

    
            $code = md5(rand(1111, 9999) . time());
            $account_data = array(
                'first_name'    => $data[$numbers[0]]['first_name'],
                'last_name'     => $data[$numbers[0]]['last_name'],
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

            echo "----";
            echo $j;
            echo "----";
        
            // echo "======================================================================<br>User Count :".$j.'<br>======================================================================<br>';
    
            // echo "Name: ". $account_data['first_name'] ." ". $account_data['last_name'] ."<br>";
            // echo "Email : ". $account_data['email'] ."<br>";
            // echo "Username : ". $account_data['username'] ."<br>";
            // echo "Password : ". $account_data['password'] ."<br>";
            // echo "Email Code : ". $account_data['email_code'] ."<br>";
            // echo "SRC : ". $account_data['src'] ."<br>";
            // echo "Timezone : ". $account_data['timezone'] ."<br>";
            // echo "Gender : ". $account_data['gender'] ."<br>";
            // echo "Active : ". $account_data['gender'] ."<br>";
            // echo "<br>";

            $login_data = [
                'server_key' => $server_key,
                'username' => $account_data['username'],
                'password' => $account_data['password'],
                'device_type' => 'windows'
            ];


            $login_curl = curl_init($login_url);

            curl_setopt($login_curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($login_curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($login_curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($login_curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($login_curl, CURLOPT_VERBOSE, true);
            curl_setopt($login_curl, CURLOPT_POST, true);
            curl_setopt($login_curl, CURLOPT_POSTFIELDS, http_build_query($login_data));

            $login_response = curl_exec($login_curl);

            $login_response_data = json_decode($login_response, true);


            if (curl_errno($login_curl)) {
                echo 'Error: ' . curl_error($login_curl);
            }

            // print_r($login_response_data);

            curl_close($login_curl);

            if($login_response_data['access_token'])
            {

                echo "----";
                echo $post_url_token = $post_url.'?access_token='.$login_response_data['access_token'];
                echo "----";

                
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
                    curl_setopt($post_curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($post_curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($post_curl, CURLOPT_VERBOSE, true);
                    curl_setopt($post_curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                    curl_setopt($post_curl, CURLOPT_CUSTOMREQUEST, 'POST');


                    if ($_SERVER['SERVER_NAME'] == 'localhost') {

                        curl_setopt($post_curl, CURLOPT_POSTFIELDS, array(
                            'server_key' => $server_key,
                            'postText' => $lorem_text[array_rand($lorem_text)],
                            'postPhotos[0]' => new CURLFile('F:/xampp/htdocs/zomohublive/data-seeing-images/photo'.$var1.'.png'),
                            // 'postPhotos[0]' => new CURLFile('/var/www/html/data-seeing-images/photo'.$var1.'.png'),
                            )
                        );


                    }
                    else
                    {
                        curl_setopt($post_curl, CURLOPT_POSTFIELDS, array(
                            'server_key' => $server_key,
                            'postText' => $lorem_text[array_rand($lorem_text)],
                            // 'postPhotos[0]' => new CURLFile('F:/xampp/htdocs/zomohublive/data-seeing-images/photo'.$var1.'.png'),
                            'postPhotos[0]' => new CURLFile('/var/www/html/data-seeing-images/photo'.$var1.'.png'),
                            )
                        );
                    }



                    $response = curl_exec($post_curl);

                    if (curl_errno($post_curl)) {
                        echo 'Error: ' . curl_error($post_curl);
                    }

                    curl_close($post_curl);
    
                }

            }

            unset($login_response, $response, $post_curl, $login_curl);
            gc_collect_cycles();

        }



    } else {
        echo "File upload failed or no file uploaded.";
    }



?>