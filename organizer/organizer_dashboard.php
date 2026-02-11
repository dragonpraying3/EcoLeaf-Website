<?php
include_once '../topbar.php'; 

//simple check
//if no login or not student will return
if (!isset($_SESSION['user']['role']) === 'organizer') {
    header("Location: organizer_portal.php");
    exit();
}

$organizerID = $_SESSION['user']['organizerId'] ?? null;

$totalParticipants = 0;
if ($organizerID) {
    $sqlParticipants = "
        SELECT COUNT(*) AS total
        FROM attendance a
        INNER JOIN evention e ON a.eventId = e.eventId
        WHERE e.organizerId = '$organizerID'
          AND a.status = 'present'
    ";
    $resultParticipants = mysqli_query($conn, $sqlParticipants);

    if ($resultParticipants && $rowP = mysqli_fetch_assoc($resultParticipants)) {
        $totalParticipants = $rowP['total'] ?? 0;
    }
}

$totalEvents = 0;
if ($organizerID) {
    $sqlEvents = "
        SELECT COUNT(*) AS total
        FROM evention
        WHERE organizerId = '$organizerID'
        AND status = 'approved'
    ";
    $resultEvents = mysqli_query($conn, $sqlEvents);

    if ($resultEvents && $rowE = mysqli_fetch_assoc($resultEvents)) {
        $totalEvents = $rowE['total'] ?? 0;
    }
}

$totalPointsDistributed = 0;
if ($organizerID) {
    $sqlPoints = "
        SELECT SUM(leaf) AS totalLeaf
        FROM evention
        WHERE organizerId = '$organizerID'
        AND status = 'approved'
    ";
    $resultPoints = mysqli_query($conn, $sqlPoints);

    if ($resultPoints && $rowPoints = mysqli_fetch_assoc($resultPoints)) {
        $totalPointsDistributed = $rowPoints['totalLeaf'] ?? 0;
    }
}

$weeklyData = [
    'fuel' => 0,
    'transport' => 0,
    'cycling_walking' => 0,
    'recycling' => 0,
    'waste' => 0,
    'electric' => 0
];

if ($organizerID) {
    $sqlWeek = "
        SELECT YEAR(calcDate) AS year, WEEK(calcDate, 1) AS week
        FROM carboncalculator
        WHERE organizerId = '$organizerID'
        ORDER BY calcDate DESC
        LIMIT 1
    ";
    $resultWeek = mysqli_query($conn, $sqlWeek);
    
    if ($rowWeek = mysqli_fetch_assoc($resultWeek)) {
        $year = $rowWeek['year'];
        $week = $rowWeek['week'];

        $sql = "
            SELECT 
                SUM(fuel) AS fuel,
                SUM(transport) AS transport,
                SUM(cycling_walking) AS cycling_walking,
                SUM(recycling) AS recycling,
                SUM(waste) AS waste,
                SUM(electric) AS electric
            FROM carboncalculator
            WHERE organizerId = '$organizerID'
              AND YEAR(calcDate) = $year
              AND WEEK(calcDate, 1) = $week
        ";
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            $weeklyData['fuel'] = (float)$row['fuel'];
            $weeklyData['transport'] = (float)$row['transport'];
            $weeklyData['cycling_walking'] = (float)$row['cycling_walking'];
            $weeklyData['recycling'] = (float)$row['recycling'];
            $weeklyData['waste'] = (float)$row['waste'];
            $weeklyData['electric'] = (float)$row['electric'];
        }
    }
}


$currentYear = date('Y'); 
$impact = [
    'plants' => 0,
    'waste' => 0,
    'recycle' => 0
];

$sqlImpact = "
    SELECT 
        SUM(treePlanted) AS plants, 
        SUM(wasteCollected) AS waste, 
        SUM(recycleItem) AS recycle 
    FROM summary
    WHERE YEAR(submittedDate) = $currentYear
    AND organizerId = '$organizerID'
";

$resultImpact = mysqli_query($conn, $sqlImpact);

if ($resultImpact && $rowImpact = mysqli_fetch_assoc($resultImpact)) {
    $impact['plants'] = $rowImpact['plants'] ?? 0;
    $impact['waste'] = $rowImpact['waste'] ?? 0;
    $impact['recycle'] = $rowImpact['recycle'] ?? 0;
}

