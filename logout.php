<?php
session_start();
session_destroy(); // ทำลาย session ทั้งหมด
header("Location: login.php"); // เปลี่ยนเส้นทางไปหน้า login
exit();

?>