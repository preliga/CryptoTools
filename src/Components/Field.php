<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-10
 * Time: 14:42
 */

namespace Components;


class Field
{
    protected $p;

    public function __construct($p)
    {
        $this->p = $p;
    }

    public function getBigInteger($value): BigInteger
    {
        return new BigInteger($value, $this->p);
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function getRandomElement(): BigInteger
    {
        // gdy p - pierwsze
        return new BigInteger(random_int(1, $this->p - 1), $this->p);
    }
}