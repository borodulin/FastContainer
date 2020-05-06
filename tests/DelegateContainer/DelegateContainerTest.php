<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\DelegateContainer;

use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\Tests\DelegateContainer\Samples\Bar;
use Borodulin\Container\Tests\DelegateContainer\Samples\Foo;
use PHPUnit\Framework\TestCase;

class DelegateContainerTest extends TestCase
{
    public function testDelegate(): void
    {
        $container1 = (new ContainerBuilder(null, [
            Bar::class,
        ]))->build();

        $container2 = (new ContainerBuilder(null, [
            Foo::class,
        ]))->build();

        $container2->delegate($container1);

        $foo = $container2->get(Foo::class);
        $this->assertInstanceOf(Foo::class, $foo);

        $bar = $container2->get(Bar::class);
        $this->assertInstanceOf(Bar::class, $bar);

        $container1->delegate($container2);

        $foo = $container1->get(Foo::class);
        $this->assertInstanceOf(Foo::class, $foo);

        $bar = $container1->get(Bar::class);
        $this->assertInstanceOf(Bar::class, $bar);
    }
}