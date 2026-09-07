/**
 * Sikkim Manipal landing page.
 *
 * Loaded only on page-templates/template-sikkim-manipal.php.
 *
 * Deliberately does NOT ship a chatbot, a WhatsApp bubble, a mobile bar or a
 * lead modal. The theme already renders all four from footer.php and
 * mbag_render_popup(), and a second set would double up on every page.
 * Lead capture here goes through Contact Form 7 and the theme popup, which
 * main.js already wires up via [data-mbag-popup].
 *
 * What is left is the three things that are genuinely this page's own:
 * the specialization chips, the number counters and the world-map pins.
 *
 * @package MBA_Admission_Guide
 */
(function () {
  'use strict';

  var root = document.querySelector('.smu');

  if (!root) {
    return;
  }

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- specialization chips ----------
     The copy lives on the buttons as data attributes, because the list is
     Customizer-driven — the JS must not hold its own copy of it. */
  var chips = root.querySelector('[data-smu-chips]');
  var specBox = root.querySelector('[data-smu-specbox]');

  function showSpec(chip) {
    if (!specBox || !chip) {
      return;
    }

    var title = document.createElement('b');
    var body = document.createElement('p');

    title.textContent = chip.getAttribute('data-smu-title') || chip.textContent;
    body.textContent = chip.getAttribute('data-smu-desc') || '';

    specBox.textContent = '';
    specBox.appendChild(title);

    if (body.textContent) {
      specBox.appendChild(body);
    }
  }

  if (chips) {
    chips.addEventListener('click', function (e) {
      var chip = e.target.closest('.smu-chip');

      if (!chip || !chips.contains(chip)) {
        return;
      }

      chips.querySelectorAll('.smu-chip').forEach(function (c) {
        c.classList.remove('is-on');
        c.setAttribute('aria-pressed', 'false');
      });

      chip.classList.add('is-on');
      chip.setAttribute('aria-pressed', 'true');
      showSpec(chip);
    });

    showSpec(chips.querySelector('.smu-chip.is-on') || chips.querySelector('.smu-chip'));
  }

  /* ---------- counters ---------- */
  function count(el) {
    var target = parseInt(el.getAttribute('data-smu-target'), 10);

    if (isNaN(target)) {
      return;
    }

    if (reduce) {
      el.textContent = target.toLocaleString('en-IN');
      return;
    }

    var dur = 1500;
    var start = performance.now();

    requestAnimationFrame(function frame(now) {
      var p = Math.min((now - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3);

      el.textContent = Math.round(target * eased).toLocaleString('en-IN');

      if (p < 1) {
        requestAnimationFrame(frame);
      }
    });
  }

  var nums = root.querySelectorAll('.smu-num[data-smu-target]');

  if (nums.length) {
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            count(entry.target);
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.4 });

      nums.forEach(function (n) {
        io.observe(n);
      });
    } else {
      nums.forEach(count);
    }
  }

})();

/**
 * Course carousel — continuous marquee.
 *
 * Implemented by advancing the container's own scrollLeft each frame rather
 * than by translating an inner track. That keeps it a real scroll container,
 * so swipe, trackpad, drag and keyboard scrolling all still work, and the
 * arrows stay meaningful — a transform-based marquee would have broken all
 * of them.
 *
 * The card set is duplicated in JS, not in PHP, so a visitor without
 * JavaScript gets the five real cards and no clones. Once content is doubled,
 * wrapping is just: when scrollLeft passes half the width, subtract half.
 * The seam is invisible because the second half is identical to the first.
 *
 * Motion pauses on hover, on keyboard focus, while the visitor is scrolling
 * it themselves, and when the tab is hidden. It never starts at all under
 * prefers-reduced-motion, and there is an explicit pause control, because
 * indefinite automatic movement needs one (WCAG 2.2.2).
 */
