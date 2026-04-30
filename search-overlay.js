(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var overlay = document.getElementById('infiniti-search-overlay');
    if (!overlay) return;

    var headerSearch = document.querySelector('.infiniti-header-search');
    var closeBtn = overlay.querySelector('.infiniti-search-overlay__close');
    var input = overlay.querySelector('.infiniti-search-overlay__input');

    function open(e) {
      if (e) e.preventDefault();
      overlay.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      if (input) input.focus();
    }

    function close() {
      overlay.classList.remove('is-open');
      document.body.style.overflow = '';
    }

    if (headerSearch) {
      headerSearch.addEventListener('submit', function (e) {
        e.preventDefault();
        open();
      });
      var btn = headerSearch.querySelector('.wp-block-search__button');
      if (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          open();
        });
      }
    }

    if (closeBtn) closeBtn.addEventListener('click', close);

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });
  });
})();
