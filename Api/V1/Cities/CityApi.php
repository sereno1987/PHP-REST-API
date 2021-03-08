<?php

include_once "../../../loader.php";
use \App\Services\CityService;
use \App\Utilities\Response;

$requestMethod=$_SERVER['REQUEST_METHOD'];
switch ($requestMethod){
    case 'GET':
        $provinceId= isset($_GET['provinceId']) ? $_GET['provinceId'] : null;
        $requestData=[
            'provinceId'=>$provinceId
        ];
        $result= getCities($requestData);
        Response::respondAndDie($result,Response::HTTP_OK);
        break;

    case 'POST':
        Response::respondAndDie(["POST Request"],Response::HTTP_OK);
        break;

    case 'DELETE':
        Response::respondAndDie(["DELETE Request"],Response::HTTP_OK);
        break;

    case 'PUT':
        Response::respondAndDie(["PUT Request"],Response::HTTP_OK);
        break;

    default:
        Response::respondAndDie(["Invalid Request method"],Response::HTTP_METHOD_NOT_ALLOWED);
}


