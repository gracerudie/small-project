<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  echo json_encode(["error" => ""]);
  exit;
}

//Helpers 
function getRequestInfo() {
  return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj) {
  header('Content-Type: application/json');
  echo $obj;
}

function returnWithError($err, $status = 400) {
  http_response_code($status);
  $retValue = json_encode([
    "id" => 0,
    "firstName" => "",
    "lastName" => "",
    "error" => $err
  ]);
  sendResultInfoAsJson($retValue);
  exit;
}

function returnWithInfo($firstName, $lastName, $id) {
  $retValue = json_encode([
    "id" => (int)$id,
    "firstName" => $firstName,
    "lastName" => $lastName,
    "error" => ""
  ]);
  sendResultInfoAsJson($retValue);
  exit;
}

$inData = getRequestInfo();

$firstName = trim($inData["firstName"] ?? "");
$lastName  = trim($inData["lastName"]  ?? "");
$login     = trim($inData["login"]     ?? "");
$password  = trim($inData["password"]  ?? "");

if ($firstName === "" || $lastName === "" || $login === "" || $password === "") {
  returnWithError("Missing required fields");
}

require_once __DIR__ . "/db.php"; // must define $conn (mysqli)

if (!isset($conn) || !($conn instanceof mysqli)) {
  returnWithError("DB connection not initialized");
}

//Check for duplicate username
$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login = ?");
if (!$stmt) {
  returnWithError("Prepare failed: " . $conn->error);
}
if (!$stmt->bind_param("s", $login)_
