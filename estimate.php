<!DOCTYPE html>
<html>
    <head>
        <title>covid 19 estimator</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    </head>
    <body>
    <?php
require_once ('src/estimator.php');
if(isset($_POST['submit']))
{
    $data = array(
        "reportedCases"=>$_POST['reportedCases'],
        "population"=>$_POST['population'],
        "timeToElapse"=>$_POST['timeToElapse'],
        "totalHospitalBeds"=>$_POST['totalHospitalBeds'],
        "periodType"=>$_POST['periodType']

    );
    $outputData = covid19ImpactEstimator($data);
    ?>
    <div class="row">
    <div class="card col-md-6">
            <div class="card-header">
            <h2>Impact</h2>
            </div>
            <div class="card-body">
            <ul>
            <li class="btn-outline-secondary">Currently Infected: <?=$outputData['impact']['currentlyInfected']?></li>
            <li class="btn-outline-secondary">Infections By Requested Time: <?=$outputData['impact']['infectionsByRequestedTime']?></li>
            <li class="btn-outline-secondary">Severe Cases By Requested Time: <?=$outputData['impact']['severeCasesByRequestedTime']?></li>
            <li class="btn-outline-secondary">Hospital Beds By Requested Time: <?=$outputData['impact']['hospitalBedsByRequestedTime']?></li>
            <li class="btn-outline-secondary">Cases For ICU by requested Time: <?=$outputData['impact']['casesForICUByRequestedTime']?></li>
            <li class="btn-outline-secondary">Cases For ventilators By Requested Time: <?=$outputData['impact']['casesForVentilatorsByRequestedTime']?></li>
            <li class="btn-outline-secondary">Dollars in Flight: <?=$outputData['impact']['dollarsInFlight']?></li>
            </ul>
            </div>
    </div>
    <div class="card col-md-6">
            <div class="card-header">
            <h2>Severe Impact</h2>
            </div>
            <div class="card-body">
            <ul>
            <li class="btn-outline-secondary">Currently Infected: <?=$outputData['severeImpact']['currentlyInfected']?></li>
            <li class="btn-outline-secondary">Infections By Requested Time: <?=$outputData['severeImpact']['infectionsByRequestedTime']?></li>
            <li class="btn-outline-secondary">Severe Cases By Requested Time: <?=$outputData['severeImpact']['severeCasesByRequestedTime']?></li>
            <li class="btn-outline-secondary">Hospital Beds By Requested Time: <?=$outputData['severeImpact']['hospitalBedsByRequestedTime']?></li>
            <li class="btn-outline-secondary">Cases For ICU by requested Time: <?=$outputData['severeImpact']['casesForICUByRequestedTime']?></li>
            <li class="btn-outline-secondary">Cases For ventilators By Requested Time: <?=$outputData['severeImpact']['casesForVentilatorsByRequestedTime']?></li>
            <li class="btn-outline-secondary">Dollars in Flight: <?=$outputData['severeImpact']['dollarsInFlight']?></li>
            </ul>
            </div>
    </div>
        
    </div>

    <?php
}

?>
    </body>
</html>