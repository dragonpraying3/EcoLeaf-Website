<?php
include_once '../topbar.php'; 
require_once __DIR__ . '/../backend/diyLogic.php';
require_once __DIR__ . '/../backend/fileLogic.php';
require_once __DIR__ . '/../backend/popup.php';
require_once __DIR__ . '/../backend/notifyLogic.php';
require_once __DIR__ . '/../backend/badgeChecker.php';

// var_dump($_SERVER["REQUEST_METHOD"], $_POST);
date_default_timezone_set('Asia/Kuala_Lumpur');

$studentId = null;
$organizerId = null;
$adminId = null;

//checking role
$role=$_SESSION['user']['role']??'guest';
$leafValue=$_SESSION['user']['leaf'];

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

// echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';

// var_dump($_SERVER["REQUEST_METHOD"], $_POST);
date_default_timezone_set('Asia/Kuala_Lumpur');


//----------------------------------------------------------------------
//get the userId of the student who buy the DIY post 
//not only get the seller id but need to get the user id of the buyer id
function getSellerUserId($conn,$itemId){
    $getUserId="SELECT ownerUser.userId
    from traderequest as t 
    
    join diyhub as d
    on t.itemId=d.itemId
    
    join student as ownerPost
    on t.sellerId=ownerPost.studentId
    
    join users as ownerUser
    on ownerPost.userId=ownerUser.userId
    
    where d.itemId=?;
    ";

    $getUserId=$conn->prepare($getUserId);
    $getUserId->bind_param("i",$itemId);
    $getUserId->execute();

    $result=$getUserId->get_result();
    $row=$result->fetch_assoc();

    return $row['userId'];
}
function getBuyerUserId($conn,$itemId){
    $getUserId="SELECT buyerUser.userId
    from traderequest as t 
    
    join diyhub as d
    on t.itemId=d.itemId
    
    join student as ownerPost
    on t.sellerId=ownerPost.studentId
    
    join users as ownerUser
    on ownerPost.userId=ownerUser.userId

    join student as buyerPost
    on t.buyerId=buyerPost.studentId

    join users as buyerUser
    on buyerPost.userId=buyerUser.userId
    
    where d.itemId=?;
    ";

    $getUserId=$conn->prepare($getUserId);
    $getUserId->bind_param("i",$itemId);
    $getUserId->execute();

    $result=$getUserId->get_result();
    $row=$result->fetch_assoc();

    return $row['userId'];
}
//----------------------------------------------------------------------
//submit the create post form
$validation="";
$errors="";
$empty_error="";
$leafError="";

