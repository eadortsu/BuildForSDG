<?php

function covid19ImpactEstimator($data)
{

    if(is_array($data)) {
        $input = array_to_object($data);
    }elseif( is_string($data)){
        $input = json_decode($data);
    }else{
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
    $factor = intval($duration / 3);
    $impact->infectionsByRequestedTime = $impact->currentlyInfected * (pow(2, $factor));
    $severImpact->infectionsByRequestedTime = $severImpact->currentlyInfected * (pow(2, $factor));

    // Challenge 2
    // Calculate severeCasesByRequestedTime
    $impact->severeCasesByRequestedTime = $impact->infectionsByRequestedTime * (15 / 100);
    $severImpact->severeCasesByRequestedTime = $severImpact->infectionsByRequestedTime * (15 / 100);

    // Calculate hospitalBedsByRequestedTime
    $availableBed = $input->totalHospitalBeds * (35 / 100);
    $impact->hospitalBedsByRequestedTime = $availableBed - $impact->severeCasesByRequestedTime;
    $severImpact->hospitalBedsByRequestedTime = $availableBed - $severImpact->severeCasesByRequestedTime;

    // Challenge 3
    $impact->casesForICUByRequestedTime = $impact->infectionsByRequestedTime * (5 / 100);
    $severImpact->casesForICUByRequestedTime = $severImpact->infectionsByRequestedTime * (5 / 100);

    //  casesForVentilatorsByRequestedTime
    $impact->casesForVentilatorsByRequestedTime = $impact->infectionsByRequestedTime * (2 / 100);
    $severImpact->casesForVentilatorsByRequestedTime = $severImpact->infectionsByRequestedTime * (2 / 100);

    // dollarsInFlight
    $impact->dollarsInFlight = $impact->infectionsByRequestedTime * $input->region->avgDailyIncomeInUSD * $input->region->avgDailyIncomePopulation * $duration;
    $severImpact->dollarsInFlight = $severImpact->infectionsByRequestedTime * $input->region->avgDailyIncomeInUSD * $input->region->avgDailyIncomePopulation * $duration;

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
function array_to_object($array) {
    $obj = new stdClass;
    foreach($array as $k => $v) {
        if(strlen($k)) {
            if(is_array($v)) {
                $obj->{$k} = array_to_object($v); //RECURSION
            } else {
                $obj->{$k} = $v;
            }
        }
    }
    return $obj;
}
function object_to_array($array) {
    $obj = [];
    foreach($array as $k => $v) {
        if(strlen($k)) {
            if(is_object($v)) {
                $obj[$k] = object_to_array($v); //RECURSION
            } else {
                $obj[$k] = $v;
            }
        }
    }
    return $obj;
}

/*$data = '{"region":{"name":"Africa","avgAge":19.7,"avgDailyIncomeInUSD":6,"avgDailyIncomePopulation":0.86},"periodType":"days","timeToElapse":12,"reportedCases":3339,"population":44508591,"totalHospitalBeds":1545034}';

echo(covid19ImpactEstimator([
    "region" => [
        "name" => "Africa",
        "avgAge" => 19.7,
        "avgDailyIncomeInUSD" => 5,
        "avgDailyIncomePopulation" => 0.71
    ],
    "periodType" => "days",
    "timeToElapse" => 30,
    "reportedCases" => 674,
    "population" => 66622705,
    "totalHospitalBeds" => 1380614
]));


echo (covid19ImpactEstimator($data));*/