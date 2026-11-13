const Toast = (() => {
  let container;

  function init() {
    container = document.getElementById('toast-container');
  }

  function show(message, type = 'success') {
    if (!container) init();
    const el = document.createElement('div');
    el.className = type === 'error' ? 'toast toast-error' : 'toast';
    el.textContent = message;
    container.appendChild(el);
    setTimeout(() => el.remove(), 4000);
  }

  return { show, init };
})();
