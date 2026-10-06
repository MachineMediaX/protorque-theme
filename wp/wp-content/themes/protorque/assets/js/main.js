(function () {
  // Header: mobile menu and search
  var header = document.querySelector('.pt-header');
  var menuBtn = document.querySelector('.pt-header__menu');
  var searchBtn = document.querySelector('.pt-header__search');
  var search = document.getElementById('pt-search');
  if (menuBtn) menuBtn.addEventListener('click', function () {
    var open = header.classList.toggle('is-open');
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
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
