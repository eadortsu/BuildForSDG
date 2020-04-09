<?php

function covid19ImpactEstimator($data)
{
    $input = (object)$data;
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
    return json_encode($output);
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


$data = '{"region":{"name":"Africa","avgAge":19.7,"avgDailyIncomeInUSD":6,"avgDailyIncomePopulation":0.86},"periodType":"days","timeToElapse":12,"reportedCases":3339,"population":44508591,"totalHospitalBeds":1545034}';

echo(covid19ImpactEstimator($data));