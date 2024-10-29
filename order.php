<?php
session_start();
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เป็น admin หรือไม่
$is_logged_in = isset($_SESSION['username']);
$is_admin = ($is_logged_in && $_SESSION['status'] === 'admin');

// ดึงข้อมูล order ทั้งหมด
$query = "SELECT o.id AS order_id, c.name AS car_name, c.purchase_price, o.sale_price
          FROM `order` o 
          JOIN cars c ON o.car_id = c.id 
          WHERE o.order_status = 'completed'";
$result = mysqli_query($conn, $query);

if ($result === false) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูลคำสั่ง: " . mysqli_error($conn)); // แสดงข้อผิดพลาด SQL
}

// คำนวณสรุปรายรับ รายจ่าย และรายได้
$total_income = 0;
$total_expense = 0;

while ($order = mysqli_fetch_assoc($result)) {
    $total_expense += $order['purchase_price'];
    $total_income += $order['sale_price'];
}

// ลบ order เมื่อมีการกดปุ่มลบ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $delete_query = "DELETE FROM `order` WHERE id = $order_id";
    mysqli_query($conn, $delete_query);
    header("Location: orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
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
                    <a href="orders.php" class="text-gray-700 hover:text-blue-500">Orders</a>

                    <?php if ($is_admin): ?>
                    <!-- ลิงก์เฉพาะสำหรับผู้ดูแลระบบ (admin) -->
                    <a href="upload.php" class="text-gray-700 hover:text-blue-500">Upload</a>
                    <a href="user_all.php" class="text-gray-700 hover:text-blue-500">Edit User</a>
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
        <h1 class="text-3xl font-bold text-center mb-6">สรุปรายการสั่งซื้อ</h1>

        <!-- แสดงสรุปรายรับ รายจ่าย -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4">สรุปรายรับ รายจ่าย</h2>
            <p><strong>รายจ่ายทั้งหมด:</strong> ฿<?= number_format($total_expense, 2); ?></p>
            <p><strong>รายรับทั้งหมด:</strong> ฿<?= number_format($total_income, 2); ?></p>
            <p><strong>กำไรทั้งหมด:</strong> ฿<?= number_format($total_income - $total_expense, 2); ?></p>
        </div>

        <!-- แสดงรายการสั่งซื้อ -->
        <table class="min-w-full bg-white rounded-lg shadow-lg">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">ชื่อรถ</th>
                    <th class="py-2 px-4 border">ราคาที่ซื้อ</th>
                    <th class="py-2 px-4 border">ราคาที่ขาย</th>
                    <th class="py-2 px-4 border">กำไร</th>
                    <th class="py-2 px-4 border">ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($conn, $query);
                while ($order = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="py-2 px-4 border"><?= $order['car_name']; ?></td>
                    <td class="py-2 px-4 border">฿<?= number_format($order['purchase_price'], 2); ?></td>
                    <td class="py-2 px-4 border">฿<?= number_format($order['sale_price'], 2); ?></td>
                    <td class="py-2 px-4 border">฿<?= number_format($order['sale_price'] - $order['purchase_price'], 2); ?></td> <!-- กำไร -->
                    <td class="py-2 px-4 border">
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['order_id']; ?>">
                            <button type="submit" name="delete_order" class="bg-red-500 text-white px-4 py-2 rounded">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
