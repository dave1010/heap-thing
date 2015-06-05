<?php


final class Box
{
    /** @var mixed */
    private $value;

    /** @var Generator */
    private $generator;

    private $checked = false;

    public function __construct($value, Generator $generator = null)
    {
        $this->value = $value;
        $this->generator = $generator;
    }

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }

    /** @return Generator */
    public function getGenerator()
    {
        return $this->generator;
    }

    /** @return boolean */
    public function isChecked()
    {
        return $this->checked;
    }

    public function markChecked()
    {
        $this->checked = true;
    }
}