<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('_navalha/update', function (Request $request) {
    $data = $request->validate(['component' => 'required|string', 'method' => 'required|string', 'args' => 'nullable|array']);

    $class = '\\App\\Navalha\\' . $data['component'];

    $result = app($class)->call($data['method'], ...$data['args'] ?? []);

    return response()->json($result);
});
