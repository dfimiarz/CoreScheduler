<?php

include_once __DIR__ . '/../view/JSONMessageSender.php';
include_once __DIR__ . '/../model/FacilityDataHandler.php';

use ccny\scidiv\cores\view\JSONMessageSender as JSONMessageSender;
use ccny\scidiv\cores\model\FacilityDataHandler as FacilityDataHandler;

$msg_sender = new JSONMessageSender();

$facility_id = null;

if (isset($_POST['facility_id']))
    $facility_id = $_POST['facility_id'];

try {
    $data_handler = new FacilityDataHandler();
    $data = $data_handler->getResources($facility_id);
} catch (\Exception $e) {
    $err_msg = "Operation failed: Error code " . $e->getCode();

    //Code 0 means that this is none-system error.
    //In this case we should be able to display the message text itself.
    if ($e->getCode() == 0) {
        $err_msg = "Operation failed: " . $e->getMessage();
    }

    $msg_sender->onError(null, $err_msg);
}

$msg_sender->onResult($data, null);
