<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\Tests\Samples\Common\Bar;
use Borodulin\Container\Tests\Samples\Common\Foo;
use Borodulin\Finder\ClassFinder;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class CacheTest extends TestCase
{
    private $cache;

    public function testCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->method('set')
            ->willReturnCallback(function ($name, $value): void {
                $this->cache = [$name, $value];
            })
        ;
        $fileFinder = (new ClassFinder())
            ->addPath(__DIR__.'/../Samples/Common')
            ->addPath(__DIR__.'/../Samples/Colors')
        ;
        $builder = (new ContainerBuilder())
            ->setClassFinder($fileFinder)
            ->setConfig([
                'test.closure' => function (Bar $bar) {
                    return new Foo($bar);
                },
                'test.alias' => 'test.closure',
            ])
            ->setCache($cache);
        $builder->build();
        $this->assertIsArray($this->cache);

        $cache
            ->method('get')
            ->willReturn($this->cache[1])
        ;
        $cache
            ->method('has')
            ->willReturn(true)
        ;
        $cache
            ->method('set')
            ->willThrowException(new ContainerException())
        ;

        $container = $builder->build();

        $this->assertIsArray($container->getIds());
    }
}
