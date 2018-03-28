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

define('PATH_FILE_AMOUNT_BLOCK', '../tube/amountBlock');
define('PATH_FILE_B', '../tube/B');
define('PATH_FILE_kB', '../tube/kB');
define('PATH_FILE_CIPHER_TEXT', '../tube/cipherText');
define('PATH_FLAG_END', '../tube/flagEnd');

//define('PATH_FILE_RESULTS', '../result/result');

define('HASH', 'sha512');
define('BLOCK_SIZE', strlen(hash(HASH, 'test')));

$index = 1;
$params = json_decode(file_get_contents($argv[$index++]), true); // params.json
$mode = strtoupper($argv[$index++]); // S or R

if ($mode == 'S') {
    $messageFile = $argv[$index++];
} else {
    $messageFile = "";
}

if ($mode == 'R') {
    $outputFile = $argv[$index++];
} else {
    $outputFile = "";
}

if (!empty($argv[$index])) {
    $show = strtoupper($argv[$index++]) == 'V';
} else {
    $show = false;
}

define('SHOW', $show);

show("------------------------------------------------\n");
show(".................Hash ElGamal...................\n");
show("------------------------------------------------\n");

$ElGamal = new \ElGamal\ElGamal($params);
$ElGamal->run($mode, $messageFile, $outputFile);

show("KONIEC");
/////////////////////////////
exit();

function show($msg){
    if (SHOW) {
        echo $msg;
    }
}