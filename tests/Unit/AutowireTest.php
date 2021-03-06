<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\Tests\Samples\Colors\Palette;
use Borodulin\Container\Tests\Samples\Common\OptionalParam;
use Borodulin\Finder\ClassFinder;
use PHPUnit\Framework\TestCase;

class AutowireTest extends TestCase
{
    public function testVariadic(): void
    {
        $fileFinder = (new ClassFinder())
            ->addPath(__DIR__.'/../Samples/Colors');
        $container = (new ContainerBuilder())
            ->setClassFinder($fileFinder)
            ->build();

        /** @var Palette $palette */
        $palette = $container->get(Palette::class);

        $this->assertCount(3, $palette->getColors());
    }

    public function testOptional(): void
    {
        $fileFinder = (new ClassFinder())
            ->addPath(__DIR__.'/../Samples/Common');

        $container = (new ContainerBuilder())
            ->setClassFinder($fileFinder)
            ->build();
        $instance = $container->get(OptionalParam::class);
        $this->assertInstanceOf(OptionalParam::class, $instance);
    }
}
