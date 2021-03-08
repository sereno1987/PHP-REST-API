<?php
namespace App\Services;

class CityService{
    public  static function getCities($data){
        $result=getCities($data);
        return $result;
    }

    public  static function createCity($data){
        $result= addCity($data);
        return $result;
    }

    public  static function deleteCity($data){
        $result= deleteCity($data);
        return $result;
    }

}
