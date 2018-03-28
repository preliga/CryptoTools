<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-15
 * Time: 17:16
 */

namespace ElGamal\Components;

use Components\Field;
use ElGamal\Worker;
use EllipticCurveAlgorithms\EllipticCurve;
use EllipticCurveAlgorithms\Point;

class Receiver extends Worker
{
    /**
     * @var string
     */
    protected $outputFile;

    public function __construct(Field $field, EllipticCurve $e, Point $G, $rzG, string $outputFile)
    {
        parent::__construct($field, $e, $G, $rzG);

        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $this->outputFile = $outputFile;
    }

    function run()
    {
        $X = $this->receive(PATH_FILE_AMOUNT_BLOCK);
        $amountBlocks = $X['amountBlock'];
        show("------------------------------------------------\n");

        for ($i = 0; $i < $amountBlocks; $i++) {
            show("\n##############################################\n");
            show("Blok nr (" . ($i + 1) . ")\n");
            show("------------------------------------------------\n");

            $this->receiveMessage();
        }
    }

    public function receiveMessage()
    {
        $X = $this->receive(PATH_FILE_B);
        $B = $this->e->createPoint($X['x'], $X['y']);

        do {
            $k = $this->field->getRandomElement(); // tajny odbiorca
            $kB = $B->mul($k); // jawny odbiorca
        } while ($kB->isInfinity());
        show("Odbiorca losuje tajna liczbe k = $k \n");
        show("Odbiorca wyliczba [k]B = [$k]($B) = $kB \nNastepnie ujawnia ten punkt. \n");

        $this->send(PATH_FILE_kB, json_encode(['x' => "$kB->x", 'y' => "$kB->y"]));

        $X = $this->receive(PATH_FILE_CIPHER_TEXT);
        $rB = $this->e->createPoint($X['rB']['x'], $X['rB']['y']);
        $hash = $X['hash'];

        $plainPoint = $this->xorWithHASH($hash, $rB->mul($k));
        $msg = $this->hexToStr($plainPoint);

        $hashFunction = HASH;
        show("Odbiorca odszyfrowuje wiadomosc: \nC2 xor $hashFunction( [k]([r]B) ) = M = '" . $msg . "' \n");

        $this->saveMessage($msg);

        $this->send(PATH_FLAG_END, 'koniec');
    }

    public function saveMessage($msg)
    {
        do {
            @$file = fopen($this->outputFile, 'a');
        } while ($file == false);

        fwrite($file, $msg);
        fclose($file);
    }
}