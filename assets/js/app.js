(function () {
  /* ── Dark Mode ─────────────────────────────────── */
  var THEME_KEY = 'mn-theme';

  function getTheme() {
    var saved = localStorage.getItem(THEME_KEY);
    if (saved) return saved;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    var icons = document.querySelectorAll('.theme-icon');
    for (var i = 0; i < icons.length; i++) {
      icons[i].className = theme === 'dark'
        ? 'bi bi-sun theme-icon'
        : 'bi bi-moon-stars theme-icon';
    }
  }

  function toggleTheme() {
    var current = document.documentElement.getAttribute('data-theme') || 'light';
    var next = current === 'dark' ? 'light' : 'dark';
    localStorage.setItem(THEME_KEY, next);
    applyTheme(next);
  }

  /* Apply immediately (before first paint) */
  applyTheme(getTheme());

  /* ── Accordion ─────────────────────────────────── */
  window.MN_INIT_ACCORDIONS = function () {
    var btns = document.querySelectorAll('.mn-accordion-btn');
    for (var b = 0; b < btns.length; b++) {
      /* Clone to remove stale listeners */
      (function (btn) {
        var fresh = btn.cloneNode(true);
        btn.parentNode.replaceChild(fresh, btn);
        fresh.addEventListener('click', function () {
          var item = fresh.closest('.mn-accordion-item');
          var body = item.querySelector('.mn-accordion-body');
          var isOpen = item.classList.contains('open');
          /* Close all */
          document.querySelectorAll('.mn-accordion-item').forEach(function (el) {
            el.classList.remove('open');
            el.querySelector('.mn-accordion-body').classList.remove('open');
          });
          /* Toggle current */
          if (!isOpen) {
            item.classList.add('open');
            body.classList.add('open');
          }
        });
      })(btns[b]);
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    /* Re-apply after DOM ready (icons now exist) */
    applyTheme(getTheme());

    /* Theme toggles */
    document.addEventListener('click', function (e) {
      if (e.target.closest('.btn-theme-toggle')) toggleTheme();
    });

    /* ── Scroll Progress ──────────────────────────── */
    var prog = document.getElementById('scroll-progress');
    if (prog) {
      window.addEventListener('scroll', function () {
        var total = document.documentElement.scrollHeight - window.innerHeight;
        prog.style.width = (total > 0 ? (window.scrollY / total) * 100 : 0) + '%';
      }, { passive: true });
    }

    /* ── Navbar shrink ───────────────────────────── */
    var nav = document.getElementById('main-nav');
    if (nav) {
      window.addEventListener('scroll', function () {
        nav.classList.toggle('scrolled', window.scrollY > 40);
      }, { passive: true });
    }

    /* ── Scroll Reveal ───────────────────────────── */
    var reveals = document.querySelectorAll('.reveal');
    if (reveals.length) {
      if ('IntersectionObserver' in window) {
        var obs = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('visible');
              obs.unobserve(entry.target);
            }
          });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        reveals.forEach(function (el) { obs.observe(el); });
      } else {
        reveals.forEach(function (el) { el.classList.add('visible'); });
      }
    }

    /* ── Counter animation ───────────────────────── */
    var counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
      var cobs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-count'), 10);
          var suffix = el.getAttribute('data-suffix') || '';
          var dur = 1600;
          var start = performance.now();
          function tick(now) {
            var p = Math.min((now - start) / dur, 1);
            el.textContent = Math.round((1 - Math.pow(1 - p, 3)) * target) + suffix;
            if (p < 1) requestAnimationFrame(tick);
          }
          requestAnimationFrame(tick);
          cobs.unobserve(el);
        });
      }, { threshold: 0.5 });
      counters.forEach(function (el) { cobs.observe(el); });
    }

    /* ── User dropdown ──────────────────────────── */
    var userBtn      = document.getElementById('mn-user-btn');
    var userDropdown = document.getElementById('mn-user-dropdown');
    if (userBtn && userDropdown) {
      userBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = userDropdown.classList.toggle('open');
        userBtn.setAttribute('aria-expanded', String(isOpen));
      });
      document.addEventListener('click', function () {
        userDropdown.classList.remove('open');
        userBtn.setAttribute('aria-expanded', 'false');
      });
    }

    /* ── Accordions ──────────────────────────────── */
    window.MN_INIT_ACCORDIONS();

    /* ── Page fade in ────────────────────────────── */
    document.body.classList.add('page-fade');

    /* ── PWA service worker ───────────────────────── */
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/maison-nord/sw.js').catch(function () {});
    }
  });
})();
