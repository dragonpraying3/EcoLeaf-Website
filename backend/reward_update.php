<?php
session_start();
require_once "database.php";

// must be post
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/rewards_management.php");
    exit;
}

$rewardId = $_POST['rewardId'] ?? null;
$action = $_POST['action'] ?? null;
$points = $_POST['pointsRequired'] ?? null;
$quantity = $_POST['quantity'] ?? null;

// delete
if ($action === 'delete') {
    
    $sql = "UPDATE rewarditem 
            SET
                status = 'inactive'
            WHERE rewardId = $rewardId";

    if ($conn->query($sql)) {
        header("Location: ../admin/rewards_management.php?success=reward_deleted");
    } else {
        header("Location: ../admin/rewards_management.php?error=db_error");
    }
    exit;
}

// valid
if (!is_numeric($points) || (int)$points < 0) {
    header("Location: ../admin/rewards_management.php?error=invalid_points");
    exit;
}

if (!is_numeric($quantity) || (int)$quantity < 0) {
    header("Location: ../admin/rewards_management.php?error=invalid_stock");
    exit;
}

// get leaf and stock
$points = (int)$points;
$quantity = (int)$quantity;

// auto status by stock
if ($quantity === 0) {
    $status = 'inactive';
} else {
    $status = 'active';
}

// update sql
$sql = "UPDATE rewarditem
    SET
        pointsRequired = $points,
        quantity = $quantity,
        status = '$status'
    WHERE rewardId = $rewardId";

if ($conn->query($sql)) {
    header("Location: ../admin/rewards_management.php?success=reward_updated");
} else {
    header("Location: ../admin/rewards_management.php?error=db_error");
}
exit;
?>