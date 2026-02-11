<?php
session_start();
include_once '../backend/database.php';

if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit("Access denied");
}

$eventId = (int)($_POST['eventId'] ?? 0);
$action  = trim($_POST['action'] ?? '');   // 'approve' or 'reject'
$leaf    = (int)($_POST['leaf'] ?? 0);
$reason  = trim($_POST['reason'] ?? '');

if ($eventId <= 0) exit("Invalid event");

if ($action === 'approve') {
    if ($leaf <= 0) exit("Invalid leaf");

    $stmt = $conn->prepare("UPDATE evention SET status='approve', leaf=? WHERE eventId=?");
    $stmt->bind_param("ii", $leaf, $eventId);
    $stmt->execute();
    echo "success";
    exit;
}

if ($action === 'reject') {

    $stmt = $conn->prepare("UPDATE evention SET status='reject' WHERE eventId=?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();

    echo "success";
    exit;
}

exit("Invalid action");