<?php
session_start();
include_once '../backend/database.php';
include_once '../backend/fileLogic.php';

date_default_timezone_set('Asia/Kuala_Lumpur');


if (!isset($_SESSION['username']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'organizer') {
    header("Location: /EcoLeaf/index.php");
    exit();
}

$username = $_SESSION['username'];


$sqlGetUserId = "SELECT userId FROM users WHERE username = ?";
$stmtGetUserId = $conn->prepare($sqlGetUserId);
$stmtGetUserId->bind_param("s", $username);
$stmtGetUserId->execute();
$resultUserId = $stmtGetUserId->get_result();

if (!$userRow = $resultUserId->fetch_assoc()) {
    die("Error: Cannot find User ID in the database.");
}
$actualUserId = $userRow['userId'];
$stmtGetUserId->close();


$sqlGetOrgId = "SELECT organizerId FROM organizer WHERE userId = ?";
$stmtGetOrgId = $conn->prepare($sqlGetOrgId);
$stmtGetOrgId->bind_param("i", $actualUserId);
$stmtGetOrgId->execute();
$resultGetOrgId = $stmtGetOrgId->get_result();

if ($orgRow = $resultGetOrgId->fetch_assoc()) {
    $organizerId = $orgRow['organizerId']; 
} else {
    die("Error: Cannot retrieve Organizer ID from the database.");
}
$stmtGetOrgId->close();


$title       = $_POST['event_title'];
$desc        = $_POST['event_description'];
$event_date  = $_POST['event_date'];
$start       = $_POST['event_start'];
$end         = $_POST['event_end'];
$venue       = $_POST['event_venue'];
$category    = $_POST['event_category'];
$capacity    = $_POST['event_capacity'];
$leaf        = $_POST['event_leaf'] ?? 0;


$createdAt   = date("Y-m-d H:i:s"); 

$imgName = null;


$today = date("Y-m-d");
if ($event_date < $today) {
    die("<script>alert('Error: Date cannot be in the past.'); window.history.back();</script>");
}

if (strtotime($end) <= strtotime($start)) {
    die("<script>alert('Error: End time must be later than start time.'); window.history.back();</script>");
}

if (mb_strlen($desc, 'UTF-8') > 300) {
    die("<script>alert('Error: Description exceeds 300 characters.'); window.history.back();</script>");
}


if (empty($_FILES['event_image']['name'])) {
    die("<script>alert('You must upload the picture'); window.history.back();</script>");
}
    $image  = $_FILES['event_image'];
    $extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    list($issues, $uploadOK) = getUploadFileError($extension, $image);

    if ($uploadOK) {
        $targetDir = '/../organizer/image/'; 
        $filePaths = changeOrganizerName($targetDir, $organizerId, $extension);
        
        
        if (saveFiletoDirectory($image, $filePaths['absolute'])) {
            $imgName = basename($filePaths['relative']); 
        } else {
            echo "<script>alert('Error: Failed to move uploaded file.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Upload Error: The file is too large! Maximum allowed is 2MB / The image files must be png or jpg format.'); window.history.back();</script>";
        exit();
    }



$status     = "pending";
$approvedAt = '0000-00-00 00:00:00';
$adminId    = NULL;
$OTP = random_int(100, 999);


$sql = "INSERT INTO evention (
    eventName, description, eventDate, startTime, endTime,
    venue, capacity, category, createAt, leaf,
    OTP_code, imageFile, status, approvedAt, organizerId, adminId
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}


$stmt->bind_param(
    "ssssssisssisssii", 
    $title,        
    $desc,         
    $event_date,   
    $start,        
    $end,          
    $venue,        
    $capacity,     
    $category,     
    $createdAt,    
    $leaf,        
    $OTP,          
    $imgName,      
    $status,       
    $approvedAt,   
    $organizerId,  
    $adminId       
);

if ($stmt->execute()) {
    echo "<script>alert('Event submitted successfully!'); window.location.href='../organizer/myEvent.php';</script>";
} else {
    echo "SQL Error: " . $stmt->error;
}
?>