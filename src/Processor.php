<?php

class Processor implements Iterator, Countable
{
    /** @var callable */
    private $comparator;

    /** @var int */
    private $max;

    private $values = [];

    private $currentKey;

    /**
     * @param array $items
     * @param callable $comparator
     */
    public function __construct(array $items, $comparator, $max = null)
    {
        $this->max = ($max === null) ? count($items) : $max;
        $this->comparator = $comparator;

        foreach ($items as $item) {
            $this->insert($item);
        }
    }

    /**
     * @param int|null $max
     * @return Traversable
     */
    public function process()
    {
        // how many results need to be checked
        $checksRequired = $this->max;

        while ($checksRequired) {

            /** @var Box $item */
            foreach ($this as $item) {

                $generator = $item->getGenerator();
                if (!$generator || !$generator->valid()) {
                    $checksRequired--;
                    continue;
                }

                $this->insert(new Box($generator->current(), $generator));

                $generator->next();

                break;
            }
        }

        $values = $this->unBox($this);

        return $values;
    }

    /**
     * @param $items Box[]
     * @return \Traversable mixed
     */
    private function unBox($items)
    {
        $values = [];

        foreach ($items as $item) {
            $values[] = $item->getValue();
        }

        return $values;
    }

    public function insert($value)
    {
        $this->values[] = $value;

        usort($this->values, $this->comparator);

        if ($this->count() > $this->max) {
            array_pop($this->values);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->values[$this->currentKey];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->currentKey++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return array_key_exists($this->currentKey, $this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->currentKey = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->values);
    }
}
