# Pride Blog Component - Quick Start Guide

## 🚀 Quick Start

### 1. Basic Implementation
Add to any page/post:
```php
[pride_blog]
```

### 2. Custom Implementation
```php
[pride_blog 
    posts_per_page="6" 
    layout="grid" 
    category="news" 
    order="DESC" 
    orderby="date"
]
```

## 🔧 Common Customizations

### 1. Change Layout
```php
// Grid Layout (default)
[pride_blog layout="grid"]

// List Layout
[pride_blog layout="list"]
```

### 2. Filter by Category
```php
// Single category
[pride_blog category="news"]

// Multiple categories (comma-separated)
[pride_blog category="news,events,updates"]
```

### 3. Change Posts Per Page
```php
[pride_blog posts_per_page="12"]
```

## 🎨 Styling Quick Tips

### 1. Grid Columns
```css
/* Change number of columns */
.pride-blog-grid {
    @apply grid-cols-2;  /* 2 columns */
    @apply md:grid-cols-3;  /* 3 columns on medium screens */
    @apply lg:grid-cols-4;  /* 4 columns on large screens */
}
```

### 2. Colors
```css
/* Change primary color */
.pride-blog-title a {
    @apply text-blue-600;  /* Change title color */
    @apply hover:text-blue-800;  /* Change hover color */
}

/* Change category tag colors */
.pride-blog-category {
    @apply bg-green-100;  /* Change background */
    @apply text-green-600;  /* Change text color */
}
```

### 3. Spacing
```css
/* Adjust card spacing */
.pride-blog-post {
    @apply p-6;  /* Change padding */
    @apply mb-8;  /* Change bottom margin */
}
```

## 🔍 Debug Tips

### 1. Check REST API
Visit these URLs in your browser:
```
/wp-json/wp/v2/posts
/wp-json/wp/v2/categories
```

### 2. Console Commands
```javascript
// Test API connection
const api = new PrideBlogAPI();
api.fetchPosts().then(console.log);

// Test UI rendering
const ui = new PrideBlogUI($('.pride-blog-container'), api);
ui.loadPosts();
```

### 3. Common Issues

#### Posts Not Loading
```javascript
// Check REST API response
fetch('/wp-json/wp/v2/posts')
    .then(response => response.json())
    .then(console.log)
    .catch(console.error);
```

#### Styling Issues
```javascript
// Check if Tailwind is loaded
console.log(window.tailwind);

// Check container settings
const container = $('.pride-blog-container');
console.log(container.data());
```

## 📦 Required Dependencies

```json
{
    "jquery": "^3.6.0",
    "tailwindcss": "^3.3.3"
}
```

## 🔗 Useful Links

- [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [jQuery Documentation](https://api.jquery.com/)

## 💡 Tips & Best Practices

1. Always use the `_embed` parameter when fetching posts for complete data
2. Implement error handling for API calls
3. Use debouncing for search functionality
4. Cache API responses when possible
5. Optimize images before display

## 🆘 Need Help?

Check the full documentation in `BLOG-COMPONENT.md` for detailed information. 
