<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-13
 * Time: 16:32
 */

namespace Components\Field;

use Components\Field;
use Components\Element\RealElement;

class FpField extends Field
{
    protected $p;

    public function __construct($p)
    {
        $this->p = $p;
    }

    public function getElement($value): RealElement
    {
        while (bccomp($value, '0') == -1) {
            $value = bcadd($value, $this->p);
        }
        $c = bcmod($value, $this->p);

        return new RealElement($this, $c);
    }

    public function getRandomElement(): RealElement
    {
        // gdy p - pierwsze
        return new RealElement($this, random_int(1, $this->p - 1), $this->p);
    }

    public function mul($a, $b): RealElement
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        if (!($b instanceof RealElement)) {
            $b = $this->getElement($b);
        }

        $c = bcmul($a->value, $b->value);
        return $this->getElement($c);
    }

    public function div($a, $b): RealElement
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        if (!($b instanceof RealElement)) {
            $b = $this->getElement($b);
        }

        $c = $this->mul($a, $b->inverse());
        return $this->getElement($c);
    }

    public function mod($a, $mod): RealElement
    {
        $c = $a->value;
        while (bccomp($c, '0') == -1) {
            $c = bcadd($c, $mod);
        }

        $c = bcmod($c, $mod);
        return $this->getElement($c);
    }

    public function inverse($a): RealElement
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        $num = $a->value;
        $mod = $this->p;
        $x = '1';
        $y = '0';
        $num1 = $mod;

        do {
            $tmp = bcmod($num, $num1);
            $q = bcdiv($num, $num1);
            $num = $num1;
            $num1 = $tmp;
            $tmp = bcsub($x, bcmul($y, $q));
            $x = $y;
            $y = $tmp;
        } while (bccomp($num1, '0'));

        if (bccomp($x, '0') < 0) {
            $x = bcadd($x, $mod);
        }
        return $this->getElement($x);
    }

    public function add($a, $b): RealElement
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        if (!($b instanceof RealElement)) {
            $b = $this->getElement($b);
        }

        $c = bcadd($a->value, $b->value);

        return $this->getElement($c);
    }

    public function sub($a, $b): RealElement
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        if (!($b instanceof RealElement)) {
            $b = $this->getElement($b);
        }

        $c = bcsub($a->value, $b->value);
        return $this->getElement($c);
    }

    public function power($a, $n): RealElement
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        $c = bcpowmod($a->value, $n, $this->p);

        return $this->getElement($c);
    }

    public function opposite($a)
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        return $this->getElement('-' . $a->value);
    }

    public function compare($a, $b)
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        if (!($b instanceof RealElement)) {
            $b = $this->getElement($b);
        }

        return bccomp($a->value, $b->value);
    }

    public function bitLength($a)
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        return strlen(base_convert($a->value, 10, 2));
    }

    public function shiftRight($a, $n)
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        $temp = substr(base_convert($a->value, 10, 2), 0, -$n);

        if ($temp == '') {
            $temp = '0';
        }

        return $this->getElement(substr(base_convert($temp, 2, 10)));
    }

    public function shiftLeft($a, $n)
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        $temp = base_convert($a->value, 10, 2);
        for ($i = 0; $i < $n; $i++) {
            $temp .= "0";
        }

        return $this->getElement(base_convert($temp, 2, 10));
    }

    public function setBit($a, $n)
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        $temp = base_convert($a->value, 10, 2);

        while (strlen($temp) <= $n) {
            $temp = "0" . $temp;
        }
        $n = strlen($temp) - $n;

        $temp = substr_replace($temp, '1', $n - 1, 1);
        return $this->getElement(base_convert($temp, 2, 10));
    }

    public function sqrt($a)
    {
        if (!($a instanceof RealElement)) {
            $a = $this->getElement($a);
        }

        $div = $this->getElement(0)->setBit($a->bitLength() / 2);
        $div2 = $div;

        while (true) {
            $y =
                $div->add(
                    $a->div($div)
                )
                    ->shiftRight(1);

            if ($y->compare($div) == 0 || $y->compare($div2)) {
                return $y->power(2)->compare($a) == 0 ? $y : false;
            }

            $div2 = $div;
            $div = $y;
        }
    }
}