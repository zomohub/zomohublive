



<?php


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/zomohublive/api/new_post?access_token=782a70cec09c2235925082cb03c27d30949a1832448617e0f87108650f82c0473d5ad5659203699047a5feca4ce02883a5643e295c7ce6cd',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('server_key' => '7c06ec12d931b721ecf2c66fb556b169','postText' => 'https://www.linkedin.com/ ddddddddddddddddd','post_color' => '33','event_id' => '1','postPrivacy' => '4','postPhotos[0]'=> new CURLFILE('F:/xampp/htdocs/zomohublive/upload/image-1.jpg'),'postPhotos[1]'=> new CURLFILE('F:/xampp/htdocs/zomohublive/upload/image-2.jpg')),
//   CURLOPT_HTTPHEADER => array(
//     'Cookie: _us=1731755150; ad-con=%7B%26quot%3Bdate%26quot%3B%3A%26quot%3B2024-11-15%26quot%3B%2C%26quot%3Bads%26quot%3B%3A%5B%5D%7D; PHPSESSID=2lbo0t6oa8psj3cujlu7a9gjqs; mode=day'
//   ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

if (curl_errno($curl)) {
    echo 'cURL error: ' . curl_error($curl);
} else {
    echo 'Response: ' . $response;
}



 






?>