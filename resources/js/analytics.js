function trackRgEvent(name, params = {}) {
    const payload = {
        event: name,
        page_location: window.location.href,
        page_path: window.location.pathname,
        page_name: document.body?.dataset?.page || 'other',
        ...params,
    };

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(payload);

    if (typeof window.gtag === 'function') {
        window.gtag('event', name, payload);
    }
}

function inferEventFromLink(link) {
    const explicit = link.getAttribute('data-rg-track');
    if (explicit) {
        return explicit;
    }

    const href = (link.getAttribute('href') || '').toLowerCase();
    if (href.includes('wa.me') || href.includes('whatsapp') || href.includes('wa.link')) {
        return 'click_whatsapp';
    }
    if (href.startsWith('tel:')) {
        return 'click_phone';
    }
    if (href.startsWith('mailto:')) {
        return 'click_email';
    }
    if (link.closest('.rg-logo-carousel')) {
        return 'click_restaurant';
    }
    if (link.closest('.rg-contact-block')) {
        return 'click_contact';
    }
    if (link.closest('.rg-fixed-social') || link.classList.contains('rg-menu-social') || link.classList.contains('rg-footer-social-icon')) {
        return 'click_social';
    }
    if (link.classList.contains('rg-header-reserva') || link.classList.contains('btn-reserva-ghost') || link.classList.contains('btn-reserva-menu')) {
        return 'click_reserva';
    }
    if (link.classList.contains('btn-provoc') || link.classList.contains('btn-provoc-menu')) {
        return 'click_provoca';
    }
    if (link.classList.contains('rg-nav-link') || link.classList.contains('rg-sticky-nav-link') || link.classList.contains('rg-menu-link')) {
        return 'click_nav';
    }
    if (link.classList.contains('rg-service-whatsapp')) {
        return 'click_whatsapp';
    }

    return null;
}

function trackableLabel(link, eventName) {
    return link.getAttribute('data-rg-label')
        || link.getAttribute('aria-label')
        || link.textContent.replace(/\s+/g, ' ').trim()
        || eventName;
}

document.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target : null;
    if (! target) {
        return;
    }

    const link = target.closest('[data-rg-track], a, button');
    if (! (link instanceof HTMLElement)) {
        return;
    }

    const eventName = inferEventFromLink(link);
    if (! eventName) {
        return;
    }

    const isHome = document.body?.dataset?.page === 'home';
    if (! isHome && ! link.hasAttribute('data-rg-track')) {
        return;
    }

    trackRgEvent(eventName, {
        event_category: isHome ? 'home' : 'site',
        event_label: trackableLabel(link, eventName),
        link_url: link.getAttribute('href') || '',
        link_text: trackableLabel(link, eventName),
    });
});
