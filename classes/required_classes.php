<?php

spl_autoload_register(function ($class) {
    $parts = explode('_', $class, 2);
    if ($parts[0] == 'Poll') {
        include_once __DIR__ . '/' . $parts[1] . '.php';
    }
});
