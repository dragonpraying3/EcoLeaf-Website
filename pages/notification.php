<?php 
include_once '../topbar.php'; 
require_once __DIR__ . '/../backend/notifyLogic.php';

// var_dump($_POST);

date_default_timezone_set('Asia/Kuala_Lumpur');

$studentId = null;
$organizerId = null;
$adminId = null;

//checking role
$role=$_SESSION['user']['role']??'guest';
$userId=$_SESSION['user']['userId']??null;

switch ($role){
    case 'student':
        $studentId=$_SESSION['user']['studentId'];
        break;
    case 'organizer':
        $organizerId=$_SESSION['user']['organizerId'];
        break;
    case 'admin':
        $adminId=$_SESSION['user']['adminId'];
        break;
    default:
        null;
}

// var_dump($_POST);

date_default_timezone_set('Asia/Kuala_Lumpur');


//-------------------------------------------------------------------
//pagination
//?page-nr the ? means URL query string 
// example: notifications.php?page-nr=3&sort=date

$start=0;
$rows_per_page=5; //number of row want to display each page

//get the total number of rows
$total_row="SELECT count(*) AS total
from notification
WHERE userId = $userId;";
$result_total= $conn->query($total_row); //object 
$row_total=$result_total->fetch_assoc();
$total_rows= $row_total['total']; //number of rows in the table

$pages=ceil($total_rows/$rows_per_page); //round up to nearest whole number e.g. 12/5=2.4 ->3 pages

//if the user click the page number start a new starting point
if (isset($_GET['page-nr'])) {
    $page = (int)$_GET['page-nr'];

    // prevent page less than 1
    if ($page < 1) {
        $page = 1;
    }

    // prevent page more than total pages
    if ($page > $pages && $pages > 0) {
        $page = $pages;
    }

    $start = ($page - 1) * $rows_per_page;
}
//-------------------------------------------------------------------
//update button
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marking'])){

    $notifyId=$_POST['notifyId']; 
    
    $marking=markAsRead($conn, $notifyId);
    if ($marking){
        echo '<script>
        console.log("success");
        </script>';
    }else{
        echo '<script>
        console.log("fail");
        </script>';
    }
}
//-------------------------------------------------------------------
//select from nottification table

//sorting 
$orderBy="sendAt DESC"; //default
if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['sort'])){
    $condition=$_GET['sort'];
    
    switch ($condition){
        case 'oldest':
            $orderBy="sendAt ASC";
            break;
        case 'newest':
            $orderBy="sendAt DESC";
            break;
        case 'unread':
            $orderBy="isRead ASC, sendAt DESC"; //get newest 
            break;
        case 'read':
            $orderBy="isRead DESC, sendAt DESC";
            break;
    }
}

$sql="SELECT * 
    FROM notification 
    WHERE userId = $userId 
    ORDER BY $orderBy
    LIMIT $start,$rows_per_page;"; //0 is starting point, 6 is number of rows to return

$sql2="SELECT COUNT(*) AS unreadCount 
    FROM notification 
    WHERE userId = $userId
    AND isRead = 0;";
    
$result = mysqli_query($conn, $sql); 
$result2=$conn->query($sql2);
$unread = 0;

