<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-15
 * Time: 17:27
 */

namespace ElGamal;

use Components\Field;
use EllipticCurveAlgorithms\EllipticCurve;
use EllipticCurveAlgorithms\Point;

abstract class Worker
{
    /**
     * @var Field
     */
    protected $field;

    /**
     * @var EllipticCurve
     */
    protected $e;

    /**
     * @var \EllipticCurveAlgorithms\Point
     */
    protected $G;

    /**
     * @var integer
     */
    protected $rzG;

    public function __construct(Field $field, EllipticCurve $e, Point $G, $rzG)
    {
        $this->field = $field;
        $this->e = $e;
        $this->G = $G;
        $this->rzG = $rzG;
    }

    public function hexToStr($message): string
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

    public function xorWithHASH($message, Point $P): string
    {
        $str = $message;
        while (strlen($str) < BLOCK_SIZE) {
            $str = "0$str";
        }

        $hash = hash(HASH, $P->__toString());

        $xor = "";
        for ($index = 0; $index < BLOCK_SIZE; $index++) {
            $dec = hexdec($str[$index]) ^ hexdec($hash[$index]);
            $xor .= dechex($dec);
        }
        return $xor;
    }

    public function ascii2hex($ascii): string
    {
        $hex = '';
        for ($i = 0; $i < strlen($ascii); $i++) {
            $byte = strtoupper(dechex(ord($ascii{$i})));
            $byte = str_repeat('0', 2 - strlen($byte)) . $byte;
            $hex .= $byte . "";
        }
        return strtolower($hex);
    }

    public function send($file, $value)
    {
        file_put_contents($file, $value);
    }

    public function receive($file)
    {
        while (!file_exists($file)) ;
        $value = json_decode(file_get_contents($file), true);
        unlink($file);

        return $value;
    }
}