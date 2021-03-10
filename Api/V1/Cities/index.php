<?php

include_once "../../../loader.php";
use App\Services\CityService;
use App\Services\CityValidationService;
use App\Utilities\Response;
use App\Utilities\CacheUtility;



$requestMethod=$_SERVER['REQUEST_METHOD'];
$cityService=new CityService();
$cityValidationService=new CityValidationService();


switch ($requestMethod){
    case 'GET':

        CacheUtility::start();
        #get from query string
        $provinceId= $_GET['provinceId'] ?? null;
        $fields=$_GET['fields'] ?? '*';
        $table='city';

        #for single responsibility purposes, the validator class should be seperated
        if(!is_null($provinceId))
            if(! $cityValidationService->validProvinceId(['provinceId'=>$provinceId]))
                Response::respondAndDie(['Error: Invalid Province Id'],Response::HTTP_NOT_FOUND);

        if ($fields!= '*'){
            if (! $cityValidationService->filterFieldsValidation(['fields'=>$fields , 'table'=> $table]))
                Response::respondAndDie(['Error: Invalid field  name'],Response::HTTP_NOT_FOUND);

        }

        $requestData=[
            'provinceId'=>$provinceId,
            'fields'=>$_GET['fields'] ?? '*',
            'page'=>$_GET['page'] ?? null ,
            'orderBy'=>$_GET['orderBy'] ?? null ,
            'pageSize'=>$_GET['pageSize'] ?? null,
        ];
        $result= $cityService->getCities($requestData);
        echo Response::respond($result,Response::HTTP_OK);
        CacheUtility::end();
        break;

    case 'POST':
        #get from body
        $requestBody=json_decode(file_get_contents("php://input"),true);

        if(!$cityValidationService->isValidCity($requestBody))
            Response::respondAndDie(['Error: Invalid Data'],Response::HTTP_NOT_ACCEPTABLE);

        if(!$cityValidationService->validProvinceId(['provinceId'=>$requestBody['provinceId']]))
            Response::respondAndDie(['Error: Invalid Province Id'],Response::HTTP_NOT_FOUND);

        $result= $cityService->createCity($requestBody);
        Response::respondAndDie($result,Response::HTTP_CREATED);
        break;


    case 'DELETE':
        #get from query string
        $cityId= $_GET['cityId'] ?? null;

        #for single responsibility purposes, the validator class should be seperated
        if(! $cityValidationService->validCityId(['cityId'=>$cityId]))
            Response::respondAndDie(['Error: Invalid City Id'],Response::HTTP_NOT_FOUND);

        $requestData=[
            'cityId'=>$cityId
        ];
        $result= $cityService->deleteCity($requestData);
        Response::respondAndDie($result,Response::HTTP_OK);
        break;


    case 'PUT':
        #get from body
        $requestBody=json_decode(file_get_contents("php://input"),true);
        if(!$cityValidationService->isValidCity($requestBody))
            Response::respondAndDie(['Error: Invalid Data'],Response::HTTP_NOT_ACCEPTABLE);

        if(!$cityValidationService->validCityId(['cityId'=>$requestBody['cityId']]))
            Response::respondAndDie(['Error: Invalid City Id'],Response::HTTP_NOT_FOUND);


        $result= $cityService->updateCity($requestBody);
        Response::respondAndDie($result,Response::HTTP_OK);
        break;

    default:
        Response::respondAndDie(["Invalid Request method"],Response::HTTP_METHOD_NOT_ALLOWED);
}


