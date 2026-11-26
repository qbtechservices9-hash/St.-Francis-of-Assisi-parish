import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const isAnimationEnabled = () => {
  if (prefersReducedMotion) {
    return false;
  }

  return document.body?.dataset?.animate !== 'false';
};

const initReveal = () => {
  if (!isAnimationEnabled()) {
    return;
  }

  const sections = document.querySelectorAll('main section');
  if (!sections.length) {
    return;
  }

  sections.forEach((section) => section.classList.add('reveal-3d'));

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );

  sections.forEach((section) => observer.observe(section));
};

const initHeaderOffset = () => {
  const header = document.querySelector('header');
  if (!header) {
    return;
  }

  const applyHeaderOffset = () => {
    const height = Math.ceil(header.getBoundingClientRect().height);
    document.documentElement.style.setProperty('--site-header-offset', `${height}px`);
  };

  applyHeaderOffset();
  window.addEventListener('resize', applyHeaderOffset);
  window.setTimeout(applyHeaderOffset, 120);
};

const initMobileMenu = () => {
  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-mobile-menu]');

  if (!toggle || !menu) {
    return;
  }

  const setState = (open) => {
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
    menu.classList.toggle('hidden', !open);
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    toggle.classList.toggle('is-open', open);

    if (open) {
      const firstLink = menu.querySelector('a');
      if (firstLink) {
        firstLink.focus();
      }
    } else if (document.activeElement && menu.contains(document.activeElement)) {
      toggle.focus();
    }
  };

  setState(false);

  toggle.addEventListener('click', () => {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    setState(!isOpen);
  });

  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setState(false));
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      setState(false);
    }
  });

  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      setState(false);
    }

    if (event.key !== 'Tab' || toggle.getAttribute('aria-expanded') !== 'true') {
      return;
    }

    const focusable = [
      toggle,
      ...menu.querySelectorAll('a, button, [tabindex]:not([tabindex="-1"])'),
    ].filter(Boolean);

    if (!focusable.length) {
      return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });
};

const initHeroTilt = () => {
  if (!isAnimationEnabled()) {
    return;
  }

  const hero = document.querySelector('.hero-3d');
  if (!hero) {
    return;
  }

  const layers = hero.querySelectorAll('[data-depth]');
  if (!layers.length) {
    return;
  }

  const handleMove = (event) => {
    const rect = hero.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width - 0.5;
    const y = (event.clientY - rect.top) / rect.height - 0.5;

    layers.forEach((layer) => {
      const depth = parseFloat(layer.dataset.depth || '0.02');
      const translateX = x * depth * 60;
      const translateY = y * depth * 60;
      layer.style.transform = `translate3d(${translateX}px, ${translateY}px, 0)`;
    });
  };

  const reset = () => {
    layers.forEach((layer) => {
      layer.style.transform = 'translate3d(0, 0, 0)';
    });
  };

  hero.addEventListener('mousemove', handleMove);
  hero.addEventListener('mouseleave', reset);
};

