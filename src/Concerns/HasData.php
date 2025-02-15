<?php
namespace Wallacemaxters\Navalha\Concerns;

trait HasData
{
    protected array $data = [];

    public function offsetExists($key): bool
    {
        return isset($this->data[$key]);
    }

    public function offsetGet($key): mixed
    {
        return $this->data[$key];
    }

    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->data[] = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    public function offsetUnset($key): void
    {
        unset($this->data[$key]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    protected function set(string $key, mixed $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    protected function data(): array
    {
        return [];
    }
}
