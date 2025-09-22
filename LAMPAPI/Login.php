<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD']==='OPTIONS'){ http_response_code(200); echo json_encode(["error"=>""]); exit; }

function getRequestInfo(){ return json_decode(file_get_contents('php://input'), true); }
function sendJson($arr,$status=200){ http_response_code($status); echo json_encode($arr); exit; }

$inData = getRequestInfo();
$login = trim($inData['login'] ?? '');
$password = trim($inData['password'] ?? '');
if($login===''||$password===''){ sendJson(["id"=>0,"firstName"=>"","lastName"=>"","error"=>'Missing credentials'],400);} 

require_once __DIR__.'/db.php';
if(!isset($conn) || !($conn instanceof mysqli)){ sendJson(["id"=>0,"firstName"=>"","lastName"=>"","error"=>'DB init failed'],500);} 

// Fetch user by login
$stmt = $conn->prepare('SELECT ID, FirstName, LastName, Password FROM Users WHERE Login=?');
if(!$stmt){ sendJson(["id"=>0,"firstName"=>"","lastName"=>"","error"=>'Prepare failed'],500);} 
if(!$stmt->bind_param('s',$login)){ $stmt->close(); sendJson(["id"=>0,"firstName"=>"","lastName"=>"","error"=>'Bind failed'],500);} 
if(!$stmt->execute()){ $err=$stmt->error; $stmt->close(); sendJson(["id"=>0,"firstName"=>"","lastName"=>"","error"=>$err],500);} 
$res = $stmt->get_result();
if($row = $res->fetch_assoc()){
  $stored = $row['Password'];
  $valid = password_verify($password, $stored) || $stored === $password; // allow legacy plain-text until migrated
  if($valid){
    sendJson(["id"=>(int)$row['ID'],"firstName"=>$row['FirstName'],"lastName"=>$row['LastName'],"error"=>""],200);
  }
}
$stmt->close(); $conn->close();
sendJson(["id"=>0,"firstName"=>"","lastName"=>"","error"=>'Invalid login'],401);
?>

