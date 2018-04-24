<?php
/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 19.04.2018
 * Time: 21:47
 */
declare(strict_types = 1);
include_once 'vendor/autoload.php';
include_once 'Data.php';
include_once 'RegressionAnalysis.php';

use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;

$dataArray = include_once 'loadData.php';
$data = new Data($dataArray);
$analysis = new RegressionAnalysis($data);
$analysis->regressionAnalysis();
list($matrixY, $matrixYModel) = $analysis->checkModel($data->getMatrixX(), $data->getBettaModel());
//var_dump($matrixY);
//var_dump($matrixYModel);
$averageError = $analysis->averageError();

$chart = new Highchart();
$chart->chart = [
    'renderTo' => 'container',
    'type' => 'line',
    'marginRight' => 130,
    'marginBottom' => 25
];
$chart->title = [
    'text' => 'Значення вартості за моделлю',
    'x' => -20
];

$chart->credits = [
    'enabled' => false
];
/*$chart->xAxis = [
    'title' => [
        //'text' => 'Номер екземпляру, k'
        'text' => 'k'
    ],
    'plotLines' => [
        [
            'value' => 0,
            'width' => 1,
            'color' => '#808080'
        ]
    ]
];*/

$chart->xAxis = [
    'type' => 'linear',
    'visible' => true,
    'title' => [
        'text' => 'k'
    ]
];

$chart->yAxis = [
    'title' => [
        'text' => 'Y[k]'
    ],
    'plotLines' => [
        [
            'value' => 0,
            'width' => 1,
            'color' => '#808080'
        ]
    ]
];
$chart->legend = [
    'layout' => 'vertical',
    'align' => 'right',
    'verticalAlign' => 'top',
    'x' => -10,
    'y' => 100,
    'borderWidth' => 0
];

$chart->series[] = [
    'name' => 'Y',
    'data' => $matrixY
];
$chart->series[] = [
    'name' => 'Y модельне',
    'data' => $matrixYModel
];

$chart->tooltip->formatter = new HighchartJsExpr(
    "function() { return '<b>'+ this.series.name +'</b><br/>'+ this.y.toFixed(5);}");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Regression analysis</title>
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <?php $chart->printScripts(); ?>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div id="container"></div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="alert alert-primary"
                 style="margin-top: 40px"><?php echo "Середня похибка = " . $averageError . "%" ?></div>
        </div>
    </div>
</div>
<script type="text/javascript"><?= $chart->render("chart1"); ?></script>
</body>
</html>
