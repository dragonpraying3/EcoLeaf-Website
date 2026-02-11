<?php
include_once '../topbar.php'; 
require_once __DIR__ . '/../backend/popup.php';

$studentId = null;
$organizerId = null;
$adminId = null;

//checking role
$role=$_SESSION['user']['role']??'guest';

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

//-------------------------------------------------------------------
//pagination
$start=0;
$rows_per_page=5;

$total_row="SELECT count(*) as total
from badge
";
$result_total=$conn->query($total_row);
$row_total=$result_total->fetch_assoc();
$total_rows= $row_total['total']; //get the attribute
$pages=ceil($total_rows/$rows_per_page);

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
//check empty form submission
function getBadgeEmptyError($name,$icon,$criteria,$value){
    $errors="";
    
    if (empty($name) && empty($icon) && empty($criteria) && empty($value)){
        $errors="⊗ All fields are required!";
        return $errors;
    }
    
    if (empty($name)){
        $errors.="Badge Name is required. ";
    }
    if (empty($icon)){
        $errors.="Icon is required. ";
    }
    if (empty($criteria)){
        $errors.="Criteria is required. ";
    }
    if (empty($value)){
        $errors.="Leaf Value is required. ";
    }
    return $errors;
}

//if same criteria and value, show duplicate error
function checkDuplicateBadge($conn,$criteria,$value){
    $duplication="";

    $checkDuplicate="SELECT criteria,value
    FROM badge
    WHERE criteria='$criteria' AND value='$value'";

    $result=$conn->query($checkDuplicate);
    if ($result->num_rows == 1){ //if have result by 1
        $duplication.="This badge has been created.";
    }
    return $duplication;
}

