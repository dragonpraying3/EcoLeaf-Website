<?php 

function insertNotify($conn,$title, $message, $userId) {
    
    $insertion = "INSERT INTO notification 
    (title, message, sendAt, isRead,userId) 
    VALUES (?, ?, NOW(), 0 , ?)";

    $insertion=$conn->prepare($insertion);
    $insertion->bind_param("ssi", $title, $message, $userId);

    if ($insertion->execute()) {
        return true;
    } else {
        error_log("Error inserting notification: " . $insertion->error);
        return false;
    }
    return true;
}

function markAsRead($conn, $notifyId) {
    $updateNotify="UPDATE notification
    SET isRead=1
    WHERE notifyId=?;";

    $notification=$conn->prepare($updateNotify);
    $notification->bind_param("i", $notifyId);
    if ($notification->execute()) {
        return true;
    } else {
        error_log("Error updating notification: " . $notification->error);
        return false;
    }
    
    return true;
}

?>