<?php
include 'config.php'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $status = isset($_POST['status']) ? $_POST['status'] : 'user'; // กำหนดค่าเริ่มต้นเป็น 'user' ถ้าไม่มีการเลือก

        // ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
        if ($password === $confirm_password) {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $country = $_POST['country'];
            $birthdate = $_POST['birthdate'];
            $gender = $_POST['gender'];

            // อัปโหลดรูปโปรไฟล์
            $target_dir = "uploads/";
            $profile_image = $target_dir . basename($_FILES["profile_image"]["name"]);
            move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image);

            // บันทึกข้อมูลลงฐานข้อมูล
            $query = "INSERT INTO users (username, password, name, phone, address, country, birthdate, gender, profile_image, status) 
                      VALUES ('$username', '$password', '$name', '$phone', '$address', '$country', '$birthdate', '$gender', '$profile_image', '$status')";

            if (mysqli_query($conn, $query)) {
                echo "<p class='text-green-500'>สมัครสมาชิกสำเร็จ!</p>";
                header("Location: login.php");
            } else {
                echo "<p class='text-red-500'>เกิดข้อผิดพลาด: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p class='text-red-500'>รหัสผ่านไม่ตรงกัน</p>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto px-4 py-10">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden p-6">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">สมัครสมาชิก</h2>

            <!-- ฟอร์มการสมัคร -->
            <form id="registration-form" method="POST" enctype="multipart/form-data">
                <!-- Step 1: ชื่อผู้ใช้และรหัสผ่าน -->
                <div id="step-1">
                    <div>
                        <label for="username" class="block text-gray-700">ชื่อผู้ใช้:</label>
                        <input type="text" id="username" name="username" class="border rounded p-2 w-full" required>
                    </div>

                    <div class="mt-4">
                        <label for="password" class="block text-gray-700">รหัสผ่าน:</label>
                        <input type="password" id="password" name="password" class="border rounded p-2 w-full" required>
                    </div>

                    <div class="mt-4">
                        <label for="confirm_password" class="block text-gray-700">ยืนยันรหัสผ่าน:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="border rounded p-2 w-full" required>
                    </div>

                    <button type="button" onclick="goToStep2()" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Next</button>
                </div>

                <!-- Step 2: ข้อมูลเพิ่มเติม -->
                <div id="step-2" class="hidden">
                    <div>
                        <label for="name" class="block text-gray-700">ชื่อ:</label>
                        <input type="text" id="name" name="name" class="border rounded p-2 w-full" required>
                    </div>

                    <div class="mt-4">
                        <label for="phone" class="block text-gray-700">เบอร์โทรศัพท์:</label>
                        <input type="text" id="phone" name="phone" class="border rounded p-2 w-full" required>
                    </div>

                    <div class="mt-4">
                        <label for="address" class="block text-gray-700">ที่อยู่:</label>
                        <textarea id="address" name="address" class="border rounded p-2 w-full" required></textarea>
                    </div>

                    <div class="mt-4">
                        <label for="country" class="block text-gray-700">ประเทศ:</label>
                        <input type="text" id="country" name="country" class="border rounded p-2 w-full" required>
                    </div>

                    <div class="mt-4">
                        <label for="birthdate" class="block text-gray-700">วันเกิด:</label>
                        <input type="date" id="birthdate" name="birthdate" class="border rounded p-2 w-full" required>
                    </div>

                    <div class="mt-4">
                        <label for="gender" class="block text-gray-700">เพศ:</label>
                        <select id="gender" name="gender" class="border rounded p-2 w-full" required>
                            <option value="ชาย">ชาย</option>
                            <option value="หญิง">หญิง</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label for="profile_image" class="block text-gray-700">รูปโปรไฟล์:</label>
                        <input type="file" id="profile_image" name="profile_image" class="border rounded p-2 w-full" required>
                    </div>

                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mt-4">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ฟังก์ชันเพื่อย้ายจาก Step 1 ไป Step 2
        function goToStep2() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;

            if (username.trim() === "" || password.trim() === "" || confirmPassword.trim() === "") {
                alert('กรุณากรอกข้อมูลให้ครบ');
                return;
            }

            if (password !== confirmPassword) {
                alert('รหัสผ่านไม่ตรงกัน');
                return;
            }

            // ซ่อน Step 1 และแสดง Step 2
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
        }
    </script>
</body>
</html>
