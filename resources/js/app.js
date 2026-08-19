import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import gsap from 'gsap';
import Swiper from 'swiper';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
window.gsap = gsap;
window.Swiper = Swiper;
window.SwiperModules = { Autoplay, EffectFade, Navigation, Pagination };

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 900,
        easing: 'ease-out-cubic',
        once: true,
        offset: 80,
        disable: false,
    });

    document.querySelectorAll('iframe[data-visit-map]').forEach((el) => {
        const src = el.getAttribute('data-src');
        if (! src) {
            return;
        }

        const loadMap = () => {
            if (el.getAttribute('src') === src) {
                return;
            }
            el.setAttribute('src', src);
        };

        if (! ('IntersectionObserver' in window)) {
            loadMap();
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                loadMap();
                observer.disconnect();
            }
        }, { rootMargin: '240px 0px' });

        observer.observe(el);
    });

    document.querySelectorAll('[data-hero-swiper]').forEach((el) => {
        const slideCount = Number(el.dataset.slideCount || el.querySelectorAll('.swiper-slide').length || 0);
        const multi = slideCount > 1;
        const nextEl = el.querySelector('.swiper-button-next');
        const prevEl = el.querySelector('.swiper-button-prev');
        const paginationEl = el.querySelector('.swiper-pagination');

        const swiper = new Swiper(el, {
            modules: [Autoplay, EffectFade, Navigation, Pagination],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            loop: multi,
            allowTouchMove: multi,
            speed: 1000,
            autoplay: multi
                ? {
                    delay: 7000,
                    disableOnInteraction: false,
                }
                : false,
            pagination: multi && paginationEl
                ? {
                    el: paginationEl,
                    clickable: true,
                }
                : undefined,
            navigation: multi && nextEl && prevEl
                ? {
                    nextEl,
                    prevEl,
                }
                : undefined,
            on: {
                init(instance) {
                    animateHeroSlide(instance.slides[instance.activeIndex]);
                },
                slideChangeTransitionStart(instance) {
                    animateHeroSlide(instance.slides[instance.activeIndex]);
                },
            },
        });

        void swiper;
    });

    document.querySelectorAll('[data-events-swiper]').forEach((el) => {
        const section = el.closest('.rg-events-section');

        new Swiper(el, {
            modules: [Autoplay, Navigation],
            slidesPerView: 1,
            spaceBetween: 28,
            loop: true,
            speed: 500,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: section?.querySelector('.events-next'),
                prevEl: section?.querySelector('.events-prev'),
            },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 30 },
                1024: { slidesPerView: 4, spaceBetween: 35 },
            },
        });
    });

    document.querySelectorAll('[data-ig-swiper]').forEach((el) => {
        new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 2,
            spaceBetween: 12,
            speed: 500,
            navigation: {
                nextEl: el.querySelector('.ig-next'),
                prevEl: el.querySelector('.ig-prev'),
            },
            breakpoints: {
                768: { slidesPerView: 3, spaceBetween: 12 },
                1024: { slidesPerView: 4, spaceBetween: 12 },
            },
        });
    });

    document.querySelectorAll('[data-logos-swiper]').forEach((el) => {
        new Swiper(el, {
            modules: [Autoplay],
            slidesPerView: 2.2,
            spaceBetween: 24,
            loop: true,
            speed: 500,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            breakpoints: {
                640: { slidesPerView: 4, spaceBetween: 28 },
                1024: { slidesPerView: 6, spaceBetween: 30 },
                1280: { slidesPerView: 8, spaceBetween: 30 },
            },
        });
    });

    document.querySelectorAll('[data-similar-carousel]').forEach((wrap) => {
        const el = wrap.querySelector('[data-similar-swiper]');
        const prevEl = wrap.querySelector('.rg-similar-nav--prev');
        const nextEl = wrap.querySelector('.rg-similar-nav--next');
        if (!el) {
            return;
        }

        const swiper = new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 1.15,
            spaceBetween: 16,
            speed: 450,
            navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                960: { slidesPerView: 3, spaceBetween: 24 },
                1200: { slidesPerView: 4, spaceBetween: 28 },
            },
            on: {
                init(instance) {
                    toggleSimilarNav(instance, wrap);
                },
                resize(instance) {
                    toggleSimilarNav(instance, wrap);
                },
            },
        });

        void swiper;
    });

    document.querySelectorAll('[data-about-gallery]').forEach((wrap) => {
        const el = wrap.querySelector('[data-about-gallery-swiper]');
        const prevEl = wrap.querySelector('.rg-about-gallery-nav--prev');
        const nextEl = wrap.querySelector('.rg-about-gallery-nav--next');
        if (!el) {
            return;
        }

        const swiper = new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 1.08,
            spaceBetween: 12,
            speed: 450,
            navigation: prevEl && nextEl ? { prevEl, nextEl } : undefined,
            breakpoints: {
                480: { slidesPerView: 1.35, spaceBetween: 14 },
                640: { slidesPerView: 2, spaceBetween: 16 },
                960: { slidesPerView: 3, spaceBetween: 20 },
                1280: { slidesPerView: 4, spaceBetween: 24 },
            },
            on: {
                init(instance) {
                    toggleSimilarNav(instance, wrap, 'rg-about-gallery--nav-hidden');
                },
                resize(instance) {
                    toggleSimilarNav(instance, wrap, 'rg-about-gallery--nav-hidden');
                },
            },
        });

        void swiper;
    });

    const header = document.getElementById('rg-header');
    if (header) {
        const heroSelectors = [
            '#top',
            'main .rg-contact-hero',
            'main .rg-visit-hero',
            'main .rg-rest-hero',
            'main .rg-rest-detail-hero',
            'main .rg-events-hero',
            'main .rg-static-hero',
            'main .rg-mirror-hero',
            'main .rg-blog-page-hero',
            'main .rg-blog-detail-hero',
        ].join(', ');

        let stickyThreshold = 80;

        const updateStickyThreshold = () => {
            const hero = document.querySelector(heroSelectors);
            stickyThreshold = hero ? Math.max(0, Math.round(hero.offsetHeight - 2)) : 80;
        };

        const onScroll = () => {
            header.classList.toggle('rg-header--sticky', window.scrollY >= stickyThreshold);
        };

        updateStickyThreshold();
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', () => {
            updateStickyThreshold();
            onScroll();
        }, { passive: true });
    }

    const topBtn = document.getElementById('scroll-top');
    if (topBtn) {
        window.addEventListener('scroll', () => {
            const show = window.scrollY >= 400;
            topBtn.classList.toggle('hidden', !show);
            topBtn.classList.toggle('flex', show);
        }, { passive: true });
    }
});

function animateHeroSlide(slide) {
    if (!slide) {
        return;
    }

    const title = slide.querySelector('[data-hero-title]');
    const subtitle = slide.querySelector('[data-hero-subtitle]');
    const desc = slide.querySelector('[data-hero-desc]');
    const cta = slide.querySelector('[data-hero-cta]');

    const timeline = gsap.timeline({ defaults: { ease: 'power3.out' } });

    if (title) {
        timeline.fromTo(title, { y: 48, opacity: 0 }, { y: 0, opacity: 1, duration: 1 }, 0.15);
    }
    if (subtitle) {
        timeline.fromTo(subtitle, { y: 28, opacity: 0 }, { y: 0, opacity: 1, duration: 0.8 }, 0.4);
    }
    if (desc) {
        timeline.fromTo(desc, { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.7 }, 0.55);
    }
    if (cta) {
        timeline.fromTo(cta, { y: 16, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6 }, 0.7);
    }
}

function toggleSimilarNav(swiper, wrap, hiddenClass = 'rg-similar-carousel--nav-hidden') {
    const needsNav = swiper.slides.length > swiper.params.slidesPerView;
    wrap.classList.toggle(hiddenClass, !needsNav);
}
