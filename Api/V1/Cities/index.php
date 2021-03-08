<?php

include_once "../../../loader.php";
use App\Services\CityService;
use App\Services\CityValidationService;
use App\Utilities\Response;

$requestMethod=$_SERVER['REQUEST_METHOD'];
$cityService=new CityService();

switch ($requestMethod){
    case 'GET':
        $cityValidationService=new CityValidationService();
        #get from query string
        $provinceId= $_GET['provinceId'] ?? null;

        #for single responsibility purposes, the validator class should be seperated
        if(! $cityValidationService->isValidProvince(['provinceId'=>$provinceId]))
            Response::respondAndDie(['Error: Invalid Province Id'],Response::HTTP_NOT_FOUND);

        $requestData=[
            'provinceId'=>$provinceId
        ];
        $result= $cityService->getCities($requestData);
        Response::respondAndDie($result,Response::HTTP_OK);

    case 'POST':
        $cityValidationService=new CityValidationService();
        #get from body
        $requestBody=json_decode(file_get_contents("php://input"),true);

        if(!$cityValidationService->isValidCity($requestBody))
            Response::respondAndDie(['Error: Invalid Data'],Response::HTTP_NOT_ACCEPTABLE);

        if(!$cityValidationService->isValidProvince(['provinceId'=>$requestBody['provinceId']]))
            Response::respondAndDie(['Error: Invalid Province Id'],Response::HTTP_NOT_FOUND);

        $result= $cityService->createCity($requestBody);
        Response::respondAndDie($result,Response::HTTP_CREATED);

    case 'DELETE':
        Response::respondAndDie(["DELETE Request"],Response::HTTP_OK);
        break;

    case 'PUT':
        Response::respondAndDie(["PUT Request"],Response::HTTP_OK);
        break;

    default:
        Response::respondAndDie(["Invalid Request method"],Response::HTTP_METHOD_NOT_ALLOWED);
}


