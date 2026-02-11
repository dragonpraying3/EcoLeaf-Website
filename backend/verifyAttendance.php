<?php
session_start();
include_once 'database.php';

// 设置时区
date_default_timezone_set('Asia/Kuala_Lumpur');

// 开启错误报告（调试用，正式上线可关闭）
ini_set('display_errors', 1);
error_reporting(E_ALL);

$studentId = (int)($_SESSION['user']['studentId'] ?? 0);
$eventId   = (int)($_POST['eventId'] ?? 0);
$otp       = trim($_POST['otp'] ?? '');

/**
 * 带有 Flash 消息的跳转函数
 */
function back_with_msg(string $msg): void {
    $_SESSION['flash_msg'] = $msg;
    header("Location: /EcoLeaf/student/myEvents.php", true, 303);
    exit;
}

// 基础验证
if ($studentId <= 0) back_with_msg("Please login again.");
if ($eventId <= 0) back_with_msg("Invalid event.");
if ($otp === '' || !ctype_digit($otp)) back_with_msg("Invalid OTP input.");

$conn->begin_transaction();

try {
    /* 1) 检查报名状态 (兼容 'approve' 和 'approved') */
    $stmt = $conn->prepare("SELECT status FROM participation WHERE studentId=? AND eventId=? LIMIT 1");
    $stmt->bind_param("ii", $studentId, $eventId);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();

    if (!$p || !in_array($p['status'], ['approve', 'approved'])) {
        throw new Exception("You are not approved for this event.");
    }

    /* 2) 检查签到记录：允许记录存在但状态为 'absent' 时继续验证 */
    $stmt = $conn->prepare("SELECT attId, status FROM attendance WHERE studentId=? AND eventId=? LIMIT 1");
    $stmt->bind_param("ii", $studentId, $eventId);
    $stmt->execute();
    $existingAtt = $stmt->get_result()->fetch_assoc();

    // 只有当状态已经是 'present' 时才拦截
    if ($existingAtt && $existingAtt['status'] === 'present') {
        throw new Exception("Attendance already verified.");
    }

    /* 3) 获取活动详情与 OTP 验证 */
    $stmt = $conn->prepare("SELECT OTP_code, leaf, eventDate, startTime, endTime FROM evention WHERE eventId=? LIMIT 1");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $ev = $stmt->get_result()->fetch_assoc();

    if (!$ev) {
        throw new Exception("Event not found.");
    }

    // OTP 匹配检查
    if ((string)$ev['OTP_code'] !== $otp) {
        throw new Exception("Attendance not matched. Please check the OTP.");
    }

    /* 4) 时间窗口检查（支持跨午夜） */
    $tz = new DateTimeZone('Asia/Kuala_Lumpur');
    $eventStart = new DateTime($ev['eventDate'].' '.$ev['startTime'], $tz);
    $eventEnd   = new DateTime($ev['eventDate'].' '.$ev['endTime'], $tz);

    if ($eventEnd < $eventStart) $eventEnd->modify('+1 day');
    $now = new DateTime('now', $tz);

    if ($now < $eventStart) throw new Exception("Event not started yet.");
    if ($now > $eventEnd)   throw new Exception("Event has ended.");

    /* 5) 计算应得叶子分数 (Total Leaf / Approved Participants) */
    $totalLeaf = (float)($ev['leaf'] ?? 0);
    $stmt = $conn->prepare("SELECT COUNT(*) AS totalJoined FROM participation WHERE eventId = ? AND status IN ('approve', 'approved')");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $cnt = $stmt->get_result()->fetch_assoc();
    $totalJoined = (int)($cnt['totalJoined'] ?? 0);

    $gainLeaves = ($totalJoined > 0) ? (int)floor($totalLeaf / $totalJoined) : 0;

    /* 6) 执行数据库更新或插入 */
    if ($existingAtt) {
        // 如果原本是 'absent'，将其更新为 'present' 并存入分数
        $stmt = $conn->prepare("UPDATE attendance SET createAt = NOW(), pointsAwards = ?, status = 'present' WHERE attId = ?");
        $stmt->bind_param("di", $gainLeaves, $existingAtt['attId']);
    } else {
        // 如果完全没有记录，则新建
        $stmt = $conn->prepare("INSERT INTO attendance (createAt, pointsAwards, status, studentId, eventId) VALUES (NOW(), ?, 'present', ?, ?)");
        $stmt->bind_param("dii", $gainLeaves, $studentId, $eventId);
    }
    $stmt->execute();

    
    if ($gainLeaves > 0) {
        $stmt = $conn->prepare("UPDATE student SET leaf = leaf + ? WHERE studentId=?");
        $stmt->bind_param("ii", $gainLeaves, $studentId);
        $stmt->execute();
    }

    $conn->commit();
    back_with_msg("🎉 Attendance verified! Total joined: {$totalJoined}. You gained {$gainLeaves} Leaf 🌱");

} catch (Exception $e) {
    $conn->rollback();
    back_with_msg($e->getMessage());
}
?>