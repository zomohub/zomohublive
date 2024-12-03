<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2018 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
$response_data = array(
    'api_status' => 400,
);

ini_set('display_errors', 1); // Enable error display
ini_set('display_startup_errors', 1); // Show startup errors
error_reporting(E_ALL); // Report all errors


if (empty($_POST['user_id'])) {
    $error_code    = 3;
    $error_message = 'user_id (POST) is missing';
}
if (empty($_POST['fetch'])) {
    $error_code    = 3;
    $error_message = 'fetch (POST) is missing';
}
if (empty($error_code)) {
    $recipient_id   = Wo_Secure($_POST['user_id']);
    $logged_user_id  = $wo['user']['user_id'];
    $recipient_data = Wo_UserData($recipient_id);
    if (empty($recipient_data)) {
        $error_code    = 6;
        $error_message = 'Recipient user not found';
    } else {
    	$can_ = 0;
		$wo['nodejs_send_notification'] = false;
		if ($wo['loggedin'] == true && $wo['config']['profileVisit'] == 1 && !empty($_POST['send_notify']) && $_POST['send_notify'] == 1) {
		    if ($recipient_data['user_id'] != $wo['user']['user_id'] && $wo['user']['visit_privacy'] == 0) {
		        if ($wo['config']['pro'] == 1) {
		            if ($recipient_data['is_pro'] == 1 && in_array($recipient_data['pro_type'], array_keys($wo['pro_packages'])) && $wo['pro_packages'][$recipient_data['pro_type']]['profile_visitors'] == 1) {
		                $can_ = 1;
		            }
		        } else {
		            $can_ = 1;
		        }
		        if ($recipient_data['visit_privacy'] == 0 && $can_ == 1) {
		            $notification_data_array = array(
		                'recipient_id' => $recipient_data['user_id'],
		                'type' => 'visited_profile',
		                'url' => 'index.php?link1=timeline&u=' . $wo['user']['username']
		            );
		            $wo['nodejs_send_notification'] = true;
		            Wo_RegisterNotification($notification_data_array);
		        }
		    }
		}

    	$response_data = array('api_status' => 200);
		$recipient_data_ = Wo_UpdateUserDetails($recipient_data, true, true, true);
        if (is_array($recipient_data_)) {
            $recipient_data = $recipient_data_;
        }
        foreach ($non_allowed as $key => $value) {
           unset($recipient_data[$value]);
        }
	    $fetch = explode(',', $_POST['fetch']);
		$data = array();
		foreach ($fetch as $key => $value) {
			$data[$value] = $value;
		}
		if (!empty($data['user_data'])) {
			$recipient_data['is_following'] = 0;
	        $recipient_data['can_follow'] = 0;
	        if (Wo_IsFollowing($recipient_id, $logged_user_id)) {
	            $recipient_data['is_following'] = 1;
	            $recipient_data['can_follow'] = 1;
	        } else {
	            if (Wo_IsFollowRequested($recipient_id, $logged_user_id)) {
	                $recipient_data['is_following'] = 2;
	                $recipient_data['can_follow'] = 1;
	            } else {
	                if ($recipient_data['follow_privacy'] == 1) {
	                    if (Wo_IsFollowing($logged_user_id, $recipient_id)) {
	                        $recipient_data['is_following'] = 0;
	                        $recipient_data['can_follow'] = 1;
	                    }
	                } else if ($recipient_data['follow_privacy'] == 0) {
	                    $recipient_data['can_follow'] = 1;
	                }
	            }
	        }
	        $recipient_data['is_following_me'] = (Wo_IsFollowing( $wo['user']['user_id'], $recipient_data['user_id'])) ? 1 : 0;
	        $recipient_data['gender_text']        = ($recipient_data['gender'] == 'male') ? $wo['lang']['male'] : $wo['lang']['female'];
        	$recipient_data['lastseen_time_text'] = Wo_Time_Elapsed_String($recipient_data['lastseen']);
        	$recipient_data['is_blocked']         = Wo_IsBlocked($recipient_data['user_id']);
        	//$recipient_data['notification_settings'] = (Array)json_decode(html_entity_decode($recipient_data['notification_settings']));
        	$response_data['user_data'] = $recipient_data;
		}

		if (!empty($data['followers'])) {
			$followers_latest = array();
			$followers = Wo_GetFollowers($recipient_data['user_id'], 'profile', 50);
			foreach ($followers as $key => $follower) {
				$follower['is_following'] = (Wo_IsFollowing($follower['user_id'], $wo['user']['user_id'])) ? 1 : 0;
				//$follower['notification_settings'] = (Array)json_decode(html_entity_decode($follower['notification_settings']));
				$followers_latest[] = $follower;
			}
			$response_data['followers'] = $followers_latest;
		}
		if (!empty($data['following'])) {
			$followings_latest = array();
			$followings = Wo_GetFollowing($recipient_data['user_id'], 'profile', 50);
			foreach ($followings as $key => $following) {
				$following['is_following'] = (Wo_IsFollowing($following['user_id'], $wo['user']['user_id'])) ? 1 : 0;
				//$following['notification_settings'] = (Array)json_decode(html_entity_decode($following['notification_settings']));
				$followings_latest[] = $following;
			}
			$response_data['following'] = $followings_latest;
		}
		if (!empty($data['liked_pages'])) {
			$response_data['liked_pages'] = Wo_GetLikes($recipient_data['user_id'], 'profile', 50);
			foreach ($response_data['liked_pages'] as $key => $value) {
                $response_data['liked_pages'][$key]['is_liked'] = Wo_IsPageLiked($value['page_id'], $wo['user']['id']);
            }
		}
		if (!empty($data['joined_groups'])) {
			$response_data['joined_groups'] = Wo_GetUsersGroups($recipient_data['user_id'], 50);
			foreach ($response_data['joined_groups'] as $key => $value) {
                $response_data['joined_groups'][$key]['is_joined'] = Wo_IsGroupJoined($value['group_id'], $wo['user']['id']);
            }
		}
		if (!empty($data['family'])) {
			$family = Wo_GetFamaly($recipient_data['user_id'],false,1);
			foreach ($family as $key => $value) {
				foreach ($non_allowed as $key1 => $value) {
			       unset($family[$key]['user_data'][$value]);
			    }
			}
			$response_data['family'] = $family;
		}
		
		if (!empty($data['images'])) {
			$images = Wo_GetUserImages($recipient_data['user_id'], 50); // Fetch images
			$response_data['images'] = array();

			foreach ($images as $image) {
				$response_data['images'][] = array(
					'id' => $image['id'],
					'post_id' => $image['post_id'],
					'postFile' => $image['postFile'], // Path to the image file
					'time' => $image['time'], // Timestamp of the post
					'likes' => Wo_CountPostLikes($image['id']), // Total likes (optional)
					'comments' => Wo_CountPostComments($image['id']), // Total comments (optional)
					'is_liked' => Wo_IsPostLiked($image['id'], $wo['user']['user_id']) // Whether the logged-in user liked the image
				);
			}
		}


		if (!empty($data['videos'])) {
				$videos = Wo_GetUserVideos($recipient_data['user_id'], 50); // Fetch videos
				$response_data['videos'] = array();

				foreach ($videos as $video) {
					$response_data['videos'][] = array(
						'id' => $video['id'],
						'post_id' => $video['post_id'],
						'postFile' => $video['postFile'], // Path to the video file
						'videoTitle' => $video['videoTitle'], // Title of the video
						'time' => $video['time'], // Timestamp of the post
						'likes' => Wo_CountPostLikes($video['id']), // Total likes (optional)
						'comments' => Wo_CountPostComments($video['id']), // Total comments (optional)
						'is_liked' => Wo_IsPostLiked($video['id'], $wo['user']['user_id']) // Whether the logged-in user liked the video
					);
				}
			}


			if (!empty($data['reels'])) {
				$reels = Wo_GetUserReels($recipient_data['user_id'], 50); // Fetch the user's reels
				$response_data['reels'] = array();

				foreach ($reels as $reel) {
					$response_data['reels'][] = array(
						'id' => $reel['id'],
						'post_id' => $reel['post_id'],
						'postFile' => $reel['postFile'], // Path to the reel file
						'videoTitle' => $reel['videoTitle'], // Title of the reel
						'time' => $reel['time'], // Timestamp of the post
						'likes' => Wo_CountPostLikes($reel['id']), // Total likes (optional)
						'comments' => Wo_CountPostComments($reel['id']), // Total comments (optional)
						'is_liked' => Wo_IsPostLiked($reel['id'], $wo['user']['user_id']) // Whether the logged-in user liked the reel
					);
				}
			}


    }
}