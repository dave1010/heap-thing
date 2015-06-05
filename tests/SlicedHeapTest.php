<?php


class SlicedHeapTest extends PHPUnit_Framework_TestCase {

    public function testInsertUnder()
    {

        $compare = function ($a, $b) {
//        $a = $a->getValue();
//        $b = $b->getValue();
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };

        $heap = new SlicedHeap(5, $compare);
        $heap->insert(1);
        $heap->insert(3);
        $heap->insert(2);

        $this->assertEquals([1, 2, 3], iterator_to_array($heap));
    }

    public function testInsertOver()
    {
        $compare = function ($a, $b) {
//        $a = $a->getValue();
//        $b = $b->getValue();
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };

        $heap = new SlicedHeap(2, $compare);
        $heap->insert(1);
        $heap->insert(3);
        $heap->insert(2);
        $heap->insert(0);

        $this->assertEquals([0, 1], iterator_to_array($heap));
    }
}
