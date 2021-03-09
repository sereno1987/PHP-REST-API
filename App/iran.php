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
    if ($data['cityId']){
        if(empty($data['cityId']) or !is_numeric($data['cityId']))
            return false;
        return empty($data['name']) ? false : true;
    }
    if ($data['cityId']){
        if(empty($data['provinceId']) or !is_numeric($data['provinceId']))
            return false;
        return empty($data['name']) ? false : true;
    }

}

function isValidProvince($data){
    return empty($data['name']) ? false : true;
}

function provinceExists($data){
    global $pdo;
    if(is_null($data['provinceId']) and !(is_numeric($data['provinceId'])))
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
    return $finalResult;

}

function cityExists($data){
    global $pdo;
    if(!(($data['cityId']) and is_numeric($data['cityId'])))
        return false;
    $city_id = $data['cityId'];
    $sql = "select id from city ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_OBJ);
    $result=[];
    foreach ($records as $record){
        $item=$record->id;
        $result[]=$item;
    }
    $finalResult=in_array ($city_id, $result);
    return $finalResult;
}

function filterFieldsValidation($data){
    global $pdo;
    $table=$data['table'];
    $fields = $data['fields'] ?? '';
    $validFields="DESCRIBE $table";
    $validFieldsStmt = $pdo->prepare($validFields);
    $validFieldsStmt->execute();
    $validFieldsStmtRecords = $validFieldsStmt->fetchAll(PDO::FETCH_OBJ);
    foreach ($validFieldsStmtRecords as $item){
        $result[]=$item->Field;
    }

    $fieldsExplode = explode(",", $fields);
    foreach ($fieldsExplode as $field){
        if (!(in_array($field, $result))){
            return false;
        }
    }

    return true;
}


#================  Read Operations  =================
#get all Cities or Cities related to a province
function getCities($data = null){
    global $pdo;
    $province_id = $data['provinceId'] ?? null;
    $fields = $data['fields'] ?? '*';
    $orderBy = $data['orderBy'] ?? null;
    $page = $data['page'] ?? null;
    $pageSize = $data['pageSize'] ?? null;
    $limit='';
    if ((is_numeric($page) and is_numeric($pageSize))){
        $start=(($page-1)*$pageSize);
        $limit="LIMIT $start, $pageSize";
    }
    $where = '';
    $orderbyString = '';
    if(!is_null($province_id)){
        $where = "where province_id = {$province_id} ";
    }
    if(!is_null($orderBy)){
        $orderbyString = "order by $orderBy";
    }

    $sql = "select $fields from city $where $orderbyString $limit";
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
function changeCityName($data){
    global $pdo;
    $sql = "update city set name =:name where id =:city_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':city_id'=>$data['cityId'],':name'=>$data['name']]);
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
function deleteCity($data){
    global $pdo;
    $city_id = $data['cityId'] ?? null;
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

