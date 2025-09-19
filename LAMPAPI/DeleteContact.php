<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  echo json_encode(["error" => ""]);
  exit;
}

// Helpers
function getRequestInfo() {
  return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj) {
  header('Content-Type: application/json');
  echo $obj;
}

function returnWithError($err, $status = 400) {
  http_response_code($status);
  sendResultInfoAsJson(json_encode(["error" => $err]));
  exit;
}

function returnWithInfo($deleted = 1) {
  sendResultInfoAsJson(json_encode(["deleted" => (int)$deleted, "error" => ""]));
  exit;
}

$inData   = getRequestInfo();
$contactId = intval($inData["contactId"] ?? 0);
$userId    = intval($inData["userId"] ?? 0);

if ($contactId <= 0 || $userId <= 0) {
  returnWithError("Missing or invalid contactId/userId");
}

require_once __DIR__ . "/db.php"; // must define $conn (mysqli)
if (!isset($conn) || !($conn instanceof mysqli)) {
  returnWithError("DB connection not initialized");
}

//Delete (scoped to owner)
$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=? AND UserID=?");
if (!$stmt) {
  $conn->close();
  returnWithError("Prepare failed: " . $conn->error);
}
if (!$stmt->bind_param("ii", $contactId, $userId)) {
  $stmt->close();
  $conn->close();
  returnWithError("Bind failed: " . $stmt->error);
}
if (!$stmt->execute()) {
  $err = $stmt->error ?: "Delete failed";
  $stmt->close();
  $conn->close();
  returnWithError($err);
}

if ($stmt->affected_rows > 0) {
  $stmt->close();
  $conn->close();
  returnWithInfo(1);
} else {
  $stmt->close();
  $conn->close();
  returnWithError("No matching contact found", 404);
}
