<?php
require_once 'src/config.php';
require_once 'src/functions.php';

$requested_post = isset($_GET['post']) ? $_GET['post'] : null;
if ($requested_post && !preg_match('/^[a-zA-Z0-9_-]+\.md$/', $requested_post)) {
    $requested_post = null;
}

$show_unpublished = isset($_GET['key']) && htmlspecialchars($_GET['key']) === 'heavenly';

if (isset($_GET['clear_cache']) && $_GET['clear_cache'] === 'yes') {
    clearCache();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$markdown_files = getMarkdownFiles($posts_directory, $show_unpublished, $drafts_directory);

$current_post = null;
$post_content = '';
$post_metadata = [];
$toc = [];
$post_directory = $posts_directory;
$view_count = 0;

if ($requested_post) {
    foreach ($markdown_files as $file_info) {
        if ($file_info['filename'] === $requested_post) {
            $post_directory = $file_info['directory'];
            break;
        }
    }
    $file_path = $post_directory . $requested_post;
    $real_path = realpath($file_path);
    $allowed_base = realpath(__DIR__);
    if ($real_path === false || strpos($real_path, $allowed_base) !== 0 || !file_exists($real_path)) {
        $requested_post = null;
    } else {
        $raw_content = file_get_contents($real_path);
        $post_metadata = extractMetadata($raw_content);
        $toc = generateTOC($post_metadata['content']);

        $article_name = pathinfo($requested_post, PATHINFO_FILENAME);
        $post_content = markdownToHtml($post_metadata['content'], $article_name, $post_directory);

        $current_post = $requested_post;

        // Increment and get view count
        $post_slug = pathinfo($requested_post, PATHINFO_FILENAME);
        $view_count = incrementPostViews($post_slug);
    }
}


require_once 'src/template.php';
?>
