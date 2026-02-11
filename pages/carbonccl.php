<?php 
include_once '../topbar.php'; 
require_once __DIR__ . '/../backend/badgeChecker.php';
require_once __DIR__ . '/../backend/popup.php';
// Carbon Calculator Page
// - Collects daily activity inputs (fuel, transport, etc.)
// - Computes emissions, savings, net footprint, and trees equivalent
// - Persists calculation and advice; triggers badge checks for student

$studentId = null;
$organizerId = null;
$adminId = null;

//checking role
$role=$_SESSION['user']['role']??'guest';

switch ($role){
    case 'student':
        $studentId=$_SESSION['user']['studentId'];
        break;
    case 'organizer':
        $organizerId=$_SESSION['user']['organizerId'];
        break;
    case 'admin':
        $adminId=$_SESSION['user']['adminId'];
        break;
    default:
        null;
}

$positives = 0;
$saved = 0;
$net = 0;
$trees = 0;

$electric = 0;
$transport =  0;
$waste = 0;
$recycling =  0;
$cycling_walking = 0;
$fuel =  0;

$fuelEmission = 0.0;
$transportEmission = 0.0;
$electricityEmission = 0.0;
$wasteEmission = 0.0;
$walkingCyclingEmission = 0.0;
$recyclingSaved = 0.0;
$walkingCyclingSaved = 0.0;
$totalEmission = 0.0;

$adviceArr = [];

