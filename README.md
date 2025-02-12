# Simple Markdown Website Engine

A lightweight PHP-based website engine that converts markdown files into a beautiful, responsive website. Perfect for personal wikis, documentation sites, or simple blogs.

## ⚠️ Disclaimer

This code was entirely generated through AI (Claude-3.5-Sonnet in Cursor IDE compose agent mode) prompts. While I personally use it, anyone else choosing to use this code does so entirely at their own risk. No warranty or guarantee is provided, and you should thoroughly review and test the code before using it in any production environment.

## Features

- 🚀 **Simple Setup** - Just PHP and markdown files, no database required
- 🔗 **Wiki-style Links** - Use `[[page name]]` for internal links
- 🖼️ **Media Support** - Easy embedding of images and videos with `![[filename]]`
- 📱 **Responsive Design** - Looks great on all devices
- 🎨 **Clean Typography** - Using Lexend Deca font for optimal readability
- 🔒 **Secure** - Built-in path traversal protection
- 🛠️ **Configurable** - Easy to customize with settings at the top of index.php

## Requirements

- PHP 8.0 or higher
- Apache with mod_rewrite (or equivalent URL rewriting for your web server)
- Write permissions for the markdown directory

## Installation

1. **Download the Files**
   ```bash
   git clone [your-repo-url]
   # or download and extract the ZIP file
   ```

2. **Set Up the Directory Structure**
   ```
   your-website/
   ├── index.php
   ├── Parsedown.php
   ├── .htaccess
   └── markdown/
       ├── index.md
       └── share/
   ```

3. **Get Parsedown**
   - Download `Parsedown.php` from [Parsedown's GitHub](https://github.com/erusev/parsedown/blob/master/Parsedown.php)
   - Place it in your root directory

4. **Create .htaccess**
   Create a file named `.htaccess` with:
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php/$1 [L,QSA]
   ```

5. **Configure Settings**
   Edit the `$CONFIG` array in `index.php`:
   ```php
   $CONFIG = [
       'site_title' => "Your Website",                    
       'markdown_dir' => __DIR__ . '/markdown',           
       'media_base_url' => 'https://your-domain.com/markdown',
       'debug_mode' => false                              
   ];
   ```

6. **Create Your First Page**
   Create `markdown/index.md` with some content:
   ```markdown
   # Welcome to My Website
   
   This is my new markdown-powered website!
   
   Check out my [[about]] page.
   ```

## Usage

### Creating Pages
- Create `.md` files in the `markdown` directory
- Use standard markdown syntax
- Files are accessible via their names (e.g., `about.md` is accessed as `/about`)

### Special Syntax

1. **Internal Links**
   ```markdown
   [[page name]]  # Links to page-name.md
   ```

2. **Images**
   ```markdown
   ![[image.jpg]]         # Regular image
   ![[image.jpg|500]]     # Image with 500px width
   ```

3. **Videos**
   ```markdown
   ![[video.mp4]]         # Video with default size
   ![[video.mp4|720]]     # Video with 720px width
   ```

### Media Files
- Place all media files in the `markdown/share/` directory
- Supported formats:
  - Images: jpg, png, gif, etc.
  - Videos: mp4

## Customization

### Styling
The default style uses a dark theme with the Lexend Deca font. You can customize the appearance by editing the CSS in `index.php`.

### Debug Mode
Set `debug_mode` to `true` in the config to see detailed error messages during setup.

## Security

- Built-in protection against directory traversal attacks
- Sanitized file paths
- HTML-escaped output

## License

This project is open source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

If you encounter any issues or have questions, please open an issue on GitHub. 