(function () {
  'use strict';

  var root = document.querySelector('.smu');

  if (!root) {
    return;
  }

  var track = root.querySelector('[data-smu-track]');
  var prev = root.querySelector('[data-smu-prev]');
  var next = root.querySelector('[data-smu-next]');
  var toggle = root.querySelector('[data-smu-toggle]');

  if (!track || !prev || !next) {
    return;
  }

  var SPEED = 38;               // px per second — a drift, not a slide
  var RESUME_AFTER = 2200;      // ms of stillness before motion resumes

  var cards = Array.prototype.slice.call(track.querySelectorAll('.smu-course-card'));
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');

  // One card plus its gap, so an arrow click advances by exactly one card.
  function stride() {
    if (!cards.length) {
      return track.clientWidth;
    }

    var styles = getComputedStyle(track);
    var gap = parseFloat(styles.columnGap || styles.gap) || 0;

    return cards[0].getBoundingClientRect().width + gap;
  }

  /* ---------- manual-only fallback ----------
     Used when the marquee cannot or should not run: too few cards to
     overflow, or the visitor asked for reduced motion. */
  function manualMode() {
    function sync() {
      var max = track.scrollWidth - track.clientWidth - 1;

      prev.disabled = track.scrollLeft <= 0;
      next.disabled = track.scrollLeft >= max;
    }

    track.addEventListener('scroll', sync, { passive: true });
    window.addEventListener('resize', sync);
    sync();
  }

  if (reduce.matches || cards.length < 2 || track.scrollWidth <= track.clientWidth + 4) {
    manualMode();
    return;
  }

  /* ---------- marquee ---------- */
  var half = 0;

  function buildLoop() {
    // Clones are decorative repeats of content already in the DOM.
    cards.forEach(function (card) {
      var clone = card.cloneNode(true);

      clone.setAttribute('aria-hidden', 'true');
      clone.classList.add('is-clone');

      // Keep duplicated buttons out of the tab order and off the a11y tree.
      clone.querySelectorAll('button, a').forEach(function (el) {
        el.setAttribute('tabindex', '-1');
      });

      track.appendChild(clone);
    });

    half = track.scrollWidth / 2;
  }

  buildLoop();
  track.classList.add('is-marquee');

  if (toggle) {
    toggle.hidden = false;
  }

  var paused = false;      // hover / focus / tab hidden
  var stopped = false;     // explicit pause button
  var idleUntil = 0;       // set while the visitor is scrolling
  var last = 0;

  function wrap() {
    if (half <= 0) {
      return;
    }

    if (track.scrollLeft >= half) {
      track.scrollLeft -= half;
    } else if (track.scrollLeft <= 0) {
      track.scrollLeft += half;
    }
  }

  function frame(now) {
    var dt = last ? (now - last) / 1000 : 0;
    last = now;

    if (!paused && !stopped && now >= idleUntil && dt > 0 && dt < 0.4) {
      track.scrollLeft += SPEED * dt;
      wrap();
    }

    requestAnimationFrame(frame);
  }

  requestAnimationFrame(frame);

  function hold() {
    idleUntil = performance.now() + RESUME_AFTER;
  }

  // Hover and focus pause outright; a hovered card is one being read.
  track.addEventListener('mouseenter', function () { paused = true; });
  track.addEventListener('mouseleave', function () { paused = false; });
  track.addEventListener('focusin', function () { paused = true; });
  track.addEventListener('focusout', function () { paused = false; });

  // Any manual scrolling wins for a moment, then the drift resumes.
  ['wheel', 'touchstart', 'pointerdown'].forEach(function (evt) {
    track.addEventListener(evt, hold, { passive: true });
  });
  track.addEventListener('scroll', wrap, { passive: true });

  document.addEventListener('visibilitychange', function () {
    paused = document.hidden;
  });

  prev.addEventListener('click', function () {
    hold();
    track.scrollBy({ left: -stride(), behavior: 'smooth' });
  });

  next.addEventListener('click', function () {
    hold();
    track.scrollBy({ left: stride(), behavior: 'smooth' });
  });

  if (toggle) {
    toggle.addEventListener('click', function () {
      stopped = !stopped;
      toggle.setAttribute('aria-pressed', stopped ? 'true' : 'false');
      toggle.setAttribute(
        'aria-label',
        stopped ? toggle.dataset.smuPlayLabel : toggle.dataset.smuPauseLabel
      );
    });
  }

  // Card widths change with the breakpoint, so the halfway mark moves too.
  window.addEventListener('resize', function () {
    half = track.scrollWidth / 2;
  });
})();

/**
 * Landing header — mobile menu toggle.
 *
 * The nav is a plain list that CSS hides below 1080px; this only flips the
 * open class and keeps aria-expanded honest. Clicking a link closes it,
 * because every link is an in-page anchor and leaving the panel covering
 * the section you just jumped to is the classic landing-page annoyance.
 */
(function () {
  'use strict';

  var toggle = document.getElementById('smuNavToggle');
  var nav = document.getElementById('smuLandingNav');

  if (!toggle || !nav) {
    return;
  }

  function close() {
    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
  }

  toggle.addEventListener('click', function () {
    var open = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  nav.addEventListener('click', function (e) {
    if (e.target.closest('a')) {
      close();
    }
  });

  // A resize back to desktop must not leave the panel stuck open.
  window.addEventListener('resize', function () {
    if (window.innerWidth > 1080) {
      close();
    }
  });
})();
