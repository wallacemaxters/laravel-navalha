<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('_navalha/update', function (Request $request) {
    $data = $request->validate([
        'component' => 'required|string',
        'method'    => 'required|string',
        'args'      => 'nullable|array',
    ]);

    $class = '\\App\\Navalha\\' . $data['component'];

    if (!class_exists($class)) {
        abort(404, 'The component ' . $class . ' not found.');
    }

    $result = app($class)->call($data['method'], ...$data['args'] ?? []);

    return $result;
});
