<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$inData = json_decode(file_get_contents('php://input'), true);

$userId = intval($inData["userId"] ?? 0);
$search = trim($inData["search"] ?? "");

if ($userId < 1) { returnWithError("Invalid userId"); }

require_once __DIR__ . "/db.php"; // must set $conn (mysqli)

if (!isset($conn) || !($conn instanceof mysqli)) {
  returnWithError("DB connection not initialized");
}

$like = "%".$search."%";

$sql = "SELECT ID AS contactId, FirstName, LastName, Phone, Email
        FROM Contacts
        WHERE UserID = ?
          AND (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?)
        ORDER BY LastName, FirstName";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  returnWithError("Prepare failed: ".$conn->error);
}

if (!$stmt->bind_param("issss", $userId, $like, $like, $like, $like)) {
  returnWithError("Bind failed: ".$stmt->error);
}

if (!$stmt->execute()) {
  returnWithError("Execute failed: ".$stmt->error);
}

$result = $stmt->get_result();
$results = [];
while ($row = $result->fetch_assoc()) {
  $results[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(["results" => $results, "error" => ""]);

function returnWithError($err) {
  http_response_code(400);
  echo json_encode(["results" => [], "error" => $err]);
  exit;
}

