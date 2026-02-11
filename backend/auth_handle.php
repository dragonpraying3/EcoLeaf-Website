<?php

session_start();
//connect db.php
require __DIR__ . '/database.php';
//$pagesBase = '../pages/';

date_default_timezone_set('Asia/Kuala_Lumpur');
//make sure is POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../access_portal.php");
    exit;
}

$portalBase = [
    'student'   => '../student/student_portal.php',
    'organizer' => '../organizer/organizer_portal.php',
    'admin'     => '../admin/admin_portal.php',
];

$dashboardBase = [
    'student'   => '../student/student_dashboard.php',
    'organizer' => '../organizer/organizer_dashboard.php',
    'admin'     => '../admin/admin_dashboard.php',
];

//basic variables
$mode = $_POST['mode'];
$role = $_POST['role'];
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$now = date('Y-m-d H:i:s');

//this is the portal file we go back to if something is wrong
if (!isset($portalBase[$role])) {
    header("Location: ../access_portal.php?error=invalid_role");
    exit;
}

$portalFile = $portalBase[$role];
$dashboardFile = $dashboardBase[$role];

//REGISTER
if ($mode === 'register') {

    //public variable
    //user table
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['fullName'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    //valid public variable first
    if ($username === '' || $password === '' || $email === '' ||
        $name === '' || $dob === '' || $gender === '' || $phone === '') {
        header("Location: {$portalFile}?error=empty&mode=register");
        exit;
    }

    // enforce password strength server-side
    $passwordPattern = '/^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z0-9!@#$%^&*(),.?":{}|<>]{8,}$/';
    if (!preg_match($passwordPattern, $password)) {
        header("Location: {$portalFile}?error=weak_password&mode=register");
        exit;
    }

    if ($role === 'student') {

        $tpNumber   = strtoupper(trim($_POST['tpNumber'] ?? ''));
        $programme  = trim($_POST['programme'] ?? '');
        $intakeCode = trim($_POST['intake'] ?? '');

        if ($tpNumber === '' || $programme === '' || $intakeCode === '') {
            header("Location: {$portalFile}?error=empty&mode=register");
            exit;
        }

        $sqlCheckTP = "SELECT studentId
                       FROM student
                       WHERE tpNumber = '$tpNumber'
                       LIMIT 1";
        $resultCheckTP = $conn->query($sqlCheckTP);

        if ($resultCheckTP && $resultCheckTP->num_rows > 0) {
            header("Location: {$portalFile}?error=tp_exists&mode=register");
            exit;
        }
    }

    //check userId or email exists
    // if true = exit
    $sqlCheck = "SELECT userId FROM users 
                 WHERE username='$username' 
                 OR email='$email' 
                 OR phone = '$phone'
                 LIMIT 1";
    $res = $conn->query($sqlCheck);

    if ($res && $res->num_rows > 0) {
        header("Location: {$portalFile}?error=user_exists&mode=register");
        exit;
    }

    //turn password to hashpassword
    $hashPassword = password_hash($password,PASSWORD_DEFAULT);

    //insert user table first
    $sqlUser = "INSERT INTO users (username, email, password, role, name, DateOfBirth, gender, phone, status)
                VALUES ('$username', '$email', '$hashPassword', '$role', '$name', '$dob', '$gender', '$phone', 'active')";

    if (!$conn->query($sqlUser)) {
        header("Location: {$portalFile}?error=user_db&mode=register");
        exit;
    }

    //create new user id
    $newUserId = $conn->insert_id;

    //student table
    if ($role === 'student') {

        $tpNumber = strtoupper(trim($_POST['tpNumber'] ?? ''));
        $programme = trim($_POST['programme'] ?? '');
        $intakeCode = trim($_POST['intake'] ?? '');

        if ($tpNumber === '' || $programme === '' || $intakeCode === '') {
            header("Location: {$portalFile}?error=empty&mode=register");
            exit;
        }

        //programme maping
        $programmeMap = [
            'cs' => 'BSc (Hons) Computer Science',
            'se' => 'BSc (Hons) Software Engineering',
            'ai' => 'BSc (Hons) Artificial Intelligence',
            'is' => 'BSc (Hons) Information Systems'
        ];
        $programmeText  = $programmeMap[$programme];
;
        $sqlStudent = "INSERT INTO student (tpNumber, programme, intakeCode, leaf, joinDate, userId)
                       VALUES ('$tpNumber', '$programmeText', '$intakeCode', 0, '$now', $newUserId)";

        if (!$conn->query($sqlStudent)) {
            header("Location: {$portalFile}?error=student_db&mode=register");
            exit;
        }
    }

    //organizer table
    if ($role === 'organizer') {

        $club = trim($_POST['club'] ?? '');
        $clubPosition = trim($_POST['clubPosition'] ?? '');

        if ($club === '' || $clubPosition === '') {
            header("Location: {$portalFile}?error=empty&mode=register");
            exit;
        }

        $clubMap = [
            "eco" => "Eco & Sustainability Club",
            "recycle" => "Recycling & Waste Management Club",
            "green" => "Green Campus Initiative",
            "energy" => "Renewable Energy Society",
            "wildlife" => "Wildlife & Nature Conservation Club",
            "community" => "Community Social Responsibility (CSR) Club",
        ];

        $clubName = $clubMap[$club];

        $sqlOrganizer = "INSERT INTO organizer (club, position, joinDate, userId)
                         VALUES ('$clubName', '$clubPosition', '$now', '$newUserId')";

        if (!$conn->query($sqlOrganizer)) {
            header("Location: {$portalFile}?error=organizer_db&mode=register");
            exit;
        }
    }

    //admin table
    if ($role === 'admin') {

        $adminPosition = trim($_POST['adminPosition'] ?? '');

        if ($adminPosition === '') {
            header("Location: {$portalFile}?error=empty&mode=register");
            exit;
        }

        $sqlAdmin = "INSERT INTO admin (position, joinDate, userId)
                     VALUES ('$adminPosition', '$now', '$newUserId')";

        if (!$conn->query($sqlAdmin)) {
            header("Location: {$portalFile}?error=admin_db&mode=register");
            exit;
        }
    }

    //success return
    header("Location: {$portalFile}?registered=1");
    exit;
}

// login
if ($mode === 'login') {

    //check empty
    if ($username === '' || $password === '') {
        header("Location: {$portalFile}?error=empty");
        exit;
    }

    //check sql
    //use 'username' and 'role' to find out password
    $sql = "SELECT userId, username, role, status, password
            FROM users 
            WHERE username='$username' AND role='$role'
            LIMIT 1";

    $result = $conn->query($sql);

    //find sql
    if ($result && $row = $result->fetch_assoc()) {

        //check account still active
        if ($row['status'] !== 'active') {
            header("Location: {$portalFile}?error=inactive");
            exit;
        }

        
        //if password enter not same with db will error
        if (!password_verify($password, $row['password'])) {
            header("Location: {$portalFile}?error=invalid");
            exit;
        }
        // store login session
        $_SESSION['username'] = $row['username'];

        // set cookies
        // this cookie set plain password , pls dont do at real system :sob:, only do this for assigment 
        if (isset($_POST['remember_me'])) {
            setcookie('remember_username', $row['username'], time() + 2592000, '/');
            setcookie('remember_password', $password, time() + 2592000, '/');
            setcookie('remember_role', $row['role'], time() + 2592000, '/');
            setcookie('remember_me', '1', time() + 2592000, '/');
        } else {
            setcookie('remember_username', '', time()-3600, '/');
            setcookie('remember_password', '', time()-3600, '/');
            setcookie('remember_role', '', time()-3600, '/');
            setcookie('remember_me', '', time()-3600, '/');
        }

        // redirect to dashboard
        if ($role === 'student') {
            header("Location: " . $dashboardFile);
        } elseif ($role === 'organizer') {
            header("Location: " . $dashboardFile);
        } else {
            header("Location: " . $dashboardFile);
        }
        exit;

    } else {
        header("Location: {$portalFile}?error=invalid");
        exit;
    }
}

// 6. Invalid mode
echo "Invalid request.";
exit;
