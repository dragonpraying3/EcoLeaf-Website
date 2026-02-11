<?php 
include_once '../topbar.php';
// Edit Profile Page
// Only allows editing Password and Phone number
// Prevents duplicate phone numbers (unique per user)
// pattern of password and phone number need same as register
$message = '';
$error = '';
$user = null;

// use session to get username
$currentUsername = $_SESSION['username'] ?? ($_SESSION['user']['username'] ?? null);

if (isset($conn) && !$error) {
    // all common field 
    $sql="SELECT u.userId, u.username, u.email, u.password, u.role, u.name, u.DateOfBirth 
    AS dateOfBirth, u.gender, u.phone, s.joinDate 
    FROM users u 
    LEFT JOIN student s 
    ON u.userId = s.userId 
    WHERE u.username=? AND u.status='active' 
    LIMIT 1";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $currentUsername);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();
        if (!$user) { 
            $error = 'Active profile not found'; 
        }
    }
    //user is organizer
    if ($user && strtolower($user['role']) === 'organizer') {
        $sql="SELECT club, position, joinDate 
        FROM organizer 
        WHERE userId=? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user['userId']);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($org = $res->fetch_assoc()) {
                $user['club'] = $org['club'] ?? '';
                $user['position'] = $org['position'] ?? '';
                $user['joinDate'] = $org['joinDate'] ?? ($user['joinDate'] ?? null);
            }
            $stmt->close();
        }
    }
    //if user is admin
    if ($user && strtolower($user['role']) === 'admin') {
        $sql="SELECT position, joinDate 
        FROM admin 
        WHERE userId=? 
        LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user['userId']);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($admin = $res->fetch_assoc()) {
                $user['position'] = $admin['position'] ?? '';
                $user['joinDate'] = $admin['joinDate'] ?? ($user['joinDate'] ?? null);
            }
            $stmt->close();
        }
    }

    // Handle update: only Password and Phone are editable
    // - Hash new password
    // - Reject duplicate phone numbers
    if (!$error && $_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
        // Only password and phone are editable
        $password = trim($_POST['password'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $oldPhone = $user['phone'] ?? '';

        // Hash the new password from user input only if provided
        $hashedPassword = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;

        // Check for duplicate phone number
        $phoneCheckStmt = $conn->prepare("SELECT userId FROM users WHERE phone = ? AND userId != ?");
        $phoneCheckStmt->bind_param("si", $phone, $user['userId']);
        $phoneCheckStmt->execute();
        $phoneCheckRes = $phoneCheckStmt->get_result();
        
        if ($phoneCheckRes->num_rows > 0) {
            $error = 'Phone number is already taken by another user.';
            $phoneCheckStmt->close();
        } else {
            $phoneCheckStmt->close();
            
            // Proceed with update
            if ($password !== '') {
                $sql="UPDATE users 
                SET password=?, phone=? 
                WHERE userId=? AND status='active'";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ssi", $hashedPassword, $phone, $user['userId']);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $error = 'Failed to update profile. Please try again.';
                }
            } else {
                $sql="UPDATE users 
                SET phone=? 
                WHERE userId=? AND status='active'";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("si", $phone, $user['userId']);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $error = 'Failed to update profile. Please try again.';
                }
            }
            if (!$error) {
                $phoneChanged = ($phone !== $oldPhone);
                if ($password !== '') {
                    $pwdLen = strlen($password);
                    if ($pwdLen < 5) {
                        $pwdPreview = substr($password, 0, 1) . str_repeat('*', max(0, $pwdLen - 1));
                    } else {
                        $pwdPreview = substr($password, 0, 1) . str_repeat('*', max(0, $pwdLen - 2)) . substr($password, -1);
                    }
                    $phoneMsg = $phoneChanged ? (" New phone number: " . htmlspecialchars($phone) . ".") : "";
                    $message = "New password: " . htmlspecialchars($pwdPreview) . " updated successfully." . $phoneMsg;
                } elseif ($phoneChanged) {
                    $message = "New phone number: " . htmlspecialchars($phone) . " updated successfully.";
                } 

                // Refresh snapshot to reflect changes
                $query="SELECT u.userId, u.username, u.email, u.password, u.role, u.name, u.DateOfBirth AS dateOfBirth, u.gender, u.phone, s.joinDate 
                FROM users u 
                LEFT JOIN student s 
                ON u.userId = s.userId 
                WHERE u.userId=? AND u.status='active'";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("i", $user['userId']);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $user = $res->fetch_assoc();
                    $stmt->close();
                }
                if ($user && strtolower($user['role']) === 'organizer') {
                    $query="SELECT club, position, joinDate 
                    FROM organizer WHERE userId=? 
                    LIMIT 1";
                    if ($stmt = $conn->prepare()) {
                        $stmt->bind_param("i", $user['userId']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($org = $res->fetch_assoc()) {
                            $user['club'] = $org['club'] ?? '';
                            $user['position'] = $org['position'] ?? '';
                            $user['joinDate'] = $org['joinDate'] ?? ($user['joinDate'] ?? null);
                        }
                        $stmt->close();
                    }
                }
                if ($user && strtolower($user['role']) === 'admin') {
                    $query="SELECT position, joinDate 
                    FROM admin 
                    WHERE userId=? LIMIT 1";
                    if ($stmt = $conn->prepare($query)) {
                        $stmt->bind_param("i", $user['userId']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($admin = $res->fetch_assoc()) {
                            $user['position'] = $admin['position'] ?? '';
                            $user['joinDate'] = $admin['joinDate'] ?? ($user['joinDate'] ?? null);
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/EcoLeaf/assets/css/carbonccl.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/EcoLeaf/assets/css/edit_profile.css">
</head>

<body>
    <?php include_once '../topbar.php'; ?>
    <div class="zone-intro body-container">
        <div id="wrapper">
            <div class="title-box">
                <div class="title">
                    <div id="title-bold">Edit Profile</div>
                    Update your personal information
                </div>
            </div>
            <?php if ($message !== '') { echo "<div class='notice'>$message</div>"; } ?>
            <?php if ($error !== '') { echo "<div class='error'>$error</div>"; } ?>
            <?php if ($user) { ?>
            <div class="profile-card">
                <div class="header">
                    <div class="avatar"><?php echo strtoupper(substr($user['name'],0,2)); ?></div>
                    <div class="info">
                        <div class="name"><?php echo htmlspecialchars($user['name']); ?></div>
                        <div class="role-pill <?php echo htmlspecialchars($user['role']); ?>">
                            <?php echo htmlspecialchars(ucfirst($user['role'])); ?></div>
                    </div>
                </div>
                <div class="notice">Only Password and Phone number can be edited. Other fields are locked.</div>
                <form id="profileForm" method="post" class="form-grid">
                    <label>Username
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"
                            readonly class="readonly-input">
                    </label>
                    <label>Email
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                            readonly class="readonly-input">
                    </label>
                    <label>Password
                        <div class="password-field">
                            <input type="password" value="********" disabled class="readonly-input">
                            <button class="icon-btn" type="button" id="resetPwdBtn" aria-label="Reset password"><i
                                    class='bx bx-revision'></i></button>
                        </div>
                    </label>
                    <input type="hidden" name="password" id="passwordHidden" value="">
                    <label>Name
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly
                            class="readonly-input">
                    </label>
                    <label>Date of Birth
                        <input type="date" name="dateOfBirth"
                            value="<?php echo htmlspecialchars($user['dateOfBirth']); ?>" readonly
                            class="readonly-input">
                    </label>
                    <label>Gender
                        <select name="gender" disabled class="readonly-input">
                            <option value="Female" <?php echo strtolower($user['gender'])==='female'?'selected':''; ?>>
                                Female</option>
                            <option value="Male" <?php echo strtolower($user['gender'])==='male'?'selected':''; ?>>Male
                            </option>
                        </select>
                    </label>

                    <?php if (strtolower($user['role']) === 'organizer') { ?>
                    <label>Club
                        <input type="text" name="club" value="<?php
                         $club=trim($user['club']); 
                         $real_club="";
                        switch ($club){
                            case 'member':
                                $real_club="Eco & Sustainability Club";
                                break;
                            case 'recycle':
                                $real_club="Recycling & Waste Management Club";
                                break;
                            case 'green':
                                $real_club="Green Campus Initiative";
                                break;
                            case 'energy':
                                $real_club="Wildlife & Nature Conservation Club";
                                break;
                            case 'wildlife':
                                $real_club="Community Social Responsibility (CSR) Club";
                                break;
                            default:
                                $real_club="Sustainable People";
                                break;
                        }
                        echo htmlspecialchars($real_club);
                        
                         ?>" readonly class="readonly-input">
                    </label>
                    <label>Position
                        <input type="text" name="position" value="<?php  
                            $real_pose="";
                            $position=trim($user['position']);
                            switch ($position){
                                case 'member':
                                    $real_pose="Member";
                                    break;
                                case 'committee':
                                    $real_pose="Committee Memrber";
                                    break;
                                case 'secretary':
                                    $real_pose="Secretary";
                                    break;
                                case 'treasurer':
                                    $real_pose="Treasurer";
                                    break;
                                case 'vice':
                                    $real_pose="Vice President";
                                    break;
                                case 'president':
                                    $real_pose="President";
                                    break;
                            }
                        echo htmlspecialchars($real_pose);
                            ?>" readonly class="readonly-input">
                    </label>
                    <?php } ?>


                    <?php if (strtolower($user['role']) === 'admin') { ?>
                    <label>Position
                        <input type="text" name="position" value="<?php 
                        $real_pose="";
                        $position=trim($user['position']);
                        switch ($position){
                            case 'system_admin':
                                $real_pose="System Administrator";
                                break;
                            case 'content_admin':
                                $real_pose="Content Manager";
                                break;
                            case 'event_admin':
                                $real_pose="Event Coordinator";
                                break;
                        }
                        echo htmlspecialchars($real_pose);
                        ?>" readonly class="readonly-input">
                    </label>
                    <?php } ?>

                    <label>Phone Number
                        <input type="tel" name="phone" id="phone"
                            value="<?php echo htmlspecialchars(isset($_POST['phone']) ? $_POST['phone'] : $user['phone']); ?>"
                            inputmode="numeric" pattern="[0-9]*" minlength="10"
                            title="Digits only, minimum 10 characters" required>
                    </label>
                    <label>Join Date
                        <input type="date" name="joinDate"
                            value="<?php echo htmlspecialchars(isset($user['joinDate']) ? date('Y-m-d', strtotime($user['joinDate'])) : ''); ?>"
                            readonly class="readonly-input">
                    </label>
                    <div class="actions">
                        <button class="btn" type="reset"><i class='bx bx-revision'></i> Reset</button>
                        <button class="btn primary" type="submit"><i class='bx bx-save'></i> Save Changes</button>
                    </div>
                </form>
                <div class="modal hidden" id="resetPwdModal">
                    <div class="modal-content">
                        <div class="modal-title">Reset Password</div>
                        <div class="modal-body">
                            <label>New Password
                                <input type="password" id="newPassword"
                                    pattern="^(?=.*[0-9])(?=.*[!@#$%^&(),.?&quot;:{}|<>])[A-Za-z0-9!@#$%^&(),.?&quot;:{}|<>]{8,}$"
                                    title="At least 8 chars, include a number and a special symbol">
                            </label>
                            <small class="hint" id="pwdHint">At least 8 chars, include a number and a special
                                symbol.</small>
                            <label>Confirm Password
                                <input type="password" id="confirmPassword">
                            </label>
                            <small class="hint" id="confirmHint"></small>
                            <div class="error" id="pwdError" style="display:none;"></div>
                        </div>
                        <div class="modal-actions">
                            <button class="btn" type="button" id="cancelReset">Cancel</button>
                            <button class="btn primary" type="button" id="confirmReset">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            } 
            ?>
        </div>
    </div>
    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
    <script src="/EcoLeaf/assets/js/edit_profile.js"></script>
</body>

</html>