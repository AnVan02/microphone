<?php
$servername = "localhost";
$username = "root";
$passwork = "";
$dbname = "database";

$conn = new mysqli($servername, $username, $passwork, $dbname);
$conn->set_charset('utf8');

if ($conn->connect_errno) {
    die("kết nối thất bai: " . $conn->connect_error);
}

$service_id = 1;
$service_sql = "SELECT * FROM services weh"


?>

<div class="banner" style="margin-top: 40px">
    <div class="row">
        <div class="col-lg-12mb-3">
            <div if="carousel-inner active ">
                <img src="" alt="">
            </div>
            <div class="hero-text">
                <button class="button-style" onclick="windown.clocation.href=''"></button>
                <h2 style="color:#FFF"></h2>


            </div>
        </div>

    </div>
</div>