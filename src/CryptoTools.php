<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-10
 * Time: 12:07
 */

require __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($class_name) {
    require "$class_name.php";
});

//use Components\BigInteger;
use Components\Field;
use EllipticCurveAlgorithms\EllipticCurve;

$f = new Field(11);

$e = new EllipticCurve($f, 10, 1);

$P = $e->createPoint('0', '10');
$Q = $e->createPoint('3', '5');

$R = $P->add($Q);
echo $R;

echo "\n";

//$R = $P->add($P);
////$R = $P->add($P);
////$R = $P->add($P);
//echo $R;
//
//echo "\n";

$H = $e->createPoint('10', '1', 1);
$S = $H->mul(5);
echo $S;
//
//echo "\n";

//$S = $H->add($H);
//echo $S;

exit();