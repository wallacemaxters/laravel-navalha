<?php

namespace WallaceMaxters\Navalha;

abstract class Component
{
    public array $data;

    abstract public function render();

    protected function set(string $key, mixed $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function call(string $method, ...$args)
    {
        if (! method_exists($this, $method)) {
            abort(400, 'Invalid method handler');
        }

        $this->$method(...$args);

        return [
            'data'      => $this->data,
            'component' => class_basename(static::class)
        ];
    }
}
