jQuery(document).ready(function($) {
    // Handle form submission
    $('#pride-blog-settings').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'pride_update_settings');
        formData.append('nonce', prideBlogAdmin.nonce);

        $.ajax({
            url: prideBlogAdmin.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const message = $('<div class="notice notice-success is-dismissible"><p>' + response.data + '</p></div>');
                    $('.pride-admin-container').prepend(message);
                    
                    // Update shortcode preview
                    updateShortcodePreview();
                    
                    // Remove message after 3 seconds
                    setTimeout(function() {
                        message.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                } else {
                    // Show error message
                    const message = $('<div class="notice notice-error is-dismissible"><p>' + response.data + '</p></div>');
                    $('.pride-admin-container').prepend(message);
                }
            },
            error: function() {
                // Show error message
                const message = $('<div class="notice notice-error is-dismissible"><p>An error occurred while saving settings.</p></div>');
                $('.pride-admin-container').prepend(message);
            }
        });
    });

    // Handle auto refresh toggle
    $('input[name="auto_refresh"]').on('change', function() {
        $('#refresh-interval-field').toggle($(this).is(':checked'));
    });

    // Initialize auto refresh field visibility
    $('#refresh-interval-field').toggle($('input[name="auto_refresh"]').is(':checked'));

    // Update shortcode preview
    function updateShortcodePreview() {
        const settings = {
            posts_per_page: $('#posts_per_page').val(),
            order: $('#order').val(),
            orderby: $('#orderby').val()
        };

        // Get selected categories
        const selectedCategories = [];
        $('input[name="selected_categories[]"]:checked').each(function() {
            selectedCategories.push($(this).val());
        });

        if (selectedCategories.length > 0) {
            settings.category = selectedCategories.join(',');
        }

        // Build shortcode
        let shortcode = '[pride_blog_slider';
        for (const [key, value] of Object.entries(settings)) {
            if (value) {
                shortcode += ` ${key}="${value}"`;
            }
        }
        shortcode += ']';

        $('#pride-shortcode-display').text(shortcode);
    }

    // Update shortcode preview on form changes
    $('.pride-settings-form').on('change', 'input, select', updateShortcodePreview);

    // Copy shortcode to clipboard
    $('#copy-shortcode').on('click', function() {
        const shortcode = $('#pride-shortcode-display').text();
        navigator.clipboard.writeText(shortcode).then(function() {
            const button = $('#copy-shortcode');
            const originalText = button.text();
            button.text('Copied!');
            setTimeout(function() {
                button.text(originalText);
            }, 2000);
        });
    });

    // Initialize shortcode preview
    updateShortcodePreview();
}); 