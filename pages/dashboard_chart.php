<?php
include '../database.php'; 

$studentID = $_SESSION['user']['studentID'] ?? null;

$weeklyData = [
    'fuel' => 0,
    'transport' => 0,
    'cycling_walking' => 0,
    'recycling' => 0,
    'waste' => 0,
    'electric' => 0
];

if ($studentID) {
    $sqlWeek = "
        SELECT WEEK(calcDate, 1) AS week
        FROM carboncalculator
        WHERE studentId = '$studentID'
        ORDER BY calcDate DESC
        LIMIT 1
    ";
    $resultWeek = mysqli_query($conn, $sqlWeek);
    if ($rowWeek = mysqli_fetch_assoc($resultWeek)) {
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
            WHERE studentId = '$studentID'
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
?>