// Calculate percentages relative to total impact for the year
$totalImpact = $impact['plants'] + $impact['waste'] + $impact['recycle'];
$plantsPercent  = $totalImpact > 0 ? round(($impact['plants'] / $totalImpact) * 100, 1) : 0;
$wastePercent   = $totalImpact > 0 ? round(($impact['waste'] / $totalImpact) * 100, 1) : 0;
$recyclePercent = $totalImpact > 0 ? round(($impact['recycle'] / $totalImpact) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoLeaf – Organizer Dashboard</title>

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
    <main class="dashboard-container">

        <!-- welcome section -->
        <section class="welcome-section">
            <h1>Hello, <span class="highlight"><?php echo htmlspecialchars($username); ?></span>! 👋</h1>
            <p class="subtitle">Ready to make a difference today? Here is your sustainability overview.</p>
        </section>

        <!-- card section - EXACTLY 4 CARDS like student dashboard -->
        <div class="dashboard-grid">

            <!-- card 1: Total Participants -->
            <div class="dash-card">
                <div class="card-icon blue-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round-icon lucide-users-round">
                        <path d="M18 21a8 8 0 0 0-16 0"/>
                        <circle cx="10" cy="8" r="5"/>
                        <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/>
                    </svg>
                </div>
                <div class="card-info">
                    <h3>Total Participants</h3>
                    <div class="big-number"><?php echo $totalParticipants; ?></div>
                </div>
            </div>

            <!-- card 2: Total Events -->
            <div class="dash-card">
                <div class="card-icon orange-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="lucide lucide-calendar-heart-icon lucide-calendar-heart">
                        <path d="M12.127 22H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5.125"/>
                        <path d="M14.62 18.8A2.25 2.25 0 1 1 18 15.836a2.25 2.25 0 1 1 3.38 2.966l-2.626 2.856a.998.998 0 0 1-1.507 0z"/>
                        <path d="M16 2v4"/>
                        <path d="M3 10h18"/>
                        <path d="M8 2v4"/>
                    </svg>
                </div>
                <div class="card-info">
                    <h3>Total Events</h3>
                    <div class="big-number"><?php echo $totalEvents; ?></div>
                </div>
            </div>

            <!-- card 3: Total Points Distributed -->
            <div class="dash-card">
                <div class="card-icon blue-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-euro-icon lucide-euro">
                        <path d="M4 10h12"/>
                        <path d="M4 14h9"/>
                        <path d="M19 6a7.7 7.7 0 0 0-5.2-2A7.9 7.9 0 0 0 6 12c0 4.4 3.5 8 7.8 8 2 0 3.8-.8 5.2-2"/>
                    </svg>
                </div>
                <div class="card-info">
                    <h3>Total Points Distributed</h3>
                    <div class="big-number"><?php echo $totalPointsDistributed; ?></div>
                </div>
            </div>

            <!-- card 4: Impact Summary -->
            <div class="dash-card action-card">
                <h3>Impact Overview (<?php echo $currentYear; ?>)</h3>
                <div class="action-buttons">
                    <div class="impact-item">
                        <span>Total Participants</span>
                        <strong><?php echo $totalParticipants; ?></strong>
                    </div>
                    <div class="impact-item">
                        <span>Total green plants</span>
                        <strong><?php echo $plantsPercent; ?>%</strong>
                    </div>
                    <div class="impact-item">
                        <span>Total waste collected</span>
                        <strong><?php echo $wastePercent; ?>%</strong>
                    </div>
                    <div class="impact-item">
                        <span>Total recycle item</span>
                        <strong><?php echo $recyclePercent; ?>%</strong>
                    </div>
                </div>
            </div>

        </div> 

        <!-- card 5: Chart -->
        <div class="dash-card chart">
            <h2>Your Carbon Footprint Overview</h2>
            <div class="dashboard-section">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <script>
        const weeklyData = {
            fuel: <?php echo $weeklyData['fuel']; ?>,
            transport: <?php echo $weeklyData['transport']; ?>,
            cycling_walking: <?php echo $weeklyData['cycling_walking']; ?>,
            recycling: <?php echo $weeklyData['recycling']; ?>,
            waste: <?php echo $weeklyData['waste']; ?>,
            electric: <?php echo $weeklyData['electric']; ?>
        };
        </script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../assets/js/dashboard_chart.js"></script>
        
    </main>
</body>
</html>