if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["publish"])){
    $name=$_POST['badge-name'];
    $icon=$_POST['icon_key'];
    $colour=$_POST['icon_color'];
    $criteria=$_POST['criteria'];
    $value=$_POST['value'];

    $errors=getBadgeEmptyError($name,$icon,$criteria,$value);
    $errors2=checkDuplicateBadge($conn,$criteria,$value);
    
    if (empty($errors) && empty($errors2)){
        $saveBadge="INSERT INTO badge(badgeName,criteria,value,icon_key,icon_colour,status,adminId)
        
        VALUES('$name','$criteria','$value','$icon','$colour','visible',?);";

        $result = $conn->prepare($saveBadge);
        if($result===false){ //check if the prepare statement is fail
            die("Error preparing statement:".$conn->error);
        }
        
        $result->bind_param("i",$adminId);

        if ($result->execute()){
                echo '<p>
                <script>
                showRelevantPop(\'badge-complete\');  
                </script>
                </p>';
        }else{
            echo "<script>console.log('Fail');</script>";
        }
    }
}

//---------------------------------------------------------------------
//update hidden and unhide status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])){
    $action=$_POST['action']??null;
    $badgeId=$_POST['badgeId']??null;

    switch ($action){
        case 'hide':
            $newStatus='hidden';
            $badgeUsed="badge-hidden";
            break;
        case 'unhide':
            $newStatus='visible';
            $badgeUsed="badge-unhidden";
            break;
        default:
            exit('Invalid action');
    }

    $updateStatus="UPDATE badge
    SET status=?
    WHERE badgeId=?;
    ";

    $status = $conn->prepare($updateStatus);
    $status ->bind_param("si",$newStatus,$badgeId);
    $status -> execute();

    if ($status ->affected_rows>0){
            echo '<p>
            <script>
            showRelevantPop("'.$badgeUsed.'");  
            </script>
            </p>';
        }else{
            echo "Update error: " . $conn->error;
    }
}
//----------------------------------------------------------------------
//delete the badge
if (isset($_POST['delete'])){
    $badgeId=$_POST['badgeId'];
    
    //php cannot wait js to process
    $updateStatus="DELETE FROM badge
    WHERE badgeId=?;
    ";
    
    $status = $conn->prepare($updateStatus);
    $status ->bind_param("i",$badgeId);
    $status -> execute();
    
    if ($status ->affected_rows>0){
        echo '<p>
         <script>
         showRelevantPop(\'badge-delete\');  
         </script>
         </p>';
    }else{
        echo "Update error: " . $conn->error;
    }
}


//----------------------------------------------------------------------
//select available badge
$available="SELECT badgeId,badgeName,criteria,value,icon_key,icon_colour
FROM badge
WHERE status='visible';
";

$whole="SELECT badgeId,badgeName,criteria,value,icon_key,icon_colour,status
FROM badge
";

$availableResult = $conn-> query($available);
$allResult = $conn-> query($whole);

$badgeCount=$availableResult->num_rows;

//----------------------------------------------------------------------
//display statistics
$query="SELECT b.badgeName,b.criteria,b.value,b.icon_key,b.icon_colour,COUNT(*) as total
FROM studentbadge as s
JOIN badge as b
ON s.badgeId=b.badgeId
GROUP BY s.badgeId
LIMIT $start,$rows_per_page ;
";
$display=$conn->query($query);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href='https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/EcoLeaf/assets/css/badgeManagement.css">
    <script type="text/javascript" src="/EcoLeaf/assets/js/badge.js" defer></script>
    <title>Badges</title>
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
        <!--------------Badge Management Introduction Zone ------------------------->
        <div class="introduction-box">
            <div class="introduction-wrapper">
                <div class="introduction-content">
                    <div class="title" id="title-bold">Badge Management</div>
                    <div class="title">Create achievement badges for tracking user progress</div>
                </div>

                <div class="create-badge-button">
                    <button type="button" id="create-badge"><i class='bx  bxs-plus'></i> Create Badge</button>
                </div>
            </div>
        </div>

        <!----Available Badges-->
        <div class="available-box">
            <div class="available-banner">
                <div class="banner-zone">
                    <div class="title" id="title-bold">Available Badges (<?php echo $badgeCount;?>)</div>
                    <div class="title">All achievement badges in the system</div>
                </div>

                <form method="GET" id="searchForm" class="search-wrapper">
                    <i class="bx bx-search search-icon"></i>
                    <input type="search" id="searching" name="searching" placeholder="Search badge by name..."
                        autocomplete="off">
                </form>

                <div class="badge-zone <?php echo $badgeCount >3? 'scroll-mode':'' ?>">
                    <?php 

                    if(!empty($_GET['searching'])){
                        $searchKey=$conn->real_escape_string($_GET['searching']);
                        
                        $execute="SELECT *
                        FROM badge
                        WHERE badgeName LIKE '%$searchKey%'
                        ORDER BY badgeName ASC";
                        $allResult = $conn-> query($execute);
                        if ($allResult->num_rows==0){
                            $search_word= "No results found for <b>" . htmlspecialchars($searchKey) . "</b>.";
                            echo "<div>$search_word</div>";
                        }
                    }else{
                        $allResult = $conn-> query($whole);
                    }
                    
                    ?>
                    <?php while ($row=$allResult->fetch_assoc()): ?>
                    <div class="badge-box">
                        <div class="upper-layer">
                            <div class="badge-image">
                                <i class='bxr  <?php echo $row['icon_key'];?>'
                                    style="color:<?php echo $row['icon_colour']; ?>;"></i>
                            </div>
                            <div class="badge-descript">
                                <div class="badge-title">
                                    <?php echo htmlspecialchars($row['badgeName']); ?>
                                </div>
                                <div class="badge-value">
                                    <?php 
                                    $realName="";
                                $criteriaName=$row['criteria'];
                                switch ($criteriaName){
                                    case 'event':
                                        $realName="Events Attended";
                                        break;
                                    case 'carbon':
                                        $realName="CO2 saved";
                                        break;
                                    case 'diy':
                                        $realName="DIY posts created";
                                        break;
                                    case 'leaf':
                                        $realName="Leaf earned";
                                        break;
                                }
                                echo htmlspecialchars($row['value']).' '.htmlspecialchars($realName); ?>
                                </div>
                            </div>
                        </div>

                        <div class="bottom-layer">
                            <div class="button-panel">
                                <form method="post">
                                    <div class="hide-button">
                                        <input type="hidden" name="badgeId" id="badgeId"
                                            value="<?php echo htmlspecialchars($row['badgeId']); ?>">

                                        <?php if($row['status']=='hidden'){
                                            echo '<button type="submit" name="action" value="unhide"><i class="bxr  bx-eye"></i> 
                                            <p>Unhide</p>
                                        </button>';
                                        }else{
                                            echo '<button type="submit" name="action" value="hide"><i class="bxr  bx-eye-slash"></i>
                                            <p>Hide</p>
                                        </button>';
                                        }
                                ?>

                                    </div>

                                    <div class="delete-button">
                                        <input type="hidden" name="badgeId" id="badgeId"
                                            value="<?php echo htmlspecialchars($row['badgeId']); ?>">

                                        <input type="hidden" name="badgeName" id="badgeName"
                                            value="<?php echo htmlspecialchars($row['badgeName']); ?>">

                                        <button type="submit" name="delete"
                                            onclick="return confirmDelete('<?php echo $row['badgeName']; ?>')">
                                            <i class='bxr  bx-trash-x'></i>
                                            <p>Delete</p>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>


        <!----User Statistics-->
        <div class="statistics-box" id="boxes">
            <div class="statistics-banner">
                <div class="banner-zone">
                    <div class="title" id="title-bold">Badge Usage Statistics</div>
                    <div class="title">Track how badges motivate user engagement</div>
                </div>

                <div class="data-zone">
                    <?php while ($row=$display->fetch_assoc()): ?>
                    <div class="data-box">
                        <div class="badge-image">
                            <i class='bxr  <?php echo $row['icon_key']; ?>'
                                style="color:<?php echo $row['icon_colour'];?>"></i>
                        </div>
                        <div class="badge-descript">
                            <div class="badge-title"><?php echo htmlspecialchars($row['badgeName']); ?></div>

                            <div class="badge-value">
                                <?php 
                                $realName="";
                                $criteriaName=$row['criteria'];
                                switch ($criteriaName){
                                    case 'event':
                                        $realName="Events Attended";
                                        break;
                                    case 'carbon':
                                        $realName="CO2 saved";
                                        break;
                                    case 'diy':
                                        $realName="DIY posts created";
                                        break;
                                    case 'leaf':
                                        $realName="Leaf earned";
                                        break;
                                }
                                
                                echo htmlspecialchars($row['value']).' '.htmlspecialchars($realName); ?>
                            </div>
                        </div>
                        <div class="badge-award">
                            <div class="award-title">Awarded to</div>
                            <div class="student-count"><?php echo htmlspecialchars($row['total']);?> Students</div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!--pagination-->
                <div class="pagination">
                    <a href="?page-nr=1#boxes">First</a>
                    <?php             
                    //if current display is greater than 1 show the previous button
                    if (isset($_GET['page-nr']) && $_GET['page-nr']>1){
                    ?>
                    <a href="?page-nr=<?php echo $_GET['page-nr']-1;?>#boxes">
                        < </a>
                            <?php }else{ ?>
                            <a>
                                < </a>
                                    <?php } ?>

                                    <div class="page-numbers">
                                        <?php for($count=1;$count<=$pages;$count++){?>
                                        <a href="?page-nr=<?php echo $count?>#boxes"><?php echo $count?>
                                        </a>
                                        <?php }?>
                                    </div>

                                    <?php if(!isset($_GET['page-nr'])){?>
                                    <a href="">></a>
                                    <?php }else{
                                        if ($_GET['page-nr']>=$pages){?>
                                    <a>> </a>
                                    <?php }else{ ?>
                                    <a href="?page-nr=<?php echo $_GET['page-nr']+1;?>#boxes">></a>
                                    <?php }}?>

                                    <a href="?page-nr=<?php echo $pages ?>#boxes">Last
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
                    Showing <?php echo $page ?> of <?php echo $pages ?> pages
                </div>
            </div>
        </div>
    </div>
    </div>

    <!----hidden create badge box-->
    <div class="create-badge-block">
        <div class="create-box">

            <div class="close-icon">
                <button type="button" id="close-button" aria-label="Close"><i class='bx  bx-x-circle'></i></button>
            </div>

            <div class="banner">
                <div class="create-title">
                    Create New Badge
                </div>
                <div class="create-descript">
                    Design a new achievement badge to encourage sustainable behavior
                </div>
            </div>

            <div class="submit-post">
                <form target="_self" method="POST" enctype="multipart/form-data" class="creatingForm" id="form">

                    <div class="first-zone">
                        <div class="create-postTitle">
                            <label for="name" class="label">Badge Name</label><br>
                            <input type="text" id="name" name="badge-name" placeholder="e.g. EcoBeginner">
                        </div>


                        <div class="select-menu">
                            <div>Badge Icon</div>
                            <div class="select-btn">
                                <span class="btn-text">Select Icon</span>
                                <i class='bxr  bx-caret-down'></i>
                            </div>

                            <!--get value of icon_key and icon_colour-->
                            <input type="hidden" name="icon_key" id="iconKey">
                            <input type="hidden" name="icon_color" id="iconColor">

                            <ul class="options">
                                <li class="option" data-value="bx-leaf-alt" data-colour="#4CAF50">
                                    <i class='bxr  bx-leaf-alt'></i>
                                </li>

                                <li class="option" data-value="bx-sapling" data-colour="#81C784">
                                    <i class='bxr  bx-sapling'></i>
                                </li>
                                <li class="option" data-value="bx-plant-pot" data-colour=" #8D6E63">
                                    <i class='bxr  bx-plant-pot'></i>
                                </li>

                                <li class="option" data-value="bx-water-drop-alt" data-colour="#2196F3">
                                    <i class='bxr  bx-water-drop-alt'></i>
                                </li>

                                <li class="option" data-value="bx-hot" data-colour="#F44336">
                                    <i class='bxr  bx-hot'></i>
                                </li>

                                <li class="option" data-value="bx-thunder" data-colour="#FFC107">
                                    <i class='bxr  bx-thunder'></i>
                                </li>

                                <li class="option" data-value="bx-recycle" data-colour="#2E7D32">
                                    <i class='bxr  bx-recycle'></i>
                                </li>

                                <li class="option" data-value="bx-bookmark-heart" data-colour="#E91E63">
                                    <i class='bxr  bx-bookmark-heart'></i>
                                </li>
                                <li class="option" data-value="bx-globe" data-colour="#03A9F4">
                                    <i class='bxr  bx-globe'></i>
                                </li>

                                <li class="option" data-value="bx-sparkles-alt" data-colour="#9C27B0">
                                    <i class='bxr  bx-sparkles-alt'></i>
                                </li>

                                <li class="option" data-value="bx-medal-star" data-colour="#FFC107">
                                    <i class='bxr  bx-medal-star'></i>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="second-zone">
                        <div class="criteria-select">
                            <label for="criteria" class="label">Achievement Criteria</label><br>
                            <select name="criteria" id="criteria">

                                <option value="" selected disabled hidden>Select Criteria</option>

                                <option value="event" class="term">Events Attended</option>
                                <option value="carbon" class="term">CO2 saved
                                </option>
                                <option value="diy" class="term">DIY posts created
                                </option>
                                <option value="leaf" class="term">Leaf earned
                                </option>
                            </select>
                        </div>

                        <div class="points">
                            <label for="value" class="label">Value</label><br>
                            <input type="number" step=1 min=0 id="points" name="value" placeholder="e.g. 50"
                                pattern="\d*">
                        </div>
                    </div>
                </form>

                <div class="badge-preview">
                    <div class="badge-box">
                        <div class="mention">Badge preview:</div>
                        <div class="layer">
                            <div class="image-badge">
                                <!--unknown icon-->
                                <i class='bxr'></i>
                            </div>
                            <div class="badge-descript">
                                <div class="title-badge">
                                </div>
                                <div class="value-badge">
                                    <span id="num">
                                    </span>
                                    <span id="text">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="submit" form="form" value="Create Badge" name="publish" id="publish">

                <div class="empty_errors">
                    <?php if (!empty($errors) || !empty($errors2)){
                    echo $errors.$errors2;
                         } ?>
                </div>

            </div>
        </div>
    </div>


    <!--doing pagination-->

    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
    <?php mysqli_close($conn)?>
</body>

</html>