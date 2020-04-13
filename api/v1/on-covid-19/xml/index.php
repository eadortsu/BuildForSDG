<?php
require_once('../../../../src/estimator.php');


header("Access-Control-Allow-Origin: *");
header('Content-Type: application/xhtml+xml');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once('../../../../src/estimator.php');


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


$data = covid19ImpactEstimator($input);
// function defination to convert array to xml
function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_array($value) ) {
            if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
    }
}


// creating object of SimpleXMLElement
$xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

// function call to convert array to xml
array_to_xml($data,$xml_data);

print $xml_data->asXML();