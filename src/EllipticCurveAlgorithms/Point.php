<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-10
 * Time: 12:37
 */

namespace EllipticCurveAlgorithms;

use Components\Element;

class Point
{
    /**
     * @var EllipticCurve
     */
    protected $ellipticCurve;

    /**
     * @var Element
     */
    protected $x;

    /**
     * @var Element
     */
    protected $y;

    /**
     * @var Integer
     */
    protected $infinity;

    public function __construct(EllipticCurve $ellipticCurve, $x, $y, $infinity)
    {
        if (!($x instanceof Element)) {
            $x = $this->ellipticCurve->field->getElement($x);
        }

        if (!($y instanceof Element)) {
            $y = $this->ellipticCurve->field->getElement($y);
        }

        $this->ellipticCurve = $ellipticCurve;
        $this->x = $x;
        $this->y = $y;
        $this->infinity = $infinity;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function add(Point $Q): Point
    {
        if ($this->infinity == 0) {
            return $Q;
        } else if ($Q->infinity == 0) {
            return $this;
        } else {
            if ($this->x->compare($Q->x) == 0) {
                if ($this->y->compare($Q->y->opposite()) == 0) {
                    return $this->ellipticCurve->createInfinityPoint();
                } else {
                    $lambda = $this->x
                        ->power(2)
                        ->mul(3)
                        ->add($this->ellipticCurve->a)
                        ->mul(
                            $this->y
                                ->mul(2)
                                ->inverse()
                        );
                }
            } else {
                $lambda = $Q->y
                    ->sub($this->y)
                    ->div(
                        $Q->x
                            ->sub($this->x)
                    );
            }

            $x = $lambda
                ->power(2)
                ->sub($this->x)
                ->sub($Q->x);

            $y = $lambda
                ->mul(
                    $this->x
                        ->sub($x)
                )
                ->sub($this->y);

            return $this->ellipticCurve->createPoint($x, $y);
        }
    }

    public function sub(Point $a) {
        return $this->add($a->opposite());
    }

    public function opposite()
    {
        $P = clone($this);
        $P->y = $P->y->opposite();

        return $P;
    }

    public function mul($n): Point
    {
        if (!($n instanceof Element)) {
            $n = $this->ellipticCurve->field->getElement($n);
        }

        if ($n->compare(0) == 0) {
            return $this->ellipticCurve->createInfinityPoint();
        } else {
            if ($n->compare(0) == -1) {
                $P = $this->opposite();
                $n = $n->opposite();
            } else {
                $P = clone($this);
            }

            $Q = $this->ellipticCurve->createInfinityPoint();
            while ($n->compare(1) == 1) {
                if ($n->mod(2)->compare(1) == 0) {
                    $Q = $Q->add($P);
                }

                $P = $P->add($P);
                $n = $n->div(2);
            }

            $Q = $Q->add($P);
            return $Q;
        }
    }

    public function __toString(): String
    {
        return "( {$this->x} : {$this->y} : {$this->infinity})";
    }

}