const initHeroSlider = () => {
  const slider = document.querySelector('[data-hero-slider]');
  if (!slider) {
    return;
  }

  const backgrounds = Array.from(slider.querySelectorAll('[data-hero-slide-bg]'));
  const panels = Array.from(slider.querySelectorAll('[data-hero-slide-panel]'));
  const dots = Array.from(slider.querySelectorAll('[data-hero-dot]'));
  const prev = slider.querySelector('[data-hero-prev]');
  const next = slider.querySelector('[data-hero-next]');

  if (backgrounds.length <= 1 || backgrounds.length !== panels.length) {
    return;
  }

  let activeIndex = 0;
  let autoplayId = null;
  const autoplayEnabled = slider.dataset.autoplay === 'true' && !prefersReducedMotion;

  const setActive = (index) => {
    activeIndex = (index + panels.length) % panels.length;

    backgrounds.forEach((background, itemIndex) => {
      const isActive = itemIndex === activeIndex;
      background.classList.toggle('is-active', isActive);
      background.setAttribute('aria-hidden', isActive ? 'false' : 'true');
    });

    panels.forEach((panel, itemIndex) => {
      const isActive = itemIndex === activeIndex;
      panel.classList.toggle('is-active', isActive);
      panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
    });

    dots.forEach((dot, itemIndex) => {
      const isActive = itemIndex === activeIndex;
      dot.classList.toggle('is-active', isActive);
      dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
  };

  const stopAutoplay = () => {
    if (autoplayId) {
      window.clearInterval(autoplayId);
      autoplayId = null;
    }
  };

  const startAutoplay = () => {
    if (!autoplayEnabled) {
      return;
    }

    stopAutoplay();
    autoplayId = window.setInterval(() => {
      setActive(activeIndex + 1);
    }, 6000);
  };

  prev?.addEventListener('click', () => {
    setActive(activeIndex - 1);
    startAutoplay();
  });

  next?.addEventListener('click', () => {
    setActive(activeIndex + 1);
    startAutoplay();
  });

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      setActive(index);
      startAutoplay();
    });
  });

  slider.addEventListener('mouseenter', stopAutoplay);
  slider.addEventListener('mouseleave', startAutoplay);
  slider.addEventListener('focusin', stopAutoplay);
  slider.addEventListener('focusout', () => {
    if (!slider.contains(document.activeElement)) {
      startAutoplay();
    }
  });

  slider.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') {
      setActive(activeIndex - 1);
      startAutoplay();
    }

    if (event.key === 'ArrowRight') {
      setActive(activeIndex + 1);
      startAutoplay();
    }
  });

  setActive(0);
  startAutoplay();
};

const initLightbox = () => {
  const lightbox = document.querySelector('[data-lightbox]');
  const content = lightbox?.querySelector('[data-lightbox-content]');

  if (!lightbox || !content) {
    return;
  }

  lightbox.setAttribute('aria-hidden', 'true');
  let lastTrigger = null;

  const trapFocus = (event) => {
    if (event.key !== 'Tab' || !lightbox.classList.contains('is-open')) {
      return;
    }

    const focusable = lightbox.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );

    if (!focusable.length) {
      return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
      return;
    }

    if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  };

  const close = () => {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    content.innerHTML = '';
    document.body.classList.remove('overflow-hidden');
    if (lastTrigger && typeof lastTrigger.focus === 'function') {
      lastTrigger.focus();
    }
  };

  const open = (payload) => {
    if (!payload) {
      return;
    }

    const { type, url, embed, playable, title } = payload;
    content.innerHTML = '';

    if (type === 'image') {
      const img = document.createElement('img');
      img.src = url;
      img.alt = title || 'Preview';
      img.className = 'w-full h-full object-contain bg-black';
      content.appendChild(img);
    } else if (type === 'video') {
      if (embed) {
        const iframe = document.createElement('iframe');
        iframe.src = embed;
        iframe.title = title || 'Video preview';
        iframe.className = 'w-full h-full';
        iframe.allow =
          'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
        iframe.allowFullscreen = true;
        content.appendChild(iframe);
      } else if (playable === '1') {
        const video = document.createElement('video');
        video.className = 'w-full h-full';
        video.controls = true;
        const source = document.createElement('source');
        source.src = url;
        video.appendChild(source);
        content.appendChild(video);
      } else {
        const link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener';
        link.className =
          'inline-flex items-center justify-center w-full h-full text-white text-lg underline';
        link.textContent = 'Open video in a new tab';
        content.appendChild(link);
      }
    }

    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
    const closeButton = lightbox.querySelector('[data-lightbox-close]');
    if (closeButton) {
      closeButton.focus();
    }
  };

  document.querySelectorAll('[data-preview]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      lastTrigger = trigger;
      open({
        type: trigger.dataset.type,
        url: trigger.dataset.url,
        embed: trigger.dataset.embed,
        playable: trigger.dataset.playable,
        title: trigger.dataset.title,
      });
    });
  });

  lightbox.addEventListener('click', (event) => {
    if (event.target.matches('[data-lightbox-close]')) {
      close();
    }
  });

  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      close();
    }
  });

  window.addEventListener('keydown', trapFocus);
};

