<?php
include_once '../topbar.php';
include_once '../backend/database.php';

date_default_timezone_set('Asia/Kuala_Lumpur'); 

if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'organizer') {
    header("Location: /EcoLeaf/index.php");
    exit();
}

$organizerId = $_SESSION['user']['organizerId'];

$sql_pending = "SELECT * FROM evention WHERE organizerId = ? AND status = 'pending' ORDER BY eventDate ASC";
$stmt = $conn->prepare($sql_pending);
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$pendingEvents = $stmt->get_result();

$sql_approved = "SELECT * FROM evention WHERE organizerId = ? AND status = 'approved' ORDER BY eventDate ASC";
$stmt = $conn->prepare($sql_approved);
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$approvedEvents = $stmt->get_result();

$sql_past = "SELECT * FROM evention WHERE organizerId = ? AND status = 'end' ORDER BY eventDate DESC";
$stmt = $conn->prepare($sql_past);
$stmt->bind_param("i", $organizerId);
$stmt->execute();
$pastEvents = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/myEvent.css">
</head>

<body>

    <div class="container">
        <div class="page-header">
            <h2>My Events</h2>
            <p>Manage your sustainability events</p>
        </div>

        <div class="section-title">Pending Approval (<?= $pendingEvents->num_rows ?>)</div>
        <?php while ($row = $pendingEvents->fetch_assoc()) { ?>
        <div class="event-card">
            <span class="status">Pending</span>
            <h4><?= htmlspecialchars($row['eventName']) ?></h4>
            <p class="event-description"><?= htmlspecialchars($row['description']) ?></p>
            <div class="event-grid">
                <p><i class='bx bx-calendar-week'></i><?= $row['eventDate'] ?></p>
                <p><i class='bx bx-time'></i><?= date("g:i A", strtotime($row['startTime'])) ?> -
                    <?= date("g:i A", strtotime($row['endTime'])) ?></p>
                <p><i class='bx bx-location-plus'></i><?= htmlspecialchars($row['venue']) ?></p>
                <p><i class='bx bx-purchase-tag-alt'></i><?= htmlspecialchars($row['category']) ?></p>
                <p><i class='bx bx-leaf'></i><?= $row['leaf'] ?></p>
                <p><i class='bx bx-group'></i><?= $row['capacity'] ?></p>
            </div>
        </div>
        <?php } ?>

        <div class="section-title">Approved & Active (<?= $approvedEvents->num_rows ?>)</div>
        <?php while ($row = $approvedEvents->fetch_assoc()) { 
        $eventId = $row['eventId'];
        $sql_p = "SELECT p.studentId, u.name, u.email, s.leaf, e.venue, e.eventDate, e.startTime, e.endTime 
                  FROM participation p 
                  JOIN student s ON p.studentId = s.studentId 
                  JOIN users u ON s.userId = u.userId 
                  JOIN evention e ON p.eventId = e.eventId
                  WHERE p.eventId = ? AND p.status = 'pending'";
        $stmt_p = $conn->prepare($sql_p);
        $stmt_p->bind_param("i", $eventId);
        $stmt_p->execute();
        $participants = $stmt_p->get_result();

        $eventEndTime = strtotime($row['eventDate'] . ' ' . $row['endTime']);
        $showSummary = (time() >= $eventEndTime);
    ?>
        <div class="event-card">
            <span class="status approved">Approved</span>
            <h4><?= htmlspecialchars($row['eventName']) ?></h4>
            <p class="event-description"><?= htmlspecialchars($row['description']) ?></p>
            <div class="event-grid">
                <p><i class='bx bx-calendar-week'></i><?= $row['eventDate'] ?></p>
                <p><i class='bx bx-time'></i><?= date("g:i A", strtotime($row['startTime'])) ?> -
                    <?= date("g:i A", strtotime($row['endTime'])) ?></p>
                <p><i class='bx bx-location-plus'></i><?= htmlspecialchars($row['venue']) ?></p>
                <p><i class='bx bx-purchase-tag-alt'></i><?= htmlspecialchars($row['category']) ?></p>
                <p><i class='bx bx-leaf'></i><?= $row['leaf'] ?></p>
                <p><i class='bx bx-group'></i><?= $row['capacity'] ?></p>
            </div>

            <div class="event-actions">
                <button onclick="togglePanel('otp-<?= $eventId ?>')">Verify Attendance</button>
                <button onclick="togglePanel('req-<?= $eventId ?>')">Participants
                    requests(<?= $participants->num_rows ?>)</button>
                <?php if ($showSummary): ?>
                <button onclick="togglePanel('sum-<?= $eventId ?>')">Submit summary</button>
                <?php endif; ?>
            </div>

            <div id="otp-<?= $eventId ?>" class="dropdown-panel">
                <a class="panel-hide" onclick="togglePanel('otp-<?= $eventId ?>')">Hide</a>
                <div class="panel-header">Verify Participant Attendance</div>
                <div class="otp-display-box">
                    <?php 
                    $otp = $row['OTP_code'] ?: "000";
                    foreach(str_split((string)$otp) as $digit) echo "<span>$digit</span>";
                    ?>
                </div>
            </div>

            <div id="req-<?= $eventId ?>" class="dropdown-panel">
                <a class="panel-hide" onclick="togglePanel('req-<?= $eventId ?>')">Hide</a>
                <div class="panel-header">View Participants Requests</div>
                <div class="req-list">
                    <?php if($participants->num_rows > 0): ?>
                    <?php while($p = $participants->fetch_assoc()): ?>
                    <div class="req-card" id="row-<?= $eventId ?>-<?= $p['studentId'] ?>">
                        <div class="req-card-header">
                            <div class="name"><?= htmlspecialchars($p['name']) ?></div>
                        </div>
                        <div class="info-line"><i class='bx bx-people-handshake'></i>
                            <?= htmlspecialchars($p['name']) ?></div>
                        <div class="info-line"><i class='bx bx-location-plus'></i> <?= htmlspecialchars($p['venue']) ?>
                        </div>
                        <div class="info-line"><i class='bx bx-calendar-week'></i>
                            <?= htmlspecialchars($p['eventDate']) ?></div>
                        <div class="info-line"><i class='bx bx-clock-10'></i>
                            <?= date("g:i a", strtotime($p['startTime'])) ?> -
                            <?= date("g:i a", strtotime($p['endTime'])) ?></div>

                        <div class="btn-dark-group">
                            <button class="req-btn-dark"
                                onclick="updateStatus(<?= $eventId ?>, <?= $p['studentId'] ?>, 'approved')">✓
                                Approve</button>
                            <button class="req-btn-dark" onclick="openReject(<?= $eventId ?>, <?= $p['studentId'] ?>)">✕
                                Reject</button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <p style="width:100%; text-align:center; padding:20px;">No pending requests.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="sum-<?= $eventId ?>" class="dropdown-panel">
                <a class="panel-hide" onclick="togglePanel('sum-<?= $eventId ?>')">Hide</a>
                <div class="panel-header">Post-Event Summary</div>
                <form onsubmit="return handleSum(event, <?= $eventId ?>)">
                    <div class="summary-form-center">
                        <div class="summary-form-row"><label>Green plants amount</label><input type="number"
                                name="plants" min="0" value="0" required></div>
                        <div class="summary-form-row"><label>Waste Collected (kg)</label><input type="number"
                                name="waste" min="0" value="0" required></div>
                        <div class="summary-form-row"><label>Recycled waste collected (kg)</label><input type="number"
                                name="recycled" min="0" value="0" required></div>
                        <button type="submit" class="summary-submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <?php } ?>

        <div class="section-title">Past Events (<?= $pastEvents->num_rows ?>)</div>
        <?php while ($row = $pastEvents->fetch_assoc()) { ?>
        <div class="event-card" style="opacity: 0.85;">
            <span class="status completed">Completed</span>
            <h4><?= htmlspecialchars($row['eventName']) ?></h4>
            <p class="event-description"><?= htmlspecialchars($row['description']) ?></p>
            <div class="event-grid">
                <p><i class='bx bx-calendar-week'></i><?= $row['eventDate'] ?></p>
                <p><i class='bx bx-time'></i><?= date("g:i A", strtotime($row['startTime'])) ?> -
                    <?= date("g:i A", strtotime($row['endTime'])) ?></p>
                <p><i class='bx bx-location-plus'></i><?= htmlspecialchars($row['venue']) ?></p>
                <p><i class='bx bx-purchase-tag-alt'></i><?= htmlspecialchars($row['category']) ?></p>
                <p><i class='bx bx-leaf'></i><?= $row['leaf'] ?></p>
                <p><i class='bx bx-group'></i><?= $row['capacity'] ?></p>
            </div>
        </div>
        <?php } ?>
    </div>

    <div id="rejectModal" class="modal-overlay">
        <div class="reject-box">
            <h3 style="text-align:center; margin-top:0;">Reject Reason</h3>
            <div style="display:flex; flex-direction:column; gap:12px; margin: 20px 0;">
                <label><input type="radio" name="rj" value="Full"> Full</label>
                <label><input type="radio" name="rj" value="Not suitable"> Not suitable</label>
                <label><input type="radio" name="rj" value="Duplicate Request"> Duplicate Request</label>
                <label><input type="radio" name="rj" value="Late Submission"> Late Submission</label>
                <label><input type="radio" name="rj" value="Schedule Conflict"> Schedule Conflict</label>
                <label><input type="radio" name="rj" value="Others"> Others</label>
            </div>
            <button class="summary-submit" id="sendReject"
                style="width:100%; margin:0; background: #e74c3c; color: white;">Send</button>
            <button onclick="closeReject()"
                style="background:none; border:none; width:100%; margin-top:15px; cursor:pointer;">Cancel</button>
        </div>
    </div>

    <script src="../assets/js/myEvent.js"></script>
</body>

</html>