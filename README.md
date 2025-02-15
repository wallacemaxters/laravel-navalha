# Navalha 
🇧🇷🇧🇷🇧🇷🇧🇷🇧🇷🇧🇷🇧🇷🇧🇷🇧🇷🇧🇷🇧🇷

Navalha (is a joke with "Razor" name in portuguese) is a small framework written for Laravel, which allows binding of Laravel data on the server-side to AlpineJS variables.

## How to Install

```bash
composer require wallacemaxters/laravel-navalha
```

## Generate a Component

```bash
php artisan navalha:make-component Products
```

This command will generate `resources/views/navalha/products.blade.php` view and `app/Navalha/Products.php` class.

## Example with pagination

```php

namespace App\Navalha;

use App\Models\Product;
use WallaceMaxters\Navalha\Component;

class Products extends Component
{
    public function __construct()
    {
        $this->paginate(1);
    }

    protected function data(): array
    {
        return [
            'page' => 1
        ];
    }

    public function paginate(int $page)
    {
        $this['products'] = Product::query()->paginate(3, page: $page);
        $this['page'] = $page;
    }

    public function render()
    {
        return view("navalha.products");
    }
}

```


```html
<div x-bind:class="{'opacity-0 duration-1000' : !$busy('paginate')}">
    Carregando...
</div>
<div class="space-y-4" >
    <template x-for="item in products.data">
        <div class="bg-neutral-300 p-3 rounded-lg shadow">
            <span x-text="`this is a text ${item.name}`" />
        </div>
    </template>

    <nav class="flex gap-2">
        <template x-for="i in products.last_page">
            <a  x-bind:class="{'opacity-50' : i === products.current_page}"
                x-on:click="$navalha.paginate(i)"
                x-text="i"
                class="text-blue-500 cursor-pointer p-2"></a>
        </template>
    </nav>
</div>

```

```blade
<html>
<head>
    @vite(['resources/css/app.css'])
    @navalhaScripts
</head>
<body>
    <div class="max-w-6xl mx-auto p-8">
        <h1 class="text-3xl font-bold">Navalha Example</h1>
        <x-navalha::component name="Products" />
    </div>
</body>
</html>
```

## Alpine special variables and methods

<table>
    <thead>
        <tr>
            <th>Variable</th>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>$busy(String|undefined)</td>
        <td>Function</td>
        <td>Indicates that an specifiy or any method is called from the server.</td>
    </tr>
    <tr>
        <td>$call(String)</td>
        <td>Function</td>
        <td>Call a public method of component in Laravel side.</td>
    </tr>
    <tr>
        <td>$navalha</td>
        <td>Object</td>
        <td>This a special object from Navalha that allows make methods call like $call().</td>
    </tr>
    </tbody>
</table>


## Handle Server Errors

Your can detect errors on call Navalha method with `navalha-errors` event.

Example:

```html
<div x-on:navalha-error="console.log($event.detail)">
    <button x-on:click="$navalha.notExistsMethod()">Test</button>
</div>
```
