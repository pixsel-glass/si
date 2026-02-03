<?php
class Profiler {
    private static $timers = [];
    private static $memory = [];

    public static function start($name) {
        self::$timers[$name] = [
            'start' => microtime(true),
            'memory_start' => memory_get_usage()
        ];
    }

    public static function end($name) {
        if (!isset(self::$timers[$name])) {
            return null;
        }

        $data = self::$timers[$name];
        $time = (microtime(true) - $data['start']) * 1000;
        $memory = memory_get_usage() - $data['memory_start'];

        self::$timers[$name]['time'] = $time;
        self::$timers[$name]['memory'] = $memory;

        return $time;
    }

    public static function getReport() {
        $report = [];
        foreach (self::$timers as $name => $data) {
            if (isset($data['time'])) {
                $report[] = sprintf(
                    "%s: %.2fms (mem: %s)",
                    $name,
                    $data['time'],
                    self::formatBytes($data['memory'])
                );
            }
        }
        return $report;
    }

    public static function dump() {
        echo "<pre style='background:#f5f5f5;padding:20px;margin:20px;font-family:monospace;font-size:12px;'>";
        echo "<strong>PROFILER REPORT:</strong>\n\n";

        $sorted = self::$timers;
        uasort($sorted, function($a, $b) {
            return ($b['time'] ?? 0) <=> ($a['time'] ?? 0);
        });

        foreach ($sorted as $name => $data) {
            if (isset($data['time'])) {
                echo sprintf(
                    "%-50s %8.2fms %10s\n",
                    $name,
                    $data['time'],
                    self::formatBytes($data['memory'])
                );
            }
        }
        echo "</pre>";
    }

    private static function formatBytes($bytes) {
        if ($bytes < 1024) return $bytes . 'B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . 'KB';
        return round($bytes / 1048576, 2) . 'MB';
    }
}