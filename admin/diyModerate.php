<?php 
include_once '../topbar.php'; 
require_once __DIR__ . '/../backend/diyLogic.php';
require_once __DIR__ . '/../backend/popup.php';
require_once __DIR__ . '/../backend/notifyLogic.php';

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

//----------------------------------------------------------------------
//get the userId of the student who posted the DIY 
function getStudentUserId($conn,$itemId){
    $getUserId="SELECT s.userId
    from diyhub as d
    join student as s
    on d.studentId=s.studentId
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
//approve & update post status

function getQuery($status){
    $query="UPDATE diyhub as d
    set d.status='$status',d.approvedAt=CURRENT_TIMESTAMP,d.adminId=?
    where d.itemId=?;
    ";
    return $query;
}
    
$success = actionButton($conn,getQuery('approve'),$adminId,'approve');

if ($success){
    $itemId=substr($_POST['itemId'],2); //remove R- or A-
    $userId=getStudentUserId($conn,$itemId);

    $postTitle=substr($_POST['postTitle'],2); //remove T-
    $title="DIY Post Approved";
    $message="Your DIY post with title ".$postTitle." is now visible to public.";
    
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
    showRelevantPop(\'post-approve\');

    </script>
    </p>';
}else{
    echo "fail";   
}

//----------------------------------------------------------------------
//reject & update post status 

$success=actionButton($conn,getQuery('reject'),$adminId,'send');
if ($success){
    $title=$_POST['reject'];
    $message=$_POST['descript'];
    
    $itemId=substr($_POST['itemId'],2); //remove R- or A-
    $userId=getStudentUserId($conn,$itemId);
    
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
    showRelevantPop(\'post-reject\');  
    </script>
    </p>';
}else{
    echo "fail";   
}

//----------------------------------------------------------------------
//showing pending post
$pending_post="SELECT d.itemId,d.title,d.description, d.leaf, d.imageFile,student.name,d.studentId
from diyhub as d

join student as s
on s.studentId=d.studentId

join users as student
on student.userId=s.userId

where d.status='pending' and student.status='active'
order by d.itemId;
";

$show_pending_post = $conn -> query($pending_post);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/EcoLeaf/assets/css/diyDesign.css">
    <script type="text/javascript" src="/EcoLeaf/assets/js/adminDIY.js" defer></script>
    <title>DIY Moderation</title>
</head>

<body>
    <?php include_once '../topbar.php'; ?>

    <div class="body-container">
        <div class="zone-intro">
            <div class=" title-box">
                <div class="description">
                    <div class="title" id="title-bold">DIY Upcycling Exchange Hub</div>
                    <div class="title">Review and verify student posts</div>
                </div>
            </div>
        </div>


        <div class="zone-post">
            <div class="post-block">

                <?php if ($show_pending_post->num_rows==0){
                        echo '<p class="sentence">Oh no! No student submit <b>DIY post</b> yet. </p>';
                }else{
                 ?>
                <?php while($row=$show_pending_post->fetch_assoc()): ?>
                <div class="post-display">
                    <div class="image-container">
                        <img src="/EcoLeaf/student/image/DIYpost/<?php echo $row['imageFile'] ?>" alt="Product Preview"
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
                            </div>
                        </div>

                        <div class="button-zone">
                            <form method="POST" action="#" id="form">
                                <div class="approveSection">
                                    <input type="hidden" name="itemId"
                                        value="A-<?php echo htmlspecialchars($row['itemId'])?>">
                                    <input type="hidden" name="postTitle"
                                        value="T-<?php echo htmlspecialchars($row['title'])?>">
                                    <button type="submit" class="approve-trading" name="approve">
                                        <i class='bx  bx-check'></i>
                                        Approve</button>
                                </div>

                                <div class="rejectSection">
                                    <input type="hidden" name="itemId"
                                        value="R-<?php echo htmlspecialchars($row['itemId'])?>">
                                    <button type="submit" class="reject-trading" name="reject"><i class='bx  bx-x'></i>
                                        Reject</button>

                                </div>
                            </form>
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
                        <!--to store reason description-->

                        <!--name attribute must be same to ensure only select 1-->
                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason1" name="reject" value="Spam">
                                <label for="reason1" class="reason">Spam</label>
                            </div>
                            <div class="reason-desc">Misleading, overly promotional, or irrelevant submission.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason2" name="reject" value="Inaccurate Information">
                                <label for="reason2" class="reason">Inaccurate or Misleading Information</label>
                            </div>
                            <div class="reason-desc">False sustainability claims or misleading environmental
                                guidance.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason3" name="reject" value="Unsafe Content">
                                <label for="reason3" class="reason">Unsafe or Dangerous DIY Activities</label>
                            </div>
                            <div class="reason-desc">Dangerous tools, chemicals, explosives, or unsafe instructions.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason4" name="reject" value="Offensive Content">
                                <label for="reason4" class="reason">Offensive or Inappropriate Content</label>
                            </div>
                            <div class="reason-desc">Vulgar language, hate speech, discrimination, or disrespectful
                                content.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason5" name="reject" value="Nudity or Sexual Content">
                                <label for="reason5" class="reason">Leaf Amount Not Suitable</label>
                            </div>
                            <div class="reason-desc">The amount of leaf does not worth the product own.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason6" name="reject" value="Violence">
                                <label for="reason6" class="reason">Violence or Harmful Behavior</label>
                            </div>
                            <div class="reason-desc">Violent content, self-harm references, or encouraging harm.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason7" name="reject" value="Illegal Activities">
                                <label for="reason7" class="reason">Illegal or Prohibited Elements</label>
                            </div>
                            <div class="reason-desc">Includes drugs, weapons, hacking, theft, etc.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason8" name="reject" value="Privacy Violation">
                                <label for="reason8" class="reason">Privacy Violation</label>
                            </div>
                            <div class="reason-desc">Private photos, personal data, or content without consent.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason9" name="reject" value="Copyright">
                                <label for="reason9" class="reason">Copyright or Intellectual Property
                                    Violation</label>
                            </div>
                            <div class="reason-desc">Copied images or projects without permission.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason10" name="reject" value="Low Quality">
                                <label for="reason10" class="reason">Low-Quality or Incomplete Submission</label>
                            </div>
                            <div class="reason-desc">Missing description, unclear steps, or poor-quality images.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason11" name="reject" value="Not Sustainable">
                                <label for="reason11" class="reason">Not Related to Sustainability</label>
                            </div>
                            <div class="reason-desc">Project does not relate to recycling, upcycling, or
                                eco-friendly
                                ideas.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason12" name="reject" value="Commercial Content">
                                <label for="reason12" class="reason">Commercial or Advertisement Content</label>
                            </div>
                            <div class="reason-desc">Promoting products, services, shops, or business.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason13" name="reject" value="Duplicate">
                                <label for="reason13" class="reason">Repetitive or Duplicate Post</label>
                            </div>
                            <div class="reason-desc">Same project submitted multiple times.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason14" name="reject" value="Environmentally Harmful">
                                <label for="reason14" class="reason">Harmful to Environment</label>
                            </div>
                            <div class="reason-desc">Encourages waste, pollution, or harmful materials.</div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason15" name="reject" value="Unoriginal">
                                <label for="reason15" class="reason">Unoriginal or Fully AI-generated
                                    Content</label>
                            </div>
                            <div class="reason-desc">Lacks originality or heavily auto-generated without student
                                effort.
                            </div>
                        </div>

                        <div class="reject-item">
                            <div class="main">
                                <input type="radio" id="reason16" name="reject" value="Unsuitable Image">
                                <label for="reason16" class="reason">Unsuitable or Irrelevant Image</label>
                            </div>
                            <div class="reason-desc">Image is not relevant to the project or inappropriate for the
                                context.
                            </div>
                        </div>
                    </form>

                </div>

                <div class="reject-button-zone">
                    <!--form="" can link the button to form div-->
                    <button type="button" id="cancel" class="canceling" form="reject-form">Cancel</button>

                    <div class="sendSection">
                        <input type="hidden" name="send">
                        <button type="submit" id="reject" name="send" form="reject-form" disabled>Send</button>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>

    <?php mysqli_close($conn)?>
</body>



</html>