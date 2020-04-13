<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once('../../../src/estimator.php');


$input = (array)json_decode(file_get_contents('php://input'), TRUE);
/*if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode([
        'error' => 'Method Not Allowed'
    ]);
    exit();
}*/

if (!validatePerson($input)) {
    return unprocessableEntityResponse();
}

header('HTTP/1.1 200 Ok');
echo json_encode(covid19ImpactEstimator($input));
