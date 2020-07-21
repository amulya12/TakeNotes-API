<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}
// INCLUDING DATABASE 
require __DIR__.'/classes/Database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// IF REQUEST METHOD IS NOT POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

// CHECKING EMPTY FIELDS
elseif(!isset($data->description) 
    || empty(trim($data->description))
    ):

    $fields = ['fields' => ['description']];
    $returnData = msg(0,422,'Please Fill the Description!',$fields);
else:
    try{
    $description=trim($data->description);
    $insert_query = "INSERT INTO `notes`(`description`) VALUES(:description)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bindValue(':description', htmlspecialchars(strip_tags($description)),PDO::PARAM_STR);
    $insert_stmt->execute();
    $returnData = msg(1,201,'Note successfully created.');   
    
    }
    catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }
endif;
echo json_encode($returnData);  