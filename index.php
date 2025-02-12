<?php
/**
 * Markdown-based Website Engine
 * Last Modified: March 19, 2024
 * 
 * INSTALLATION GUIDE:
 * 1. Required files and structure:
 *    - index.php (this file)
 *    - Parsedown.php (get from https://github.com/erusev/parsedown/blob/master/Parsedown.php)
 *    - /markdown/ directory (create this to store your .md files)
 *    - /markdown/share/ directory (create this for media files if you plan to use images/videos)
 *    - /markdown/index.md (create this as your homepage)
 * 
 * 2. Server requirements:
 *    - PHP 8.0 or higher
 *    - Apache with mod_rewrite (or equivalent URL rewriting for your server)
 * 
 * 3. Optional but recommended:
 *    - .htaccess file with:
 *      RewriteEngine On
 *      RewriteCond %{REQUEST_FILENAME} !-f
 *      RewriteCond %{REQUEST_FILENAME} !-d
 *      RewriteRule ^(.*)$ index.php/$1 [L,QSA]
 * 
 * 4. Usage:
 *    - Place markdown files in the /markdown directory
 *    - Use [[page name]] for internal links (no .md extension needed)
 *    - Use ![[image.jpg]] for images in /markdown/share/
 *    - Use ![[video.mp4]] for videos in /markdown/share/
 *    - Add |width after media for custom size: ![[image.jpg|500]]
 * 
 * 5. Configure the settings below to match your setup
 */

// Site Configuration
$CONFIG = [
    'markdown_dir' => __DIR__ . '/markdown',               // Directory containing markdown files
    'debug_mode' => false                                  // Set to true to enable debug logging
];

// Optional settings with fallbacks
$CONFIG['site_title'] = $CONFIG['site_title'] ?? 'Markdown Site';  // Default title if not set
$CONFIG['media_base_url'] = $CONFIG['media_base_url'] ?? '';      // Empty base URL will make media links relative

// Enable error reporting based on debug mode
error_reporting($CONFIG['debug_mode'] ? E_ALL : 0);
ini_set('display_errors', $CONFIG['debug_mode'] ? 1 : 0);

require 'Parsedown.php';

// Get the requested file path from the URL
$requestUri = $_SERVER['REQUEST_URI'];
$originalPath = parse_url($requestUri, PHP_URL_PATH);
$requestedPath = urldecode(ltrim(str_replace('/index.php', '', $originalPath), '/'));
$requestedPath = str_replace('+', ' ', $requestedPath);

// Clean the filename
$requestedPath = preg_replace('/[^a-zA-Z0-9\s\/\-\_\.]/', '', $requestedPath);

// Set default page and handle file extension
if ($requestedPath === '') {
    $requestedPath = 'index.md';
} elseif (!str_ends_with($requestedPath, '.md')) {
    $requestedPath .= '.md';
}

// Build file path
$filePath = rtrim($CONFIG['markdown_dir'], '/') . '/' . $requestedPath;

// Security check with path handling
if (!file_exists($filePath) || !realpath($filePath) || strpos(realpath($filePath), realpath($CONFIG['markdown_dir'])) !== 0) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    if ($CONFIG['debug_mode']) {
        echo "<pre>Debug Info:\n";
        echo "Requested Path: " . htmlspecialchars($requestedPath) . "\n";
        echo "File Path: " . htmlspecialchars($filePath) . "\n";
        echo "</pre>";
    }
    exit;
}

// Read and render the markdown content
$markdownContent = file_get_contents($filePath);

// Function to render Markdown
function renderMarkdown($markdown, $config) {
    // Handle video and image rendering with URL encoding and width support
    $markdown = preg_replace_callback('/!\[\[(.*?)\]\]/', function($matches) use ($config) {
        $parts = explode('|', $matches[1]);
        $filename = trim($parts[0]);
        $width = isset($parts[1]) ? intval(trim($parts[1])) : null;
        
        $encoded = rawurlencode($filename);
        $style = $width ? "width: {$width}px; height: auto;" : "max-width: 100%; height: auto;";
        
        // Use root-relative path if no media_base_url is set
        $mediaPath = $config['media_base_url'] ? $config['media_base_url'] : '/markdown';
        
        if (str_ends_with(strtolower($filename), '.mp4')) {
            return '<video controls style="' . $style . '"><source src="' . $mediaPath . '/share/' . $encoded . '" type="video/mp4">Your browser does not support the video tag.</video>';
        } else {
            return '<img src="' . $mediaPath . '/share/' . $encoded . '" alt="" style="' . $style . '">';
        }
    }, $markdown);

    // Convert [[links]] with proper URL encoding
    $markdown = preg_replace_callback(
        '/\[\[(.*?)\]\]/',
        function ($matches) {
            $text = $matches[1];
            $link = rawurlencode(str_replace(' ', '+', $text));

            if (filter_var($text, FILTER_VALIDATE_URL)) {
                return '<a href="' . htmlspecialchars($text) . '">' . htmlspecialchars($text) . '</a>';
            } else {
                return '<a href="/index.php/' . $link . '">' . htmlspecialchars($text) . '</a>';
            }
        },
        $markdown
    );

    $Parsedown = new Parsedown();
    return $Parsedown->text($markdown);
}

// Render the markdown content
$htmlContent = renderMarkdown($markdownContent, $CONFIG);

// Set the title
$title = ($requestedPath === 'index.md') ? $CONFIG['site_title'] : pathinfo($requestedPath, PATHINFO_FILENAME);

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #f0f0f0;
            line-height: 1.2;
            font-family: "Lexend Deca", serif;
            font-optical-sizing: auto;
            font-weight: 300;
            font-style: normal;
            margin-top: 0.2em;
            margin-bottom: 0.5em;
        }
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0.2em;
            margin-bottom: 0.2em;
        }
        h1 { margin-left: 1em; }
        h2 { margin-left: 2em; }
        h3 { margin-left: 3em; }
        h4 { margin-left: 4em; }
        h5 { margin-left: 5em; }
        h6 { margin-left: 6em; }
        p, ul, ol, img, video, source {
            margin-left: 7em;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }
        ul ul, ol ol {
            margin-left: -1em;
            margin-top: 0.3em;
            margin-bottom: 0.3em;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php echo $htmlContent; ?>
</body>
</html>