$target_dir = 'image/DIYpost/'; // Directory to save uploaded files

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publish'])){
    $title=trim($_POST['project-title'])??'';
    $description=trim($_POST['project-description'])??'';
    $leaf=trim($_POST['project-points'])??null;
    $image=$_FILES['project-image']; //it is a file value

    $imageName=$target_dir.basename($image['name']); //set the target file path
    $imageExtension=strtolower(pathinfo($imageName,PATHINFO_EXTENSION)); //get the extension of the file (png,jpg)
    
    //function of errors
    $validation=getCreatingFormError($title,$description,$leaf,$image);
    $validateFile=getUploadFileError($imageExtension,$image);
    list($errors,$uploadOK)=$validateFile; //Destructure array into variables
    
    // testing
    // if (!empty($errors)) {
    //     var_dump($errors);
    // }
    
    if (empty($validation) && empty($errors)){
        
        if ($uploadOK){
                //change the file name
                $path=changeFileName($target_dir,$studentId,$imageExtension);
                //relative path
                $relativePath = $path['relative']; // save in DB
                //absolute path
                $absolutePath = $path['absolute']; // move_to_upload()
                
                //function save to the file directory
                $uploadDone=saveFiletoDirectory($image,$absolutePath);
                
                if ($uploadDone){
                    
                    $postAt=date("Y-m-d H:i:s");
                    $approvedAt='0000-00-00 00:00:00';
                    
                    $insertCreatingPost="INSERT INTO diyhub(title,description,leaf,imageFile,postAt,status,approvedAt,studentId,adminId)
                    VALUES ('$title','$description','$leaf','$relativePath','$postAt','pending','$approvedAt',?,null);
                    ";
                    
                    $insertPost = $conn->prepare($insertCreatingPost);
                                        
                    if($insertPost===false){ //check if the prepare statement is fail
                        die("Error preparing statement:".$conn->error);
                    }
                    $insertPost -> bind_param("i",$studentId);
                    
                    if ($insertPost->execute()) {
                    //notification 
                    //use while loop to send to all role=admin users

                        $getAllAdmin="SELECT userId 
                        from users
                        where role='admin';";

                        $showAdmin=$conn->query($getAllAdmin);
                        while ($row=$showAdmin->fetch_assoc()): 

                        $userId=$row['userId']; //get the admin user id
                            
                        $title="New DIY post received";
                        $message="There are new post waiting for your actions";
                        
                        $status=insertNotify($conn,$title,$message,$userId);
                        endwhile;

                        checkStudentBadge($conn,$studentId);
                        $badgePop=$_SESSION['badge']??'';
                        unset($_SESSION['badge']);
                        
                        if ($status){
                            echo '<p>
                            <script>
                            console.log("sent")  
                            </script>
                            </p>';
                        }

                        echo '<p>
                        <script>
                        console.log(\'New record created successfully\');
                        
                        showRelevantPop(\'post-request-sent\');

                        </script>
                        </p>';
                    } else {
                        echo "Error: " . $insertPost->error;
                    }
                    $insertPost->close(); //close the statement
                }
        }
    }
}

//----------------------------------------------------------------------
//submit the trade post form
if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['trading'])){
    $GETitemId=trim($_POST['itemId']);
    $itemId=substr($GETitemId,2);

    $GETsellerId=trim($_POST['sellerId']);
    $sellerId=substr($GETsellerId,4);

    $GETLeaf=trim($_POST['leaf']);
    $leaf=substr($GETLeaf,2);
    
    $location=trim($_POST['trade-location'] ??''); // null coalescing operator (provide default value if key is undefined)
    $startTime=trim($_POST['trade-datetimestart']??'');
    $endTime=trim($_POST['trade-datetimeend']);
    
    $requestAt=date("Y-m-d H:i:s");
    $completedAt='0000-00-00 00:00:00';

    $status=false;

    $empty_error=getTradingFormError($location,$startTime);
    $leafError=getLeafNotEnoughError($leafValue,$leaf);

    if (empty($empty_error) && empty($leafError)){
          $saveTrade="INSERT INTO traderequest(status,location,sellerConfirm,requestConfirm,startTime,endTime,requestAt,completedAt,itemId,sellerId,buyerId)
    
        VALUES ('pending','$location','$status','$status','$startTime','$endTime','$requestAt','$completedAt',?,?,?);
        ";

        $insertTrade = $conn->prepare($saveTrade);
        if($insertTrade===false){ //check if the prepare statement is fail
            die("Error preparing statement:".$conn->error);
        }
        $insertTrade -> bind_param("iii",$itemId,$sellerId,$studentId);
        if ($insertTrade-> execute()){

            $title="New trade request received";
            $message="There are new trading post waiting for your actions";
            $userId=getSellerUserId($conn,$itemId);
            
            //sent to the buyer
            $status=insertNotify($conn,$title,$message,$userId);
            
            echo '<p>
            <script>
            console.log(\'Trade request sent\');

            showRelevantPop(\'trade-request-sent\');

            </script>
            </p>';
        }else {
            echo "Error: " . $insertTrade->error;
        }
        $insertTrade -> close();  
    }
    //echo "<script>console.log('".$startTime."');</script>";
}

//----------------------------------------------------------------------
//approve trading button
function getQuery($status){
    $query="UPDATE traderequest as t
        set t.status='$status'
        where t.sellerId=? and t.itemId=?;
        ";
    return $query;
}

$success=actionButton($conn,getQuery('approve'),$studentId,'approve');
if ($success){
    $title="Trade Request Approved";
    
    $itemId=substr($_POST['itemId'],2); //remove R- or A-
    $userId=getBuyerUserId($conn,$itemId);
    
    $tradeTitle=substr($_POST['tradeTitle'],2); //remove T-
    $message="Your trading of ".$tradeTitle." is approved by the post owner.";
    
    $status=insertNotify($conn,$title,$message, $userId);

    if ($status){
         echo '<p>
    <script>
    console.log("sent")  
    </script>
    </p>';
    }else{
        echo '<p>
    <script>
    console.log("fail to sent")  
    </script>
    </p>';
    }
    
    
    echo '<p>
    <script>
    showRelevantPop(\'trade-approve\');

    </script>
    </p>';
}else{
    echo "fail";   
}

//----------------------------------------------------------------------
//reject trading button
$success=actionButton($conn,getQuery('reject'),$studentId,'send');
if ($success){
    $title=$_POST['reject'];
    $message=$_POST['descript'];
    
    $itemId=substr($_POST['itemId'],2); //remove R- or A-
    $userId=getBuyerUserId($conn,$itemId);
    
    $status=insertNotify($conn,$title,$message,$userId);

    if ($status){
         echo '<p>
    <script>
    console.log("sent")  
    </script>
    </p>';
    }else{
        echo '<p>
    <script>
    console.log("fail to sent")  
    </script>
    </p>';
    }
    
    echo '<p>
    <script>
    showRelevantPop(\'post-reject\');  
    </script>
    </p>';
}else{
    echo "fail";   
}


//----------------------------------------------------------------------
//trading completion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['trade-complete'])){

    $GETitemId=trim($_POST['itemId']);
    $itemId=substr($GETitemId,2);

    $GETsellerId=trim($_POST['sellerId']);
    $sellerId=substr($GETsellerId,2);

    $GETbuyerId=trim($_POST['buyerId']);
    $buyerId=substr($GETbuyerId,2);
    
    $completedAt='0000-00-00 00:00:00';

    $sql="SELECT t.sellerConfirm,t.requestConfirm,t.sellerId
    FROM traderequest as t
    WHERE t.itemId=$itemId;";

    $row=mysqli_fetch_assoc(mysqli_query($conn,$sql));
    $sellerConfirm = $row['sellerConfirm'];
    $requestConfirm  = $row['requestConfirm'];
    
    if ($studentId==$sellerId){
        $sellerConfirm=1;

        $title="Seller side confirmation";
        $message="Your trading post has been complete from seller side";
        $userId=getBuyerUserId($conn,$itemId);
        
        //sent to the buyer
        $status=insertNotify($conn,$title,$message,$userId);
    }
    if ($studentId==$buyerId){
        $requestConfirm=1;

        $title="Buyer side confirmation";
        $message="Your trading post has been complete from buyer side";
        $userId=getSellerUserId($conn,$itemId);
        
        //sent to the buyer
        $status=insertNotify($conn,$title,$message,$userId);
    }
      //update complete time and deduct user leaf (buyer only)
    if ($sellerConfirm==1 and $requestConfirm==1){
        $completedAt=date("Y-m-d H:i:s");

        $title="Trading post completed";
        $message="The post is permanently closed.";
        $userId=getBuyerUserId($conn,$itemId);
        $status=insertNotify($conn,$title,$message,$userId);
        
        //sent to the buyer and seller
        $userId=getSellerUserId($conn,$itemId);
        $status=insertNotify($conn,$title,$message,$userId);

        //update buyerId deduction points 
        //update sellerI addition points
        if ($studentId==$buyerId){
            $originleaf=$_SESSION['user']['leaf'];
            $PostLeaf=trim($_POST['leaf']);

            //select seller id and get the leaf
            $getSellerInfo="SELECT t.sellerId,s.leaf
            FROM tradeRequest as t
            JOIN student as s
            ON t.sellerId=s.studentId
            WHERE t.itemId=$itemId;";
            
            $sellerInfo=$conn->query($getSellerInfo);

            $sellerID=null;
            $sellerLeaf=null;
            if ($sellerInfo -> num_rows>0){
                while ($seller = $sellerInfo -> fetch_assoc()){
                    $sellerID=$seller['sellerId'];
                    $sellerLeaf=$seller['leaf'];
                }
            }
            
            $newLeaf=$originleaf-$PostLeaf;
            $sellerLeaf=$sellerLeaf+$PostLeaf;

            $updatLeaf="UPDATE student as s
            SET s.leaf='$newLeaf'
            WHERE s.studentId='$studentId';";
            $updatLeaf2="UPDATE student as s
            SET s.leaf='$sellerLeaf'
            WHERE s.studentId='$sellerID';";

            $row=$conn->query($updatLeaf);
            $row2=$conn->query($updatLeaf2);
            if ($row===TRUE && $row2==TRUE){
                 echo '<script>console.log("success");
                 showRelevantPop(\'trade-complete\'); 

                 </script>';
            }else{
                echo "Update error: " . $conn->error;
            }
        }        
    }
    $query = "UPDATE traderequest as t 
    set t.sellerConfirm=?,t.requestConfirm=?,t.completedAt=?
    WHERE itemId=?;";

    $completion = $conn->prepare($query);
    $completion->bind_param("iisi", $sellerConfirm, $requestConfirm, $completedAt,$itemId);
    $completion->execute();

    if ($completion->affected_rows > 0) {
        echo '<script>console.log("Complete done");
        showRelevantPop(\'mark-complete\');

        </script>';
    } else {
        echo "Error: " . $conn->error;
    }
}

//----------------------------------------------------------------------
//showing post query
$content="SELECT d.itemId,d.title,d.description,d.leaf,d.imageFile,d.approvedAt,u.name,s.studentId 
from users as u

join student as s
on u.userId=s.userId

join diyhub as d
on s.studentId=d.studentId

where d.status='approve' 
and u.status='active'
and NOT EXISTS (
    select * from traderequest as t
    where t.itemId=d.itemId 
    AND t.status IN ('pending', 'approve')
) 
and s.studentId!=?
order by d.itemId
;";

//$showing_post=$conn->query($content); //because need to fetch all relevant result 
$display_post = $conn -> prepare($content);
$display_post -> bind_param("i",$studentId);
$display_post -> execute();
$display_post_result = $display_post -> get_result();


//----------------------------------------------------------------------
// showing pending trading post
$trading_content="SELECT d.itemId,d.title,d.leaf,d.imageFile,t.buyerId,buyerUser.name,t.location,t.startTime,t.endTime,t.requestAt
from traderequest as t

join diyhub as d
on t.itemId=d.itemId

join student as ownerPost
on t.sellerId=ownerPost.studentId

join users as ownerUser
on ownerPost.userId=ownerUser.userId

join student as buyerPost
on t.buyerId=buyerPost.studentId

join users as buyerUser
on buyerPost.userId=buyerUser.userId

where ownerUser.status='active' and t.sellerid=? and t.status=?;
";

$showing_trading_request = $conn->prepare($trading_content);
$status="pending";
$showing_trading_request -> bind_param("is",$studentId,$status);
$showing_trading_request -> execute();
$result=$showing_trading_request -> get_result();


//----------------------------------------------------------------------
// $showing_trading_request_post=$conn->query($trading_content);
// showing approve trading post
$completion="SELECT d.title,d.leaf,d.imageFile,t.sellerId,t.buyerId,buyerUser.name as buyerName,ownerUser.name as ownerName,t.location,t.startTime,t.endTime,t.itemId,t.sellerConfirm,t.requestConfirm
from diyhub as d

join traderequest as t
on t.itemId=d.itemId

join student as ownerPost
on ownerPost.studentId=t.sellerId

join users as ownerUser
on ownerPost.userId=ownerUser.userId

join student as buyerPost
on t.buyerId=buyerPost.studentId

join users as buyerUser
on buyerPost.userId=buyerUser.userId

where (t.sellerConfirm =0 OR t.requestConfirm =0 ) 
AND ownerUser.status='active'
AND (t.sellerId = ? OR t.buyerId = ?) 
AND t.status = ?
";

//$run the completion displayment 
$completion_display=$conn->prepare($completion);
$status="approve";
$completion_display -> bind_param("iis",$studentId,$studentId,$status);
$completion_display -> execute();
$completion_result = $completion_display -> get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href='https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/EcoLeaf/assets/css/diyDesign.css">
    <script type="text/javascript" src="/EcoLeaf/assets/js/DIYjs.js" defer></script>
    <title>DIY Hub</title>
</head>

<body>
    <?php include_once '../topbar.php'; ?>

    <div class="body-container">
        <!--------------DIY Upcycling Exchange Hub-------------------------->
        <div class="zone-intro">
            <div class=" title-box">
                <div class="description">
                    <div class="title" id="title-bold">DIY Upcycling Exchange Hub</div>
                    <div class="title">Share your creative upcycling projects and get inspired</div>
                </div>

                <div class="create-post-button">
                    <button type="button" id="create-post"><i class='bx  bxs-plus'></i> Create Post</button>
                </div>
            </div>
        </div>

        <!--------------display of DIY Upcycling Exchange Hub-------------------------->
        <div class="zone-post">
            <div class="post-block">
                <?php if ($display_post_result->num_rows==0){
                    echo '<p class="sentence">No <b>DIY post</b> yet.  </p>';
                }else{
                 ?>
                <?php while($row=$display_post_result->fetch_assoc()): ?>
                <div class="post-display">
                    <div class="image-container">
                        <img src="/EcoLeaf/student/<?php echo $target_dir.$row['imageFile'] ?>" alt="Product Preview"
                            id="imega-from-user">
                    </div>

                    <div class="post-word">
                        <div class="post-detail">
                            <div class="POSTtitle">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </div>

                            <div class="post-point">
                                <i class='bx  bx-leaf'></i>
                                <div class="leaf"><?php echo htmlspecialchars($row['leaf']); ?></div>
                            </div>
                        </div>

                        <div class="post-process">
                            <div class="POSTdescript">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </div>

                            <div class="post-detail2">
                                <div class="post-owner"><i class='bx  bxs-face'></i>
                                    <?php echo htmlspecialchars($row['name'])?>
                                </div>
                                <div class="post-time">
                                    <?php 
                                echo date("j,D Y g:i a", strtotime($row['approvedAt'])); ?>
                                </div>
                            </div>

                            <button type="submit" class="TRADE">
                                <!--connection-->
                                <input type="hidden" name="itemId" value="A-<?php echo htmlspecialchars($row['itemId'])?>
                            ">

                                <input type="hidden" name="sellerId" value="Sel-<?php 
                            echo htmlspecialchars($row['studentId'])?>">

                                <input type="hidden" name="leaf" value="L-<?php echo htmlspecialchars($row['leaf'])?>
                            ">
                                <i class='bx  bx-swap-horizontal'></i>
                                Trade
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php } ?>
            </div>
        </div>

        <!--------------DIY Project Request Centre-------------------------->
        <div class="zone-project-request-title" id="request-management">
            <div class="title-box">
                <div class="description">
                    <div class="title" id="title-bold">DIY Project Request Centre</div>
                    <div class="title">Manage incoming project requests and decide whether to approve or reject them
                    </div>
                </div>
            </div>
        </div>

        <div class="zone-project-request">
            <div class="post-block trade-pending-post">
                <?php if ($result->num_rows==0){
                    echo '<p class="sentence">No <b>trading request</b> yet.  </p>';
                }else{
                 ?>
                <?php while($row=$result->fetch_assoc()): ?>
                <div class="post-display">
                    <div class="image-container">
                        <img src="/EcoLeaf/student/<?php echo $target_dir.$row['imageFile']; ?>" alt="Product Preview"
                            id="imega-from-user">
                    </div>


                    <div class="post-word">
                        <div class="post-detail">
                            <div class="POSTtitle">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </div>

                            <div class="post-point">
                                <i class='bx  bx-leaf'></i>
                                <div class="leaf"><?php echo htmlspecialchars($row['leaf']); ?></div>
                            </div>
                        </div>
                        <div class="trade-item">
                            <div class="post-trader"><i class='bx  bx-people-handshake'></i>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </div>
                            <div class="post-location"><i class='bx  bx-location'></i>
                                <?php echo htmlspecialchars($row['location']); ?>
                            </div>
                            <div class="post-date"><i class='bx bx-calendar-week'></i>
                                <?php 
                            echo date("Y-m-d", strtotime($row['startTime'])); ?>
                            </div>
                            <div class="post-timeRange"><i class='bx  bx-clock-10'></i>
                                <?php echo date("g:i a", strtotime($row['startTime'])) .htmlspecialchars('  -  ') .date("g:i a", strtotime($row['endTime'])); ?>
                            </div>
                        </div>


                        <div class="button-zone">
                            <form method="POST" action="#" id="form">
                                <div class="approveSection">
                                    <input type="hidden" name="itemId"
                                        value="A-<?php echo htmlspecialchars($row['itemId'])?>">

                                    <input type="hidden" name="tradeTitle"
                                        value="T-<?php echo htmlspecialchars($row['title'])?>">
                                    <button type="submit" class="approve-trading" name="approve"><i
                                            class='bx  bx-check'></i>
                                        Approve</button>
                                </div>

                                <div class="rejectSection">
                                    <input type="hidden" name="itemId"
                                        value="R-<?php echo htmlspecialchars($row['itemId'])?>">
                                    <button type="submit" class="reject-trading" name="reject"><i class='bx  bx-x'></i>
                                        Reject</button>
                                </div>
                        </div>
                        </form>
                        <div class="timeRemain">
                            <div class="time">
                                <?php 
                                echo $row['requestAt'];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php } ?>
            </div>
        </div>

        <div class="reject-block">
            <div class="reject-box">
                <div class="title-panel">
                    <div class="reject-title">Reject Reason</div>
                </div>

                <div class="reason-content">
                    <form action="" id="reject-form" method="post" class="reject-reason-form">
                        <input type="hidden" name="itemId" id="reject-itemId">
                        <input type="hidden" name="descript" id="descript">

                        <!-- Trading Reject Reasons -->
                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason1" name="reject" value="Invalid Location">
                                <label for="reason1" class="reason">Invalid / Unsafe Location</label>
                            </div>
                            <div class="reason-desc">The proposed meetup place is unsafe, unclear, or too far.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason2" name="reject" value="Invalid Time">
                                <label for="reason2" class="reason">Unsuitable Proposed Time</label>
                            </div>
                            <div class="reason-desc">The selected date or time does not work for the trade.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason3" name="reject" value="Item Condition Issue">
                                <label for="reason3" class="reason">Item is Damaged / Broken</label>
                            </div>
                            <div class="reason-desc">The item listed has defects, missing parts, or poor condition.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason4" name="reject" value="Not Free">
                                <label for="reason4" class="reason">Item Not Free as Stated</label>
                            </div>
                            <div class="reason-desc">The listing claims the item is free, but additional charges appear.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason5" name="reject" value="Reserved">
                                <label for="reason5" class="reason">Item Reserved for Another Buyer</label>
                            </div>
                            <div class="reason-desc">This item has been reserved for another buyer who contacted
                                earlier.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason10" name="reject" value="Rule Violation">
                                <label for="reason10" class="reason">Buyer Did Not Follow Platform Rules</label>
                            </div>
                            <div class="reason-desc">The buyer requested actions that violate trading guidelines.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason14" name="reject" value="Needs Repair">
                                <label for="reason14" class="reason">Item Requires Repair Before Trading</label>
                            </div>
                            <div class="reason-desc">The seller noticed the item needs repair and cannot trade it now.
                            </div>
                        </div>
                    </form>

                </div>

                <div class="reject-button-zone">
                    <button type="button" id="cancel" class="canceling" form="reject-form">Cancel</button>

                    <div class="sendSection">
                        <input type="hidden" name="send">
                        <button type="submit" id="reject" name="send" form="reject-form" disabled>Send</button>
                    </div>
                </div>
            </div>
        </div>


        <!--------------Upcycle Trade Tracking-------------------------->
        <div class="zone-project-completion-title" id="trade-management">
            <div class="title-box">
                <div class="description">
                    <div class="title" id="title-bold">Upcycle Trade Tracking</div>
                    <div class="title">Track your eco-friendly trade progress in real time.</div>
                </div>
            </div>
        </div>

        <div class="zone-trade-tracking">
            <div class="post-block trade-approve-post">
                <?php if ($completion_result->num_rows==0){
                    echo '<p class="sentence">No <b>trading post</b> yet. Be calm ~ </p>';
                }else{
                 ?>
                <?php while ($row =$completion_result -> fetch_assoc()): ?>
                <div class="post-display">
                    <div class="image-container">
                        <img src="/EcoLeaf/student/<?php echo $target_dir.$row['imageFile'] ?>" alt="Product Preview"
                            id="imega-from-user">
                    </div>

                    <div class="post-word">
                        <div class="post-detail">
                            <div class="POSTtitle">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </div>

                            <div class="post-point">
                                <i class='bx  bx-leaf'></i>
                                <div class="leaf">
                                    <?php echo htmlspecialchars($row['leaf']); ?> </div>
                            </div>
                        </div>

                        <div class="trade-item">
                            <div class="postOwner"><i class='bx  bxs-face'></i>
                                <?php if ($row['sellerId']==$studentId){
                                    echo "You";
                                }else{
                                    echo htmlspecialchars($row['ownerName']);
                                }
                                
                                ?>
                            </div>

                            <div class="post-trader"><i class='bx  bx-people-handshake'></i>
                                <?php 
                                if ($row['buyerId']==$studentId){
                                    echo "You";
                                }else{
                                    echo htmlspecialchars($row['buyerName']);
                                } ?>
                            </div>

                            <div class="post-location"><i class='bx  bx-location'></i>
                                <?php echo htmlspecialchars($row['location']); ?>
                            </div>

                            <div class="post-date"><i class='bx  bx-calendar-week'></i>
                                <?php 
                            echo date("Y-m-d", strtotime($row['startTime'])); ?>
                            </div>
                            <div class="post-timeRange"><i class='bx  bx-clock-10'></i>
                                <?php 
                            echo date("g:i a", strtotime($row['startTime'])) .htmlspecialchars('  -  ') .date("g:i a", strtotime($row['endTime'])); ?>
                            </div>
                        </div>

                        <form method="POST">
                            <input type="hidden" id="upcycle-itemId" name="itemId" value="I-<?php 
                                echo htmlspecialchars($row['itemId']);?>">

                            <input type="hidden" name="leaf" value="<?php 
                                echo htmlspecialchars($row['leaf']);?>">

                            <input type="hidden" id="upcycle-sellerId" name="sellerId" value="S-<?php 
                                echo htmlspecialchars($row['sellerId']);?>">

                            <input type="hidden" id="upcycle-buyerId" name="buyerId" value="B-<?php 
                                echo htmlspecialchars($row['buyerId']);?>">

                            <input type="hidden" id="upcycle-sellerConfirm" name="sellerConfirm" value="<?php 
                                echo htmlspecialchars($row['sellerConfirm']);?>">

                            <input type="hidden" id="upcycle-requestConfirm" name="requestConfirm" value="<?php 
                                echo htmlspecialchars($row['requestConfirm']);?>">


                            <?php 
                            if ($row['sellerId']==$studentId){
                                if ($row['sellerConfirm']==1){
                                    echo '<button class="waiting-post" name="trade-waiting" disabled>Waiting...</button>';
                                }else{
                                    echo '<button type="submit" class="complete-post" name="trade-complete" id="complete-post"><i
                                    class=\'bx  bx-checks\'></i>
                                Complete</button>';
                                }
                            }

                            if ($row['buyerId']==$studentId){
                                if ($row['requestConfirm']==1){
                                    echo '<button class="waiting-post" name="trade-waiting" disabled> Waiting...</button>';
                                }else{
                                    echo '<button type="submit" class="complete-post" name="trade-complete" id="complete-post"><i
                                    class="bx  bx-checks"></i>
                                Complete</button>';
                                }
                            }
                            ?>
                        </form>

                        <div class="timeRemain">
                            <div class="timing"><?php
                                echo $row['endTime'];
                                ?></div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php } ?>
            </div>
        </div>
    </div>
    </div>

    <!--------------hidden create post block-------------------------->
    <div class="create-post-block ">
        <div class="create-post-box">

            <div class="close-icon">
                <button type="button" id="close-button" aria-label="Close"><i class='bx  bx-x-circle'></i></button>
            </div>

            <div class="banner-line">
                <div class="post-title">
                    Share Your DIY Project
                </div>
                <div class="post-descript">
                    Inspire others with your upcycling creations
                </div>
            </div>

            <div class="submit-post">
                <form target="_self" method="POST" enctype="multipart/form-data" class="creatingForm">

                    <div class="create-postTitle">
                        <label for="title" class="label">Project title</label><br>
                        <input type="text" id="titlfe" name="project-title" placeholder="e.g., Plastic Bottle Planter">
                    </div>

                    <div class="description">
                        <label for="description" class="label">Description</label><br>
                        <textarea id="description" rows="3" cols="40" name="project-description"
                            placeholder="Descript your project in about 10-30 words"></textarea>
                    </div>

                    <div class="points">
                        <label for="points" class="label">Leaf for trade</label><br>
                        <input type="number" step=any min=0 id="points" name="project-points" pattern="\d*">
                    </div>

                    <div class="image">
                        <p class="label">Project Image</p>
                        <div class="image-block">
                            <i class='bx  bx-image-portrait'></i>
                            <input type="file" id="image" name="project-image">
                            <label for="image" id="file-label">Click to upload images</label><br>
                        </div>
                        <div class="pic-label">
                            Upload file:
                            <span class="picture-name">No file chosen</span>
                        </div>
                    </div>

                    <input type="submit" value="Publish Post" name="publish" id="publish">
                </form>
                <p class="validation-error">
                    <?php if (!empty($validation) || !empty($errors)){
                    echo $validation.$errors;
                }
                ?></p>
            </div>
        </div>
    </div>

    <!--hidden trading post-->
    <div class="trade-post-block">
        <div class="trading-box">

            <div class="close-button">
                <button type="button" id="exit-button" aria-label="Close" class="CLOSE"><i
                        class='bx  bx-x-circle'></i></button>
            </div>

            <div class="banner">
                <div class="trade-title">
                    Trade with <span class="name"></span>
                </div>
            </div>

            <div class="submit-trade">
                <div class="create-postTitle">
                    <p class="label">Project title</p>
                    <div class="name-display" id="TITLE"></div>
                </div>

                <form target="_self" method="POST" enctype="multipart/form-data" class="tradingForm">
                    <!--connection of itemId-->
                    <input type="hidden" name="itemId" id="trade-itemId">
                    <!--connection of sellerId-->
                    <input type="hidden" name="sellerId" id="trade-sellerId">
                    <!--connection of leaf-->
                    <input type="hidden" name="leaf" id="trade-leaf">

                    <div class="location-selection">
                        <label for="trade-location" class="label">Select Locations: </label><br>
                        <select name="trade-location" id="trade-location">

                            <option value="" selected disabled hidden>Please select a location</option>

                            <option value="Cafeteria APU – Main Counter">Cafeteria APU – Main Counter</option>
                            <option value="APU Library – Front Entrance Gate">APU Library – Front Entrance Gate
                            </option>
                            <option value="APU Student Lounge – Sofa Area">APU Student Lounge – Sofa Area</option>
                            <option value="APU Block B – Security Gate">APU Block B – Security Gate</option>
                            <option value="APU Block C – Glass Lift Area">APU Block C – Glass Lift Area</option>
                            <option value="APU Exam Hall – Entrance">APU Exam Hall – Entrance</option>
                            <option value="APU Level 3 – Main Reception Counter">APU Level 3 – Main Reception
                                Counter
                            </option>
                            <option value="APU Canteen – Center Zone">APU Canteen – Center Zone</option>
                            <option value="APU Level 3 – APU Globe Logo Area">APU Level 3 – APU Globe Logo Area
                            </option>
                            <option value="APU Block E – Vending Machine Corner">APU Block E – Vending Machine
                                Corner
                            </option>
                        </select>
                    </div>

                    <div class="time-selection">
                        <label for="trade-datetimestart" class="label">Trade date and time:</label><br>

                        <input type="datetime-local" id="trade-datetimestart" name="trade-datetimestart">
                        <i class='bxr  bx-minus'></i>

                        <input type="datetime-local" id="trade-datetimeend" name="trade-datetimeend" readonly>
                        <p id="time-showing"></p>
                    </div>

                    <input type="submit" value="Confirm Trade" id="trade" name="trading">
                </form>
                <div class="empty_errors">
                    <?php if (!empty($empty_error) || !empty($leafError)){
                    echo $empty_error.$leafError;
                         } ?>
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($badgePop)):?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php foreach ($badgePop as $badge):?>
        showRelevantPop('badge-gain');
        <?php endforeach; ?>
    });
    </script>
    <?php endif; ?>

    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>

    <?php mysqli_close($conn)?>
</body>

</html>