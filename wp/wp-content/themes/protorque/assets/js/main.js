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
    function set(open) {
      card.classList.toggle('is-open', open);
      if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    if (fine) {
      var initial = card.classList.contains('is-open');
      card.addEventListener('mouseenter', function () { set(true); });
      card.addEventListener('mouseleave', function () { set(initial); });
    }
    if (btn) btn.addEventListener('click', function () { set(!card.classList.contains('is-open')); });
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
    // On small screens the cards sit under the map instead of floating over it.
    holder.hidden = false;
    markers.forEach(function (m) { holder.appendChild(m.querySelector('.pt-map__card')); });
  }
  function open(m) {
    markers.forEach(function (x) {
      var on = x === m;
      x.classList.toggle('is-open', on);
      x.querySelector('.pt-map__pin').setAttribute('aria-expanded', on ? 'true' : 'false');
      var card = document.getElementById(x.querySelector('.pt-map__pin').getAttribute('aria-controls'));
      if (card) card.classList.toggle('is-open', on);
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
})();
