<?php
session_start();
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $purchase_price = $_POST['purchase_price']; // ราคาที่ซื้อมาด้วย
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $features = isset($_POST['features']) ? implode(', ', $_POST['features']) : ''; // รวบรวมฟีเจอร์ที่ถูกเลือก
    $type = $_POST['type']; // รับค่าประเภทรถจาก Dropdown

    // การอัพโหลดรูป
    $target_dir = "uploads/";
    $image = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $image);

    // บันทึกข้อมูลลงในฐานข้อมูล
    $query = "INSERT INTO cars (name, brand, model, year, price, purchase_price, `condition`, description, features, image, type) 
              VALUES ('$name', '$brand', '$model', '$year', '$price', '$purchase_price', '$condition', '$description', '$features', '$image', '$type')";
    mysqli_query($conn, $query);

    // หลังจากบันทึกสำเร็จ
    header('Location: index.php');
    exit();
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
    <title>Upload Car</title>
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
                    <a href="car_all.php" class="text-gray-700 hover:text-blue-500">Edit Car</a>
                    <a href="order.php" class="text-gray-700 hover:text-blue-500">Orders</a> <!-- เพิ่มลิงก์ Orders -->
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
        <h1 class="text-3xl font-bold text-center mb-6">เพิ่มข้อมูลรถใหม่</h1>
        <form method="POST" enctype="multipart/form-data" class="max-w-2xl mx-auto bg-white p-6 rounded shadow-md">

            <!-- ชื่อรถ -->
            <label for="name" class="block text-gray-700">ชื่อรถ</label>
            <input type="text" name="name" class="border rounded p-2 w-full mb-4" placeholder="ชื่อรถ" required>

            <!-- ยี่ห้อ -->
            <label for="brand" class="block text-gray-700">ยี่ห้อ</label>
            <input type="text" name="brand" class="border rounded p-2 w-full mb-4" placeholder="ยี่ห้อ" required>

            <!-- รุ่นรถ -->
            <label for="model" class="block text-gray-700">รุ่น</label>
            <input type="text" name="model" class="border rounded p-2 w-full mb-4" placeholder="รุ่นรถ" required>

            <!-- ปีที่ผลิต -->
            <label for="year" class="block text-gray-700">ปีที่ผลิต</label>
            <input type="number" name="year" class="border rounded p-2 w-full mb-4" placeholder="ปีที่ผลิต" required>

            <!-- สถานะ (ใหม่/มือสอง) -->
            <label for="condition" class="block text-gray-700">สถานะ</label>
            <div class="flex space-x-4 mb-4">
                <label><input type="radio" name="condition" value="ใหม่" required> ใหม่</label>
                <label><input type="radio" name="condition" value="มือสอง" required> มือสอง</label>
            </div>

            <!-- ฟีเจอร์ (Checkbox) -->
            <label for="features" class="block text-gray-700">ฟีเจอร์</label>
            <div class="flex space-x-4 mb-4">
                <label><input type="checkbox" name="features[]" value="แอร์"> แอร์</label>
                <label><input type="checkbox" name="features[]" value="วิทยุ"> วิทยุ</label>
                <label><input type="checkbox" name="features[]" value="กล้องหลัง"> กล้องหลัง</label>
            </div>

            <!-- ประเภทรถ (Dropdown) -->
            <label for="type" class="block text-gray-700">ประเภทรถ</label>
            <select name="type" class="border rounded p-2 w-full mb-4" required>
                <option value="">เลือกประเภทรถ</option>
                <option value="รถยนต์">รถยนต์</option>
                <option value="จักรยานยนต์">จักรยานยนต์</option>
                <option value="บิ๊กไบค์">บิ๊กไบค์</option>
                <option value="รถเก๋ง">รถเก๋ง</option>
                <option value="รถกระบะ">รถกระบะ</option>
            </select>

            <!-- ราคา -->
            <label for="price" class="block text-gray-700">ราคา</label>
            <input type="number" name="price" class="border rounded p-2 w-full mb-4" placeholder="ราคา" required>

            <!-- ราคาที่ซื้อมาด้วย -->
            <label for="purchase_price" class="block text-gray-700">ราคาที่ซื้อมาด้วย</label>
            <input type="number" name="purchase_price" class="border rounded p-2 w-full mb-4"
                placeholder="ราคาที่ซื้อมาด้วย" required>

            <!-- รายละเอียดเพิ่มเติม -->
            <label for="description" class="block text-gray-700">รายละเอียดเพิ่มเติม</label>
            <textarea name="description" class="border rounded p-2 w-full mb-4" placeholder="รายละเอียดเพิ่มเติม"
                required></textarea>

            <!-- อัพโหลดรูป -->
            <label for="image" class="block text-gray-700">อัพโหลดรูป</label>
            <input type="file" name="image" class="border rounded p-2 w-full mb-4" required>

            <!-- ปุ่มส่งข้อมูล -->
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">เพิ่มรถ</button>
        </form>
    </div>
</body>

</html>