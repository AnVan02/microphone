<?php

function api($url)
{
    return json_decode(file_get_contents($url), true);
}

$action = $_GET["action"] ?? "";

switch ($action) {

    case "start":
        $res = api("http://127.0.0.1:5000/start");

        if (isset($res["error"])) {
            echo "❌ " . $res["error"];
        } else {
            echo "🟢 Bạn đã kết nối thành công!";
        }
        break;

    case "stop":
        api("http://127.0.0.1:5000/stop");
        echo "🔴 Đã ngắt kết nối!";
        break;

    case "record":
        $res = api("http://127.0.0.1:5000/record");

        if (isset($res["error"])) {
            echo "❌ " . $res["error"];
        } else {
            echo "🗣 Văn bản thu được: " . $res["text"];
        }
        break;

    default:
        echo "Dùng: ?action=start | stop | record";
}
