(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var overlay = document.getElementById('infiniti-search-overlay');
    if (!overlay) return;
    var openBtn = document.querySelector('.infiniti-search-toggle');
    var closeBtn = overlay.querySelector('.infiniti-search-overlay__close');
    var input = overlay.querySelector('.infiniti-search-overlay__input');

    function open() {
      overlay.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      if (input) input.focus();
    }

    function close() {
      overlay.classList.remove('is-open');
      document.body.style.overflow = '';
    }

    if (openBtn) openBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });
  });
})();
