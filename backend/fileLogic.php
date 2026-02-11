<?php 

function changeFileName($targetDir,$studentID,$extension){

    $formatedId=str_pad($studentID,5,"0",STR_PAD_LEFT); //1 -> 00001
    $desireName='S'.$formatedId;
    
    $absoluteDir = dirname(__DIR__) . '/student/' . trim($targetDir, '/\\') . '/'; //_DIR_ = /Ecoleaf/backend
    //dirname(_DIR_)= base path
    ///ecoleaf/student/image/DIYpost/
    if (!is_dir($absoluteDir)) {
        mkdir($absoluteDir, 0777, true);
    }

    $index= 0;
    $finalName=$desireName.'_'. $index . '.' . $extension;
    
    //file_exists only check the absolute path
    while (file_exists($absoluteDir . $finalName)) {
        $index++;
        $finalName = $desireName . "_" . $index . "." . $extension;
    }
    
    // return [
    //     "relative" => $targetDir . $finalName,
    //     "absolute" => $absoluteDir . $finalName
    // ];
    return [
        "relative" =>  $finalName,
        "absolute" => $absoluteDir . $finalName
    ];
}

function saveFiletoDirectory($image,$targetDestination){
    $uploadDone=false;
    
    $temporary_path=$image['tmp_name'];
    
    //(file,destination) -move file to a new destination
    if (move_uploaded_file($temporary_path,$targetDestination)){
        $uploadDone=true;
    }else{
        $uploadDone=false;
    } 
    return $uploadDone;
}

function getUploadFileError($imageExtension,$image){
    $issues="";
    $uploadOK= true;
    
    //checking is a real image or virus file
    if ($image['error'] === 4) {   //4 is UPLOAD_ERR_NO_FILE error
        // $issues .= "No file was uploaded.";
        $uploadOK = false;
    } else {
        if (!empty($image['tmp_name'])) {
            $check = getimagesize($image['tmp_name']);
            if ($check === false){
                $issues .= "File is not an image. ";
                $uploadOK = false;
            }
        } else {
            $uploadOK = false;
        }
                
        if ($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension !="jpeg"){
            $issues.="Sorry ,only JPG ,JPEG or PNG files are allow. ";
            $uploadOK=false;
        }

        if ($image['size'] > 2000000){ //~2MB
            $issues.="Sorry ,your file is too large. ";
            $uploadOK=false;
        }
    }
    //return as array
    return [$issues,$uploadOK];
}


function changeAdminImageName($targetDir, $adminId, $extension){

    $formattedId = str_pad($adminId, 5, "0", STR_PAD_LEFT); // 1 -> 00001
    $desireName = 'A' . $formattedId;

    $absoluteDir = dirname(__DIR__) . '/admin/' . trim($targetDir, '/\\') . '/';
        if (!is_dir($absoluteDir)) {
        mkdir($absoluteDir, 0777, true);
    }


    $index = 0;
    $finalName = $desireName . '_' . $index . '.' . $extension;

    while (file_exists($absoluteDir . $finalName)) {
        $index++;
        $finalName = $desireName . '_' . $index . '.' . $extension;
    }

    return [
        "relative" => $finalName,                  
        "absolute" => $absoluteDir . $finalName    
    ];
}

function changeOrganizerName($targetDir,$organizerId,$extension){

    $formatedId=str_pad($organizerId,5,"0",STR_PAD_LEFT); //1 -> 00001
    $desireName='O'.$formatedId;

    $absoluteDir = dirname(__DIR__) . '/organizer/' . trim($targetDir, '/\\') . '/'; //_DIR_ = /Ecoleaf/backend
    //dirname(_DIR_)= base path

    if (!is_dir($absoluteDir)) {
        mkdir($absoluteDir, 0777, true);
    }

    $index= 0;
    $finalName=$desireName.'_'. $index . '.' . $extension;
    
    //file_exists only check the absolute path
    while (file_exists($absoluteDir . $finalName)) {
        $index++;
        $finalName = $desireName . "_" . $index . "." . $extension;
    }
    
    return [
        "relative" =>  $finalName,
        "absolute" => $absoluteDir . $finalName
    ];
}
?>