<?php
$posts_directory = 'posts/';
$drafts_directory = 'drafts/';
$default_post = null;

function loadConfig($config_file = 'config/config.yml') {
    $config = [
        'title' => 'My Blog',
        'description' => 'A simple blog',
        'secret_key' => 'secret'
    ];

    if (file_exists($config_file)) {
        $content = file_get_contents($config_file);
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\s*:\s*(.*)$/', $line, $matches)) {
                $key = trim($matches[1]);
                $value = trim($matches[2]);
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                $config[$key] = $value;
            }
        }
    }
    return $config;
}

$blog_config = loadConfig();
?>
