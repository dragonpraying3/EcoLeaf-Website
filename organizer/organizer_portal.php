<?php
// current role portal file
$currentRole = 'organizer';

$rememberUsername = '';
$rememberPassword = '';
$rememberChecked  = false;

if (isset($_COOKIE['remember_me'], $_COOKIE['remember_role']) && $_COOKIE['remember_role'] === $currentRole) {
    $rememberUsername = $_COOKIE['remember_username'] ?? '';
    $rememberPassword = $_COOKIE['remember_password'] ?? '';
    $rememberChecked  = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoLeaf - Organizer Access</title>
    <link rel="stylesheet" href="../assets/css/portal.css">
</head>

<body class="register-page">

    <div class="portal-card">

        <div class="icon-slot" aria-hidden="true">
            <img src="../assets/image/EcoLeaf_icon.png" alt="EcoLeaf logo">
        </div>

        <div class="welcome-copy">
            <p>Organizer Portal</p>
            <h1>APU Sustainability Participation Platform</h1>
        </div>

        <div class="tab-bar">
            <button class="tab-btn active" data-type="login">Login</button>
            <button class="tab-btn" data-type="register">Register</button>
        </div>

        <p id="form-heading" class="form-heading" data-role="Organizer">Organizer Login</p>

        <?php if (isset($_GET['registered'])): ?>
        <div class="alert success">
            <span class="alert-message">Registration successful! Please login with your new account.</span>
            <span class="alert-close">&times;</span>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
        <div class="alert error">
            <span class="alert-message">
                <?php
                    switch ($_GET['error']) {
                        case 'empty': echo "Please fill in all required fields."; break;
                        case 'user_exists': echo "This username or email is already taken."; break;
                        case 'invalid': echo "Invalid username or password."; break;
                        case 'db': echo "Database error. Please try again later."; break;
                        case 'inactive': echo "This account is inactive. Contact admin."; break;
                        case 'organizer_db': echo "System error creating organizer account."; break;
                        case 'weak_password': echo "Password must be at least 8 chars and include a number and a special symbol."; break;
                        default: echo "An unknown error occurred.";
                    }
                    ?>
            </span>
            <span class="alert-close">&times;</span>
        </div>
        <?php endif; ?>

        <div class="forms-wrapper">
            <form class="portal-form" id="Organizer-form" method="post" action="../backend/auth_handle.php">
                <input type="hidden" name="mode" id="form-mode" value="login">
                <input type="hidden" name="role" value="organizer">

                <div class="form-grid base-grid">
                    <!-- Username -->
                    <label>
                        <span>Username</span>
                        <input type="text" name="username" value="<?= htmlspecialchars($rememberUsername) ?>"
                            placeholder="Enter your username" pattern="[A-Za-z0-9]+" required>
                        <small class="helper-text register-note">Username cannot contain spaces.</small>
                    </label>

                    <!-- Password -->
                    <label>
                        <span>Password</span>
                        <input id="passwordInput"
                        type="password" name="password" placeholder="Enter your password"
                            value="<?= htmlspecialchars($rememberPassword) ?>" required>
                        <small class="helper-text register-note">Password must be unique</small>
                    </label>
                </div>

                <!-- remember me box -->
                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember_me" value="1" <?= $rememberChecked ? 'checked' : '' ?>>
                        <span>Remember Me</span>
                    </label>
                </div>

                <div class="form-grid extra-grid">

                    <!-- Full Name -->
                    <label>
                        <span>Full Name</span>
                        <input type="text" name="fullName" placeholder="As per organizer record" required>
                    </label>

                    <!-- Contact Number -->
                    <label>
                        <span>Contact Number</span>
                        <input type="tel" name="phone" placeholder="60123456789" inputmode="numeric"
                            pattern="[0-9]*" minlength="10" required>
                    </label>

                    <!-- Date of Birth -->
                    <label>
                        <span>Date of Birth</span>
                        <input type="date" name="dob" max="<?=date('Y-m-d') ?>" required>
                    </label>

                    <!-- Gender -->
                    <div class="control-group">
                        <span>Gender</span>
                        <div class="inline-group">
                            <label><input type="radio" name="gender" value="male" required> Male</label>
                            <label><input type="radio" name="gender" value="female"> Female</label>
                        </div>
                    </div>

                    <!-- Email -->
                    <label class="full-span">
                        <span>Email</span>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </label>

                    <!-- Club -->
                    <label>
                        <span>Club</span>
                        <select name="club" required>
                            <option value="">Please select your Club</option>
                            <option value="eco">Eco & Sustainability Club</option>
                            <option value="recycle">Recycling & Waste Management Club</option>
                            <option value="green">Green Campus Initiative</option>
                            <option value="energy">Renewable Energy Society</option>
                            <option value="wildlife">Wildlife & Nature Conservation Club</option>
                            <option value="community">Community Social Responsibility (CSR) Club</option>
                        </select>
                    </label>

                    <!-- Club Position -->
                    <label>
                        <span>Club Position</span>
                        <select name="clubPosition" required>
                            <option value="">Please select your Club Position</option>
                            <option value="member">Member</option>
                            <option value="committee">Committee Memrber</option>
                            <option value="secretary">Secretary</option>
                            <option value="treasurer">Treasurer</option>
                            <option value="vice">Vice President</option>
                            <option value="president">President</option>

                        </select>
                    </label>
                </div>

                <div class="form-actions">
                    <a class="back-btn" href="../access_portal.php">Back</a>
                    <button type="submit" class="primary-btn" id="portal-submit">Login</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/portal.js"></script>
</body>

</html>
