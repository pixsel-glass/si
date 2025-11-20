<?php
require_once('config.php'); // Подключаем конфиг OpenCart

$csvFile = 'romania_cities.csv'; // Укажите путь к вашему файлу

if (!file_exists($csvFile)) {
    die("Ошибка: Файл не найден!\n");
}

// Подключение к базе
$mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Устанавливаем кодировку
$mysqli->set_charset("utf8mb4");

// === Проверяем, существует ли таблица oc_city ===
$tableCheck = $mysqli->query("SHOW TABLES LIKE 'oc_city'");
if ($tableCheck->num_rows == 0) {
    echo "Таблица oc_city не найдена. Создаю таблицу...\n";
    $createTableSQL = "
        CREATE TABLE oc_city (
            city_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            country_id INT UNSIGNED NOT NULL,
            zone_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
    ";
    if ($mysqli->query($createTableSQL)) {
        echo "Таблица oc_city успешно создана!\n";
    } else {
        die("Ошибка создания таблицы: " . $mysqli->error . "\n");
    }
}

// Читаем CSV-файл
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    fgetcsv($handle); // Пропускаем заголовки
    
    $imported = 0;
    $skipped = 0;

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $regionName = trim($data[0]);
        $cityName = trim($data[1]);

        if (empty($regionName) || empty($cityName)) {
            echo "Пропущена строка (пустое значение): " . implode(", ", $data) . "\n";
            $skipped++;
            continue;
        }

        // === Проверка наличия региона в oc_zone ===
        $stmt = $mysqli->prepare("SELECT zone_id FROM oc_zone WHERE country_id = 175 AND name = ?");
        $stmt->bind_param("s", $regionName);
        $stmt->execute();
        $stmt->bind_result($zoneId);
        $stmt->fetch();
        $stmt->close();
        
        if (!$zoneId) {
            // Если региона нет — добавляем
            $stmt = $mysqli->prepare("INSERT INTO oc_zone (country_id, name, code, status) VALUES (175, ?, '', 1)");
            $stmt->bind_param("s", $regionName);
            if ($stmt->execute()) {
                $zoneId = $stmt->insert_id;
                echo "Добавлен регион: $regionName\n";
            } else {
                echo "Ошибка при добавлении региона: " . $stmt->error . "\n";
            }
            $stmt->close();
        }

        // === Проверка наличия города в oc_city (учитываем зону) ===
        $stmt = $mysqli->prepare("SELECT city_id FROM oc_city WHERE country_id = 175 AND name = ? AND zone_id = ?");
        $stmt->bind_param("si", $cityName, $zoneId);
        $stmt->execute();
        $stmt->bind_result($cityId);
        $stmt->fetch();
        $stmt->close();
        
        if (!$cityId) {
            // Добавляем город
            $stmt = $mysqli->prepare("INSERT INTO oc_city (country_id, zone_id, name) VALUES (175, ?, ?)");
            $stmt->bind_param("is", $zoneId, $cityName);
            if ($stmt->execute()) {
                echo "Добавлен город: $cityName (Регион: $regionName)\n";
                $imported++;
            } else {
                echo "Ошибка при добавлении города: " . $stmt->error . "\n";
            }
            $stmt->close();
        } else {
            $skipped++;
        }
    }
    fclose($handle);
} else {
    die("Ошибка: Не удалось открыть файл!\n");
}

$mysqli->close();
echo "Импорт завершен! Добавлено: $imported, Пропущено: $skipped\n";
?>