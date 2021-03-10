<?php

namespace App\Utilities;

use App\Utilities\Response;


class CacheUtility{
    protected static $cacheFile;
    protected static $cacheEnabled= CACHE_ENABLED;
    const EXPIRE_TIME = 3600;


    public static function init(){
        self::$cacheFile= CACHE_DIR. md5($_SERVER['REQUEST_URI']). ".json";
        if ($_SERVER['REQUEST_METHOD']!='GET')
            self::$cacheEnabled=0;
    }

    public static function cacheExists()
    {
        return (file_exists(self::$cacheFile) and ((time()- self::EXPIRE_TIME)< filemtime(self::$cacheFile)));
    }

    public static function start()
    {
        self::init();
        if(!self::$cacheEnabled)
            return;

        if(self::cacheExists()){
            Response::setHeaders();    //preventing two rimes set headers
            readfile(self::$cacheFile);
            exit();
        }
        ob_start();

    }

    #make cache file
    public static function end()
    {
        self::init();
        if(!self::$cacheEnabled)
            return;

        $cacheFile=fopen(self::$cacheFile,'w');
        fwrite($cacheFile,ob_get_contents());
        fclose($cacheFile);

        #the below is alternative but less secure
        #file_put_contents($cacheFile,ob_get_contents());

        ob_end_flush();

    }

    public static function flush(){
        $files= glob(CACHE_DIR.'*'); //get all file names in array in this directory
        foreach ($files as $file){
            if (is_file($file))
                unlink($file);
        }

    }

}

