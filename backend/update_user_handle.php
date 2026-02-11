<?php
require_once "database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/user_managmenet.php");
    exit;
}


$userId = $_POST['userId'];
$action = $_POST['action'] ?? null;



// if action is reset that means reset the password to default(pass123)
if ($action === 'reset') {

    resetPassword($conn, $userId);
    header("Location: ../admin/user_edit.php?userId=$userId&reset=1");
    exit;
}

// if action is delete then change the status to inactive
if ($action === 'delete') {

    $sqlStatus = "UPDATE users 
                 SET status = 'inactive'
                 WHERE userId = '$userId'";

    $conn->query($sqlStatus);
    
    $returnTo = $_POST['returnTo'] ?? "user_management.php";

    // auto flll in
    $sep = (strpos($returnTo, '?') !== false) ? '&' : '?';

    header("Location: ../admin/$returnTo{$sep}deleted=1");
    exit;
}

if (checkUserDuplicateOnUpdate($conn, $userId, $_POST['email'], $_POST['phone'])) {
    header("Location: ../admin/user_edit.php?userId=$userId&duplicate_user=1");
    exit;
}

// get user role
$sqlRole = "SELECT role FROM users WHERE userId = '$userId'";
$row = $conn->query($sqlRole)->fetch_assoc();
$role = $row['role'];

if ($role === 'student') {
    if (checkStudentTPDuplicateOnUpdate($conn, $userId, $_POST['tpNumber'])) {
        header("Location: ../admin/user_edit.php?userId=$userId&tp_exists=1");
        exit;
    }
}

updateUserTable($conn, $_POST, $userId);


if ($role === 'student') {
    updateStudentTable($conn, $_POST, $userId);
} elseif ($role === 'organizer') {
    updateOrganizerTable($conn, $_POST, $userId);
} elseif ($role === 'admin') {
    updateAdminTable($conn, $_POST, $userId);
}

header("Location: ../admin/user_edit.php?userId=$userId&updated=1");
exit;


// function
function resetPassword($conn, $userId) {
    // default password
    $defaultPassword = 'password@123';
    // hash the password
    $hashed = password_hash($defaultPassword, PASSWORD_DEFAULT);

    $sqlReset = "UPDATE users 
                 SET password = '$hashed'
                 WHERE userId = '$userId'";

    return $conn->query($sqlReset);

}

// data = $_POST when using the function
function updateUserTable($conn, $data, $userId) {
    $status = $data['status'];
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $dob = $data['dob'];
    $gender = $data['gender'];

    // update user table
    $sqlUserUpdate = "UPDATE users
                    SET 
                        name = '$name',
                        email = '$email',
                        phone = '$phone',
                        DateOfBirth = '$dob',
                        gender = '$gender',
                        status = '$status'
                    WHERE userId = '$userId'";

    return $conn->query($sqlUserUpdate);
}

function updateStudentTable($conn, $data, $userId) {

    $tpNumber = $data['tpNumber'];
    $intakeCode = $data['intakeCode'];
    $programme  = $data['programme'];
    $leaf   = $data['leaf'];

    $sqlStudentUpdate = "UPDATE student
                         SET 
                            tpNumber = '$tpNumber',
                            intakeCode = '$intakeCode',
                            programme = '$programme',
                            leaf = '$leaf'
                         WHERE userId = '$userId'";

    return $conn->query($sqlStudentUpdate);
}

function updateOrganizerTable($conn, $data, $userId) {

    $club = $data['club'];
    $position  = $data['organizerPosition'];

    $clubMap = [
    "eco" => "Eco & Sustainability Club",
    "recycle" => "Recycling & Waste Management Club",
    "green" => "Green Campus Initiative",
    "energy" => "Renewable Energy Society",
    "wildlife" => "Wildlife & Nature Conservation Club",
    "community" => "Community Social Responsibility (CSR) Club",
    ];

    $clubName = $clubMap[$club];

    $sqlOrgUpdate = "UPDATE organizer
                     SET 
                        club = '$clubName', 
                        position = '$position'
                     WHERE userId = '$userId'";

    return $conn->query($sqlOrgUpdate);
}

function updateAdminTable($conn, $data, $userId) {

    $position = $data['adminPosition'];

    $sqlAdminUpdate = "UPDATE admin
                       SET 
                          position = '$position'
                       WHERE userId = '$userId'";

    return $conn->query($sqlAdminUpdate);
}

function checkUserDuplicateOnUpdate($conn, $userId, $email, $phone) {

    $userId = (int)$userId;
    $email = trim($email);
    $phone = trim($phone);

    $sql = "SELECT userId
            FROM users
            WHERE (email = '$email' OR phone = '$phone')
              AND userId != $userId
            LIMIT 1";

    $res = $conn->query($sql);

    return ($res && $res->num_rows > 0);
}

function checkStudentTPDuplicateOnUpdate($conn, $userId, $tpNumber) {

    $userId = (int)$userId;
    $tpNumber = trim($tpNumber);

    $sql = "SELECT studentId
            FROM student
            WHERE tpNumber = '$tpNumber'
              AND userId != $userId
            LIMIT 1";

    $res = $conn->query($sql);

    return ($res && $res->num_rows > 0);
}
?>