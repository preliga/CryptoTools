<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-10
 * Time: 14:42
 */

namespace Components;


abstract class Field
{
    abstract public function getElement($value);

    abstract public function getRandomElement();

    abstract public function mul($a, $b);

    abstract public function div($a, $b);

    abstract public function mod($a, $n);

    abstract public function inverse($a);

    abstract public function add($a, $b);

    abstract public function sub($a, $b);

    abstract public function power($a, $n);

    abstract public function opposite($a);

    abstract public function compare($a, $b);

    abstract public function bitLength($a);

    abstract public function shiftRight($a, $n);

    abstract public function shiftLeft($a, $n);

    abstract public function setBit($a, $n);

    abstract public function sqrt($a);
}