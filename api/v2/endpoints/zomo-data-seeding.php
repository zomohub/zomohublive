
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
        
        
            gc_collect_cycles();

        }



    } else {
        echo "File upload failed or no file uploaded.";
    }



?>