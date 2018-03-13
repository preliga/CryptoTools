<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-10
 * Time: 12:27
 */

namespace EllipticCurveAlgorithms;

use Components\Element;
use Components\Field;

class EllipticCurve
{
    /**
     * @var Field
     */
    protected $field;

    /**
     * @var Element
     */
    protected $a;

    /**
     * @var Element
     */
    protected $b;

    #E = 'y^2 = x^3 + ax + b';
    public function __construct(Field $field, $a, $b)
    {
        $this->field = $field;

        if (!($a instanceof Element)) {
            $a = $this->field->getElement($a);
        }

        if (!($b instanceof Element)) {
            $b = $this->field->getElement($b);
        }

        $this->a = $a;
        $this->b = $b;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function createPoint($a, $b, $infinity = 1): Point
    {
        if (!($a instanceof Element)) {
            $a = $this->field->getElement($a);
        }

        if (!($b instanceof Element)) {
            $b = $this->field->getElement($b);
        }

        return new Point($this, $a, $b, $infinity);
    }

    public function createInfinityPoint(): Point
    {
        return new Point($this, $this->field->getElement(0), $this->field->getElement(1), 0);
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

    }
}