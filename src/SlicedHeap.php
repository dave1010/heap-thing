<?php


final class SlicedHeap extends SplHeap
{
    /** @var int */
    private $max;
    /** @var */
    private $comparator;

    public function __construct($max, $comparator)
    {
        $this->max = $max;
        $this->comparator = $comparator;
    }

    public function compare($a, $b) {
        $comparator = $this->comparator;
        return $comparator($a, $b);
    }

    public function insert($value)
    {
        parent::insert($value);

        if ($this->count() > $this->max) {
            $this->extract();
        }
    }
}
