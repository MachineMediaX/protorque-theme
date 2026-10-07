// Primary navigation: mega menus (hover and click), tabs, equipment preview, mobile accordion.
(function () {
  var header = document.querySelector('.pt-header');
  if (!header) return;
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var items = [].slice.call(header.querySelectorAll('.pt-nav__item[data-mega]'));
  var closeTimer;

  function setOpen(item, on) {
    items.forEach(function (x) {
      var isOn = on && x === item;
      x.classList.toggle('is-open', isOn);
      x.querySelector('.pt-nav__link').setAttribute('aria-expanded', isOn ? 'true' : 'false');
    });
  }
  items.forEach(function (item) {
    var link = item.querySelector('.pt-nav__link');
    if (fine) {
      item.addEventListener('mouseenter', function () { clearTimeout(closeTimer); setOpen(item, true); });
      item.addEventListener('mouseleave', function () { closeTimer = setTimeout(function () { setOpen(item, false); }, 180); });
    }
    link.addEventListener('click', function (e) {
      // First click/tap opens the panel; a second click follows the link.
      if (!item.classList.contains('is-open')) { e.preventDefault(); setOpen(item, true); }
    });
    link.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown' || e.key === ' ') { e.preventDefault(); setOpen(item, true); var f = item.querySelector('.pt-mega a, .pt-mega button'); if (f) f.focus(); }
    });
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(null, false); });
  document.addEventListener('click', function (e) { if (!header.contains(e.target)) setOpen(null, false); });

  // Split mode tabs
  header.querySelectorAll('.pt-mega--split').forEach(function (mega) {
    var tabs = [].slice.call(mega.querySelectorAll('.pt-mega__tab'));
    function activate(tab) {
      tabs.forEach(function (t) {
        var on = t === tab;
        t.classList.toggle('is-active', on);
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        var pane = document.getElementById(t.getAttribute('aria-controls'));
        if (pane) { pane.classList.toggle('is-active', on); pane.hidden = !on; }
      });
    }
    tabs.forEach(function (t) {
      t.addEventListener('click', function () { activate(t); });
      if (fine) t.addEventListener('mouseenter', function () { activate(t); });
    });
  });

  // Equipment preview
  header.querySelectorAll('.pt-mega--equipment').forEach(function (mega) {
    var title = mega.querySelector('.pt-mega__previewtitle');
    var blurb = mega.querySelector('.pt-mega__previewblurb');
    var cta = mega.querySelector('.pt-mega__cta');
    var img = mega.querySelector('.pt-mega__previewimg img');
    var links = [].slice.call(mega.querySelectorAll('.pt-mega__links a[data-title]'));
    function show(a) {
      links.forEach(function (l) { l.classList.toggle('is-active', l === a); });
      title.textContent = a.dataset.title;
      blurb.textContent = a.dataset.blurb || '';
      cta.textContent = 'Explore ' + a.dataset.title;
      cta.href = a.href;
      if (a.dataset.image) { img.src = a.dataset.image; img.hidden = false; } else { img.hidden = true; }
    }
    links.forEach(function (a) {
      a.addEventListener('mouseenter', function () { show(a); });
      a.addEventListener('focus', function () { show(a); });
    });
    if (links[0]) links[0].classList.add('is-active');
  });

  // Mobile menu
  var menuBtn = header.querySelector('.pt-header__menu');
  var mobile = document.getElementById('pt-mobile-nav');
  if (menuBtn && mobile) {
    menuBtn.addEventListener('click', function () {
      var open = mobile.hidden;
      mobile.hidden = !open;
      header.classList.toggle('is-open', open);
      menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    mobile.querySelectorAll('.pt-mnav__toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var sub = btn.parentNode.nextElementSibling;
        var open = sub.hidden;
        sub.hidden = !open;
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });
  }
})();