if ($result && $result2 ->num_rows>0){
    $row2=$result2->fetch_assoc();
    $unread = $row2['unreadCount'];
    
} else {
    echo "<script>No rows found.</script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notification</title>
    <link rel="stylesheet" href="/EcoLeaf/assets/css/notificationDesign.css">
    <script type="text/javascript" src="/EcoLeaf/assets/js/notify.js" defer></script>
</head>

<?php 

if(isset($_GET['page-nr'])){
    $id=$_GET['page-nr'];
}else{
    $id=1;
}
?>

<body id="<?php echo $id; ?>">
    <?php include_once '../topbar.php'; ?>
    <div class="body-container">

        <!--title-->
        <div class="banner-block">
            <div class="banner">
                <div class="description">
                    <div class="subtitle" id="title-bold">Notifications (<?php 
                        echo htmlspecialchars($unread);
                        ?>)
                    </div>
                    <div class="subtitle">Stay updated with the latest alerts and messages.</div>
                </div>
            </div>
        </div>

        <div class="search-section">
            <form method="get" id="searchForm">
                <input type="search" id="searching" name="searching" placeholder="Search notifications by title...">

                <select name="sort" id="sorting">
                    <option value="" selected desabled hidden>Sort by</option>
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="unread">Unread First</option>
                    <option value="read">Read First</option>
                </select>
            </form>
        </div>

        <div class="table-zone">
            <table id="notify">
                <tr class="first">
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>

                <?php 
                if (!empty($_GET['searching'])){
                    // Sanitize user input to prevent SQL injection
                    //e.g. '/''/\/%/\' all will break the search bar
                    $searchKey=$conn->real_escape_string($_GET['searching']);

                    //any that contain the search key
                    //limit by 5 per page
                    $query = "SELECT * FROM notification 
                    WHERE title LIKE '%$searchKey%' 
                    AND userId = $userId
                    ORDER BY $orderBy
                    LIMIT $start,$rows_per_page;
                    ";

                    $result = mysqli_query($conn, $query);
                    
                    //if no result shows back with original table
                    if ($result->num_rows == 0) {
                        $search_word = "No results found for <b>" . htmlspecialchars($searchKey) . "</b>.";
                        echo "<tr><td colspan='4' class='no-result' >$search_word</td></tr>"; //colspan is merge 4 columns
                    }
                    
                }else{
                    $result=$conn->query($sql);
                }
                ?>

                <?php while ($row=$result->fetch_assoc()): ?>
                <form method="POST">
                    <tr class="content">
                        <input type="hidden" class="status" value="<?php echo $row['isRead']; ?>">

                        <td class="left"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td class="left"><?php echo htmlspecialchars($row['message']); ?></td>
                        <td><?php echo date("j M Y, H:i", strtotime($row['sendAt'])); ?></td>
                        <td>
                            <input type="hidden" class="status" name="notifyId" value="<?php echo $row['notifyId']; ?>">

                            <?php 
                            if ($row['isRead']==0){
                                    echo '<button type="submit" class="read-btn" name="marking" value="Mark as Read">Mark as Read
                            </button>'; 
                            } else {
                            echo '<button type="button" class="read-btn read" value="Read" disabled>Read</button>';
                            }
                        ?>
                </form>
                </td>
                </tr>
                <?php endwhile; ?>
            </table>

        </div>

        <!--pagination-->
        <div class="pagination">
            <?php 
            //check whether have sort 
            $sortTitle=$_GET['sort']??'';
            ?>
            <a href="?page-nr=1&sort=<?php echo $sortTitle;?>">First</a>

            <?php             
            //if current display is greater than 1 show the previous button
            if (isset($_GET['page-nr']) && $_GET['page-nr']>1){
            ?>
            <a href="?page-nr=<?php echo $_GET['page-nr']-1 ?>&sort=<?php echo $sortTitle;?>">
                < </a>
                    <?php }else{ ?>
                    <a>
                        < </a>
                            <!--normal disabled button-->
                            <?php } ?>

                            <?php if(!isset($_GET['page-nr'])){
                                ?>
                            <a href="?page-nr=2">></a>
                            <?php 
                        }else{ 
                            if($_GET['page-nr']>= $pages){
                            ?>
                            <a>
                                > </a>
                            <?php }else{
                          ?>
                            <a href="?page-nr=<?php echo $_GET['page-nr']+1; ?>&sort=<?php echo $sortTitle;?>">></a>

                            <?php }}?>


                            <a href="?page-nr=<?php echo $pages ?>&sort=<?php echo $sortTitle;?>">Last
                            </a>
        </div>
        <div class="page-info">

            <?php 
            if (!isset($_GET['page-nr'])){
                $page=1;
            }else{
                $page=$_GET['page-nr'];
            }
            
            ?>
            Showing <?php echo $page ?> of <?php echo $pages ?> pages |
            <?php echo $start+1 ?>-<?php echo $start+5<$total_rows?$start+5:$total_rows ?>
            of
            <?php echo $total_rows ?> records
        </div>

    </div>
    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
</body>

</html>