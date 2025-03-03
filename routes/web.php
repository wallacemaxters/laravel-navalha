<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use WallaceMaxters\Navalha\RequestData;

Route::post('_navalha/update', function (Request $request) {

    $data = new RequestData($request);

    $class = '\\App\\Navalha\\' . $data->component();

    if (!class_exists($class)) {
        abort(404, 'The component ' . $class . ' not found.');
    }

    $result = app($class)($data);

    return $result;
});
