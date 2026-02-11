<?php
ob_start();
include_once '../backend/database.php';
include_once '../backend/notifyLogic.php';
include_once '../topbar.php';

if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    die("Access denied");
}

$adminId = (int)($_SESSION['user']['adminId'] ?? 0);
$userId  = (int)($_SESSION['user']['userId'] ?? 0);

if ($adminId <= 0 && $userId > 0) {
    $st = $conn->prepare("SELECT adminId FROM admin WHERE userId=? LIMIT 1");
    $st->bind_param("i", $userId);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $adminId = (int)($row['adminId'] ?? 0);
    $st->close();
}

if ($adminId <= 0) {
    die("AdminId not found (session/database). Fix your login session.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = (int)($_POST['eventId'] ?? 0);
    $action  = $_POST['action'] ?? '';
    $leaf    = (int)($_POST['leaf'] ?? 0);

    if ($eventId > 0 && in_array($action, ['approved','reject'], true)) {
        $st = $conn->prepare("
            SELECT e.eventName, o.userId AS organizerUserId
            FROM evention e
            JOIN organizer o ON e.organizerId = o.organizerId
            WHERE e.eventId=? LIMIT 1
        ");
        $st->bind_param("i", $eventId);
        $st->execute();
        $info = $st->get_result()->fetch_assoc();
        $st->close();

        $eventName       = $info['eventName'] ?? 'Your event';
        $organizerUserId = (int)($info['organizerUserId'] ?? 0);

        if ($action === 'approved') {
            if ($leaf <= 0) {
                die("Leaf must be > 0");
            }
            $stmt = $conn->prepare("
                UPDATE evention
                SET status='approved',
                    leaf=?,
                    approvedAt=NOW(),
                    adminId=?
                WHERE eventId=? AND status='pending'
            ");
            $stmt->bind_param("iii", $leaf, $adminId, $eventId);
        } else {
            $stmt = $conn->prepare("
                UPDATE evention
                SET status='reject',
                    approvedAt=NOW(),
                    adminId=?
                WHERE eventId=? AND status='pending'
            ");
            $stmt->bind_param("ii", $adminId, $eventId);
        }

        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected > 0 && $organizerUserId > 0) {
            $title = ($action === 'approved') ? "Event Approved" : "Event Rejected";
            if ($action === 'approved') {
                $message = "Your event '{$eventName}' has been approved.";
            } else {
                $rejectReason = trim($_POST['rejectReason'] ?? '');
                if ($rejectReason === '') $rejectReason = 'No reason given';
                $message = "Your event '{$eventName}' has been rejected. Reason: {$rejectReason}";
            }
            insertNotify($conn, $title, $message, $organizerUserId);
        }
    }
    header("Location: eventApproval.php");
    exit;
}

$sql = "
    SELECT  e.eventId,
            e.eventName,
            e.description,
            e.category,
            e.eventDate,
            e.startTime,
            e.endTime,
            e.venue,
            e.capacity,
            e.leaf,
            e.imageFile,
            u.name AS organizerName,
            o.club AS organizerClub
    FROM evention e
    LEFT JOIN organizer o ON e.organizerId = o.organizerId
    LEFT JOIN users u ON o.userId = u.userId
    WHERE e.status='pending'
    ORDER BY e.createAt DESC
";
$result = $conn->query($sql);
$pendingCount = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Approval Queue</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/EcoLeaf/assets/css/topbarDesign.css">
    <link rel="stylesheet" href="/EcoLeaf/assets/css/eventApprovalDesign.css?v=<?= time() ?>">
</head>

<body>

    <div class="events-page">
        <section class="events-hero">
            <div class="hero-text">
                <h1>Event Approval Queue</h1>
                <p>Review and approved pending event proposals from organizers</p>
            </div>
        </section>

        <div class="pending-box">
            <div class="pending-title">Pending Event Proposal (<?= $pendingCount ?>)</div>

            <?php if ($result && $result->num_rows > 0): ?>
            <section class="events-grid">
                <?php while ($e = $result->fetch_assoc()): ?>
                <article class="event-card">
                    <div class="event-banner"
                        style="background-image:url('/EcoLeaf/organizer/image/<?= htmlspecialchars($e['imageFile'] ?? '') ?>');">
                        <span class="status-pill">Pending</span>
                    </div>

                    <div class="event-body">
                        <div class="event-header">
                            <div class="event-name"><?= htmlspecialchars($e['eventName'] ?? '') ?></div>
                        </div>

                        <p class="event-desc"><?= htmlspecialchars($e['description'] ?? '') ?></p>

                        <div class="event-meta-2col">
                            <div class="meta-col">
                                <div class="meta-line">
                                    <i class='bx bx-user'></i>
                                    <span><?= htmlspecialchars($e['organizerName'] ?? '—') ?></span>
                                </div>
                                <div class="meta-line">
                                    <i class='bx bx-group'></i>
                                    <span><?= htmlspecialchars($e['organizerClub'] ?? '—') ?></span>
                                </div>
                                <div class="meta-line">
                                    <i class='bx bx-location-plus'></i>
                                    <span><?= htmlspecialchars($e['venue'] ?? '—') ?></span>
                                </div>
                            </div>
                            <div class="meta-col">
                                <div class="meta-line">
                                    <i class='bx bx-category'></i>
                                    <span><?= htmlspecialchars($e['category'] ?? '—') ?></span>
                                </div>
                                <div class="meta-line">
                                    <i class='bx bx-calendar-week'></i>
                                    <span><?= htmlspecialchars($e['eventDate'] ?? '—') ?></span>
                                </div>
                                <div class="meta-line">
                                    <i class='bx bx-clock-10'></i>
                                    <span>
                                        <?= htmlspecialchars(date("g:i a", strtotime($e['startTime'] ?? '00:00:00'))) ?>
                                        -
                                        <?= htmlspecialchars(date("g:i a", strtotime($e['endTime'] ?? '00:00:00'))) ?>
                                    </span>
                                </div>
                                <div class="meta-line">
                                    <i class='bx bx-user-check'></i>
                                    <span><?= (int)($e['capacity'] ?? 0) ?></span>
                                </div>
                            </div>
                        </div>

                        <?php
                      $leafVal = (int)($e['leaf'] ?? 0);
                      if ($leafVal <= 0) $leafVal = 50;
                    ?>

                        <form method="POST" class="approval-form">
                            <input type="hidden" name="eventId" value="<?= (int)($e['eventId'] ?? 0) ?>">
                            <input type="hidden" name="action" class="action-field" value="approved">
                            <input type="hidden" name="rejectReason" class="reject-reason-field" value="">

                            <div class="points-wrap">
                                <label class="points-label"><i class='bx bx-leaf'></i> Green Points leaf</label>
                                <input type="number" name="leaf" class="points-input" value="<?= $leafVal ?>" min="0"
                                    required>
                            </div>

                            <div class="approval-actions">
                                <button type="submit" class="btn-approve">Approved</button>
                                <button type="button" class="btn-reject" onclick="openRejectModal(this)">Reject</button>
                            </div>
                        </form>
                    </div>
                </article>
                <?php endwhile; ?>
            </section>
            <?php else: ?>
            <div class="no-pending">
                <p>No pending event proposals to review at this moment.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="rejectModal" class="reject-modal" aria-hidden="true"
        style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7);">
        <div class="reject-box"
            style="background:#fff; width:90%; max-width:450px; margin:8% auto; padding:30px; border-radius:15px; position:relative;">
            <h3 style="margin-bottom:20px; color:#333;">Select Rejection Reason</h3>

            <div class="reject-options" style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
                <label class="reject-option"><input type="radio" name="reject_choice" value="Capacity Reached"> Capacity
                    Reached</label>
                <label class="reject-option"><input type="radio" name="reject_choice"
                        value="Does Not Meet Event Criteria" checked> Does Not Meet Event Criteria</label>
                <label class="reject-option"><input type="radio" name="reject_choice"
                        value="Unable to Approve at This Time"> Unable to Approve at This Time</label>
                <label class="reject-option"><input type="radio" name="reject_choice" value="Others"> Others</label>
            </div>

            <textarea id="rejectOther" class="reject-other" placeholder="If Others, type here..."
                style="width:100%; height:100px; padding:12px; border:1px solid #ddd; border-radius:8px; margin-bottom:20px; resize:none; font-family:inherit;"></textarea>

            <div class="modal-btns" style="display:flex; justify-content:flex-end; gap:15px;">
                <button type="button" class="reject-cancel" onclick="closeRejectModal()"
                    style="padding:10px 20px; background:#f0f0f0; border:none; border-radius:8px; cursor:pointer;">Cancel</button>
                <button type="button" class="reject-send" onclick="submitReject()"
                    style="padding:10px 20px; background:#e74c3c; color:white; border:none; border-radius:8px; cursor:pointer;">Confirm
                    Rejection</button>
            </div>
        </div>
    </div>

    <script>
    let currentActiveForm = null;

    function openRejectModal(btn) {

        currentActiveForm = btn.closest('.approval-form');
        document.getElementById('rejectModal').style.display = 'block';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    function submitReject() {
        if (!currentActiveForm) return;

        let selectedReason = document.querySelector('input[name="reject_choice"]:checked').value;
        if (selectedReason === 'Others') {
            let otherText = document.getElementById('rejectOther').value.trim();
            selectedReason = otherText !== '' ? otherText : 'Others';
        }


        currentActiveForm.querySelector('.action-field').value = 'reject';
        currentActiveForm.querySelector('.reject-reason-field').value = selectedReason;


        currentActiveForm.submit();
    }


    window.onclick = function(event) {
        let modal = document.getElementById('rejectModal');
        if (event.target == modal) {
            closeRejectModal();
        }
    }
    </script>

</body>

</html>
<?php 
ob_end_flush(); 
?>