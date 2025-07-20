<?php

if (!function_exists('config')) {
    function config(string $key)
    {
        static $configs = [];

        $file = __DIR__ . '/../../config/' . explode('.', $key)[0] . '.php';
        if (!isset($configs[$file]) && file_exists($file)) {
            $configs[$file] = require $file;
        }

        $segments = explode('.', $key);
        array_shift($segments);

        return array_reduce($segments, fn($carry, $segment) => $carry[$segment] ?? null, $configs[$file] ?? []);
    }
}
