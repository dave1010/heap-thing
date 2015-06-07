<?php

namespace Base\BetterValues;

final class Box
{
    /** @var mixed */
    private $value;

    /** @var \Iterator */
    private $iterator;

    public function __construct($value, \Iterator $iterator = null)
    {
        $this->value = $value;
        $this->iterator = $iterator;
    }

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }

    /** @return \Iterator */
    public function getIterator()
    {
        return $this->iterator;
    }
}
