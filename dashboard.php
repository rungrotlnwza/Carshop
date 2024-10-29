<?php
session_start();
include 'config.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // ถ้าไม่ได้เข้าสู่ระบบ, เปลี่ยนเส้นทางไปยังหน้า login
    exit();
}

// ดึงข้อมูลผู้ใช้ที่ล็อกอินอยู่
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result); // ข้อมูลผู้ใช้

// ตรวจสอบว่ามีการส่งข้อมูลการแก้ไข
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];

    // ตรวจสอบการแก้ไขรหัสผ่าน
    if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password === $confirm_password) {
            // เข้ารหัสรหัสผ่านใหม่
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET name='$name', phone='$phone', address='$address', country='$country', birthdate='$birthdate', gender='$gender', password='$hashed_password' WHERE username='$username'";
        } else {
            $error = "รหัสผ่านไม่ตรงกัน!";
        }
    } else {
        // อัปเดตข้อมูลผู้ใช้โดยไม่แก้ไขรหัสผ่าน
        $query = "UPDATE users SET name='$name', phone='$phone', address='$address', country='$country', birthdate='$birthdate', gender='$gender' WHERE username='$username'";
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    if (mysqli_query($conn, $query)) {
        $success = "อัปเดตข้อมูลสำเร็จ!";
        // ดึงข้อมูลใหม่หลังจากอัปเดต
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "เกิดข้อผิดพลาดในการอัปเดต: " . mysqli_error($conn);
    }
}

// ตรวจสอบว่าผู้ใช้เป็น admin หรือไม่
$is_logged_in = isset($_SESSION['username']);
$is_admin = ($is_logged_in && $_SESSION['status'] === 'admin'); // ตรวจสอบว่าเป็น admin หรือไม่
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Responsive Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="text-lg font-bold text-gray-700">
                    <a href="#" class="hover:text-blue-500">CarShop</a>
                </div>

                <!-- Menu for larger screens -->
                <div class="hidden md:flex space-x-4">
                    <a href="index.php" class="text-gray-700 hover:text-blue-500">รถยอดฮิต</a>
                    <a href="motorcycles.php" class="text-gray-700 hover:text-blue-500">จักรยานยนต์</a>
                    <a href="bigbikes.php" class="text-gray-700 hover:text-blue-500">บิ๊กไบค์</a>
                    <a href="sedans.php" class="text-gray-700 hover:text-blue-500">รถเก๋ง</a>
                    <a href="pickups.php" class="text-gray-700 hover:text-blue-500">รถกระบะ</a>

                    <?php if ($is_admin): ?>
                        <!-- ลิงก์เฉพาะสำหรับผู้ดูแลระบบ (admin) -->
                        <a href="upload.php" class="text-gray-700 hover:text-blue-500">Upload</a>
                        <a href="user_all.php" class="text-gray-700 hover:text-blue-500">Edit User</a>
                        <a href="editcar.php" class="text-gray-700 hover:text-blue-500">Edit Car</a>
                    <?php endif; ?>
                </div>

                <div class="hidden md:flex space-x-4">
                    <?php if ($is_logged_in): ?>
                        <a href="dashboard.php" class="text-gray-700 hover:text-blue-500">Dashboard</a>
                        <a href="logout.php" class="text-gray-700 hover:text-blue-500">Logout</a>
                    <?php else: ?>
                        <a href="register.php" class="text-gray-700 hover:text-blue-500">Register</a>
                        <a href="login.php" class="text-gray-700 hover:text-blue-500">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-10">
        <h2 class="text-2xl font-bold text-gray-700 mb-6">ข้อมูลส่วนตัวของคุณ</h2>

        <?php if (isset($success)): ?>
            <p class="text-green-500 mb-4"><?= $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?= $error; ?></p>
        <?php endif; ?>

        <!-- แสดงข้อมูลผู้ใช้ -->
        <div class="mb-4">
            <p class="text-gray-700"><strong>ชื่อผู้ใช้:</strong> <?= $user['username']; ?></p>
            <p class="text-gray-700"><strong>สถานะผู้ใช้:</strong> <?= $user['status']; ?></p>
        </div>

        <!-- แบบฟอร์มแก้ไขข้อมูล -->
        <form method="POST">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">ชื่อ:</label>
                <input type="text" name="name" id="name" value="<?= $user['name']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-gray-700">เบอร์โทรศัพท์:</label>
                <input type="text" name="phone" id="phone" value="<?= $user['phone']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="address" class="block text-gray-700">ที่อยู่:</label>
                <textarea name="address" id="address" class="border rounded p-2 w-full" required><?= $user['address']; ?></textarea>
            </div>

            <div class="mb-4">
                <label for="country" class="block text-gray-700">ประเทศ:</label>
                <input type="text" name="country" id="country" value="<?= $user['country']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="birthdate" class="block text-gray-700">วันเกิด:</label>
                <input type="date" name="birthdate" id="birthdate" value="<?= $user['birthdate']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="gender" class="block text-gray-700">เพศ:</label>
                <select name="gender" id="gender" class="border rounded p-2 w-full" required>
                    <option value="ชาย" <?= ($user['gender'] == 'ชาย') ? 'selected' : ''; ?>>ชาย</option>
                    <option value="หญิง" <?= ($user['gender'] == 'หญิง') ? 'selected' : ''; ?>>หญิง</option>
                    <option value="อื่นๆ" <?= ($user['gender'] == 'อื่นๆ') ? 'selected' : ''; ?>>อื่นๆ</option>
                </select>
            </div>

            <!-- แบบฟอร์มเปลี่ยนรหัสผ่าน -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700">รหัสผ่านใหม่ (ถ้าไม่ต้องการเปลี่ยนให้เว้นว่าง):</label>
                <input type="password" name="password" id="password" class="border rounded p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="block text-gray-700">ยืนยันรหัสผ่านใหม่:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="border rounded p-2 w-full">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">อัปเดตข้อมูล</button>
        </form>
    </div>
</body>

</html>
