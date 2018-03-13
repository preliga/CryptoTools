<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 * Date: 2018-03-13
 * Time: 16:35
 */

namespace Components\Element;

use Components\Element;

class RealElement extends Element
{
    protected $value;

    public function __construct($filed, $value)
    {
        parent::__construct($filed);
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}