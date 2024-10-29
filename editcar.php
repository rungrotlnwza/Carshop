<?php
session_start();
include 'config.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามีการส่งค่า id ของรถมา
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];
    $query = "SELECT * FROM cars WHERE id = '$car_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $car = mysqli_fetch_assoc($result);
    } else {
        echo "ไม่พบข้อมูลรถ";
        exit();
    }
} else {
    echo "ไม่มีการระบุ id ของรถ";
    exit();
}

// ตรวจสอบสิทธิ์ผู้ใช้ว่าเป็น admin หรือไม่
$is_logged_in = isset($_SESSION['username']);
$is_admin = ($is_logged_in && $_SESSION['status'] === 'admin');

if (!$is_admin) {
    echo "คุณไม่มีสิทธิ์ในการแก้ไขข้อมูลนี้";
    exit();
}

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $sale_status = $_POST['sale_status']; // รับสถานะการขาย

    // อัปโหลดรูปภาพใหม่ถ้ามีการอัปโหลด
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);

        $query = "UPDATE cars SET name='$name', brand='$brand', model='$model', year='$year', price='$price', `condition`='$condition', description='$description', type='$type', image='$image', sale_status='$sale_status' WHERE id='$car_id'";
    } else {
        // อัปเดตข้อมูลรถโดยไม่เปลี่ยนรูปภาพ
        $query = "UPDATE cars SET name='$name', brand='$brand', model='$model', year='$year', price='$price', `condition`='$condition', description='$description', type='$type', sale_status='$sale_status' WHERE id='$car_id'";
    }

    if (mysqli_query($conn, $query)) {
        $success = "อัปเดตข้อมูลสำเร็จ!";
        // ดึงข้อมูลใหม่หลังจากอัปเดต
        $result = mysqli_query($conn, "SELECT * FROM cars WHERE id = '$car_id'");
        $car = mysqli_fetch_assoc($result);
    } else {
        $error = "เกิดข้อผิดพลาดในการอัปเดต: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลรถ</title>
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
                    <a href="car_all.php" class="text-gray-700 hover:text-blue-500">Edit Car</a>
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
        <h2 class="text-2xl font-bold text-gray-700 mb-6">แก้ไขข้อมูลรถ: <?= $car['name']; ?></h2>

        <?php if (isset($success)): ?>
            <p class="text-green-500 mb-4"><?= $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?= $error; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">ชื่อรถ:</label>
                <input type="text" name="name" id="name" value="<?= $car['name']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="brand" class="block text-gray-700">ยี่ห้อ:</label>
                <input type="text" name="brand" id="brand" value="<?= $car['brand']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="model" class="block text-gray-700">รุ่น:</label>
                <input type="text" name="model" id="model" value="<?= $car['model']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="year" class="block text-gray-700">ปีที่ผลิต:</label>
                <input type="number" name="year" id="year" value="<?= $car['year']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-gray-700">ราคา:</label>
                <input type="number" name="price" id="price" value="<?= $car['price']; ?>" class="border rounded p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label for="condition" class="block text-gray-700">สถานะ (ใหม่/มือสอง):</label>
                <select name="condition" id="condition" class="border rounded p-2 w-full" required>
                    <option value="ใหม่" <?= ($car['condition'] == 'ใหม่') ? 'selected' : ''; ?>>ใหม่</option>
                    <option value="มือสอง" <?= ($car['condition'] == 'มือสอง') ? 'selected' : ''; ?>>มือสอง</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700">ประเภท:</label>
                <select name="type" id="type" class="border rounded p-2 w-full" required>
                    <option value="รถยนต์" <?= ($car['type'] == 'รถยนต์') ? 'selected' : ''; ?>>รถยนต์</option>
                    <option value="จักรยานยนต์" <?= ($car['type'] == 'จักรยานยนต์') ? 'selected' : ''; ?>>จักรยานยนต์</option>
                    <option value="บิ๊กไบค์" <?= ($car['type'] == 'บิ๊กไบค์') ? 'selected' : ''; ?>>บิ๊กไบค์</option>
                    <option value="รถเก๋ง" <?= ($car['type'] == 'รถเก๋ง') ? 'selected' : ''; ?>>รถเก๋ง</option>
                    <option value="รถกระบะ" <?= ($car['type'] == 'รถกระบะ') ? 'selected' : ''; ?>>รถกระบะ</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="sale_status" class="block text-gray-700">สถานะการขาย:</label>
                <select name="sale_status" id="sale_status" class="border rounded p-2 w-full" required>
                    <option value="available" <?= ($car['sale_status'] == 'available') ? 'selected' : ''; ?>>ยังไม่ขาย</option>
                    <option value="sold" <?= ($car['sale_status'] == 'sold') ? 'selected' : ''; ?>>ขายแล้ว</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700">รายละเอียดเพิ่มเติม:</label>
                <textarea name="description" id="description" class="border rounded p-2 w-full" required><?= $car['description']; ?></textarea>
            </div>

            <!-- การอัปโหลดรูปภาพ -->
            <div class="mb-4">
                <label for="image" class="block text-gray-700">รูปภาพ:</label>
                <?php if (!empty($car['image'])): ?>
                    <img src="<?= $car['image']; ?>" alt="Car Image" class="w-20 h-20 object-cover mb-2">
                <?php endif; ?>
                <input type="file" name="image" id="image" class="border rounded p-2 w-full">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">อัปเดตข้อมูล</button>
        </form>
    </div>
</body>

</html>
