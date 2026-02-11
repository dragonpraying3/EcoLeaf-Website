<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../student/reward_redemption.php?error=invalid_request");
    exit;
}

// allow only student role
if ($_SESSION['user']['role'] !== 'student') {
    header("Location: ../student/access_portal.php?error=not_allowed");
    exit;
}

$studentId = (int)$_SESSION['user']['studentId'];
$redemptId = isset($_POST['redemptId']) ? (int)$_POST['redemptId'] : 0;

// basic validation
if ($redemptId <= 0) {
    header("Location: ../student/reward_redemption.php?error=collect_not_found");
    exit;
}

// verify redemption belongs to this student and is ready for collection
$sqlFetch = "SELECT redemptId, status
             FROM redemption
             WHERE redemptId = $redemptId
               AND studentId = $studentId
             LIMIT 1";

$resultFetch = $conn->query($sqlFetch);

if (!$resultFetch || $resultFetch->num_rows !== 1) {
    header("Location: ../student/reward_redemption.php?error=collect_not_found");
    exit;
}

$redemption = $resultFetch->fetch_assoc();

// only allow marking successful redemptions as collected
if ($redemption['status'] === 'collected') {
    header("Location: ../student/reward_redemption.php?success=collected");
    exit;
}

if ($redemption['status'] !== 'success') {
    header("Location: ../student/reward_redemption.php?error=collect_invalid_status");
    exit;
}

$sqlUpdate = "UPDATE redemption
              SET status = 'collected'
              WHERE redemptId = $redemptId
                AND studentId = $studentId
                AND status = 'success'";

$conn->query($sqlUpdate);

if ($conn->affected_rows <= 0) {
    header("Location: ../student/reward_redemption.php?error=collect_failed");
    exit;
}

header("Location: ../student/reward_redemption.php?success=collected");
exit;