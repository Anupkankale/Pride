jQuery(document).ready(function($) {
    // Tab Navigation
    $('.pride-admin-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        
        const $this = $(this);
        const target = $this.attr('href');
        
        // Update tabs
        $('.pride-admin-tabs .nav-tab').removeClass('nav-tab-active');
        $this.addClass('nav-tab-active');
        
        // Update content
        $('.pride-admin-tabs .tab-pane').removeClass('active');
        $(target).addClass('active');
    });

    // Settings Form Validation
    $('.pride-settings-form').on('submit', function(e) {
        const $form = $(this);
        const $speed = $form.find('input[name="slider_speed"]');
        const speed = parseInt($speed.val());

        if (speed < 1000) {
            e.preventDefault();
            alert('Slider speed must be at least 1000ms (1 second)');
            $speed.focus();
        }
    });

    // Media Upload Button
    $('.pride-media-upload').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const $input = $button.siblings('input[type="hidden"]');
        const $preview = $button.siblings('.pride-media-preview');
        
        const frame = wp.media({
            title: 'Select or Upload Media',
            button: {
                text: 'Use this media'
            },
            multiple: false
        });

        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.id);
            
            if (attachment.type === 'image') {
                $preview.html(`<img src="${attachment.url}" alt="Preview" style="max-width: 150px;">`);
            } else {
                $preview.html(`<div class="pride-media-preview-placeholder">${attachment.filename}</div>`);
            }
        });

        frame.open();
    });

    // Color Picker
    if ($.fn.wpColorPicker) {
        $('.pride-color-picker').wpColorPicker();
    }

    // Preview Generator
    function updatePreview() {
        const speed = $('#slider_speed').val();
        const arrows = $('#slider_arrows').is(':checked');
        const dots = $('#slider_dots').is(':checked');
        
        const shortcode = `[pride_slider images="1,2,3" speed="${speed}" arrows="${arrows}" dots="${dots}"]`;
        $('#preview_shortcode').text(shortcode);
    }

    // Update preview on change
    $('.pride-settings-form input, .pride-settings-form select').on('change', updatePreview);
}); 