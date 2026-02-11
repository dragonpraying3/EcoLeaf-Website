<?php
session_start();
include_once '../backend/database.php';

if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'organizer') {
    exit("Unauthorized");
}

$organizerId = $_SESSION['user']['organizerId'];
$eventId = $_POST['eventId'] ?? null;


$plants = (int)($_POST['plants'] ?? 0);
$waste  = (int)($_POST['waste'] ?? 0); 
$recycled = (int)($_POST['recycled'] ?? 0);

if (!$eventId) {
    exit("Missing Event ID");
}


$sql_sum = "INSERT INTO summary (treePlanted, wasteCollected, recycleItem, submittedDate, eventId, organizerId) 
            VALUES (?, ?, ?, NOW(), ?, ?)";
$stmt_sum = $conn->prepare($sql_sum);

$stmt_sum->bind_param("iiiii", $plants, $waste, $recycled, $eventId, $organizerId);

if ($stmt_sum->execute()) {
    
    $sql_update = "UPDATE evention SET status = 'end' WHERE eventId = ? AND organizerId = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $eventId, $organizerId);
    $stmt_update->execute();
    echo "success";
} else {
    echo "Error: " . $conn->error;
}
?>