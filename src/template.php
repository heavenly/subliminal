<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_post ? htmlspecialchars($post_metadata['title']) : htmlspecialchars($blog_config['title']); ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <meta name="description" content="<?php echo htmlspecialchars($post_metadata['description'] ?? $blog_config['description']); ?>">
</head>
<body>
    <header class="header">
        <h1><a href="index.php" class="blog-title"><?php echo htmlspecialchars($blog_config['title']); ?></a></h1>
        <div class="header-info">
            <p><?php echo htmlspecialchars($blog_config['description']); ?></p>
            <p>powered by <a class="read-more" href="https://github.com/heavenly/subliminal">hellish technology</a></p>
        </div>
    </header>

    <?php if ($current_post): ?>
        <nav class="breadcrumb">
            <a href="index.php">← Back to all posts</a>
        </nav>
        <div class="post-layout">
            <?php if (!empty($toc)): ?>
            <aside class="toc-sidebar">
                <nav class="toc">
                    <h2>table of contents</h2>
                    <ul>
                        <?php foreach ($toc as $item): ?>
                            <li><a href="#<?php echo $item['id']; ?>" class="toc-h<?php echo $item['level']; ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </aside>
            <?php endif; ?>
            <main class="content single-post">
                <article>
                    <header class="post-header">
                        <h1><?php echo htmlspecialchars($post_metadata['title']); ?></h1>
                        <div class="meta">
                            published on <?php echo strtolower(date('F j, Y', strtotime($post_metadata['date']))); ?>
                            <?php
                            $last_modified_timestamp = filemtime($real_path);
                            $published_timestamp = strtotime($post_metadata['date']);
                            if ($last_modified_timestamp > $published_timestamp): ?>
                                • edited on <?php echo strtolower(date('F j, Y', $last_modified_timestamp)); ?>
                            <?php endif; ?>
                            • <span class="reading-time"></span>
                        </div>
                        <?php if ($post_metadata['description']): ?>
                            <p class="post-description"><?php echo htmlspecialchars($post_metadata['description']); ?></p>
                        <?php endif; ?>
                    </header>
                    <div class="post-content">
                        <?php echo $post_content; ?>
                    </div>
                </article>
            </main>
        </div>
    <?php else: ?>
        <main class="content">
            <?php if (empty($markdown_files)): ?>
                <div class="no-posts">
                    <h2>## no posts found</h2>
                    <p>create some <code>.md</code> files in the <code>posts/</code> directory to get started!</p>
                </div>
            <?php else: ?>
                                <div class="posts-list">
    <h2>recent posts</h2>
    <?php foreach ($markdown_files as $file_info): ?>
        <?php
        $filename = $file_info['filename'];
        $file_path = $file_info['directory'] . $filename;
        $raw_content = file_get_contents($file_path);
        $metadata = extractMetadata($raw_content);
        $post_url = 'index.php?post=' . urlencode(htmlspecialchars($filename)) . ($show_unpublished ? ('&key=' . $blog_config['secret_key']) : '');
        ?>
        <article class="post-preview">
            <h3><a href="<?php echo $post_url; ?>"><?php echo htmlspecialchars($metadata['title']); ?></a></h3>
            <div class="meta">
                <?php echo strtolower(date('F j, Y', strtotime($metadata['date']))); ?>
                <?php if ($file_info['directory'] === $drafts_directory): ?>
                    <span style="color: #c54b99; font-weight: bold;"> • draft</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($metadata['description'])): ?>
                <p class="post-excerpt"><?php echo htmlspecialchars($metadata['description']); ?></p>
            <?php endif; ?>

            <?php
            if (!empty($metadata['tags']) && is_array($metadata['tags'])): ?>
                <div class="post-tags">
                    <?php foreach ($metadata['tags'] as $index => $tag): ?>
                        <?php $tag = trim($tag); ?>
                        <?php if (!empty($tag)): ?>
                            <span class="tag"><?php echo htmlspecialchars($tag); ?></span><?php if ($index < count($metadata['tags']) - 1): ?> <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="<?php echo $post_url; ?>" class="read-more">read more →</a>
        </article>
    <?php endforeach; ?>
</div>

            <?php endif; ?>
        </main>
    <?php endif; ?>

    <button id="back-to-top" class="back-to-top" title="Back to top">↑</button>
    <script>
        window.blogData = {
            currentPost: <?php echo json_encode($current_post); ?>,
            hasContent: <?php echo json_encode(!empty($post_content)); ?>
        };
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>
