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

    document.querySelectorAll('[data-hero-swiper]').forEach((el) => {
        const swiper = new Swiper(el, {
            modules: [Autoplay, EffectFade, Navigation, Pagination],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            loop: true,
            speed: 1000,
            autoplay: {
                delay: 7000,
                disableOnInteraction: false,
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev'),
            },
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

    const header = document.getElementById('rg-header');
    if (header) {
        const onScroll = () => {
            header.classList.toggle('rg-header--sticky', window.scrollY > 80);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
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
