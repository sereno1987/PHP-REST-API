<?php
namespace App\Services;

class CityValidationService{
    public  static function isValidProvince($data){
        $result= isProvinceExists($data);
        return $result;
    }

    public  static function isValidCity($data){
        $result= isValidCity($data);
        return $result;
    }

}
