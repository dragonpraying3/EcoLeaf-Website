<?php
include_once("../topbar.php");

if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: admin_portal.php");
    exit();
}
// search & filter
// search
$search = trim($_GET['search'] ?? '');
$search = $conn->real_escape_string($search);

// get role and status variable
$role   = $_GET['role']   ?? 'all';
$status = $_GET['status'] ?? 'all';

// add roles to array , bc is fixed
$validRoles   = ['student', 'organizer', 'admin'];
$validStatus  = ['active', 'inactive'];

// double checking
if (!in_array($role, $validRoles)) {
    $role = 'all';
}
if (!in_array($status, $validStatus)) {
    $status = 'all';
}

$where = "WHERE 1 ";

if ($search !== '') {
    $where .= " AND (
        u.name LIKE '%$search%' 
        OR u.username LIKE '%$search%' 
        OR u.email LIKE '%$search%'
    )";
}

if ($role !== 'all') {
    $where .= " AND u.role = '$role'";
}

if ($status !== 'all') {
    $where .= " AND u.status = '$status'";
}

// pagination
// setting the start value
$start = 0;

// setting the number of rows to display in 1 page
$rows_per_page = 6;

// get the total number of rows(nr) user
$records = $conn->query("SELECT COUNT(*) AS total FROM users u $where");
$rowCount = $records->fetch_assoc();
$nr_of_rows = (int)$rowCount['total'];

//  calculate the nr of pages
//  ceil = always round up 
//  eg. 5.3 -> 6.0
$totalPage = ceil($nr_of_rows / $rows_per_page);

// if the user click on the pagination will set a new start point
if(isset($_GET['page-nr'])) {
    $page = $_GET['page-nr'] - 1;
    $start = $page * $rows_per_page;
}
$sql = "SELECT u.userId,u.name,u.username,u.email,u.role,u.status,u.gender,u.phone,
    CASE 
        WHEN u.role = 'student'   THEN s.joinDate
        WHEN u.role = 'organizer' THEN o.joinDate
        WHEN u.role = 'admin'     THEN a.joinDate
    END AS joinedDate,
    s.tpNumber,
    s.programme,
    s.intakeCode,
    s.leaf,
    o.club,
    o.position   AS organizerPosition,
    a.position   AS adminPosition
    FROM users u
    LEFT JOIN student   s ON s.userId = u.userId
    LEFT JOIN organizer o ON o.userId = u.userId
    LEFT JOIN admin     a ON a.userId = u.userId
    $where
    ORDER BY joinedDate DESC, u.userId DESC
    LIMIT $start, $rows_per_page";

$result = $conn->query($sql);

$currentPage = isset($_GET['page-nr']) ? $_GET['page-nr'] : 1;
$prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
$nextPage = ($currentPage < $totalPage) ? $currentPage + 1 : null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoLeaf - User Management</title>

    <!--fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!--css-->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/userManagement.css">
</head>

<body>

    <div class="dashboard-container">
        <!--search and filter-->
        <form method="get" class="controls-bar">
            <div class="search-wrapper">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <!-- search -->
                <input type="text" name="search" value="<?=htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Search name, ID or email">
            </div>

            <!--role filter -->
            <select name="role" class="filter-select" onchange="this.form.submit()">
                <option value="all" <?= $role === 'all' ? 'selected' : '' ?>>All Roles</option>
                <option value="student" <?= $role === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="organizer" <?= $role === 'organizer' ? 'selected' : '' ?>>Organizer</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <!--status filter-->
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Status</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </form>

        <!-- data table-->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User Info</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <!--user info-->
                        <td>
                            <?php
                        //  avatar taking first 2 letter
                        $avatar = strtoupper(substr($row['name'], 0, 2));
                        $name = $row['name'];

                        $subtitle = $row['email'] . " (" . ucfirst($row['role']) . ")";

                        echo "
                        <div class='user-info-cell'>
                            <div class='table-avatar'>$avatar</div>
                            <div class='user-details'>
                                <h4>$name</h4>
                                <span>$subtitle</span>
                            </div>
                        </div>
                        ";
                    ?>
                        </td>

                        <!--role-->
                        <td>
                            <span class="badge badge-<?= $row['role'] ?>">
                                <?= ucfirst($row['role']) ?>
                            </span>
                        </td>

                        <!--status-->
                        <td>
                            <span class="status-dot status-<?= $row['status'] ?>"></span>
                            <?= ucfirst($row['status']) ?>
                        </td>

                        <!--joined date-->
                        <td><?= date("M d, Y", strtotime($row['joinedDate'])) ?></td>

                        <!--button-->
                        <td>
                            <a class="action-btn" href="user_edit.php?userId=<?= $row['userId'] ?>">Edit</a>
                            <form class="inline-form" action="../backend/update_user_handle.php" method="POST">
                                <input type="hidden" name="userId" value="<?= (int)$row['userId'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <!-- this for get the current url -->
                                <input type="hidden" name="returnTo"
                                    value="user_management.php?<?= http_build_query($_GET) ?>">
                                <button type="submit" class="action-btn delete-btn"
                                    onclick="return confirm('Delete this user?');">Delete</button>
                            </form>
                        </td>

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- pagination  -->
        <div class="pagination">

            <!-- first page -->
            <?php 
            if($currentPage <= 1) {
                echo "<a class='page-btn disabled'>First</a>";
            }else {
                echo "<a class='page-btn' href='?page-nr=1'>First</a>";
            }
        ?>

            <!-- Previous page -->
            <?php
            if ($prevPage) {
                echo "<a class='page-btn' href='?page-nr=$prevPage'>Prev</a>";
            } else {
                echo "<a class='page-btn disabled'>Prev</a>";
            }
        ?>

            <!-- page number -->
            <?php 
        // i = page number
            for ($i=1; $i <= $totalPage; $i++) { 
                $activeClass = ($i == $currentPage) ? "active" : "";
                echo "<a class='page-btn $activeClass' href='?page-nr=$i'>$i</a>";
            }
        ?>
            <!-- Next page -->
            <?php  
            if(!isset($_GET['page-nr'])) {
                echo '<a class="page-btn" href="?page-nr=2">Next</a>';
            } else {
                if ($_GET['page-nr'] >= $totalPage) {
                    echo '<a class="page-btn disabled">Next</a>';
                } else {
                    echo "<a class='page-btn' href='?page-nr={$nextPage}'>Next</a>";
                }
            }
        ?>


            <!-- Last page -->
            <?php 
        if($currentPage >= $totalPage) {
            echo "<a class='page-btn disabled'>Last</a>";
        }else {
            echo "<a class='page-btn'href='?page-nr={$totalPage}'>Last</a>";
        }
        ?>


        </div>
    </div>

</body>

</html>