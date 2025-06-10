# Pride Plugin - Blog Component Documentation

## Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Architecture](#architecture)
- [API Reference](#api-reference)
- [UI Components](#ui-components)
- [Customization](#customization)
- [Troubleshooting](#troubleshooting)

## Overview

The Blog Component is a feature-rich WordPress plugin component that provides a modern, responsive blog display system with advanced filtering, searching, and pagination capabilities. It uses the WordPress REST API for data fetching and Tailwind CSS for styling.

## Features

- 📱 Responsive grid and list layouts
- 🔍 Real-time search functionality
- 🏷️ Category filtering
- 🔄 Dynamic sorting options
- 📄 AJAX-powered pagination
- 🎨 Modern UI with Tailwind CSS
- 🌙 Dark mode support
- ⚡ Optimized performance
- 🔌 WordPress REST API integration

## Installation

1. Ensure the Pride Plugin is installed in your WordPress installation
2. The Blog Component is automatically included with the plugin
3. Verify your WordPress REST API is properly configured
4. Check your permalink settings (should not be set to "Plain")

## Usage

### Basic Implementation

Add the blog component to any page or post using the shortcode:

```php
[pride_blog]
```

### Advanced Implementation

Use shortcode attributes to customize the display:

```php
[pride_blog 
    posts_per_page="9" 
    layout="grid" 
    category="" 
    order="DESC" 
    orderby="date"
]
```

### Shortcode Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| posts_per_page | number | 9 | Number of posts to display per page |
| layout | string | "grid" | Display layout ("grid" or "list") |
| category | string | "" | Filter by category slug |
| order | string | "DESC" | Sort order ("DESC" or "ASC") |
| orderby | string | "date" | Sort field ("date", "title", etc.) |

## Architecture

The component follows a modular architecture with clear separation of concerns:

### File Structure

```
pride-plugin/
├── components/
│   └── blog/
│       ├── js/
│       │   ├── pride-blog-api.js    # API handling
│       │   ├── pride-blog-ui.js     # UI management
│       │   └── pride-blog.js        # Main initialization
│       ├── css/
│       │   └── pride-blog-tailwind.css
│       └── class-pride-blog.php     # PHP component class
```

### Component Classes

1. **PrideBlogAPI** (pride-blog-api.js)
   - Handles REST API communication
   - Manages data fetching and formatting
   - Provides clean API interfaces

2. **PrideBlogUI** (pride-blog-ui.js)
   - Manages UI interactions
   - Handles rendering and updates
   - Controls user interface states

3. **Pride_Blog** (class-pride-blog.php)
   - WordPress integration
   - Shortcode handling
   - Asset management

## API Reference


### REST API Endpoints

```javascript
// Fetch Posts
GET /wp-json/wp/v2/posts

// Parameters:
{
    page: number,           // Page number
    per_page: number,       // Posts per page
    search: string,         // Search term
    categories: string,     // Category IDs
    orderby: string,        // Sort field
    order: string,         // Sort direction
    _embed: boolean        // Include embedded data
}

// Fetch Categories
GET /wp-json/wp/v2/categories
```

### Custom REST Fields

```javascript
// Featured Media URL
'featured_media_url' {
    get_callback: function(post) {
        // Returns the featured image URL
    }
}

// Author Information
'author_info' {
    get_callback: function(post) {
        // Returns author data including avatar
    }
}

// Categories Information
'categories_info' {
    get_callback: function(post) {
        // Returns formatted category data
    }
}
```

## UI Components

### Search Bar
```html
<div class="pride-blog-search">
    <input type="text" placeholder="Search posts...">
</div>
```

### Sort Dropdown
```html
<div class="pride-blog-sort">
    <select>
        <option value="date-DESC">Newest First</option>
        <option value="date-ASC">Oldest First</option>
        <option value="title-ASC">Title A-Z</option>
        <option value="title-DESC">Title Z-A</option>
    </select>
</div>
```

### Category Filters
```html
<div class="pride-blog-filters">
    <div class="pride-blog-filter-section">
        <h3>Categories</h3>
        <!-- Dynamic category checkboxes -->
    </div>
</div>
```

### Post Grid/List
```html
<div class="pride-blog-grid"> <!-- or pride-blog-list -->
    <article class="pride-blog-post">
        <!-- Post content structure -->
    </article>
</div>
```

## Customization

### Tailwind CSS Classes

The component uses Tailwind CSS classes that can be customized in:
`components/blog/css/pride-blog-tailwind.css`

### Layout Customization

1. **Grid Layout**
```css
.pride-blog-grid {
    @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6;
}
```

2. **List Layout**
```css
.pride-blog-list {
    @apply space-y-8;
}
```

### Theme Integration

To match your theme's colors:

1. Modify the color classes in the Tailwind CSS file
2. Update the dark mode configuration if needed
3. Adjust the container widths and padding

## Troubleshooting

### Common Issues

1. **Posts Not Loading**
   - Check WordPress REST API status
   - Verify permalink settings
   - Check browser console for errors
   - Verify REST API permissions

2. **Styling Issues**
   - Ensure Tailwind CSS is properly loaded
   - Check for CSS conflicts
   - Verify responsive breakpoints

3. **Performance Issues**
   - Optimize image sizes
   - Enable WordPress caching
   - Check server response times

### Debug Mode

Add the `debug` attribute to enable debug mode:
```php
[pride_blog debug="true"]
```

## Support

For additional support:
1. Check the WordPress documentation
2. Review the REST API documentation
3. Contact the plugin maintainer

## Contributing

1. Fork the repository
2. Create a feature branch
3. Submit a pull request

## License

This component is part of the Pride Plugin and is licensed under the GPL v2 or later. 
