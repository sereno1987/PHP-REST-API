<?php
#not oop, so we have to include this one
include_once __DIR__ . "/App/iran.php";
include_once __DIR__ . "/App/Utilities/helpers.php";


spl_autoload_register(function ($class){
     #but using PSR4

    $classFile = __DIR__.DIRECTORY_SEPARATOR."$class.php";
    $classFile  = str_replace('\\', '/', $classFile);

    if(file_exists($classFile) && is_readable($classFile)){
        include $classFile;
//        echo "my autoloader => $class used".PHP_EOL;
    }
    else {
        echo "$classFile not found!"."\n";
    }

});
