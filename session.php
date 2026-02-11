<?php

if (!isset($_SESSION['username'])) {
// Redirect to login page if session is not set
    echo "<script>alert('You have not login yet!');
    window.location.href='/EcoLeaf/index.php';</script>";
    exit();
}
?>