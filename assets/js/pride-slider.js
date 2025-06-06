jQuery(document).ready(function($) {
    class PrideSlider {
        constructor(element) {
            this.slider = element;
            this.slides = this.slider.find('.pride-slide');
            this.currentSlide = 0;
            this.slideCount = this.slides.length;
            this.speed = parseInt(this.slider.data('speed')) || 3000;
            this.showArrows = this.slider.data('arrows') !== 'false';
            this.showDots = this.slider.data('dots') !== 'false';
            this.interval = null;
            this.isHovered = false;

            this.init();
        }

        init() {
            // Add loading state
            this.slider.addClass('pride-slider-loading');

            // Wait for images to load
            Promise.all(
                this.slides.find('img').map((i, img) => {
                    return new Promise((resolve) => {
                        if (img.complete) {
                            resolve();
                        } else {
                            $(img).on('load', resolve);
                        }
                    });
                })
            ).then(() => {
                this.setupSlider();
            });
        }

        setupSlider() {
            // Remove loading state
            this.slider.removeClass('pride-slider-loading');

            // Add navigation if needed
            if (this.showArrows) {
                this.addArrows();
            }

            if (this.showDots) {
                this.addDots();
            }

            // Show first slide
            this.slides.first().addClass('active');
            
            // Add hover events
            this.slider.on('mouseenter', () => {
                this.isHovered = true;
                this.pauseSlider();
            });

            this.slider.on('mouseleave', () => {
                this.isHovered = false;
                this.startSlider();
            });

            // Start autoplay
            this.startSlider();
        }

        addArrows() {
            const nav = $('<div class="pride-slider-nav"></div>');
            const prevBtn = $('<button class="pride-slider-prev">❮</button>');
            const nextBtn = $('<button class="pride-slider-next">❯</button>');

            nav.append(prevBtn, nextBtn);
            this.slider.append(nav);

            prevBtn.on('click', () => this.prevSlide());
            nextBtn.on('click', () => this.nextSlide());
        }

        addDots() {
            const dots = $('<div class="pride-slider-dots"></div>');
            
            for (let i = 0; i < this.slideCount; i++) {
                const dot = $('<button class="pride-slider-dot"></button>');
                if (i === 0) dot.addClass('active');
                dot.on('click', () => this.goToSlide(i));
                dots.append(dot);
            }

            this.slider.append(dots);
        }

        startSlider() {
            if (!this.isHovered && this.slideCount > 1) {
                this.interval = setInterval(() => this.nextSlide(), this.speed);
            }
        }

        pauseSlider() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        }

        nextSlide() {
            this.goToSlide((this.currentSlide + 1) % this.slideCount);
        }

        prevSlide() {
            this.goToSlide((this.currentSlide - 1 + this.slideCount) % this.slideCount);
        }

        goToSlide(index) {
            if (index === this.currentSlide) return;

            // Update slides
            this.slides.removeClass('active');
            this.slides.eq(index).addClass('active');

            // Update dots
            if (this.showDots) {
                this.slider.find('.pride-slider-dot').removeClass('active');
                this.slider.find('.pride-slider-dot').eq(index).addClass('active');
            }

            this.currentSlide = index;
        }
    }

    // Initialize all sliders on the page
    $('.pride-slider').each(function() {
        new PrideSlider($(this));
    });
}); 