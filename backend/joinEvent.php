<?php
session_start();
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/database.php';

$studentId = (int)($_SESSION['user']['studentId'] ?? 0);
$eventId   = (int)($_POST['eventId'] ?? 0);

if ($studentId <= 0) { echo "ERR|Please login again."; exit; }
if ($eventId <= 0)   { echo "ERR|Invalid event."; exit; }


$stmt = $conn->prepare("SELECT organizerId FROM evention WHERE eventId=? LIMIT 1");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) { echo "ERR|Event not found."; exit; }

$organizerId = (int)$event['organizerId'];

/* 2) If already requested, return current status */
$stmt = $conn->prepare("SELECT status FROM participation WHERE studentId=? AND eventId=? LIMIT 1");
$stmt->bind_param("ii", $studentId, $eventId);
$stmt->execute();
$exist = $stmt->get_result()->fetch_assoc();

if ($exist) {
    // Always return OK|pending / OK|approve / OK|reject
    echo "OK|" . $exist['status'];
    exit;
}

/* 3) Insert as pending */
$stmt = $conn->prepare("
    INSERT INTO participation (registerDate, status, studentId, eventId, organizerId)
    VALUES (NOW(), 'pending', ?, ?, ?)
");
$stmt->bind_param("iii", $studentId, $eventId, $organizerId);

if ($stmt->execute()) {
    echo "OK|pending";
} else {
    echo "ERR|DB error: " . $conn->error;
}
exit;