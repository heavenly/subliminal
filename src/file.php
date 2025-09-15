<?php

function getMarkdownFiles($directory, $include_to_publish = false, $to_publish_dir = '') {
    
    $cache = loadCache();
    if ($cache === false) {
        $cache = ['posts' => [], 'markdown' => []];
    }
    $files = [];
    $cache_needs_update = false;

    
    $directories_to_scan = [$directory];

    
    if ($include_to_publish && !empty($to_publish_dir) && is_dir($to_publish_dir)) {
        $directories_to_scan[] = $to_publish_dir;
    }

    
    foreach ($directories_to_scan as $current_directory) {
        if (is_dir($current_directory)) {
            $items = scandir($current_directory);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..' && pathinfo($item, PATHINFO_EXTENSION) === 'md') {
                    $file_path = $current_directory . $item;
                    $cache_key = $current_directory . $item;

                    if (file_exists($file_path)) {
                        $current_hash = generateContentHash($file_path);

                        
                        if (isset($cache['posts'][$cache_key]) &&
                            isCacheValidForFile($file_path, $cache['posts'][$cache_key]['content_hash'])) {
                            
                            $cached_data = $cache['posts'][$cache_key];
                            $files[] = [
                                'filename' => $item,
                                'date' => $cached_data['date'],
                                'timestamp' => $cached_data['timestamp'],
                                'directory' => $current_directory,
                                'tags' => $cached_data['tags'] ?? []
                            ];
                        } else {
                            
                            $raw_content = file_get_contents($file_path);
                            $metadata = extractMetadata($raw_content);
                            $file_data = [
                                'filename' => $item,
                                'date' => $metadata['date'],
                                'timestamp' => strtotime($metadata['date']),
                                'directory' => $current_directory,
                                'tags' => $metadata['tags'] ?? []
                            ];
                            $files[] = $file_data;

                            
                            $cache['posts'][$cache_key] = [
                                'content_hash' => $current_hash,
                                'date' => $metadata['date'],
                                'timestamp' => strtotime($metadata['date']),
                                'tags' => $metadata['tags'] ?? []
                            ];
                            $cache_needs_update = true;
                        }
                    }
                }
            }
        }
    }

    
    if ($cache_needs_update) {
        saveCache($cache);
    }

    
    usort($files, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    return $files;
}
?>