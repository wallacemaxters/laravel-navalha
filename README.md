## Example

```php

namespace App\Navalha;

use App\Models\Produto;
use WallaceMaxters\Navalha\Component;

class Produtos extends Component
{
    protected function data(): array
    {
        return [
            'produtos' => [],
            'pagina'   => 1
        ];
    }

    public function updateProdutos(int $page)
    {
        $this['produtos'] = Produto::query()->paginate(3, page: $page);
        $this['pagina']   = $page;
    }

    public function render()
    {
        return view("navalha.produtos");
    }
}

```


```html
<div class="duration-500" x-bind:class="{'opacity-0' : !$busy.updateProdutos}">
    Carregando
</div>
<div class="space-y-4" x-init="$call('updateProdutos', 1)">
    <template x-for="item in produtos.data">
        <div class="bg-neutral-300 p-3 rounded-lg shadow">
            <span x-text="`this is a text ${item.nome}`" />
        </div>
    </template>
    <button x-show="produtos.to !== null" class="ui-button" x-on:click="$call('updateProdutos', pagina + 1)"
        x-text="`Próxima página ${pagina + 1}`">
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
        <x-navalha::component name="Produtos" />
    </div>
</body>
</html>
```
