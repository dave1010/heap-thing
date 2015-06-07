<?php

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    private function getComparator()
    {
        return function (Box $a, Box $b) {
            $a = $a->getValue();
            $b = $b->getValue();
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };
    }

    public function testEmpty()
    {
        $p = new Processor([], $this->getComparator());
        $this->assertEquals([], $p->process());
    }

    public function testOne()
    {
        $item = 1;
        $box = new Box($item);
        $list = [$box];
        $p = new Processor($list, $this->getComparator());
        $this->assertEquals([$item], $p->process());
    }

    public function testSingleShunt()
    {
        $generator = function () {
            yield 2;
        };

        $items = [
            new Box(1, $generator()),
            new Box(10),
            new Box(20),
        ];

        $p = new Processor($items, $this->getComparator());
        $this->assertEquals([1, 2, 10], $p->process());
    }

    public function testInsertBackwards()
    {
        $generator = function () {
            yield 2;
            yield 1;
        };

        $items = [
            new Box(3, $generator()),
            new Box(10),
            new Box(20),
        ];

        $p = new Processor($items, $this->getComparator());
        $this->assertEquals([1, 2, 3], $p->process());
    }

    public function testNotShunted()
    {
        $generator = function () {
            yield 10;
        };

        $items = [
            new Box(1, $generator()),
            new Box(2),
            new Box(3),
        ];

        $p = new Processor($items, $this->getComparator());
        $this->assertEquals([1, 2, 3], $p->process());

    }

    public function testGeneratorReused()
    {
        $generator = function () {
            yield 2;
            yield 3;
        };

        $items = [
            new Box(1, $generator()),
            new Box(4),
            new Box(5),
        ];

        $p = new Processor($items, $this->getComparator());
        $this->assertEquals([1, 2, 3], $p->process());

    }


    public function testNoIterations()
    {
        $items = [new Box(1), new Box(2)];
        $p = new Processor($items, $this->getComparator());
        $this->assertEquals([1, 2], $p->process());

    }

    public function testLargerMaxSize()
    {
        $generator = function () {
            yield 2;
        };

        $items = [
            new Box(1, $generator()),
            new Box(3),
            new Box(4),
        ];

        $p = new Processor($items, $this->getComparator(), 4);
        $this->assertEquals([1, 2, 3, 4], $p->process());
    }


    public function testAttemptLargerMaxSize()
    {
        $generator = function () {
            yield 2;
        };

        $items = [
            new Box(1, $generator()),
        ];

        $p = new Processor($items, $this->getComparator(), 10);
        $this->assertEquals([1, 2], $p->process());
    }


    public function testSmallerMaxSize()
    {
        $items = [
            new Box(1),
            new Box(2),
            new Box(3),
        ];

        $p = new Processor($items, $this->getComparator(), 2);
        $this->assertEquals([1, 2], $p->process());
    }
}
