<?php

namespace WallaceMaxters\Navalha;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Wallacemaxters\Navalha\Concerns\HasData;

abstract class Component implements ArrayAccess, JsonSerializable
{
    use HasData;

    abstract public function render();

    public function mount(): void {}

    final public function __checkIfValidMethod(string $method)
    {
        if (! method_exists($this, $method) || in_array($method, ['render', 'setUp', 'mount', 'set'])) {
            abort(400, 'Invalid method handler');
        }
    }

    final public function __invoke(RequestData $data)
    {
        $method = $data->method();

        $this->__checkIfValidMethod($method);

        $result = $this->$method(...$data->getAsArguments());

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

        App::call([$this, 'mount']);
    }
}
