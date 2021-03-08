<?php
try {
    $pdo = new PDO("mysql:dbname=iran;host=localhost", 'root', 'root');
    $pdo->exec("set names utf8;");
    // echo "Connection OK!";
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

#==============  Simple Validators  ================
function isValidCity($data){
    if(empty($data['provinceId']) or !is_numeric($data['provinceId']))
        return false;
    return empty($data['name']) ? false : true;
}

function isValidProvince($data){
    return empty($data['name']) ? false : true;
}
function isProvinceExists($data){
    global $pdo;
    if(!(($data['provinceId']) and is_numeric($data['provinceId'])))
        return false;
    $province_id = $data['provinceId'];

    $sql = "select id from province ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_OBJ);
    $result=[];
    foreach ($records as $record){
        $item=$record->id;
        $result[]=$item;
    }
    $finalResult=in_array ($province_id, $result);
    var_dump($finalResult);
    return $finalResult;

}

#================  Read Operations  =================
#get all Cities or Cities related to a province
function getCities($data = null){
    global $pdo;
    $province_id = $data['provinceId'] ?? null;
    $where = '';
    if(!is_null($province_id)){
        $where = "where province_id = {$province_id} ";
    }
    $sql = "select * from city $where";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $records;
}

#get all Provinces
function getProvinces($data = null){
    global $pdo;
    $sql = "select * from province";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $records;
}

#================  Create Operations  =================
function addCity($data){
    global $pdo;
    if(!isValidCity($data)){
        return false;
    }
    $sql = "INSERT INTO `city` (`province_id`, `name`) VALUES (:province_id, :name);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':province_id'=>$data['provinceId'],':name'=>$data['name']]);
    return $stmt->rowCount();
}

function addProvince($data){
    global $pdo;
    if(!isValidProvince($data)){
        return false;
    }
    $sql = "INSERT INTO `province` (`name`) VALUES (:name);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name'=>$data['name']]);
    return $stmt->rowCount();
}


#================  Update Operations  =================
function changeCityName($city_id,$name){
    global $pdo;
    $sql = "update city set name = '$name' where id = $city_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}
function changeProvinceName($province_id,$name){
    global $pdo;
    $sql = "update province set name = '$name' where id = $province_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}

#================  Delete Operations  =================
function deleteCity($city_id){
    global $pdo;
    $sql = "delete from city where id = $city_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}
function deleteProvince($province_id){
    global $pdo;
    $sql = "delete from province where id = $province_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}

