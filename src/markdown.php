<?php

function markdownToHtml($markdown, $article_name = '', $directory = 'posts') {
    
    $content_hash = md5($markdown . $article_name);
    $cache_key = 'markdown_' . $content_hash;

    
    $cache = loadCache();
    if ($cache !== false && isset($cache['markdown'][$cache_key])) {
        return $cache['markdown'][$cache_key];
    }

    
    if ($cache === false) {
        $cache = ['posts' => [], 'markdown' => []];
    }

    
    $html = preg_replace_callback('/^(#{1,3})\s+(.+)$/m', function($matches) {
        $level = strlen($matches[1]);
        $title = $matches[2];
        return "<h{$level} id=\"" . generateId($title) . "\">{$title}</h{$level}>";
    }, $markdown);

    
    
    $html = preg_replace_callback('/\[([^\]]+\.(jpg|jpeg|png|gif|webp|svg))\]/i', function($matches) use ($article_name, $directory) {
        $image_name = $matches[1];
        $article_dir = !empty($article_name) ? $article_name : 'default';
        $image_path = 'assets/images/' . $article_dir . '/' . $image_name;
        return '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars(pathinfo($image_name, PATHINFO_FILENAME)) . '" loading="lazy">';
    }, $html);

    
    $html = preg_replace_callback('/^```(\w+)?\n(.*?)\n```$/ms', function($matches) {
        $language = !empty($matches[1]) ? 'language-' . htmlspecialchars($matches[1]) : '';
        $code = htmlspecialchars($matches[2]);
        return '<pre><code class="' . $language . '">' . $code . '</code></pre>';
    }, $html);
    $html = preg_replace('/`([^`\n]+)`/', '<code>$1</code>', $html);

    
    $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/(?<!\*)\*([^*\n]+)\*(?!\*)/', '<em>$1</em>', $html);

    
    $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $html);

    
    $html = preg_replace('/^> (.+)$/m', '<blockquote>$1</blockquote>', $html);

    
    $html = preg_replace_callback('/(?:^|\n)- (.+?)(?=\n- |\n\n|$)/s', function($matches) {
        $items = explode("\n- ", $matches[0]);
        $list_items = '';
        foreach ($items as $item) {
            if (!empty(trim($item))) {
                $list_items .= '<li>' . trim($item) . '</li>';
            }
        }
        return '<ul>' . $list_items . '</ul>';
    }, $html);

    
    $html = preg_replace('/\$(\d{1,3}(?:,\d{3})*(?:\.\d{1,2})?)/', '<span class="money">\$$1</span>', $html);

    
    $lines = explode("\n", $html);
    $result = '';
    $current_paragraph = '';
    $in_code_block = false;
    $in_list = false;

    foreach ($lines as $line) {
        $trimmed_line = trim($line);

        
        if (strpos($line, '<pre>') !== false) {
            $in_code_block = true;
        }
        if (strpos($line, '</pre>') !== false) {
            $in_code_block = false;
        }

        
        if (strpos($trimmed_line, '<ul>') !== false) {
            $in_list = true;
        }
        if (strpos($trimmed_line, '</ul>') !== false) {
            $in_list = false;
        }

        
        if ($in_code_block ||
            preg_match('/^<(h[1-6]|pre|ul|ol|img)/', $trimmed_line) ||
            $in_list) {
            
            if (!empty($current_paragraph)) {
                $result .= '<p>' . nl2br(trim($current_paragraph)) . '</p>' . "\n";
                $current_paragraph = '';
            }
            $result .= $line . "\n";
        } else if (empty($trimmed_line)) {
            
            if (!empty($current_paragraph)) {
                $result .= '<p>' . nl2br(trim($current_paragraph)) . '</p>' . "\n";
                $current_paragraph = '';
            }
            
            $result .= "<br>\n";
        } else {
            
            if (!empty($current_paragraph)) {
                $current_paragraph .= "\n" . $line;
            } else {
                $current_paragraph = $line;
            }
        }
    }

    
    if (!empty($current_paragraph)) {
        $result .= '<p>' . nl2br(trim($current_paragraph)) . '</p>' . "\n";
    }

    
    $cache['markdown'][$cache_key] = $result;
    saveCache($cache);

    return $result;
}

    
function generateId($text) {
    $text = trim(strip_tags($text));
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

    
function extractMetadata($content) {
    $metadata = [
        'title' => 'Untitled Post',
        'date' => date('Y-m-d'),
        'description' => '',
        'tags' => [],
        'content' => $content
    ];

    
    if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches)) {
        $yaml_content = $matches[1];
        $metadata['content'] = $matches[2];

        
        if (preg_match('/title:\s*(.+)$/m', $yaml_content, $title_match)) {
            $metadata['title'] = trim($title_match[1], '"\'');
        }
        if (preg_match('/date:\s*(.+)$/m', $yaml_content, $date_match)) {
            $metadata['date'] = trim($date_match[1], '"\'');
        }
        if (preg_match('/description:\s*(.+)$/m', $yaml_content, $desc_match)) {
            $metadata['description'] = trim($desc_match[1], '"\'');
        }
        if (preg_match('/tags:\s*(.+)$/m', $yaml_content, $tags_match)) {
            $tags_string = trim($tags_match[1], '"\'');
            $metadata['tags'] = array_filter(explode(';', $tags_string));
        }
    } else {
        
        if (preg_match('/^#\s+(.+)$/m', $content, $title_match)) {
            $metadata['title'] = $title_match[1];
        }
    }

    return $metadata;
}

    
function generateTOC($content) {
    preg_match_all('/^(#{1,3})\s+(.+)$/m', $content, $matches, PREG_SET_ORDER);
    $toc = [];

    foreach ($matches as $match) {
        $level = strlen($match[1]);
        $title = $match[2];
        $id = generateId($title);
        $toc[] = [
            'level' => $level,
            'title' => $title,
            'id' => $id
        ];
    }

    return $toc;
}
?>