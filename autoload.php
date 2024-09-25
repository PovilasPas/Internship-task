<?php

spl_autoload_register(
    function (string $class): void {
        $prefix = "App\\";
        $baseDir = __DIR__ . "/src/";
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relativeClass = substr($class, $len);
        $path = $baseDir . str_replace("\\", "/", $relativeClass) . ".php";
        if (file_exists($path)) {
            include $path;
        }
    }
);
