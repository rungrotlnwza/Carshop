<?php
session_start(); // เริ่มต้นการทำงาน session
include 'config.php'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query เพื่อตรวจสอบว่ามี username และ password ตรงกับที่ฐานข้อมูลหรือไม่
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    // ตรวจสอบว่าพบข้อมูลหรือไม่
    if (mysqli_num_rows($result) === 1) {
        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $user = mysqli_fetch_assoc($result);
        $status = $user['status']; // ดึงสถานะของผู้ใช้ (user, admin, banned)

        // ตรวจสอบสถานะของผู้ใช้ ถ้าไม่ใช่ banned ให้เข้าใช้งานได้
        if ($status !== 'banned') {
            // ถ้า login สำเร็จ สร้าง session และเปลี่ยนเส้นทางไปหน้า index.php
            $_SESSION['username'] = $username;
            $_SESSION['status'] = $status; // เก็บสถานะของผู้ใช้ใน session
            header("Location: index.php");
            exit();
        } else {
            // กรณีผู้ใช้ถูก banned
            $error = "บัญชีของคุณถูกแบน";
        }
    } else {
        // ถ้า login ไม่สำเร็จ แสดงข้อความผิดพลาด
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto mt-20 bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-700 mb-6">เข้าสู่ระบบ</h2>

        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?= $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">ชื่อผู้ใช้:</label>
                <input type="text" name="username" id="username" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700">รหัสผ่าน:</label>
                <input type="password" name="password" id="password" class="border rounded p-2 w-full" required>
            </div>
            <div>
            <label for="register">i don't have a accout <a href="register.php">register</a></label>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
