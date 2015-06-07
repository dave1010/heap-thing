<?php

class BetterValuesFinderTest extends \PHPUnit_Framework_TestCase
{
    private function getComparator()
    {
        return function ($a, $b) {
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };
    }

    public function testEmpty()
    {
        $p = new BetterValuesFinder([], $this->getComparator());
        $this->assertEquals([], $p->getBest());
    }

    public function testOne()
    {
        $item = 1;
        $box = new Box($item);
        $list = [$box];
        $p = new BetterValuesFinder($list, $this->getComparator());
        $this->assertEquals([$item], $p->getBest());
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

        $p = new BetterValuesFinder($items, $this->getComparator());
        $this->assertEquals([1, 2, 10], $p->getBest());
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

        $p = new BetterValuesFinder($items, $this->getComparator());
        $this->assertEquals([1, 2, 3], $p->getBest());
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

        $p = new BetterValuesFinder($items, $this->getComparator());
        $this->assertEquals([1, 2, 3], $p->getBest());

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

        $p = new BetterValuesFinder($items, $this->getComparator());
        $this->assertEquals([1, 2, 3], $p->getBest());

    }


    public function testNoIterations()
    {
        $items = [new Box(1), new Box(2)];
        $p = new BetterValuesFinder($items, $this->getComparator());
        $this->assertEquals([1, 2], $p->getBest());

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

        $p = new BetterValuesFinder($items, $this->getComparator(), 4);
        $this->assertEquals([1, 2, 3, 4], $p->getBest());
    }


    public function testAttemptLargerMaxSize()
    {
        $generator = function () {
            yield 2;
        };

        $items = [
            new Box(1, $generator()),
        ];

        $p = new BetterValuesFinder($items, $this->getComparator(), 10);
        $this->assertEquals([1, 2], $p->getBest());
    }


    public function testSmallerMaxSize()
    {
        $items = [
            new Box(1),
            new Box(2),
            new Box(3),
        ];

        $p = new BetterValuesFinder($items, $this->getComparator(), 2);
        $this->assertEquals([1, 2], $p->getBest());
    }
}
