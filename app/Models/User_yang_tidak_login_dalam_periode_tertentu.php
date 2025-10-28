<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "user_yang_tidak_login_dalam_periode_tertentu";


$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


$conn->query("CREATE DATABASE IF NOT EXISTS $db");
$conn->select_db($db);

//tabel user data dummy
$conn->query("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50),
        email VARCHAR(100),
        last_login DATETIME NULL
    )
");


$check = $conn->query("SELECT COUNT(*) AS total FROM users");
$row = $check->fetch_assoc();
if ($row['total'] == 0) {
    $conn->query("
        INSERT INTO users (username, email, last_login) VALUES
        ('andi', 'andi@gmail.com', '2025-09-10 14:22:00'),
        ('budi', 'budi@gmail.com', '2025-10-10 09:00:00'),
        ('citra', 'citra@gmail.com', NULL),
        ('dina', 'dina@gmail.com', '2025-07-01 18:15:00')
    ");
}

//periode 30 hari
$days = 30;


$sql = "SELECT * FROM users WHERE last_login IS NULL OR last_login < (NOW() - INTERVAL ? DAY)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $days);
$stmt->execute();
$result = $stmt->get_result();


echo "<h2>Daftar user tidak login lebih dari $days hari terakhir:</h2>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $last_login = $row['last_login'] ?? "Belum pernah login";
        echo "👤 " . $row['username'] . " |  " . $row['email'] . " |  " . $last_login . "<br>";
    }
} else {
    echo "✅ Semua user aktif dalam $days hari terakhir.";
}

$stmt->close();
$conn->close();
?>