const initPreloader = () => {
  const preloader = document.querySelector('[data-preloader]');
  if (!preloader) {
    return;
  }

  const hide = () => {
    preloader.classList.add('is-hidden');
    window.setTimeout(() => {
      if (preloader.parentNode) {
        preloader.parentNode.removeChild(preloader);
      }
    }, 500);
  };

  window.addEventListener('load', hide);
  window.setTimeout(hide, 3500);
};

const initCountdowns = () => {
  const timers = Array.from(document.querySelectorAll('[data-countdown]'));
  if (!timers.length) {
    return;
  }

  const pad = (value) => String(value).padStart(2, '0');

  const updateTimer = (timer) => {
    const targetValue = timer.dataset.countdown;
    const targetDate = targetValue ? new Date(targetValue) : null;

    if (!targetDate || Number.isNaN(targetDate.getTime())) {
      return;
    }

    const now = new Date();
    let diff = Math.floor((targetDate.getTime() - now.getTime()) / 1000);

    const status = timer.closest('.card')?.querySelector('[data-countdown-status]');

    if (diff <= 0) {
      diff = 0;
      if (status) {
        status.textContent = 'Happening now';
      }
    } else if (status) {
      status.textContent = 'Starts in';
    }

    const days = Math.floor(diff / 86400);
    const hours = Math.floor((diff % 86400) / 3600);
    const minutes = Math.floor((diff % 3600) / 60);
    const seconds = diff % 60;

    const daysEl = timer.querySelector('[data-countdown-days]');
    const hoursEl = timer.querySelector('[data-countdown-hours]');
    const minutesEl = timer.querySelector('[data-countdown-minutes]');
    const secondsEl = timer.querySelector('[data-countdown-seconds]');

    if (daysEl) daysEl.textContent = pad(days);
    if (hoursEl) hoursEl.textContent = pad(hours);
    if (minutesEl) minutesEl.textContent = pad(minutes);
    if (secondsEl) secondsEl.textContent = pad(seconds);
  };

  const updateAll = () => {
    timers.forEach(updateTimer);
  };

  updateAll();
  window.setInterval(updateAll, 1000);
};

const initDirections = () => {
  const trigger = document.querySelector('[data-directions]');
  if (!trigger) {
    return;
  }

  const destination = trigger.dataset.destination;
  if (!destination) {
    return;
  }

  const openDirections = (origin) => {
    const base = 'https://www.google.com/maps/dir/?api=1';
    const destParam = `&destination=${encodeURIComponent(destination)}`;
    const originParam = origin ? `&origin=${encodeURIComponent(origin)}` : '';
    const url = `${base}${originParam}${destParam}`;
    if (origin) {
      window.location.assign(url);
    } else {
      window.open(url, '_blank', 'noopener');
    }
  };

  trigger.addEventListener('click', () => {
    const popup = window.open('', '_blank');

    if (!navigator.geolocation) {
      if (popup) {
        popup.location.href = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(destination)}`;
      } else {
        openDirections();
      }
      return;
    }

    trigger.disabled = true;
    trigger.textContent = 'Locating...';
    trigger.setAttribute('aria-busy', 'true');

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const origin = `${position.coords.latitude},${position.coords.longitude}`;
        trigger.disabled = false;
        trigger.textContent = 'Use My Location';
        trigger.setAttribute('aria-busy', 'false');
        const url = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destination)}`;
        if (popup) {
          popup.location.href = url;
        } else {
          window.location.assign(url);
        }
      },
      () => {
        trigger.disabled = false;
        trigger.textContent = 'Use My Location';
        trigger.setAttribute('aria-busy', 'false');
        const url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(destination)}`;
        if (popup) {
          popup.location.href = url;
        } else {
          window.location.assign(url);
        }
      },
      { timeout: 8000 }
    );
  });
};

document.addEventListener('DOMContentLoaded', () => {
  initHeaderOffset();
  initMobileMenu();
  initReveal();
  initHeroSlider();
  initHeroTilt();
  initLightbox();
  initPreloader();
  initCountdowns();
  initDirections();
});


