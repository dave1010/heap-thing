<?php

class Processor
{
    /** @var Box[] */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function process()
    {

        $best = [];
        $items = $this->items;

        $max = count($items);

        $checksRequired = $max;
        // how many results need to be checked

        $compare = function ($a, $b) {
            $a = $a->getValue();
            $b = $b->getValue();
            if ($a === $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        };


        $items = new SlicedHeap($max, $compare);
        foreach ($this->items as $item) {
            $items->insert($item);
        }
//var_dump(iterator_to_array($items));die();

        //new SplMaxHeap()


        while ($checksRequired) {

            /** @var Box $item */
            foreach ($items as $item) {
                if ($item->isChecked()) {
                    continue;
                }

                // this item needs checking

                $generator = $item->getGenerator();
                if (!$generator || !$generator->valid()) {
                    $item->markChecked();
                    $checksRequired--;
                    continue;
                }

                $items[] = new Box($generator->current(), $generator);
                //usort($items, $compare);
                //$items = array_slice($items, 0, $max);
                $generator->next();


                break;
            }

            // TODO: either $items needs array access, or iterate
            //$item = $items[$cursor];
        }

        $values = $this->unBox($items);

        return $values;
    }

    /**
     * @param $items Box[]
     * @return array mixed
     */
    private function unBox($items)
    {
        return array_map(function (Box $box) {
            return $box->getValue();
        }, $items);
    }
}
