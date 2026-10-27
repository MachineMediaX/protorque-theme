(function () {
  // Header search
  var searchBtn = document.querySelector('.pt-header__search');
  var search = document.getElementById('pt-search');
  if (searchBtn && search) searchBtn.addEventListener('click', function () {
    var open = search.hasAttribute('hidden');
    if (open) { search.removeAttribute('hidden'); search.querySelector('input').focus(); }
    else { search.setAttribute('hidden', ''); }
    searchBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  // Cards: expand on hover (pointer) or tap (touch), per Development Brief Part 2, Sections 6 and 7
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  document.querySelectorAll('.pt-card').forEach(function (card) {
    var btn = card.querySelector('.pt-card__toggle');
    // A card with a "rest" image (B&W with the logo in colour) swaps to the full-colour file when it opens.
    var im = card.querySelector('.pt-card__img--rest');
    if (im) { im.dataset.restSrc = im.getAttribute('src'); im.dataset.restSrcset = im.getAttribute('srcset'); }
    function set(open) {
      card.classList.toggle('is-open', open);
      if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (im) {
        im.setAttribute('srcset', open ? im.dataset.srcset : im.dataset.restSrcset);
        im.setAttribute('src', open ? im.dataset.src : im.dataset.restSrc);
      }
    }
    if (fine) {
      var initial = card.classList.contains('is-open');
      card.addEventListener('mouseenter', function () { set(true); });
      card.addEventListener('mouseleave', function () { set(initial); });
    }
    // Tap anywhere on the card (image, title or the plus) to open it; a tap on the open panel's link still follows the link.
    card.addEventListener('click', function (e) {
      if (e.target.closest('.pt-card__panel a')) return;
      e.preventDefault();
      set(!card.classList.contains('is-open'));
    });
  });
})();

// About page map: one card open at a time; hover opens on pointer devices, tap toggles everywhere.
(function () {
  var map = document.querySelector('[data-map]');
  if (!map) return;
  var markers = [].slice.call(map.querySelectorAll('.pt-map__marker'));
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var holder = map.querySelector('.pt-map__cards');
  var narrow = window.matchMedia('(max-width: 900px)').matches;
  if (narrow && holder) {
    // On small screens the cards sit under the map, and a row of location buttons stands in for the
    // tiny markers (Calgary and Red Deer are a few pixels apart at phone width).
    holder.hidden = false;
    var chips = document.createElement('div');
    chips.className = 'pt-map__chips';
    markers.forEach(function (m) {
      var pin = m.querySelector('.pt-map__pin');
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'pt-map__chip';
      var city = m.querySelector('.pt-map__city');
      b.textContent = city ? city.textContent.replace(/,.*$/, '') : (pin.getAttribute('aria-label') || '');
      b.setAttribute('aria-controls', pin.getAttribute('aria-controls'));
      b.addEventListener('click', function () { open(m.classList.contains('is-open') ? null : m); });
      m._chip = b;
      chips.appendChild(b);
    });
    holder.appendChild(chips);
    markers.forEach(function (m) { holder.appendChild(m.querySelector('.pt-map__card')); });
  }
  function open(m) {
    markers.forEach(function (x) {
      var on = x === m;
      x.classList.toggle('is-open', on);
      x.querySelector('.pt-map__pin').setAttribute('aria-expanded', on ? 'true' : 'false');
      var card = document.getElementById(x.querySelector('.pt-map__pin').getAttribute('aria-controls'));
      if (card) card.classList.toggle('is-open', on);
      if (x._chip) x._chip.classList.toggle('is-active', on);
    });
  }
  var initial = markers.filter(function (m) { return m.classList.contains('is-open'); })[0];
  if (initial) open(initial);
  markers.forEach(function (m) {
    var pin = m.querySelector('.pt-map__pin');
    pin.addEventListener('click', function () { open(m.classList.contains('is-open') ? null : m); });
    if (fine) pin.addEventListener('mouseenter', function () { open(m); });
    // Cards near the bottom of the map open upward so they stay inside the section.
    if (parseFloat(m.style.top) > 58) m.classList.add('pt-map__marker--up');
  });
  // A click anywhere outside the open card (and off a pin) closes it.
  document.addEventListener('click', function (e) {
    if (e.target.closest('.pt-map__card') || e.target.closest('.pt-map__pin') || e.target.closest('.pt-map__chip')) return;
    if (markers.some(function (m) { return m.classList.contains('is-open'); })) open(null);
  });
})();

// Process tabs on the service landings (How we run tubular operations / How drilling verification works).
(function () {
  document.querySelectorAll('[data-tabs]').forEach(function (sec) {
    var tabs = [].slice.call(sec.querySelectorAll('.pt-tabs__tab'));
    var panels = [].slice.call(sec.querySelectorAll('.pt-tabs__panel'));
    function activate(i, focus) {
      tabs.forEach(function (t, k) {
        var on = k === i;
        t.classList.toggle('is-active', on);
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.setAttribute('tabindex', on ? '0' : '-1');
      });
      panels.forEach(function (p, k) { p.hidden = k !== i; p.classList.toggle('is-active', k === i); });
      if (focus) tabs[i].focus();
    }
    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () { activate(i); });
      t.addEventListener('keydown', function (e) {
        var n = e.key === 'ArrowRight' ? i + 1 : e.key === 'ArrowLeft' ? i - 1 : e.key === 'Home' ? 0 : e.key === 'End' ? tabs.length - 1 : null;
        if (n === null) return;
        e.preventDefault();
        activate((n + tabs.length) % tabs.length, true);
      });
    });
  });
})();

// Equipment lineup: hover (pointer) or tap (touch) swaps the cutout for the field photo or video; chips filter by application.
(function () {
  var grid = document.querySelector('.pt-eqgrid');
  if (!grid) return;
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  grid.querySelectorAll('.pt-eq').forEach(function (el) {
    var vid = el.querySelector('video');
    function setField(on) {
      el.classList.toggle('show-field', on);
      if (vid) { if (on) { var p = vid.play(); if (p && p.catch) p.catch(function () {}); } else { vid.pause(); } }
    }
    if (fine) {
      el.addEventListener('mouseenter', function () { setField(true); });
      el.addEventListener('mouseleave', function () { setField(false); });
    }
    var t = el.querySelector('.pt-eq__toggle');
    if (t) t.addEventListener('click', function () {
      var on = !el.classList.contains('show-field');
      setField(on);
      t.setAttribute('aria-pressed', on ? 'true' : 'false');
      t.textContent = on ? 'Product' : 'In the field';
    });
  });
  var chips = [].slice.call(document.querySelectorAll('.pt-eqchip'));
  chips.forEach(function (b) {
    b.addEventListener('click', function () {
      var app = b.dataset.app;
      chips.forEach(function (c) { c.setAttribute('aria-pressed', c === b ? 'true' : 'false'); });
      grid.querySelectorAll('.pt-eq').forEach(function (c) { c.classList.toggle('is-out', app !== 'all' && c.dataset.app !== app); });
    });
  });
})();
