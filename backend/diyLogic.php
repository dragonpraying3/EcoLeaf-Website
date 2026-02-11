<?php 

//----------------------------------------------------------------------
//approve/reject  & update post status

function actionButton($conn,$approveSQL,$userId,$value){
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST[$value])){
    $GETitemId=trim($_POST['itemId']);
    $itemId=substr($GETitemId,2);
    
    $updatePost = $conn -> prepare($approveSQL);
    if (!$updatePost) {
    die("Prepare failed: " . $conn->error);
    }

    $updatePost -> bind_param("ii",$userId,$itemId);
    $updatePost -> execute();

    if($updatePost->affected_rows > 0){
        return true;
    } else {
        return false;
    }
    return true;
}
}


//function of checking form empty
function getCreatingFormError($title,$description,$leaf,$image){
    $errors="";
    
    if (empty($title) && empty($description) && empty($leaf) && $image['size'] == 0){
        $errors="⊗ All fields are required!";
        return $errors;
    }
    
    if (empty($title)){
        $errors.="Project Title is required. ";
    }
    if (empty($description)){
        $errors.="Description is required. ";
    }
    if (empty($leaf)){
        $errors.="Points for trade is required. ";
    }
    if ($image['error'] == UPLOAD_ERR_NO_FILE){
        $errors.="Image file is required. ";
    }
    return $errors;
}

function getTradingFormError($location,$datetime){
    $empty_error="";

    if (empty($location) && empty($datetime)){
        $empty_error="⊗ All fields are required! ";
        return $empty_error;
    }
    
    if (empty($location)){
        $empty_error.="Location is required. ";
    }

    if (empty($datetime)){
        $empty_error.="Start datetime is required. ";
    }
    return $empty_error;
}
function getLeafNotEnoughError($leafValue,$leaf){
    $leafError="";
    
    if ($leafValue<$leaf){
        $leafError="Your leaf are not enough! ";
    }
    
    return $leafError;
}
?>