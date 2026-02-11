<?php 
include_once '../topbar.php'; 
require_once __DIR__ . '/../backend/notifyLogic.php';

function checkStudentBadge($conn,$studentId){
    if (!isset($_SESSION['badge'])){
        $_SESSION['badge']=[]; //open new array if not exists yet
    }

    $eventCriteria="SELECT count(*) AS total
    from attendance
    WHERE studentId=? AND status='present'";

    //round down the decimal number
    $carbonCriteria="SELECT FLOOR(SUM(amountSaved)) as result
    FROM carboncalculator
    WHERE studentId=?;";

    $diyCriteria="SELECT count(*) AS sum
    from diyhub
    WHERE studentId=? AND status='approve'";

    $LeafCriteria="SELECT leaf
    FROM student
    WHERE studentId=?";

    //select available badge
    $available="SELECT badgeId,badgeName,criteria,value
    FROM badge
    WHERE status='visible';
    ";
    
    $availableResult = $conn-> query($available);
    
    if ($availableResult -> num_rows > 0){
        //get all visible badges
        while ($row=$availableResult->fetch_assoc()){
            $query='';
            switch ($row['criteria']){
                case 'event':
                    $query=$eventCriteria;
                    break;
                case 'carbon':
                    $query=$carbonCriteria;
                    break;
                case 'diy':
                    $query=$diyCriteria;
                    break;
                case 'leaf':
                    $query=$LeafCriteria;
                    break;
            }

            $badgeUnlock = $conn -> prepare($query);
            $badgeUnlock -> bind_param("i",$studentId);
            $badgeUnlock -> execute();

            $result=$badgeUnlock ->get_result() -> fetch_assoc(); //get the array
            
            //get the student value based on criteria
            //by access the key of array 
             switch ($row['criteria']){
                case 'event':
                    $studentValue=$result['total']; 
                    break;
                case 'carbon':
                    $studentValue=$result['result']; 
                    break;
                case 'diy':
                    $studentValue=$result['sum']; 
                    break;
                case 'leaf':
                    $studentValue=$result['leaf']; 
                    break;
            }

            //check if student enough value to get the badge
            if ($studentValue >= $row['value']){
                //if enough, then check duplication of badge student awarded
                $sql="SELECT * FROM studentbadge
                WHERE studentId=? AND badgeId=?";
                $checkDuplicate = $conn -> prepare($sql);
                $checkDuplicate -> bind_param("ii",$studentId,$row['badgeId']);
                $checkDuplicate ->execute();
                $dupResult = $checkDuplicate->get_result();
                
                $badgeId=$row['badgeId'];
                $badgeName=$row['badgeName'];
                if ($dupResult  -> num_rows>0){
                    //if have badge then skip
                    continue;
                }else{
                    $insert="INSERT INTO studentbadge(earnAt,badgeId,studentId)
                    VALUES(NOW(),?,?)";
                    
                    $insertBadge = $conn->prepare($insert);
                    $insertBadge ->bind_param("ii",$badgeId,$studentId);
                    
                    if ($insertBadge->execute()){
                        $title="New Badge Unlock!";
                        $message="You have successfully unlock the Badge '".$badgeName."'";
                        
                        $_SESSION['badge'][]=[
                            'badgeName'=>$badgeName
                        ];

                        $getting="SELECT u.userId
                        FROM users as u
                        JOIN student as s
                        ON u.userId=s.userId
                        WHERE s.studentId='$studentId'";
                        
                        $getId=$conn->query($getting);
                        $userId='';
                        
                        if ($getId -> num_rows==1){
                            while ($row=$getId->fetch_assoc()){
                                $userId=$row['userId'];
                            }
                            insertNotify($conn,$title,$message,$userId);
                        }
                    }else{
                        continue;
                    }
                }
            }else{
                continue;
            }
        }
    }

}

?>