function checkEmpty($fuel, $transport, $waste, $recycling, $cycling_walking, $electric) {
    if (empty($fuel) || empty($transport) || empty($waste) || empty($recycling) || empty($cycling_walking) || empty($electric)) {
        $totalEmission = 0.0;
        $saved = 0.0;
        $net = 0.0;
        $trees = 0.0;
        $adviceArr = ['Please fill in all fields to get an accurate calculation.'];
        return false;
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calculateBtn'])) {    
    $fuel = (double)$_POST['fuel'];
    $transport =  (double)$_POST['transport'] ;
    $waste = (double)$_POST['waste'];
    $recycling = (double)$_POST['recycling'];
    $cycling_walking = (double)$_POST['cycling_walking'];
    $electric = (double)$_POST['electric'];
    
    $enable=checkEmpty($fuel, $transport, $waste, $recycling, $cycling_walking, $electric);

    if ($enable) {
        //the emmision of co2 in by every 1kg/ 1 litre / 1km
        $fuelEmission = $fuel * 2.31;
        $transportEmission = $transport * 0.105;
        $electricityEmission = $electric * 0.584;
        $wasteEmission = $waste * 1.9;
        
        $walkingCyclingSaved = 0; //do not emit co2
        $recyclingSaved = $recycling * 1.5; //per 1 kg of waste
        
        $saved = $recyclingSaved;
        $totalEmission = $fuelEmission + $transportEmission + $electricityEmission + $wasteEmission;
        $saved = $recyclingSaved + $walkingCyclingSaved;
        $net = $totalEmission - $saved;
        $trees = $net / 10;
        
        $maxName = 'Fuel';
        $maxVal = $fuelEmission;
        if ($transportEmission > $maxVal) { 
            $maxName = 'Transport'; 
            $maxVal = $transportEmission; 
        }
        if ($electricityEmission > $maxVal) { 
            $maxName = 'Electricity'; 
            $maxVal = $electricityEmission; 
        }
        if ($wasteEmission > $maxVal) { 
            $maxName = 'Waste'; 
            $maxVal = $wasteEmission; 
        }
        
        $adviceArr = [];
        if ($waste >= 10) { 
            $adviceArr[] = 'Reduce waste, reuse items, and compost organics.';
        }
        if ($net > 50) { 
            $adviceArr[] = 'Focus on ' . $maxName . ' to reduce footprint by 20%.'; 
        }
        if ($transport >= 20) { 
            $adviceArr[] = 'Use public transport, carpool, or reduce trips.'; 
        }
        if ($electric >= 20) { 
            $adviceArr[] = 'Switch to LED lighting and improve appliance efficiency.'; 
        }
        if ($fuel >= 20) { 
            $adviceArr[] = 'Drive less, maintain vehicle efficiency, consider cleaner fuels.'; 
        }
        if ($recycling < 5) { 
            $adviceArr[] = 'Increase recycling of paper, plastic, metal, and glass.'; 
        }
        if ($cycling_walking < 5) { 
            $adviceArr[] = 'Add short walking or cycling trips to your routine.'; 
        }
        if (count($adviceArr) === 0) { 
            $adviceArr[] = 'Great balance. Keep reinforcing low-impact habits.'; 
        }
        
        $adviceText = implode("\n", $adviceArr);

        
        $sql="INSERT INTO carboncalculator (fuel, transport, cycling_walking, recycling, waste, electric, result, amountSaved, advice, calcDate, studentId, organizerId, adminId) VALUES (?,?,?,?,?,?,?,?,?,NOW(),?,?,?)";
        
        $insertion=$conn->prepare($sql);
        $insertion->bind_param('ddddddddsiii', $fuel, $transport, $cycling_walking, $recycling, $waste, $electric, $totalEmission, $saved, $adviceText, $studentId, $organizerId, $adminId);
        if ($insertion->execute()){
            if ($studentId !== null){
                checkStudentBadge($conn,$studentId);
                $badgePop=$_SESSION['badge']??[]; 
                unset($_SESSION['badge']); 
            }
        }
        $insertion->close();
    }
}
                
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Calculator</title>
    <link rel="stylesheet" href="/EcoLeaf/assets/css/carbonccl.css">
    <link href='https://cdn.boxicons.com/3.0.6/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
    <script src="/EcoLeaf/assets/js/carbonccl.js" defer></script>
</head>

<body>
    <?php include_once '../topbar.php'; ?>
    <div class="zone-intro body-container">
        <form id="calcForm" method="post">
            <div id="wrapper">
                <div class="title-box">
                    <div class="title">
                        <div id="title-bold">Carbon Footprint Calculator</div>
                        <div id="title-down">Track your daily activities and see your environmental impact.</div>
                    </div>
                </div>
                <div class="layout">
                    <div class="left-stack">
                        <div class="card">
                            <div class="card-title"><i class='bx bx-car'></i> Transportation (per day)</div>
                            <div class="slider-group">
                                <label for="fuel">Fuel Usage (liters/day)</label>
                                <input type="number" id="fuel" name="fuel" min="0" max="30" step="0.1"
                                    placeholder="e.g.4liters/day" required>
                            </div>
                            <div class="slider-group">
                                <label for="transport">Public Transport (km/day)</label>
                                <input type="number" id="transport" name="transport" min="0" max="200" step="0.1"
                                    placeholder="e.g. 5km/day" required>
                            </div>
                            <div class="slider-group">
                                <label for="cycling_walking">Walking/Cycling
                                    (km/day)</label>
                                <input type="number" id="cycling_walking" name="cycling_walking" min="0" max="50" step="0.1"
                                     placeholder="e.g. 2km/day" required>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-title"><i class='bx bx-bolt'></i> Energy Usage (per day)</div>
                            <div class="slider-group">
                                <label for="electric">Electricity Usage (kWh/day)</label>
                                <input type="number" id="electric" name="electric" min="0" max="100" step="0.1"
                                    placeholder="e.g. 5kWh/day" required>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-title"><i class='bx bx-recycle'></i> Waste Management (per day)</div>
                            <div class="slider-group">
                                <label for="recycling">Recycling Efforts (kg/day)</label>
                                <input type="number" id="recycling" name="recycling" min="0" max="20" step="0.1"
                                    placeholder="e.g. 5kg/day" required>
                            </div>
                            <div class="slider-group">
                                <label for="waste">General Waste (kg/day)</label>
                                <input type="number" id="waste" name="waste" min="0" max="20" step="0.1"
                                    placeholder="e.g. 2kg/day" required>
                            </div>
                        </div>
                    </div>

                    <div class="impact-panel">
                        <div class="panel-title"><i class='bx bx-leaf'></i> Your Daily Carbon Impact</div>
                        <div class="stats">
                            <div class="stat-card">
                                <div class="label">Total Emissions</div>
                                <div class="value" id="totalEmissions"><?php echo number_format($totalEmission, 2); ?>
                                    kg
                                    CO₂</div>
                            </div>
                            <div class="stat-card">
                                <div class="label">CO₂ Saved</div>
                                <div class="value" id="co2Saved"><?php echo number_format($saved, 2); ?> kg CO₂</div>
                            </div>
                        </div>
                        <div class="net-card">
                            <div>Net Carbon Footprint</div>
                            <div class="net-big" id="netFootprint"><?php echo number_format($net, 2); ?> kg CO₂</div>
                            <div class="net-sub" id="treesEq">Equivalent to <?php echo number_format($trees, 1); ?>
                                trees needed</div>
                        </div>
                        <div class="breakdown">
                            <div class="break-item">
                                <div class="name"><i class='bx bx-car'></i> Car</div>
                                <div class="amount pos" id="bdFuel">+<?php echo number_format($fuel, 2); ?> kg</div>
                            </div>
                            <div class="break-item">
                                <div class="name"><i class='bx bx-bus'></i> Public Transport</div>
                                <div class="amount pos" id="bdTransport">+<?php echo number_format($transport, 2); ?> kg
                                </div>
                            </div>
                            <div class="break-item">
                                <div class="name"><i class='bx bx-bolt'></i> Electricity</div>
                                <div class="amount pos" id="bdElectric">+<?php echo number_format($electric, 2); ?> kg
                                </div>
                            </div>
                            <div class="break-item">
                                <div class="name"><i class='bx bx-trash'></i> Waste</div>
                                <div class="amount pos" id="bdWaste">+<?php echo number_format($waste, 2); ?> kg</div>
                            </div>
                            <div class="break-item">
                                <div class="name"><i class='bx bx-recycle'></i> Recycling</div>
                                <div class="amount neg" id="bdRecycling">-<?php echo number_format($recycling, 2); ?> kg
                                </div>
                            </div>
                            <div class="break-item">
                                <div class="name"><i class='bxr  bxs-walking'></i> Walking/Cycling</div>
                                <div class="amount neg" id="bdCycling_walking">
                                    -<?php echo number_format($cycling_walking, 2); ?> kg</div>
                            </div>
                        </div>
                        <div class="advice">
                            <h2>Personalized Advice</h2>
                            <ul id="adviceList">
                                <?php foreach ($adviceArr as $a) { echo '<li>' . htmlspecialchars($a) . '</li>'; } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" id="calcBtn" name="calculateBtn" class="save-btn"><i
                            class='bx bx-calculator'></i> Calculate and Save</button>
                </div>
            </div>
        </form>
    </div>

    <?php if(!empty($badgePop)):?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php foreach ($badgePop as $badge): ?>
        showRelevantPop('badge-gain');
        <?php endforeach; ?>
    });
    </script>
    <?php endif; ?>

    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>

</body>

</html>