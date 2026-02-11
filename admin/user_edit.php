<?php
require_once "../backend/database.php";
$userId = $_GET['userId'];

//  get user information
$sqlUser = "SELECT userId, username, name, email, phone, DateOfBirth, gender, status, role
            FROM users
            WHERE userId = '$userId'";

$result = $conn->query($sqlUser);

// if cant find user return
if ($result->num_rows === 0) {
    header("Location: user_management.php");
    exit;
}

$user = $result->fetch_assoc();

// get user role and take remaining data
$role = $user['role'];

if ($role === 'student') {
    $sqlStudent = "SELECT tpNumber, programme, intakeCode, leaf, joinDate
                   FROM student  
                   WHERE userId = '$userId'";
    $student = $conn->query($sqlStudent)->fetch_assoc();
    $joinDate = $student['joinDate'];
}

if ($role === 'organizer') {
    $sqlOrg = "SELECT club, position, joinDate
               FROM organizer 
               WHERE userId = '$userId'";
    $organizer = $conn->query($sqlOrg)->fetch_assoc();
    $joinDate = $organizer['joinDate'];
}

if ($role === 'admin') {
    $sqlAdmin = "SELECT position, joinDate
                 FROM admin 
                 WHERE userId = '$userId'";
    $admin = $conn->query($sqlAdmin)->fetch_assoc();
    $joinDate = $admin['joinDate'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoLeaf - Edit User</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- css -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/userInformation.css">
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>

    <!-- link -->
    <script type="text/javascript" src="../assets/js/userEdit.js" defer></script>


</head>

<body data-user-status="<?= htmlspecialchars($user['status']) ?>" <?php if (isset($_GET['updated'])): ?>
    data-toast="updated" <?php endif; ?> <?php if (isset($_GET['reset'])): ?> data-toast="reset" <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?> data-toast="deleted" <?php endif; ?> <?php if (isset($_GET['tp_exists'])): ?>
    data-toast="tp_exists" <?php endif; ?> <?php if (isset($_GET['duplicate_user'])): ?> data-toast="duplicate_user"
    <?php endif; ?>>

    <div class="dashboard-container">
        <div class="edit-card">

            <!-- header -->
            <div class="card-header">
                <h1 class="card-title">Edit User Details</h1>
                <a href="user_management.php" class="btn btn-cancel">
                    &larr; Back
                </a>
            </div>

            <!-- user update form -->
            <form id="updateUserForm" method="post" action="../backend/update_user_handle.php">
                <div class="layout-grid">

                    <!-- left panel -->
                    <div class="left-panel">
                        <div class="avatar-large">JD</div>
                        <span class="role-badge-large-<?= $user['role'] ?>"><?= $user['role'] ?></span>

                        <!-- status -->
                        <div class="input-group">
                            <label>Status</label>
                            <select id="statusSelect" name="status" class="status-inactive"
                                onchange="updateStatusColor(this)" disabled>
                                <option value="active" <?= ($user['status'] === 'active') ? 'selected' : '' ?>>Active
                                </option>
                                <option value="inactive" <?= ($user['status'] === 'inactive') ? 'selected' : '' ?>>
                                    Inactive</option>
                            </select>
                            <!-- hidden for post -->
                            <input type="hidden" name="status" value="<?= htmlspecialchars($user['status']) ?>">
                        </div>

                        <!-- username -->
                        <div class="input-group">
                            <label>Username</label>
                            <input type="text" name="username" value="<?= $user['username'] ?>" disabled>
                        </div>

                        <!-- password section -->
                        <div class="security-box">
                            <span class="security-label">Security</span>
                            <button type="submit" form="resetUserForm" name="action" value="reset"
                                class="btn btn-reset">Reset Password</button>
                        </div>

                        <!-- meta info -->
                        <div class="meta-info">
                            <div class="meta-item">
                                <span>Joined:</span>
                                <strong><?= substr($joinDate, 0, 10) ?></strong>
                            </div>
                            <div class="meta-item">
                                <span>ID:</span>
                                <strong><?= htmlspecialchars($userId) ?></strong>
                                <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- right panel -->
                    <div class="right-panel">

                        <div class="fields-container">

                            <!-- overall information -->
                            <div class="section-label">Account Information</div>

                            <!-- row 1 -->
                            <div class="input-group">
                                <label>Full Name</label>
                                <input type="text" name="name" value="<?= $user['name'] ?>">
                            </div>

                            <div class="input-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?= $user['email'] ?>">
                            </div>

                            <div class="input-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" inputmode="numeric" pattern="[0-9]*" minlength="10"
                                    maxlength="12" value="<?= $user['phone'] ?>" required>
                            </div>

                            <!-- row 2 -->

                            <div class="input-group">
                                <label>Date of Birth</label>
                                <input type="date" name="dob" value="<?= $user['DateOfBirth'] ?>">
                            </div>

                            <div class="input-group">
                                <label>Gender</label>
                                <select name="gender">
                                    <option value="Male" <?= ($user['gender'] === 'Male') ? 'selected' : '' ?>>Male
                                    </option>
                                    <option value="Female" <?= ($user['gender'] === 'Female') ? 'selected' : '' ?>>
                                        Female</option>
                                </select>
                            </div>

                            <!-- personal information -->
                            <!-- student -->
                            <?php if ($role === 'student'): ?>

                            <div class="section-label">Student Details</div>

                            <div class="input-group">
                                <label>TP Number</label>
                                <input type="text" name="tpNumber" value="<?= $student['tpNumber'] ?>">
                            </div>

                            <div class="input-group">
                                <label>Intake Code</label>
                                <input type="text" name="intakeCode" value="<?= $student['intakeCode'] ?>">
                            </div>

                            <div class="input-group">
                                <label>Programme</label>
                                <select name="programme">
                                    <option value="cs" <?= ($student['programme'] == 'cs') ? 'selected' : '' ?>>Computer
                                        Science</option>
                                    <option value="se" <?= ($student['programme'] == 'se') ? 'selected' : '' ?>>Software
                                        Engineering</option>
                                    <option value="ai" <?= ($student['programme'] == 'ai') ? 'selected' : '' ?>>
                                        Artificial Intelligence</option>
                                    <option value="is" <?= ($student['programme'] == 'is') ? 'selected' : '' ?>>
                                        Information Systems</option>
                                </select>
                            </div>

                            <div class="input-group">
                                <label>
                                    <i class="bx bxs-leaf"></i>
                                    Leaf
                                </label>
                                <input type="number" name="leaf" value="<?= (int)$student['leaf'] ?>">
                            </div>
                            <!-- organizer -->
                            <?php elseif ($role === 'organizer'): ?>

                            <div class="section-label">Organizer Details</div>

                            <div class="input-group">
                                <label>Club Name</label>
                                <select name="club">
                                    <option value="eco"
                                        <?= ($organizer['club'] === 'Eco & Sustainability Club') ? 'selected' : '' ?>>
                                        Eco & Sustainability Club
                                    </option>
                                    <option value="recycle"
                                        <?= ($organizer['club'] === 'Recycling & Waste Management Club') ? 'selected' : '' ?>>
                                        Recycling & Waste Management Club
                                    </option>
                                    <option value="green"
                                        <?= ($organizer['club'] === 'Green Campus Initiative') ? 'selected' : '' ?>>
                                        Green Campus Initiative
                                    </option>
                                    <option value="energy"
                                        <?= ($organizer['club'] === 'Renewable Energy Society') ? 'selected' : '' ?>>
                                        Renewable Energy Society
                                    </option>
                                    <option value="wildlife"
                                        <?= ($organizer['club'] === 'Wildlife & Nature Conservation Club') ? 'selected' : '' ?>>
                                        Wildlife & Nature Conservation Club
                                    </option>
                                    <option value="community"
                                        <?= ($organizer['club'] === 'Community Social Responsibility (CSR) Club') ? 'selected' : '' ?>>
                                        Community Social Responsibility (CSR) Club
                                    </option>
                                </select>
                            </div>

                            <div class="input-group">
                                <label>Position</label>
                                <select name="organizerPosition">
                                    <option value="member"
                                        <?= ($organizer['position'] === 'member') ? 'selected' : '' ?>>Member</option>
                                    <option value="committee"
                                        <?= ($organizer['position'] === 'committee') ? 'selected' : '' ?>>Committee
                                        Member</option>
                                    <option value="secretary"
                                        <?= ($organizer['position'] === 'secretary') ? 'selected' : '' ?>>Secretary
                                    </option>
                                    <option value="treasurer"
                                        <?= ($organizer['position'] === 'treasurer') ? 'selected' : '' ?>>Treasurer
                                    </option>
                                    <option value="vice" <?= ($organizer['position'] === 'vice') ? 'selected' : '' ?>>
                                        Vice</option>
                                    <option value="president"
                                        <?= ($organizer['position'] === 'president') ? 'selected' : '' ?>>President
                                    </option>
                                </select>
                            </div>

                            <!-- admin -->
                            <?php elseif ($role === 'admin'): ?>

                            <div class="section-label">Admin Details</div>

                            <div class="input-group">
                                <label>Position</label>
                                <select name="adminPosition">
                                    <option value="system_admin"
                                        <?= ($admin['position'] == 'system_admin') ? 'selected' : '' ?>>System Admin
                                    </option>
                                    <option value="content_admin"
                                        <?= ($admin['position'] == 'content_admin') ? 'selected' : '' ?>>Content Admin
                                    </option>
                                    <option value="event_admin"
                                        <?= ($admin['position'] == 'event_admin') ? 'selected' : '' ?>>Event Admin
                                    </option>
                                </select>
                            </div>

                            <?php endif; ?>

                        </div> <!-- end fields -->


                        <!-- foot -->
                        <div class="form-actions">
                            <button type="submit" form="deleteUserForm" class="btn btn-delete" name="action"
                                value="delete"
                                onclick="return confirm('Are you sure you want to delete this user?');">Delete
                                User</button>

                            <div class="right-buttons">
                                <a href="user_management.php" class="btn btn-cancel">Cancel</a>
                                <button type="submit" class="btn btn-save">Save Changes</button>
                            </div>
                        </div>

                    </div> <!-- End Right Panel -->
                </div>
            </form>
        </div>
    </div>

    <!-- delete form -->
    <form id="deleteUserForm" action="../backend/update_user_handle.php" method="POST">
        <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">
        <input type="hidden" name="returnTo" value="user_edit.php?userId=<?= (int)$userId ?>">
    </form>

    <!-- reset password form -->
    <form id="resetUserForm" action="../backend/update_user_handle.php" method="POST">
        <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">
        <input type="hidden" name="action" value="reset">
    </form>


    <!-- toast message -->
    <div id="toast" class="toast"></div>

    <!-- javascript -->

</body>

</html>