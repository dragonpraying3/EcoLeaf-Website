<?php
session_start();
include_once '../backend/database.php';

if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'organizer') {
    http_response_code(403);
    exit("Forbidden");
}

$organizerId = $_SESSION['user']['organizerId'];

$eventId   = $_POST['eventId'] ?? null;
$studentId = $_POST['studentId'] ?? null;
$status    = $_POST['status'] ?? null;
$reason    = $_POST['reason'] ?? '';

if (!$eventId || !$studentId || !$status) {
    http_response_code(400);
    exit("Missing parameters");
}

if (!in_array($status, ['approved','rejected'])) {
    http_response_code(400);
    exit("Invalid status");
}

$sql_event = "SELECT eventName FROM evention WHERE eventId = ? AND organizerId = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("ii", $eventId, $organizerId);
$stmt_event->execute();
$result_event = $stmt_event->get_result();
if($result_event->num_rows === 0){
    exit("Event not found");
}
$event = $result_event->fetch_assoc();
$eventName = $event['eventName'];

$sql = "UPDATE participation SET status = ?, organizerId = ? WHERE eventId = ? AND studentId = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siii", $status, $organizerId, $eventId, $studentId);

if(!$stmt->execute()){
    exit("Database update failed: ".$stmt->error);
}


if ($status === 'approved') {
    $check_sql = "SELECT attId FROM attendance WHERE studentId = ? AND eventId = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $studentId, $eventId);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows === 0) {
        $sql_att = "INSERT INTO attendance (studentId, eventId, status, pointsAwards, createAt) VALUES (?, ?, 'absent', 0, NOW())";
        $stmt_att = $conn->prepare($sql_att);
        $stmt_att->bind_param("ii", $studentId, $eventId);
        $stmt_att->execute();
    }
}

$sql_get_user = "SELECT userId FROM student WHERE studentId = ?";
$stmt_user = $conn->prepare($sql_get_user);
$stmt_user->bind_param("i", $studentId);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$studentRow = $res_user->fetch_assoc();

if ($studentRow) {
    $targetUserId = $studentRow['userId'];
$title = "Event Participation {$status} - {$eventName}";
$message = $status === 'approved' 
            ? "Your participation request for '{$eventName}' has been approved!" 
            : "Your participation request for '{$eventName}' has been rejected. Reason: ".$reason;

$sql_notify = "INSERT INTO notification (title, message, isRead, sendAt, userId) VALUES (?, ?, 0, NOW(), ?)";
    $stmt_notify = $conn->prepare($sql_notify);
    $stmt_notify->bind_param("ssi", $title, $message, $targetUserId);
    $stmt_notify->execute();
}

echo "success";


