<?php

namespace WallaceMaxters\Navalha;

use ArrayAccess;
use JsonSerializable;
use Wallacemaxters\Navalha\Concerns\HasData;

abstract class Component implements ArrayAccess, JsonSerializable
{
    use HasData;

    abstract public function render();

    final public function call(string $method, ...$args)
    {
        if (! method_exists($this, $method) || in_array($method, ['call', 'render', 'setUp'])) {
            abort(400, 'Invalid method handler');
        }

        $result = $this->$method(...$args);

        if ($result === null) {

            return [
                'data'      => $this->data,
                'component' => class_basename(static::class)
            ];
        }

        return $result;

    }

    final public function setUp()
    {
        $this->data = [...$this->data, ...$this->data()];
    }
}
