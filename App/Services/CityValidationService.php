<?php
namespace App\Services;

class CityValidationService{
    public  static function validProvinceId($data){
        $result= provinceExists($data);
        return $result;
    }

    public  static function isValidCity($data){
        $result= isValidCity($data);
        return $result;
    }

    public  static function validCityId($data){
        $result= cityExists($data);
        return $result;
    }

    public  static function filterFieldsValidation($data){
        $result= filterFieldsValidation($data);
        return $result;
    }

}
