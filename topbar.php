<?php
session_start();
include('session.php');
include_once 'backend/database.php';
$username = $_SESSION['username']; 

$user="SELECT u.userId,u.role,u.name,u.username,u.email,s.leaf,s.studentId,o.organizerId,a.adminId
FROM users AS u
LEFT JOIN student AS s 
ON u.userId = s.userId
LEFT JOIN organizer AS o
ON u.userId = o.userId
LEFT JOIN admin AS a
ON u.userId = a.userId
WHERE u.username =  '$username';
";

$result=$conn->query($user);

if ($result->num_rows>0){ //if got result 
    $row=$result->fetch_assoc(); //fetch the row as an associative array
    // $user_role=$row['role']; //get the role
    
    $_SESSION['user']=[
    'userId'=>trim($row['userId']),
    'name'=>trim($row['name']),
    'role'=>trim($row['role']),
    'username'=>trim($row['username']),
    'email'=>trim($row['email']),
    'leaf' => $row['leaf'], 
    'studentId'   => $row['studentId'],   // null if not student
    'organizerId' => $row['organizerId'], // null if not organizer
    'adminId'     => $row['adminId']   // null if not admin

];
} else {
    $_SESSION['user'] = [
        'userId'=>null,
        'name' => 'Guest',
        'role' => 'guest',
        'username' => '',
        'email' => '',
        'leaf' => null, 
        'studentId' => null,
        'organizerId' => null,
        'adminId' => null
    ]; //default role if no active user found
}
?>


<!--connect to boxicons and css-->
<link href='https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="/EcoLeaf/assets/css/topbarDesign.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" src="/EcoLeaf/assets/js/topbar.js" defer></script>

<!--top bar-->
<div class="topbar">
    <div class="logo">
        <img src="/EcoLeaf/assets/image/EcoLeaf.png" alt="EcoLeaf Logo">
    </div>

    <div class="button">
        <div class="small-icon">
            <a href="/EcoLeaf/pages/notification.php" class="notification">

                <?php 
                //check if have unread notification

                $sql="SELECT * FROM notification 
                WHERE userId = ".$_SESSION['user']['userId']." AND isRead = 0;"; 

                $result=$conn->query($sql);
                if ($result->num_rows>0){
                    echo "<i class='bx  bx-bell'></i>
                <span class='badge'></span></a>";
                }else{
                    echo "<i class='bx  bx-bell'></i>";
                }
                ?>

                <a href="/EcoLeaf/pages/information.php" class="information"><i class='bx  bx-info-circle'></i></a>

                <div class="profile-trigger">
                    <div class="user-icon"><i class='bx bx-user-circle'></i>
                    </div>

                    <div class="profile">
                        <div class="profile-menu">
                            <div class="user-info">
                                <div class="profile-picture">
                                    <?php
                        //  avatar taking first 2 letter
                        $avatar = strtoupper(substr($row['name'], 0, 2));

                        echo "
                            <div class='table-avatar'>$avatar</div>
                        ";
                    ?>
                                </div>

                                <div class="personal">
                                    <div id="account">
                                        <?php echo htmlspecialchars($_SESSION['user']['username'] ?? ''); ?>
                                    </div>
                                    <div id="email">
                                        <?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="action">
                                <a href="/EcoLeaf/pages/edit_profile.php" class="edit">
                                    <div id="edit">Edit Profile</div>
                                </a>
                                <a href="/EcoLeaf/logout.php" class="ion logout">
                                    <div id="logout">Logout</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<!--check user role-->
<script>
const user_role = "<?php if(isset($_SESSION['user']['role'])){
    echo trim($_SESSION['user']['role']);}?>";
console.log("role : " + user_role);
</script>

<!--student navigation bar-->
<div class="navigation nav-student">
    <div class="navbar">
        <ul>
            <li><a href="/EcoLeaf/student/student_dashboard.php">Dashboard</a></li>

            <li class="event">
                <a href="/EcoLeaf/student/UpcomingEvents.php">Events <i class='bx  bx-caret-down'></i> </a>
            </li>

            <li><a href="/EcoLeaf/pages/carbonccl.php">Calculator</a></li>

            <li class="DIY">
                <a href="/EcoLeaf/student/diy.php">DIY Hub <i class='bx  bx-caret-down'></i> </a>
            </li>

            <li><a href="/EcoLeaf/student/leaderboard.php">Leaderboard</a></li>
            <li><a href="/EcoLeaf/student/reward_redemption.php">Rewards</a></li>
        </ul>
    </div>
    <div class="event-dropdown">
        <div class="event-1"><a href="/EcoLeaf/student/UpcomingEvents.php">Upcoming Event</a></div>
        <div class="event-1"><a href="/EcoLeaf/student/myEvents.php">My Event</a></div>
    </div>
    <div class="DIY-dropdown">
        <div class="diy-1"><a href="#request-management">Project Request</a></div>
        <div class="diy-1"><a href="#trade-management">Trade Tracking</a></div>
    </div>
</div>

<!--organizer navigation bar-->
<div class="navigation nav-organizer">
    <div class="navbar">
        <ul>
            <li><a href="/EcoLeaf/organizer/organizer_dashboard.php">Dashboard</a></li>
            <li><a href="/EcoLeaf/organizer/createEvent.php">Create Event</a></li>
            <li><a href="/EcoLeaf/organizer/myEvent.php">My Event</a></li>
            <li><a href="/EcoLeaf/pages/carbonccl.php">Calculator</a></li>
        </ul>
    </div>
</div>

<!--admin navigation bar-->
<div class="navigation nav-admin">
    <div class="navbar">
        <ul>
            <li><a href="/EcoLeaf/admin/admin_dashboard.php">Dashboard</a></li>
            <li><a href="/EcoLeaf/admin/eventApproval.php">Events</a></li>
            <li><a href="/EcoLeaf/admin/badgeManagement.php">Badges</a></li>
            <li><a href="/EcoLeaf/admin/rewards_management.php">Rewards</a></li>
            <li><a href="/EcoLeaf/pages/carbonccl.php">Calculator</a></li>
            <li><a href="/EcoLeaf/admin/diyModerate.php">Moderation</a></li>
            <li><a href="/EcoLeaf/admin/user_management.php">Users</a></li>
        </ul>
    </div>
</div>