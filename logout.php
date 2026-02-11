<?php 

session_start(); //resumes the current session based on session identifier(ID)
session_destroy(); //destroy all data registed to a session (cannot use without session_start)

echo "<script>
console.log('Logged Out');
window.location.href='index.php';
</script>";

?>