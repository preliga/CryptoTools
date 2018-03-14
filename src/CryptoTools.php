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
//$f = new FpField(11);
//$e = new EllipticCurve($f, 10, 1);
//
//$P = $e->createPoint(3, 5); // wiadomość
//
//echo "P: $P \n";
//
//$B = $e->createPoint(10,10);//$e->generateRandomPoint(); // jawny nadawca
//echo "B: $B \n";
//
//$k = $f->getElement(7); // tajny odbiorca
//echo "k: $k \n";
//
//$kB = $B->mul($k); // jawny odbiorca
//echo "kB: $kB \n";
//
//$r = $f->getRandomElement(); // tajny nadawca
//echo "r: $r \n";
//
//$rB = $B->mul($r);
//echo "rB: $rB \n";
//
//$P_rkb = $P->add($kB->mul($r));
//echo "P+r(kB): $P_rkb \n";
//
//echo "cipherPoint: [$rB, $P_rkb] \n";
//
//$plainPoint = $P_rkb->sub($rB->mul($k));
//echo "plainPoint: $plainPoint \n";
/////////////////////////////
const BLOCK_SIZE = 40;

// HASH ElGamal
echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n";
echo "......................................................................Hash ElGamal......................................................................\n";
echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n";
$message = 'Message';
//$message = 'Values outside the valid range (0..255) will be bitwise and\'ed with 255, which is equivalent to the following algorithm:';
echo "Nadawca chcę wysłać wiadomość: '$message' \n";

$amountBlocks = ceil(strlen($message) / (BLOCK_SIZE / 2));

echo "Liczba bloków wiadomości: $amountBlocks \n";
echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n";

for ($i = 0; $i < $amountBlocks; $i++) {
    $msg = substr($message, $i * BLOCK_SIZE / 2, BLOCK_SIZE / 2);

    echo "\n########################################################################################################################################################\n";
    echo "Blok nr (" . ($i + 1) .") M = '$msg' \n";
    echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n";
    ElGamal($msg);
}

echo "KONIEC";
/////////////////////////////
exit();

function ElGamal($message)
{
    $f = new FpField(71);
    #E = 'y^2 = x^3 + ax + b';
    $e = new EllipticCurve($f, 10, 1);

    $M = ascii2hex($message);
    echo "Nadawca koduje wiadomość M w postaci Hex: '$M' \n";

    $B = $e->generateRandomPoint(); // jawny nadawca
    echo "Nadawca losuje punkt B na krzywej eliptycznej: $B Nastepniie go ujawnia. \n";

    echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n";

    $k = $f->getElement(7); // tajny odbiorca
    echo "Odbiorca losuje tajną liczbę k = $k \n";

    $kB = $B->mul($k); // jawny odbiorca
    echo "Odbiorca wyliczba [k]B = [$k]($B) = $kB Następnie ujawnia ten punkt. \n";

    echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n";

    $r = $f->getRandomElement(); // tajny nadawca
    echo "Nadawca losuje tajną liczbę r = $r \n";

    $rB = $B->mul($r);
    echo "Nadawca wyliczba [r]B = [$r]$B = $rB \n";

    $hash = xorWithHASH($M, $kB->mul($r));

    echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n";

    echo "Nadawca szyfruje wiadomość M w postaci (C1, C2) = ([r]B, M xor SHA1( [r]([k]B) ) ) = ($rB, $hash) \n";

    $plainPoint = xorWithHASH($hash, $rB->mul($k));
    echo "Odbiorca odszyfrowuje wiadomość C2 xor SHA1( [k]([r]B) ) = M = '" . hexToStr($plainPoint) . "' \n";

    echo "--------------------------------------------------------------------------------------------------------------------------------------------------------\n\n";
}

function hexToStr($message): string
{
    $str = "";
    for ($i = 0; $i < strlen($message); $i += 2) {
        $hex = $message[$i] . $message[$i + 1];
        if ($hex != '00') {
            $dec = hexdec($hex);
            $str .= chr($dec);
        }
    }

    return $str;
}

function xorWithHASH($message, \EllipticCurveAlgorithms\Point $P): string
{
    $str = $message;
    while (strlen($str) < BLOCK_SIZE) {
        $str = "0$str";
    }
    $hash = sha1($P->__toString());

    $xor = "";
    for ($index = 0; $index < BLOCK_SIZE; $index++) {
        $dec = hexdec($str[$index]) ^ hexdec($hash[$index]);
        $xor[$index] = dechex($dec);
    }

    return $xor;
}

function ascii2hex($ascii): string
{
    $hex = '';
    for ($i = 0; $i < strlen($ascii); $i++) {
        $byte = strtoupper(dechex(ord($ascii{$i})));
        $byte = str_repeat('0', 2 - strlen($byte)) . $byte;
        $hex .= $byte . "";
    }
    return strtolower($hex);
}