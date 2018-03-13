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

use Components\Field\FpField;
use EllipticCurveAlgorithms\EllipticCurve;

// ElGamal
$f = new FpField(11);
$e = new EllipticCurve($f, 10, 1);

$P = $e->createPoint(5, 6); // wiadomość

echo "P: $P \n";

$B = $e->generateRandomPoint(); // jawny nadawca
echo "B: $B \n";

$k = $f->getElement(7); // tajny odbiorca
echo "k: $k \n";

$kB = $B->mul($k); // jawny odbiorca
echo "kB: $kB \n";

$r = $f->getRandomElement(); // tajny nadawca
echo "r: $r \n";

$rB = $B->mul($r);
echo "rB: $rB \n";

$P_rkb = $P->add($kB->mul($r));
echo "P+r(kB): $P_rkb \n";

echo "cipherPoint: [$rB, $P_rkb] \n";

$plainPoint = $P_rkb->sub($rB->mul($k));
echo "plainPoint: $plainPoint \n";

exit();