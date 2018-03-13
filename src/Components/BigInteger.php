<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-10
 * Time: 14:20
 */

namespace Components;

class BigInteger
{
    protected $value;

    protected $mod = null;

    protected $field = null;

    public function __construct($value, $filed = null)
    {
        $this->value = $value;

        if ($filed instanceof Field) {
            $this->field = $filed;
        } else {
            $this->field = new Field($filed);
        }

        $this->mod = $this->field->p;

        if (!empty($mod)) {
            $this->value = $this->modulo($this->mod);//
        }
    }

    public function mul($b): BigInteger
    {
        if (!($b instanceof BigInteger)) {
            $b = new BigInteger($b, $this->mod);
        }

        $c = bcmul($this->value, $b->value);
        return new BigInteger($c, $this->mod);
    }

    public function div($b): BigInteger
    {
        if (!($b instanceof BigInteger)) {
            $b = new BigInteger($b, $this->mod);
        }

        if (empty($this->mod)) {
            $c = bcdiv($this->value, $b->value);
        } else {
            $c = $this->mul($b->inverse());
        }

        return new BigInteger($c, $this->mod);
    }

    public function modulo($mod)
    {
        while (bccomp($this->value, '0') == -1) {
            $this->value = bcadd($this->value, $mod);
        }
        return bcmod($this->value, $mod);
    }

    public function inverse()
    {
        $num = $this->value;
        $mod = $this->mod;
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
        return $x;
    }

    public function add($b): BigInteger
    {
        if (!($b instanceof BigInteger)) {
            $b = new BigInteger($b, $this->mod);
        }

        $c = bcadd($this->value, $b->value);

        return new BigInteger($c, $this->mod);
    }

    public function sub($b): BigInteger
    {
        if (!($b instanceof BigInteger)) {
            $b = new BigInteger($b, $this->mod);
        }

        $c = bcsub($this->value, $b->value);
        return new BigInteger($c, $this->mod);
    }

    public function power($n): BigInteger
    {
        if (!empty($this->mod)) {
            $c = bcpowmod($this->value, $n, $this->mod);
        } else {
            $c = bcpow($this->value, $n);
        }

        return new BigInteger($c, $this->mod);
    }

    public function opposite()
    {
        $P = clone($this);

        $P->value = '-' . $P->value;

        if (!empty($P->mod)) {
            $P->value = $P->modulo($P->mod);
        }

        return $P;
    }

    public function compare($b)
    {
        if ($b instanceof BigInteger) {
            $b = $b->value;
        }

        return bccomp($this->value, $b);
    }

    public function bitLength()
    {
        return strlen(base_convert($this->value, 10, 2));
    }

    public function shiftRight($n)
    {
        $temp = substr(base_convert($this->value, 10, 2), 0, -$n);

        if ($temp == '') {
            $temp = '0';
        }

        return new BigInteger(substr(base_convert($temp, 2, 10), $this->mod));
    }

    public function shiftLeft($n)
    {
        $temp = base_convert($this->value, 10, 2);
        for ($i = 0; $i < $n; $i++) {
            $temp .= "0";
        }

        return new BigInteger(base_convert($temp, 2, 10), $this->mod);
    }

    public function setBit($n)
    {
        $temp = base_convert($this->value, 10, 2);

        while (strlen($temp) <= $n) {
            $temp = "0" . $temp;
        }
        $n = strlen($temp) - $n;

        $temp = substr_replace($temp, '1', $n - 1, 1);
        return new BigInteger(base_convert($temp, 2, 10), $this->mod);
    }

    public function sqrt2()
    {
        do {
            $g = $this->field->getRandomElement();
        } while (empty($g->sqrt()));

    }

    public function sqrt()
    {
        $div = (new BigInteger(0, $this->mod))->setBit($this->bitLength() / 2);
        $div2 = $div;

        while (true) {
            $y =
                $div->add(
                    $this->div($div)
                )
                    ->shiftRight(1);

            if ($y->compare($div) == 0 || $y->compare($div2)) {
                return $y->power(2)->compare($this) == 0 ? $y : false;
            }

            $div2 = $div;
            $div = $y;
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}