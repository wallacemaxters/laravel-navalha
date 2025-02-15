
## Generate a Component

```bash
php artisan navalha:make-component Product
```

This command will generate `resources/views/navalha/product.blade.php` view and `app/Navalha/Product.php` class.

## Example with pagination

```php

namespace App\Navalha;

use App\Models\Product;
use WallaceMaxters\Navalha\Component;

class Products extends Component
{
    protected function data(): array
    {
        return [
            'products' => [],
            'page'   => 1
        ];
    }

    public function updateProducts(int $page)
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
<div class="duration-500" x-bind:class="{'opacity-0' : !$busy.updateProducts}">
    Loading
</div>
<div class="space-y-4" x-init="$call('updateProducts', 1)">
    <template x-for="item in products.data">
        <div class="bg-neutral-300 p-3 rounded-lg shadow">
            <span x-text="`this is a text ${item.name}`" />
        </div>
    </template>
    <button 
        x-show="products.to !== null" 
        class="ui-button" 
        x-on:click="$call('updateProducts', page + 1)"
        x-text="`Next Page ${page + 1}`">
    </button>
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
