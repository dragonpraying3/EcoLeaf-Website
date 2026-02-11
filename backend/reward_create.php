<?php
session_start();
require_once "database.php";
require_once "fileLogic.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/rewards_management.php");
    exit;
}

$adminId = $_POST['adminId'];    

if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../pages/access_portal.php?error=admin_only");
    exit;
}

// get data
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'active';
$points = $_POST['pointsRequired'] ?? '';
$quantity  = $_POST['quantity'] ?? '';

// basic validation
if ($name === '') {
    header("Location: ../admin/rewards_management.php?error=name_required");
    exit;
}

if (!is_numeric($points) || (int)$points < 0) {
    header("Location: ../admin/rewards_management.php?error=invalid_points");
    exit;

}

if (!is_numeric($quantity) || (int)$quantity < 0) {
    header("Location: ../admin/rewards_management.php?error=invalid_quantity");
    exit;
}


$points = (int)$points;
$quantity = (int)$quantity;


$status = $_POST['status'] ?? 'active';

// validate status
if ($status !== 'active' && $status !== 'inactive') {
    $status = 'active';
}

// image upload (optional)
$imageFileName = null;

if (isset($_FILES['imageFile'])) {
    
    $image = $_FILES['imageFile'];
    // $_FILES = {
    // 'name'
    // 'type'   
    // 'error'
    // 'size'
    // }
    $imageExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

    // validate image
    [$issues, $uploadOK] = getUploadFileError($imageExtension, $image);
    // upload not ok then return and show error
    if (!$uploadOK) {
        header("Location: ../admin/rewards_management.php?error=" . urlencode($issues));
        exit;
    }

    // generate admin image name (admin/image/)
    $fileInfo = changeAdminImageName('image', $adminId, $imageExtension);

    // move uploaded file
    if (!saveFiletoDirectory($image, $fileInfo['absolute'])) {
        header("Location: ../admin/rewards_management.php?error=upload_failed");
        exit;
    }

    // store only file name in database
    $imageFileName = $fileInfo['relative'];
}

// escape strings to avoid sql breaking
$name = $conn->real_escape_string($name);
$description = $conn->real_escape_string($description);
$status = $conn->real_escape_string($status);
$imageFileName = $conn->real_escape_string($imageFileName);

// insert reward data
$sql = "INSERT INTO rewarditem
        (adminId, name, description, pointsRequired, quantity, status, imageFile)
        VALUES
        ($adminId, '$name', '$description', $points, $quantity, '$status', '$imageFileName')
";

if (!$conn->query($sql)) {
    header("Location: ../admin/rewards_management.php?error=db_failed");
    exit;
}

// success
header("Location: ../admin/rewards_management.php?success=reward_created");
exit;
?>