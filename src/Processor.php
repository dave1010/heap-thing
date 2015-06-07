<?php

class Processor
{
    /** @var callable */
    private $comparator;

    /** @var int */
    private $limit;

    private $values = [];

    /**
     * @param array $values values to start from
     * @param callable $comparator how to compare values
     * @param int|null $limit maximum number of values to return when processed
     */
    public function __construct(array $values, callable $comparator, $limit = null)
    {
        $this->comparator = $comparator;
        $this->limit = ($limit === null) ? count($values) : $limit;

        foreach ($values as $value) {
            $this->insert($value);
        }
    }

    /** @return array */
    public function process()
    {
        // how many results need to be checked
        $checksRequired = $this->limit;

        while ($checksRequired) {

            /** @var Box $item */
            foreach ($this->values as $item) {

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
        foreach ($this->values as $item) {
            $values[] = $item->getValue();
        }

        return $values;
    }

    private function insert($value)
    {
        $this->values[] = $value;

        // this resorts all values, when inserting to a heap would be better
        usort($this->values, $this->comparator);

        if (count($this->values) > $this->limit) {
            array_pop($this->values);
        }
    }
}
