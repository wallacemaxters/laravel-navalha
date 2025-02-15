@props(['name', 'tag' => 'div'])
@php
$instance = app('\\App\\Navalha\\' . $name);

$instance->setUp();

$params = [
    'component' => $name,
    'data'      => $instance->jsonSerialize(),
    'csrf'      => csrf_token()
];

@endphp
<{{ $tag }} x-data="__navalha_component__(@js($params))">
{!! $instance->render() !!}
</{{ $tag }}>
