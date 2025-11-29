<?php
// Kết nối database
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "spa_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy thông tin dịch vụ
$service_id = 1; // ID của Hydrafacial
$service_sql = "SELECT * FROM services WHERE id = $service_id";
$service_result = $conn->query($service_sql);
$service = $service_result->fetch_assoc();

// Lấy chi tiết dịch vụ
$details_sql = "SELECT * FROM service_details WHERE service_id = $service_id";
$details_result = $conn->query($details_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $service['name']; ?> - Spa Service</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: white;
            font-size: 3rem;
            font-weight: 300;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }

        .service-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .service-info h2 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .price {
            color: #f5576c;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .description {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }