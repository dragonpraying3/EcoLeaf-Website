<?php
session_start();
require_once 'database.php';

// check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../student/reward_redemption.php?error=invalid_request");
    exit;
}

// check role
if ($_SESSION['user']['role'] !== 'student') {
    header("Location: ../student/access_portal.php?error=not_allowed");
    exit;
}

// get Id
$rewardId = (int)$_POST['rewardId'];
$studentId = (int)$_SESSION['user']['studentId'];

// get point from frontend
// use this for double checking
// prevent user use devtoolss change the point
// use frontendpoint compare database point is equal or not
$frontEndPoint = (int)$_POST['pointRequired'];

$sqlRewardInfo = "SELECT pointsRequired, quantity, status
                  FROM rewarditem
                  WHERE rewardId = $rewardId
                  LIMIT 1";

$resultRewardInfo= $conn -> query($sqlRewardInfo);

// result found more than 1 or 0 then error
if ($resultRewardInfo->num_rows !== 1) {
    header("Location: ../student/reward_redemption.php?error=reward_not_found");
    exit;
}

$reward = $resultRewardInfo->fetch_assoc();

$dbPointRequired = (int)$reward['pointsRequired'];
$quantity = (int)$reward['quantity'];
$status = $reward['status'];

// double checking status
if ($status !== 'active') {
    header("Location: ../student/reward_redemption.php?error=reward_inactive");
    exit;
}

// check stock
if ($quantity <= 0) {
    header("Location: ../student/reward_redemption.php?error=out_of_stock");
    exit;
}

// check frontend and database point required is it same
if ($frontEndPoint > 0 && $frontEndPoint !== $dbPointRequired) {
    header("Location: ../student/reward_redemption.php?error=invalid_reward_data");
    exit;
}

// get student leaf
$sqlStudentLeaf = "SELECT leaf
                   FROM student
                   WHERE studentId = $studentId
                   LIMIT 1";

$resultStudentLeaf = $conn->query($sqlStudentLeaf);

// double checking student exist
if (!$resultStudentLeaf || $resultStudentLeaf->num_rows !== 1) {
    header("Location: ../student/reward_redemption.php?error=student_not_found");
    exit;
}

$student = $resultStudentLeaf->fetch_assoc();
$currentLeaf = (int)$student['leaf'];

// check is it enough points 
if ($currentLeaf < $dbPointRequired) {
    header("Location: ../student/reward_redemption.php?error=not_enough_points");
    exit;
}

// check reward is redeem by same person or not
$sqlCheckRedeemed = "SELECT redemptId, status
                     FROM redemption
                     WHERE studentId = $studentId
                       AND rewardId = $rewardId
                     LIMIT 1";

$resultCheck = $conn->query($sqlCheckRedeemed);

if ($resultCheck && $resultCheck->num_rows > 0) {
    header("Location: ../student/reward_redemption.php?error=already_redeemed");
    exit;
}

// deduct leaf
$sqlDeductLeaf = "UPDATE student
                  SET leaf = leaf - $dbPointRequired
                  WHERE studentId = $studentId
                    AND leaf >= $dbPointRequired";

$conn->query($sqlDeductLeaf);


if ($conn->affected_rows <= 0) {
    header("Location: ../student/reward_redemption.php?error=points_changed");
    exit;
}

// deduct stock
$sqlDeductStock = "UPDATE rewarditem
                   SET quantity = quantity - 1,
                       status = CASE
                                  WHEN quantity - 1 <= 0 THEN 'inactive'
                                  ELSE status
                                END
                   WHERE rewardId = $rewardId
                   AND status = 'active'
                   AND quantity > 0";

$conn->query($sqlDeductStock);

// if doesnt affect any row then refund the points
if ($conn->affected_rows <= 0) {

    // refund points 
    $sqlRefundLeaf = "UPDATE student
                      SET leaf = leaf + $dbPointRequired
                      WHERE studentId = $studentId";
    $conn->query($sqlRefundLeaf);

    header("Location: ../student/reward_redemption.php?error=stock_changed");
    exit;
}

// insert redemption record 
$sqlInsertRedemption = "INSERT INTO redemption (redemptAt, status, studentId, rewardId)
                        VALUES (NOW(), 'success', $studentId, $rewardId)";

$conn->query($sqlInsertRedemption);

header("Location: ../student/reward_redemption.php?success=redeemed");
exit;
?>