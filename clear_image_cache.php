<?php
set_time_limit (0);
ini_set('memory_limit', '5512M');

$application_config = 'config.php';
if (file_exists($application_config)) {
    require_once($application_config);
} else {
    die('Ошибка: Не удалось загрузить config.php!');
}

$directories = [
    DIR_IMAGE . 'cachewebp/pixsel/products/',
    DIR_IMAGE . 'cachewebp/pixsel/categories/'
];

foreach ($directories as $directory) {
    // Рекурсивно ищем все файлы в директории
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if ($file->isFile()) {
            $filename = $file->getFilename();
            
            // Более точная проверка шаблона имени файла
            if (preg_match('/^[a-z]+_\w+-(\d+)x(\d+)(?:\..+)?$/i', $filename, $matches)) {
                $width = (int)$matches[1];
                $height = (int)$matches[2];
                
                // Проверяем только файлы, где в конце есть разрешение
                if ($width > 1000 || $height > 1000) {
                    // Для безопасности сначала выведем что будет удалено
                    echo "Найден файл для удаления: " . $file->getPathname() . " ($width x $height)\n";
                    // Раскомментируйте следующую строку для реального удаления
                    unlink($file->getPathname());
                }
            }
        }
    }
}

echo "Готово! (режим предпросмотра, раскомментируйте unlink для реального удаления)\n";
?>