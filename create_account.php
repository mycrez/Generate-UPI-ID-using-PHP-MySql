<?php

$userphone = $_GET['phone'];

$client_id = "CF90xxxxxxxxxxxxxxxxH82";
$client_secret = "476516xxxxxxxxxxxxxxxxxxxxxxxxxxxx2ej949";
$auth_url = "https://cac-gamma.cashfree.com/cac/v1/authorize";
$createva_url = "https://cac-gamma.cashfree.com/cac/v1/createVA";

$response = array();
$ch = curl_init($auth_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Client-ID: ' . $client_id,
    'X-Client-Secret: ' . $client_secret
));
$apiResponse = curl_exec($ch);
curl_close($ch);
$jsonArrayResponse = json_decode($apiResponse, true);
$authToken = $jsonArrayResponse['data']['token'];

$va = createva($authToken, 1, $createva_url); //virtual bank acount
if ($va['status'] == "SUCCESS") {
    $response['accountno'] = $va['data']['accountNumber'];
    $response['ifsc'] = $va['data']['ifsc'];
    $response['status1'] = $va['status'];
    $response['message'] = $va['message'];
    $acc_messege = $va['message'];
    $acc_number =  $va['data']['accountNumber'];
    $ifsc_code = $va['data']['ifsc'];
    $vpa = createva($authToken, 2, $createva_url); //virtual upi id
    if ($vpa['status'] == "SUCCESS") {
        $response['upi_id'] = $vpa['data']['vpa'];
        $upi_id = $vpa['data']['vpa'];
        $response['status2'] = $vpa['status'];
        $response['message'] = $vpa['message'];
        $upi_messege = $vpa['message'];
        $activation_status = 1;

       // echo $acc_number;
       // echo $ifsc_code;
       // echo $upi_id;

    } else {
        $response['status1'] = $vpa['status'];
        $response['message'] = $vpa['message'];
        $upi_messege = $vpa['message'];
        $activation_status = 0;
    }
} else {
    $response['status1'] = $va['status'];
    $response['message'] = $va['message'];
    echo $acc_messege = $va['message'];
    exit;
}



function createva($token, $type, $url)
{
    $param1 = "";
    $param2 = "";
    if ($type == 1) {
        $param1 = "vAccountId";
        $param2 = $_GET['phone'];
    } else {
        $param1 = "virtualVpaId";
        $param2 = "N" . $_GET['phone'];
    }
    $postRequest = array(
        $param1 => $param2,
        'name' => $_GET['name'],
        'phone' => $_GET['phone'],
        'email' => $_GET['email'],
        'remitterAccount' => '409000000000',
        'remitterIfsc' => 'REMI0000000'
        // 'notifGroup' => 'TestGroup'
        // 'createMultiple' => '1'
    );
    $data = json_encode($postRequest, JSON_UNESCAPED_SLASHES);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $apiResponse = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($apiResponse, true);
    return $decoded;
}
//echo json_encode($response);


echo $acc_number;
echo $ifsc_code;
echo $upi_id;