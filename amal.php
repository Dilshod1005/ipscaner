<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP va Portlar</title>
    <style>
        .container {
            display: flex;
            justify-content: space-between;
        }
        .card {
            flex-basis: 45%;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        h2 {
            margin-top: 0;
        }
        .left {
            float: left;
        }
        .right {
            float: right;
        }
        .open {
            color: green;
        }
        .closed {
            color: red;
        }
        /* Button styling */
        .download-button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 24px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 12px;
        }

        .download-button:hover {
            background-color: #45a049; /* Darker Green */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card left">
        <h2>Ma'lumotlar</h2>
        <?php
        // Foydalanuvchi tomonidan kiritilgan IP manzilini olish
        $ip_address = $_POST['ip_address'];

        // Foydalanuvchi tomonidan kiritilgan port oralig'ini olish
        $port_range = $_POST['port_range'];
        list($start_port, $end_port) = explode("-", $port_range);

        // IP manzilni tekshirish va joylashgan joy haqida ma'lumotlarni olish
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://ipinfo.io/{$ip_address}/json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $ip_details = json_decode($response);
        curl_close($ch);

        // Faylga saqlangan ma'lumotlarni ekranga chiqarish
        if ($ip_details && isset($ip_details->ip)) {
            echo "<p><strong>IP Manzil:</strong> " . $ip_details->ip . "</p>";
            echo "<p><strong>Shahar:</strong> " . (isset($ip_details->city) ? $ip_details->city : "Ma'lumot mavjud emas") . "</p>";
            echo "<p><strong>Davlat:</strong> " . (isset($ip_details->country) ? $ip_details->country : "Ma'lumot mavjud emas") . "</p>";
            echo "<p><strong>Tashkilot:</strong> " . (isset($ip_details->org) ? $ip_details->org : "Ma'lumot mavjud emas") . "</p>";
            echo "<p><strong>Vaqt Mintaqasi:</strong> " . (isset($ip_details->timezone) ? $ip_details->timezone : "Ma'lumot mavjud emas") . "</p>";
            echo "<p><strong>Kompaniya:</strong> " . (isset($ip_details->org) ? $ip_details->org : "Ma'lumot mavjud emas") . "</p>";
        } else {
            echo "IP manzilining ma'lumotlarini olishda xatolik yuzaga keldi.";
        }
        ?>
    </div>

    <div class="card right">
        <h2>Portlar</h2>
        <ul>
            <?php
            // Portlarni tekshirish va chiqarish
            for ($port = $start_port; $port <= $end_port; $port++) {
                $fp = @fsockopen($ip_address, $port, $errno, $errstr, 0.1);
                if ($fp) {
                    echo "<li>Port $port: <span class='open'>Ochiq</span></li>";
                    fclose($fp);
                } else {
                    echo "<li>Port $port: <span class='closed'>Yopiq</span></li>";
                }
            }
            ?>
        </ul>
    </div>
</div>

<!-- "Malumotlarni yuklab olish" tugmasi -->
<form action="malumotlar.txt" method="get" target="_blank">
    <input type="submit" class="download-button" value="Ma'lumotlar yuklab olingan faylga o'tish">
</form>

<?php
// Faylga ma'lumotlarni yozish
$file_path = "malumotlar.txt";
$data_to_write = "IP Manzil: " . $ip_details->ip . "\n";
$data_to_write .= "Shahar: " . (isset($ip_details->city) ? $ip_details->city : "Ma'lumot mavjud emas") . "\n";
$data_to_write .= "Davlat: " . (isset($ip_details->country) ? $ip_details->country : "Ma'lumot mavjud emas") . "\n";
$data_to_write .= "Tashkilot: " . (isset($ip_details->org) ? $ip_details->org : "Ma'lumot mavjud emas") . "\n";
$data_to_write .= "Vaqt Mintaqasi: " . (isset($ip_details->timezone) ? $ip_details->timezone : "Ma'lumot mavjud emas") . "\n";
$data_to_write .= "Kompaniya: " . (isset($ip_details->org) ? $ip_details->org : "Ma'lumot mavjud emas") . "\n\n";

if (file_put_contents($file_path, $data_to_write, FILE_APPEND | LOCK_EX) !== false) {
    echo "<p>Ma'lumotlar fayliga muvaffaqiyatli saqlandi.</p>";
} else {
    echo "<p>Ma'lumotlar fayliga yozishda xatolik yuzaga keldi.</p>";
}
?>

</body>
</html>
git