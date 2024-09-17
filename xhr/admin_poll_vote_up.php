<?php 
if ($f == 'admin_poll_vote_up') {

    if (!empty($_GET['id']) && Wo_CheckMainSession($hash_id) === true) {
        $poll_id = Wo_GetPollIDFromAnsID($_GET['id']);
        $user_id = $wo['user']['user_id'];
        $poll_ans_id = $_GET['id'];
        if (Wo_IsAdminPollVoted($poll_id, $wo['user']['user_id'])) {
            $data = array(
                'status' => 400,
                'text' => $wo['lang']['you_have_already_voted']
            );

            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        } else {
            $vote = Wo_VoteUp($_GET['id'], $wo['user']['user_id']);

            $fields    = '(`poll_id`, `poll_ans_id`, `user_id`)';
            $query_one = "INSERT INTO " . T_POLLS_ANS_COUNT . " {$fields} VALUES ('{$poll_id}', '{$poll_ans_id}', '{$user_id}')";
            $sql       = mysqli_query($sqlConnect, $query_one);

            if ($sql) {
                $data = array(
                    'status' => 200,
                    'text' => $wo['lang']['you_have_voted']
                );
            }

        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}




