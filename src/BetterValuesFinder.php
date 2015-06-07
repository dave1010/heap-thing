<?php

namespace Base\BetterValues;

/**
 * Find the best set of values
 *
 * Values are based on an initial set and values returned from iterators
 */
class BetterValuesFinder
{
    /** @var callable */
    private $comparator;

    /** @var int */
    private $limit;

    private $boxes = [];

    /**
     * @param Box[] $boxes of values and iterators to start from
     * @param callable $comparator how to compare values inside boxes
     * @param int|null $limit maximum number of values to return when processed
     */
    public function __construct(array $boxes, callable $comparator, $limit = null)
    {
        // make $comparator work with Boxes
        $this->comparator = function (Box $a, Box $b) use ($comparator) {
            return $comparator($a->getValue(), $b->getValue());
        };

        $this->limit = ($limit === null) ? count($boxes) : $limit;

        foreach ($boxes as $box) {
            $this->insert($box);
        }
    }

    /** @return array */
    public function getBest()
    {
        // how many results need to be checked
        $checksRequired = $this->limit;

        while ($checksRequired) {

            /** @var Box $item */
            foreach ($this->boxes as $item) {

                $iterator = $item->getIterator();
                if (!$iterator || !$iterator->valid()) {
                    $checksRequired--;
                    continue;
                }

                $this->insert(new Box($iterator->current(), $iterator));

                $iterator->next();

                break;
            }
        }

        $values = $this->unBox();

        return $values;
    }


    /** @return array */
    private function unBox()
    {
        $values = [];

        /** @var Box $item */
        foreach ($this->boxes as $item) {
            $values[] = $item->getValue();
        }

        return $values;
    }

    private function insert(Box $box)
    {
        $this->boxes[] = $box;

        // this resorts all values, when inserting to a heap would be better
        usort($this->boxes, $this->comparator);

        if (count($this->boxes) > $this->limit) {
            array_pop($this->boxes);
        }
    }
}
