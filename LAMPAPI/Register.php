<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); echo json_encode(["error" => ""]); exit; }

function getRequestInfo() { return json_decode(file_get_contents('php://input'), true); }
function sendJson($arr, $status=200) { http_response_code($status); echo json_encode($arr); exit; }
function errorOut($msg,$status=400){ sendJson(["id"=>0,"firstName"=>"","lastName"=>"","error"=>$msg],$status);} 
function successOut($id,$f,$l){ sendJson(["id"=>(int)$id,"firstName"=>$f,"lastName"=>$l,"error"=>""],200);} 

$inData = getRequestInfo();
$firstName = trim($inData['firstName'] ?? '');
$lastName  = trim($inData['lastName']  ?? '');
$login     = trim($inData['login']     ?? '');
$password  = trim($inData['password']  ?? '');

if($firstName===''||$lastName===''||$login===''||$password==='') { errorOut('Missing required fields'); }
if(strlen($login) > 50 || strlen($firstName) > 50 || strlen($lastName) > 50) { errorOut('Field too long'); }

// TODO: Upgrade Users.Password column to hold hashed values (VARCHAR(255)).
$hash = password_hash($password, PASSWORD_DEFAULT);

require_once __DIR__ . '/db.php';
if (!isset($conn) || !($conn instanceof mysqli)) { errorOut('DB connection not initialized',500); }

// Duplicate check
$stmt = $conn->prepare('SELECT ID FROM Users WHERE Login=?');
if(!$stmt){ errorOut('Prepare failed: '.$conn->error,500); }
if(!$stmt->bind_param('s',$login)){ $stmt->close(); errorOut('Bind failed: '.$stmt->error,500);} 
if(!$stmt->execute()){ $err=$stmt->error; $stmt->close(); errorOut('Execute failed: '.$err,500);} 
$res = $stmt->get_result();
if($res && $res->num_rows>0){ $stmt->close(); errorOut('Username already exists',409);} 
$stmt->close();

// Insert
$stmt = $conn->prepare('INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?,?,?,?)');
if(!$stmt){ errorOut('Prepare failed: '.$conn->error,500);} 
if(!$stmt->bind_param('ssss',$firstName,$lastName,$login,$hash)){ $stmt->close(); errorOut('Bind failed: '.$stmt->error,500);} 
if(!$stmt->execute()){ $err=$stmt->error; $stmt->close(); errorOut('Execute failed: '.$err,500);} 
$newId = $stmt->insert_id; $stmt->close(); $conn->close();
successOut($newId,$firstName,$lastName);
?>

