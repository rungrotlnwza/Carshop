<?php
session_start();
include 'config.php';

// ตรวจสอบว่ามีการส่งค่า id ของรถมา
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];
    // ดึงข้อมูลของรถที่มี id ที่ตรงกัน
    $query = "SELECT * FROM cars WHERE id = '$car_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $car = mysqli_fetch_assoc($result); // เก็บข้อมูลรถในตัวแปร $car
    } else {
        echo "ไม่พบข้อมูลรถ";
        exit();
    }
} else {
    echo "ไม่มีการระบุ id ของรถ";
    exit();
}

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือยัง
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ฟังก์ชันการซื้อ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    // อัปเดตสถานะของรถเป็น "sold"
    $update_car_status = "UPDATE cars SET sale_status = 'sold' WHERE id = '$car_id'";
    mysqli_query($conn, $update_car_status);

    // เพิ่มข้อมูลลงในตาราง order
    $username = $_SESSION['username'];
    $order_date = date('Y-m-d H:i:s');
    $insert_order = "INSERT INTO `order` (car_id, username, order_date) VALUES ('$car_id', '$username', '$order_date')";
    mysqli_query($conn, $insert_order);

    // ส่งกลับไปที่หน้าเดิมหลังจากซื้อเสร็จ
    header("Location: detail.php?id=$car_id&purchase=success");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดรถ</title>
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
                </div>

                <div class="hidden md:flex space-x-4">
                    <a href="dashboard.php" class="text-gray-700 hover:text-blue-500">Dashboard</a>
                    <a href="logout.php" class="text-gray-700 hover:text-blue-500">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Section: Display Car Details -->
    <div class="container mx-auto py-12">
        <h1 class="text-3xl font-bold text-center mb-6">รายละเอียดรถ: <?= $car['name']; ?></h1>
        
        <div class="bg-white shadow-lg rounded-lg overflow-hidden max-w-2xl mx-auto">
            <img src="<?= $car['image']; ?>" alt="Car Image" class="w-full h-64 object-cover">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-700 mb-4"><?= $car['name']; ?> - <?= $car['brand']; ?></h2>
                <p class="text-gray-600 mb-2"><strong>ประเภท:</strong> <?= $car['type']; ?></p>
                <p class="text-gray-600 mb-2"><strong>ปีที่ผลิต:</strong> <?= $car['year']; ?></p>
                <p class="text-gray-600 mb-2"><strong>ราคา:</strong> ฿<?= number_format($car['price']); ?></p>
                <p class="text-gray-600 mb-2"><strong>สถานะ:</strong> <?= $car['condition']; ?></p>
                <p class="text-gray-600 mb-2"><strong>ฟีเจอร์:</strong> <?= $car['features']; ?></p>
                <p class="text-gray-600 mb-4"><strong>รายละเอียดเพิ่มเติม:</strong> <?= $car['description']; ?></p>

                <?php if ($car['sale_status'] == 'available') { ?>
                    <p class="text-green-500 font-bold mb-4">สถานะการขาย: ยังไม่ขาย</p>

                    <!-- ปุ่มซื้อ -->
                    <form method="POST">
                        <button type="submit" name="buy" class="bg-blue-500 text-white px-4 py-2 rounded">ซื้อ</button>
                    </form>
                <?php } else { ?>
                    <p class="text-red-500 font-bold mb-4">สถานะการขาย: ขายแล้ว</p>
                <?php } ?>

                <!-- ข้อความแสดงความสำเร็จ -->
                <?php if (isset($_GET['purchase']) && $_GET['purchase'] == 'success'): ?>
                    <p class="text-green-500 mt-4">การสั่งซื้อสำเร็จ!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-200 py-10 mt-10">
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
</body>

</html>
