<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoLeaf – Choose Your Role</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="./assets/css/roleSelect.css">
</head>

<body class="register-page">
    <div class="selection-card">
        <div class="icon-slot">
            <img src="./assets/image/EcoLeaf_icon.png" alt="EcoLeaf logo">
        </div>

        <div class="welcome-copy">
            <p>Welcome to EcoLeaf</p>
            <h1 class="tab-bar">Sustainability Participation Platform</h1>
            <p class="subtext">Please select your role to access your dashboard.</p>
        </div>

        <div class="role-row">
            <a class="role-card" href="student/student_portal.php">
                <div class="role-icon" aria-hidden="true">
                    <img src="./assets/image/student.png" alt="Student role">
                </div>
                <h2>Student</h2>
            </a>

            <a class="role-card" href="organizer/organizer_portal.php">
                <div class="role-icon" aria-hidden="true">
                    <img src="./assets/image/organizer.png" alt="Organizer role">
                </div>
                <h2>Organizer</h2>
            </a>

            <a class="role-card" href="admin/admin_portal.php">
                <div class="role-icon" aria-hidden="true">
                    <img src="./assets/image/admin.png" alt="Administrative role">
                </div>
                <h2>Administrative</h2>
            </a>
        </div>
    </div>
</body>

</html>