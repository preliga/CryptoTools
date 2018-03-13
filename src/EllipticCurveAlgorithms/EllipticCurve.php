<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-10
 * Time: 12:27
 */

namespace EllipticCurveAlgorithms;

use Components\BigInteger;
use Components\Field;

class EllipticCurve
{
    /**
     * @var Field
     */
    protected $field;

    /**
     * @var BigInteger
     */
    protected $a;

    /**
     * @var BigInteger
     */
    protected $b;

    #E = 'y^2 = x^3 + ax + b';
    public function __construct(Field $field, $a, $b)
    {
        $this->field = $field;
        $this->a = $field->getBigInteger($a);
        $this->b = $field->getBigInteger($b);
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function createPoint($a, $b, $infinity = 1): Point
    {
        if (!($a instanceof BigInteger)) {
            $a = $this->field->getBigInteger($a);
        }

        if (!($b instanceof BigInteger)) {
            $b = $this->field->getBigInteger($b);
        }

        return new Point($this, $a, $b, $infinity);
    }

    public function createInfinityPoint(): Point
    {
        return new Point($this, $this->field->getBigInteger(0), $this->field->getBigInteger(1), 0);
    }

    public function isPointOnCurve(Point $p)
    {
        $a = $p->y
            ->power(2);

        $b = $p->x
            ->power(3)
            ->add(
                $this->a->mul($p->x)
            )
            ->add(
                $this->b
            );

        return $a->compare($b) == 0;
    }

    public function generateRandomPoint()
    {
        do {
//            $x = $this->field->getRandomElement();

            $x = new BigInteger(3, 11);
            $y =
                $x->power(3)
                ->add(
                    $this->a
                        ->mul($x)
                )
                ->add($this->b)
                ->sqrt()
            ;

            echo "$x : $y \n";
            die();
        } while(empty($y));

        $p = $this->createPoint($x, $y);

        if (!$this->isPointOnCurve($p)) {
            $p = $this->generateRandomPoint();
        }

        return $p;
    }
}