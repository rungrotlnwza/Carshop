<?php
session_start();
include 'config.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // ถ้าไม่ได้เข้าสู่ระบบ, เปลี่ยนเส้นทางไปยังหน้า login
    exit();
}

// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$query = "SELECT username, name, profile_image FROM users";
$result = mysqli_query($conn, $query);

// ตรวจสอบว่าผู้ใช้เป็น admin หรือไม่
$is_logged_in = isset($_SESSION['username']);
$is_admin = ($is_logged_in && $_SESSION['status'] === 'admin'); // ตรวจสอบว่าเป็น admin หรือไม่
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
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
                        <a href="edituser.php" class="text-gray-700 hover:text-blue-500">Edit User</a>
                        <a href="car_all.php" class="text-gray-700 hover:text-blue-500">Edit Car</a>
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

    <div class="container mx-auto py-12">
        <h1 class="text-3xl font-bold text-center mb-6">ผู้ใช้ทั้งหมด</h1>

        <!-- ช่อง Input สำหรับค้นหา -->
        <input type="text" id="searchInput" placeholder="ค้นหาด้วยชื่อ" class="border rounded p-2 w-full mb-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="userCards">
            <?php while ($user = mysqli_fetch_assoc($result)) { ?>
                <div class="bg-white shadow-lg rounded-lg overflow-hidden user-card">
                    <img src="<?= $user['profile_image']; ?>" alt="User Image" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-xl font-bold text-gray-700 user-name"><?= $user['name']; ?></h3>
                        <a href="edituser.php?username=<?= $user['username']; ?>" class="text-blue-500 hover:underline mt-2 block">แก้ไข</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-200 py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Section 1: Contact Information -->
                <div>
                    <h3 class="text-lg font-bold mb-4">ข้อมูลติดต่อ</h3>
                    <p>ที่อยู่: 1234 ถนนหลัก, กรุงเทพ</p>
                    <p>โทรศัพท์: (02) 123-4567</p>
                    <p>อีเมล: contact@carshop.com</p>
                </div>

                <!-- Section 2: Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4">ลิงก์ด่วน</h3>
                    <ul>
                        <li><a href="#" class="hover:underline">เกี่ยวกับเรา</a></li>
                        <li><a href="#" class="hover:underline">นโยบายความเป็นส่วนตัว</a></li>
                        <li><a href="#" class="hover:underline">เงื่อนไขการให้บริการ</a></li>
                    </ul>
                </div>

                <!-- Section 3: Follow Us -->
                <div>
                    <h3 class="text-lg font-bold mb-4">ติดตามเรา</h3>
                    <ul class="flex space-x-4">
                        <li><a href="#" class="hover:text-gray-400">Facebook</a></li>
                        <li><a href="#" class="hover:text-gray-400">Twitter</a></li>
                        <li><a href="#" class="hover:text-gray-400">Instagram</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-600 pt-4 text-center">
                <p>&copy; 2024 CarShop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Script สำหรับฟังก์ชันค้นหาแบบ realtime -->
    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const userCards = document.querySelectorAll('.user-card');

            userCards.forEach(function (card) {
                const userName = card.querySelector('.user-name').textContent.toLowerCase();
                if (userName.includes(searchValue)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
