<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-15
 * Time: 17:15
 */

namespace ElGamal\Components;

use Components\Field;
use ElGamal\Worker;
use EllipticCurveAlgorithms\EllipticCurve;
use EllipticCurveAlgorithms\Point;

class Sender extends Worker
{
    /**
     * @var string
     */
    protected $messageFile;

    public function __construct(Field $field, EllipticCurve $e, Point $G, $rzG, string $messageFile)
    {
        parent::__construct($field, $e, $G, $rzG);

        $this->messageFile = $messageFile;
    }

    public function run()
    {
        $this->removeFiles();

        $message = file_get_contents($this->messageFile);
        show("Nadawca chce wyslac wiadomosc: '$message' \n");
        $amountBlocks = ceil(strlen($message) / (BLOCK_SIZE / 2));
        show("Liczba blokow wiadomosci: $amountBlocks \n");

        $this->send(PATH_FILE_AMOUNT_BLOCK, json_encode(['amountBlock' => "$amountBlocks"]));

        show("------------------------------------------------\n");

        for ($i = 0; $i < $amountBlocks; $i++) {
            $msg = substr($message, $i * BLOCK_SIZE / 2, BLOCK_SIZE / 2);

            show("\n##############################################\n");
            show("Blok nr (" . ($i + 1) . ") M = '$msg' \n");
            show("------------------------------------------------\n");

            $this->sendMessage($msg);
        }
    }

    public function sendMessage($message)
    {
        $M = $this->ascii2hex($message);
        show("Nadawca koduje wiadomosc M w postaci Hex: '$M' \n");

        $B = $this->e->generateRandomPoint($this->G); // jawny nadawca
        show("Nadawca losuje punkt B na krzywej eliptycznej: $B \nNastepniie go ujawnia. \n");

        $this->send(PATH_FILE_B, json_encode(['x' => "$B->x", 'y' => "$B->y"]));

        $X = $this->receive(PATH_FILE_kB);
        $kB = $this->e->createPoint($X['x'], $X['y']);
        show("------------------------------------------------\n");

        do {
            $r = $this->field->getRandomElement(); // tajny nadawca
            $rB = $B->mul($r);
        } while ($rB->isInfinity());

        show("Nadawca losuje tajna liczbe r = $r \n");
        show("Nadawca wyliczba [r]B = [$r]$B = $rB \n");
        $hash = $this->xorWithHASH($M, $kB->mul($r));

        show("------------------------------------------------\n");
        $hashFunction = HASH;
        show("Nadawca szyfruje wiadomosc M w postaci: \n(C1, C2) = ([r]B, M xor $hashFunction( [r]([k]B) ) ) = ($rB, $hash) \n");

        $this->send(PATH_FILE_CIPHER_TEXT, json_encode(['rB' => ['x' => "$rB->x", 'y' => "$rB->y"], 'hash' => $hash]));

        while (file_exists(PATH_FILE_CIPHER_TEXT)) ;
    }

    public function removeFiles()
    {
        if (file_exists(PATH_FILE_AMOUNT_BLOCK)) {
            unlink(PATH_FILE_AMOUNT_BLOCK);
        }

        if (file_exists(PATH_FILE_B)) {
            unlink(PATH_FILE_B);
        }

        if (file_exists(PATH_FILE_kB)) {
            unlink(PATH_FILE_kB);
        }

        if (file_exists(PATH_FILE_CIPHER_TEXT)) {
            unlink(PATH_FILE_CIPHER_TEXT);
        }
    }
}