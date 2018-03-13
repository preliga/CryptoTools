<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-13
 * Time: 16:34
 */

namespace Components;


abstract class Element
{
    protected $filed;

    public function __construct(Field $filed)
    {
        $this->filed = $filed;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function mul($b): Element
    {
        return $this->filed->mul($this, $b);
    }

    public function div($b): Element
    {
        return $this->filed->div($this, $b);
    }

    public function mod($mod): Element
    {
        return $this->filed->mod($this, $mod);
    }

    public function inverse(): Element
    {
        return $this->filed->inverse($this);
    }

    public function add($b): Element
    {
        return $this->filed->add($this, $b);
    }

    public function sub($b): Element
    {
        return $this->filed->sub($this, $b);
    }

    public function power($n): Element
    {
        return $this->filed->power($this, $n);
    }

    public function opposite()
    {
        return $this->filed->opposite($this);
    }

    public function compare($b)
    {
        return $this->filed->compare($this, $b);
    }

    public function bitLength()
    {
        return $this->filed->bitLength($this);
    }

    public function shiftRight($n)
    {
        return $this->filed->shiftRight($this, $n);
    }

    public function shiftLeft($n)
    {
        return $this->filed->shiftLeft($this, $n);
    }

    public function setBit($n)
    {
        return $this->filed->setBit($this, $n);
    }

    public function sqrt()
    {
        return $this->filed->sqrt($this);
    }
}