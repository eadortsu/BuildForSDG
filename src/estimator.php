<?php

function covid19ImpactEstimator($data)
{

    if (is_array($data)) {
        $input = array_to_object($data);
    } elseif (is_string($data)) {
        $input = json_decode($data);
    } else {
        $input = $data;
    }
    $impact = new stdClass();
    $severImpact = new stdClass();
    $output = new stdClass();


    // Calculate impact.currentlyInfected
    $impact->currentlyInfected = $input->reportedCases * 10;

    // Calculate severImpact.currentlyInfected
    $severImpact->currentlyInfected = $input->reportedCases * 50;

    // Calculate infectionsByRequestedTime
    $duration = normaliseDuration($input->periodType, $input->timeToElapse);
    $factor = intval(($duration / 3));
    $impact->infectionsByRequestedTime = intval($impact->currentlyInfected * (pow(2, $factor)));
    $severImpact->infectionsByRequestedTime = intval($severImpact->currentlyInfected * (pow(2, $factor)));

    // Challenge 2
    // Calculate severeCasesByRequestedTime
    $impact->severeCasesByRequestedTime = intval($impact->infectionsByRequestedTime * (15 / 100));
    $severImpact->severeCasesByRequestedTime = intval($severImpact->infectionsByRequestedTime * (15 / 100));

    // Calculate hospitalBedsByRequestedTime
    $availableBed = $input->totalHospitalBeds * (35 / 100);
    $impact->hospitalBedsByRequestedTime = intval($availableBed - $impact->severeCasesByRequestedTime);
    $severImpact->hospitalBedsByRequestedTime = intval($availableBed - $severImpact->severeCasesByRequestedTime);

    // Challenge 3
    $impact->casesForICUByRequestedTime = intval($impact->infectionsByRequestedTime * (5 / 100));
    $severImpact->casesForICUByRequestedTime = intval($severImpact->infectionsByRequestedTime * (5 / 100));

    //  casesForVentilatorsByRequestedTime
    $impact->casesForVentilatorsByRequestedTime = intval($impact->infectionsByRequestedTime * (2 / 100));
    $severImpact->casesForVentilatorsByRequestedTime = intval($severImpact->infectionsByRequestedTime * (2 / 100));

    // dollarsInFlight
    $impact->dollarsInFlight = intval(($impact->infectionsByRequestedTime * $input->region->avgDailyIncomePopulation * $input->region->avgDailyIncomeInUSD) / $duration);
    $severImpact->dollarsInFlight = intval(($severImpact->infectionsByRequestedTime * $input->region->avgDailyIncomePopulation * $input->region->avgDailyIncomeInUSD) / $duration);

    $output->data = $input; // the input data you got
    $output->impact = $impact; // your best case estimation
    $output->severeImpact = $severImpact; // your severe case estimation
    return object_to_array($output);
}

function normaliseDuration($periodType, $timeToElapse)
{
    $days = 0;

    switch ($periodType) {
        case "days":
            $days = $timeToElapse;
            break;
        case "weeks":
            $days = 7 * $timeToElapse;
            break;
        case "months":
            $days = 30 * $timeToElapse;
            break;
        case "years":
            $days = 365 * $timeToElapse;
            break;
        default:
            break;
    }

    return $days;
}

function array_to_object($array)
{
    $obj = new stdClass;
    foreach ($array as $k => $v) {
        if (strlen($k)) {
            if (is_array($v)) {
                $obj->{$k} = array_to_object($v); //RECURSION
            } else {
                $obj->{$k} = $v;
            }
        }
    }
    return $obj;
}

function object_to_array($array)
{
    $obj = [];
    foreach ($array as $k => $v) {
        if (strlen($k)) {
            if (is_object($v)) {
                $obj[$k] = object_to_array($v); //RECURSION
            } else {
                $obj[$k] = $v;
            }
        }
    }
    return $obj;
}

function unprocessableEntityResponse()
{
   header( 'HTTP/1.1 422 Unprocessable Entity');
   echo json_encode([
        'error' => 'Invalid input'
    ]);

}

function notFoundResponse()
{
    $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
    $response['body'] = null;
    return $response;
}

function validatePerson($input)
{
    if (!isset($input['region'])) {
        return false;
    }
    if (!isset($input['region']['name'])) {
        return false;
    }
    if (!isset($input['region']['avgDailyIncomeInUSD'])) {
        return false;
    }

     if (!isset($input['region']['avgDailyIncomePopulation'])) {
        return false;
    }

    if (!isset($input['periodType'])) {
        return false;
    }
    if (!isset($input['timeToElapse'])) {
        return false;
    }
    if (!isset($input['reportedCases'])) {
        return false;
    }
    if (!isset($input['population'])) {
        return false;
    }
    if (!isset($input['totalHospitalBeds'])) {
        return false;
    }
    return true;
}



//
//$data = '{"region":{"name":"Africa","avgAge":19.7,"avgDailyIncomeInUSD":3,"avgDailyIncomePopulation":0.63},"periodType":"days","timeToElapse":18,"reportedCases":200,"population":4199209,"totalHospitalBeds":234434}';
//
///*print_r(covid19ImpactEstimator([
//    "region" => [
//        "name" => "Africa",
//        "avgAge" => 19.7,
//        "avgDailyIncomeInUSD" => 5,
//        "avgDailyIncomePopulation" => 0.71
//    ],
//    "periodType" => "days",
//    "timeToElapse" => 30,
//    "reportedCases" => 674,
//    "population" => 66622705,
//    "totalHospitalBeds" => 1380614
//]));*/
//
//
//print_r(covid19ImpactEstimator($data));