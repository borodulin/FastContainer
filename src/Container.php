<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Borodulin\Container\Autowire\AutowireItemInterface;
use Borodulin\Container\Autowire\AutowireItemResolver;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $items;
    /**
     * @var AutowireItemResolver
     */
    private $autowireItemResolver;
    /**
     * @var ContainerInterface[]
     */
    private $delegates = [];
    /**
     * @var ContainerInterface|null
     */
    private $parameterBag;

    public function __construct(array $items)
    {
        $this->items = $items;
        $this->items[ContainerInterface::class] = $this;
        $this->autowireItemResolver = new AutowireItemResolver($this);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->items[$id])) {
            if ($this->items[$id] instanceof AutowireItemInterface) {
                $this->items[$id] = $this->autowireItemResolver->resolve($this->items[$id]);
            }

            return $this->items[$id];
        }

        foreach ($this->delegates as $container) {
            if ($container->has($id)) {
                $this->items[$id] = $container->get($id);

                return $this->items[$id];
            }
        }
        throw new NotFoundException($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (isset($this->items[$id])) {
            return true;
        }

        foreach ($this->delegates as $container) {
            if ($container->has($id)) {
                return true;
            }
        }

        return false;
    }

    public function getIds(): array
    {
        return array_keys($this->items);
    }

    public function delegate(ContainerInterface $container): self
    {
        $this->delegates[] = $container;

        return $this;
    }

    public function getParameterBag(): ?ContainerInterface
    {
        return $this->parameterBag;
    }

    public function setParameterBag(?ContainerInterface $parameterBag): self
    {
        $this->parameterBag = $parameterBag;

        return $this;
    }
}
