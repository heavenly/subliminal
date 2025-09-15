# my blog

a simple php-based blog system that converts markdown files to html pages.

## features

- **markdown support**: write posts in markdown with support for headers, lists, code blocks, images, and links
- **automatic toc**: generates table of contents for posts with headers
- **caching**: caches processed html and metadata for better performance
- **responsive design**: mobile-friendly layout with dark/light theme support
- **image handling**: supports images per post with lazy loading
- **draft support**: unpublished posts in drafts/ directory
- **simple deployment**: no database required, just php and files

## structure

```
blog/
├── index.php          # main entry point
├── src/               # php source files
│   ├── cache.php      # caching functions
│   ├── config.php     # configuration
│   ├── file.php       # file processing
│   ├── functions.php  # utility functions
│   ├── markdown.php   # markdown to html converter
│   └── template.php   # html template
├── assets/            # static assets
│   ├── css/
│   │   └── styles.css # main stylesheet
│   ├── js/
│   │   └── script.js  # client-side scripts
│   ├── fonts/
│   │   └── asm-regular.otf
│   └── images/        # post images
├── config/
│   └── config.yml     # blog configuration
├── posts/             # published posts (markdown)
├── drafts/            # unpublished posts
├── cache/             # cached data
└── docs/              # documentation
```

## installation

1. clone or download the project files
2. ensure php is installed on your server
3. place files in your web directory
4. create markdown files in `posts/` directory
5. configure `config/config.yml` with your blog details
6. access `index.php` in your browser

## configuration

edit `config/config.yml` to set:
- blog title and description
- secret key for draft access

## writing posts

create `.md` files in `posts/` with front matter:

```yaml
---
title: my post title
date: 2024-01-01
description: brief description
tags: tag1; tag2
---

# post content

write your content in markdown here.

## images

place images in `assets/images/post-name/` and reference as `[image.jpg]`
```

## usage

- visit `index.php` to see all posts
- click post titles to read individual posts
- use `?key=your_key` to view drafts
- use `?clear_cache=yes` to clear cache

## customization

- modify `assets/css/styles.css` for styling
- update `src/template.php` for layout changes
- extend `src/markdown.php` for additional markdown features

## requirements

- php 7.0+
- web server (apache, nginx, etc.)
- no database needed

## license

this is a simple blog system. use at your own risk.