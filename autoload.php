<?php

spl_autoload_register(function(string $class) {
   $path = str_replace("\\", DIRECTORY_SEPARATOR, $class);
   $filePath = $path . ".php";
   if(file_exists($filePath)) {
       include $filePath;
   }
});