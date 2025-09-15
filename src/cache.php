<?php

define('CACHE_DIR', __DIR__ . '/../cache/');
define('CACHE_FILE', CACHE_DIR . 'posts_cache.json');

function generateContentHash($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }
    return md5_file($file_path) . '_' . filemtime($file_path);
}

function loadCache() {
    if (!file_exists(CACHE_FILE)) {
        return false;
    }

    $cache_content = file_get_contents(CACHE_FILE);
    if ($cache_content === false) {
        return false;
    }

    $cache = json_decode($cache_content, true);
    return $cache ?: false;
}

function saveCache($cache_data) {
    if (!is_dir(CACHE_DIR)) {
        mkdir(CACHE_DIR, 0755, true);
    }

    $cache_data['generated_at'] = time();
    $json = json_encode($cache_data, JSON_PRETTY_PRINT);

    return file_put_contents(CACHE_FILE, $json) !== false;
}

function isCacheValidForFile($file_path, $cached_hash) {
    $current_hash = generateContentHash($file_path);
    return $current_hash === $cached_hash;
}

function clearCache() {
    if (file_exists(CACHE_FILE)) {
        unlink(CACHE_FILE);
        return true;
    }
    return false;
}

function getCacheStats() {
    if (!file_exists(CACHE_FILE)) {
        return ['status' => 'no_cache'];
    }

    $cache = loadCache();
    if (!$cache) {
        return ['status' => 'invalid_cache'];
    }

    $stats = [
        'status' => 'valid',
        'generated_at' => date('Y-m-d H:i:s', $cache['generated_at']),
        'posts_cached' => count($cache['posts'] ?? []),
        'markdown_cached' => count($cache['markdown'] ?? []),
        'cache_size_kb' => round(filesize(CACHE_FILE) / 1024, 2)
    ];

    return $stats;
}
?>