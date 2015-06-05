<?php

class ProcessorTest extends \PHPUnit_Framework_TestCase {

    public function testEmpty()
    {
        $p = new Processor([]);
        $this->assertEquals([], $p->process());
    }

    public function testOne()
    {
        $item = 1;
        $box = new Box($item);
        $list = [$box];
        $p = new Processor($list);
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

        $p = new Processor($items);
        $this->assertEquals([1, 2, 10], $p->process());
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

        $p = new Processor($items);
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

        $p = new Processor($items);
        $this->assertEquals([1, 2, 3], $p->process());

    }


//    public function testNoIterations()
//    {
//        $items = [1, 2];
//        $p = new Processor($items);
//        $this->assertEquals($items, $p->process());
//
//    }

    public function testLargerMaxSize()
    {}


    /*
    public function testStub()
    {
        $dependency = $this->prophesize(\Foo\DependencyInterface::class);
        $dependency->boolGenerator(1)->willReturn(true);
        $foo = new Foo($dependency->reveal());

        $this->assertTrue($foo->baz());
    }

    public function testMock()
    {
        $dependency = $this->prophesize(\Foo\DependencyInterface::class);
        $foo = new Foo($dependency->reveal());

        $dependency->boolGenerator(1)->shouldBeCalled();

        $foo->baz();
    }

    public function testSpy()
    {
        $dependency = $this->prophesize(\Foo\DependencyInterface::class);
        $foo = new Foo($dependency->reveal());

        $foo->baz();

        $dependency->boolGenerator(1)->shouldHaveBeenCalled();
    }
    */
}
