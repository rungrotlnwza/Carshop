<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "car_shop";

// สร้างการเชื่อมต่อ
$conn = new mysqli($host, $user, $password, $db);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>