<?php 
if ($f == "load-more-events") {
    $html = "";
    $limit = 10; // Number of events to load per request
    $data = array(
        'status' => 404,
        "html" => $wo['lang']['no_result'],
        "is_last_page" => false
    );

    if (isset($_GET['offset']) && is_numeric($_GET['offset'])) {
        $offset = Wo_Secure($_GET['offset']);
        
        if ($s == "upcomming") {
            $events = Wo_GetEvents(array(
                "offset" => $offset,
                "limit" => $limit
            ));
            if (count($events) > 0) {
                foreach ($events as $wo['event']) {
                    $html .= Wo_LoadPage('events/includes/events-list');
                }
                $data = array(
                    'status' => 200,
                    "html" => $html,
                    "is_last_page" => count($events) < $limit // Set true if fewer than limit events are returned
                );
            }
        } else if ($s == "going") {
            $events = Wo_GetGoingEvents($offset, $limit);
            if (count($events) > 0) {
                foreach ($events as $wo['event']) {
                    $html .= Wo_LoadPage('events/includes/events-going-list');
                }
                $data = array(
                    'status' => 200,
                    "html" => $html,
                    "is_last_page" => count($events) < $limit
                );
            }
        } else if ($s == "invited") {
            $events = Wo_GetInvitedEvents($offset, $limit);
            if (count($events) > 0) {
                foreach ($events as $wo['event']) {
                    $html .= Wo_LoadPage('events/includes/events-invited-list');
                }
                $data = array(
                    'status' => 200,
                    "html" => $html,
                    "is_last_page" => count($events) < $limit
                );
            }
        } else if ($s == "interested") {
            $events = Wo_GetInterestedEvents($offset, $limit);
            if (count($events) > 0) {
                foreach ($events as $wo['event']) {
                    $html .= Wo_LoadPage('events/includes/events-interested-list');
                }
                $data = array(
                    'status' => 200,
                    "html" => $html,
                    "is_last_page" => count($events) < $limit
                );
            }
        } else if ($s == "past") {
            $events = Wo_GetPastEvents($offset, $limit);
            if (count($events) > 0) {
                foreach ($events as $wo['event']) {
                    $html .= Wo_LoadPage('events/includes/events-list');
                }
                $data = array(
                    'status' => 200,
                    "html" => $html,
                    "is_last_page" => count($events) < $limit
                );
            }
        }
    }

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
