<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$inData = json_decode(file_get_contents('php://input'), true);

$userId    = intval($inData["userId"] ?? 0);
$firstName = trim($inData["firstName"] ?? "");
$lastName  = trim($inData["lastName"] ?? "");
$phone     = trim($inData["phone"] ?? "");
$email     = trim($inData["email"] ?? "");

if ($userId < 1 || $firstName === "" || $lastName === "" || $phone === "" || $email === "") {
  returnWithError("Missing required fields");
}

require_once __DIR__ . "/db.php"; // must set $conn (mysqli)

if (!isset($conn) || !($conn instanceof mysqli)) {
  returnWithError("DB connection not initialized");
}

$sql = "INSERT INTO Contacts (UserID, FirstName, LastName, Phone, Email) VALUES (?,?,?,?,?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  returnWithError("Prepare failed: ".$conn->error);
}

if (!$stmt->bind_param("issss", $userId, $firstName, $lastName, $phone, $email)) {
  returnWithError("Bind failed: ".$stmt->error);
}

if (!$stmt->execute()) {
  returnWithError("Execute failed: ".$stmt->error);
}

$id = $stmt->insert_id;

$stmt->close();
$conn->close();

returnWithInfo(["contactId" => $id]);

function returnWithError($err) {
  http_response_code(400);
  echo json_encode(["error" => $err]);
  exit;
}
function returnWithInfo($arr) {
  $arr["error"] = "";
  echo json_encode($arr);
  exit;
}

