# Pride UI Components WordPress Plugin

A beautiful and modern WordPress plugin that provides a responsive image slider and UI components for your website.

## Features

- Responsive image slider
- Customizable transition speed
- Navigation arrows and dots
- Touch-friendly for mobile devices
- Pause on hover
- Loading state indicator
- Modern and clean design
- Fully responsive

## Installation

1. Download the plugin zip file
2. Go to WordPress admin panel > Plugins > Add New
3. Click "Upload Plugin" and choose the downloaded zip file
4. Click "Install Now" and then "Activate"

## Usage

### Basic Slider

Add a slider to any post or page using the shortcode:

```
[pride_slider images="1,2,3"]
```

Replace `1,2,3` with the WordPress media library IDs of your images.

### Advanced Options

The slider supports several customization options:

```
[pride_slider 
    images="1,2,3" 
    speed="3000" 
    arrows="true" 
    dots="true"
]
```

#### Parameters

- `images`: Comma-separated list of image IDs from the WordPress media library (required)
- `speed`: Transition speed in milliseconds (default: 3000)
- `arrows`: Show navigation arrows (default: true)
- `dots`: Show navigation dots (default: true)

## Styling

The plugin comes with a modern, clean design out of the box. You can customize the appearance by adding custom CSS to your theme.

### Example Custom CSS

```css
.pride-slider {
    /* Custom max-width */
    max-width: 800px;
}

.pride-slider-dot {
    /* Custom dot color */
    background: rgba(0, 0, 0, 0.5);
}

.pride-slider-dot.active {
    /* Custom active dot color */
    background: rgba(0, 0, 0, 1);
}
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- Modern web browser with JavaScript enabled

## Support

For support, feature requests, or bug reports, please visit our [GitHub repository](https://github.com/your-username/pride-plugin) or contact us through our website.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Created by [Your Name] 