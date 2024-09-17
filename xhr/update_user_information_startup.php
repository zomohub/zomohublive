<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if ($f == 'update_user_information_startup' && Wo_CheckSession($hash_id) === true) {
    if (isset($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0) {
        $Userdata = Wo_UserData($_POST['user_id']);
        if (!empty($Userdata['user_id'])) {
            $age_data = '00-00-0000';
            if (!empty($_POST['birthday']) && preg_match('@^\s*(3[01]|[12][0-9]|0?[1-9])\-(1[012]|0?[1-9])\-((?:19|20)\d{2})\s*$@', $_POST['birthday'])) {
                $newDate  = date("Y-m-d", strtotime($_POST['birthday']));
                $age_data = $newDate;
            } else {
                if (!empty($_POST['age_year']) || !empty($_POST['age_day']) || !empty($_POST['age_month'])) {
                    if (empty($_POST['age_year']) || empty($_POST['age_day']) || empty($_POST['age_month'])) {
                        $errors[] = $error_icon . $wo['lang']['please_choose_correct_date'];
                    } else {
                        $age_data = $_POST['age_year'] . '-' . $_POST['age_month'] . '-' . $_POST['age_day'];
                    }
                }
            }
            $Update_data = array(
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'country_id' => $_POST['country'],
                'about' => $_POST['about'],
                'language' => 'english',
                // Zomo Customization
                'user_profession' => $_POST['user_profession'],
                'user_interest' => $_POST['interest'],
                'user_favourite_football_players'=> $_POST['favourite_football_players'],
                'user_football_experience'=> $_POST['football_experience'],
                'user_injury_history'=> $_POST['injury_history'],
                'user_gears_supplier'=> $_POST['gears_supplier'],
                'user_playing_foot'=> $_POST['playing_foot'],
                'user_player_height'=> $_POST['player_height'],
                'user_skill_level'=> $_POST['skill_level'],
                'user_internation_team'=> $_POST['player_internation_team'],
                'user_position'=> $_POST['player_position'],
                'user_current_club'=> $_POST['player_current_club'],
                'user_contract_until'=> $_POST['player_contract_until'],
                'user_transfer_history_season'=> $_POST['transfer_history_season'],
                'user_transfer_history_season_date'=> $_POST['transfer_history_season_date'],
                'user_transfer_history_left_date'=> $_POST['transfer_history_left_date'],
                'user_transfer_history_join_date'=> $_POST['transfer_history_join_date'],
                'user_current_mark_value'=> $_POST['player_current_mark_value'],
                'user_agent'=> $_POST['player_agent'],
                'user_boots_size'=> $_POST['player_boots_size'],
                'user_jersey_size'=> $_POST['player_jersey_size'],
                'user_language' => $_POST['language'],
                'birthday' => $age_data,
                'start_up_info' => 1
            );
            if (Wo_UpdateUserData($_POST['user_id'], $Update_data)) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
