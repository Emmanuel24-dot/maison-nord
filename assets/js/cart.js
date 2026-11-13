(function () {
  var CART_KEY = 'mn-cart-v1';

  /* ── Panier (état) ────────────────────────────────── */
  var MN_CART = {
    items: [],

    load: function () {
      try {
        var raw = localStorage.getItem(CART_KEY);
        if (raw) this.items = JSON.parse(raw);
      } catch (e) { this.items = []; }
    },

    save: function () {
      localStorage.setItem(CART_KEY, JSON.stringify(this.items));
      this._updateUI();
      this._syncToServer();
    },

    add: function (product, size, color, qty) {
      var existing = this._find(product.id, size, color);
      if (existing) {
        existing.qty += qty;
      } else {
        this.items.push({
          id:    product.id,
          name:  product.name,
          price: product.price,
          image: product.image,
          size:  size,
          color: color,
          qty:   qty
        });
      }
      this.save();
    },

    remove: function (id, size, color) {
      this.items = this.items.filter(function (i) {
        return !(i.id === id && i.size === size && i.color === color);
      });
      this.save();
    },

    updateQty: function (id, size, color, delta) {
      var item = this._find(id, size, color);
      if (!item) return;
      item.qty = Math.max(1, item.qty + delta);
      this.save();
    },

    total: function () {
      return this.items.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
    },

    count: function () {
      return this.items.reduce(function (s, i) { return s + i.qty; }, 0);
    },

    _find: function (id, size, color) {
      return this.items.find(function (i) {
        return i.id === id && i.size === size && i.color === color;
      });
    },

    /* Envoyer le panier au serveur (fire-and-forget) */
    _syncToServer: function () {
      if (!window.MN_AUTH || !window.MN_AUTH.logged) return;
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'api/cart-sync.php', true);
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.send(JSON.stringify({ items: this.items }));
    },

    /* Charger le panier depuis le serveur et fusionner avec localStorage */
    loadFromServer: function (callback) {
      if (!window.MN_AUTH || !window.MN_AUTH.logged) {
        if (callback) callback();
        return;
      }
      var self = this;
      var xhr = new XMLHttpRequest();
      xhr.open('GET', 'api/cart-sync.php', true);
      xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) return;
        if (xhr.status === 200) {
          try {
            var data = JSON.parse(xhr.responseText);
            if (data.items && data.items.length > 0) {
              /* Fusionner : priorité à la quantité la plus haute */
              data.items.forEach(function (serverItem) {
                var existing = self._find(serverItem.id, serverItem.size, serverItem.color);
                if (existing) {
                  existing.qty = Math.max(existing.qty, parseInt(serverItem.qty, 10) || 1);
                } else {
                  self.items.push(serverItem);
                }
              });
              /* Persister la fusion */
              localStorage.setItem(CART_KEY, JSON.stringify(self.items));
              self._syncToServer();
            }
          } catch (e) {}
        }
        if (callback) callback();
      };
      xhr.onerror = function () { if (callback) callback(); };
      xhr.send();
    },

    _updateUI: function () {
      var count = this.count();
      /* Badge nav */
      var badge = document.getElementById('nav-cart-count');
      if (badge) {
        badge.textContent = count;
        badge.classList.toggle('visible', count > 0);
      }
      this._renderDrawer();
    },

    _renderDrawer: function () {
      var itemsEl  = document.getElementById('cart-items-list');
      var totalEl  = document.getElementById('cart-total-value');
      var footerEl = document.getElementById('cart-footer');
      if (!itemsEl) return;

      if (this.items.length === 0) {
        itemsEl.innerHTML =
          '<div class="cart-empty">' +
            '<i class="bi bi-bag"></i>' +
            '<p>Votre panier est vide.</p>' +
            '<a href="boutique.php" class="btn-mn" onclick="window.MN_CART_CLOSE()">' +
              '<span>Découvrir la collection</span><i class="bi bi-arrow-right"></i>' +
            '</a>' +
          '</div>';
        if (footerEl) footerEl.style.display = 'none';
      } else {
        if (footerEl) footerEl.style.display = '';
        itemsEl.innerHTML = this.items.map(function (item) {
          var eid = item.id.replace(/'/g, "\\'");
          var esz = item.size.replace(/'/g, "\\'");
          var eco = item.color.replace(/'/g, "\\'");
          return '<div class="cart-item">' +
            '<img class="cart-item-img" src="' + item.image + '" alt="' + item.name + '">' +
            '<div class="cart-item-info">' +
              '<div class="cart-item-name">' + item.name + '</div>' +
              '<div class="cart-item-meta">' + item.size + ' · ' + item.color + '</div>' +
              '<div class="cart-item-price">' + window.MN_FORMAT_PRICE(item.price * item.qty) + '</div>' +
              '<div class="cart-item-controls">' +
                '<div class="cart-item-qty">' +
                  '<button type="button" aria-label="Diminuer" onclick="window.MN_CART.updateQty(\'' + eid + '\',\'' + esz + '\',\'' + eco + '\',-1)">' +
                    '<i class="bi bi-dash"></i>' +
                  '</button>' +
                  '<span>' + item.qty + '</span>' +
                  '<button type="button" aria-label="Augmenter" onclick="window.MN_CART.updateQty(\'' + eid + '\',\'' + esz + '\',\'' + eco + '\',1)">' +
                    '<i class="bi bi-plus"></i>' +
                  '</button>' +
                '</div>' +
                '<button type="button" class="cart-item-remove" onclick="window.MN_CART.remove(\'' + eid + '\',\'' + esz + '\',\'' + eco + '\')">' +
                  'Retirer' +
                '</button>' +
              '</div>' +
            '</div>' +
          '</div>';
        }).join('');
      }

      if (totalEl) totalEl.textContent = window.MN_FORMAT_PRICE(this.total());
    }
  };

  /* ── API publique ────────────────────────────────── */
  window.MN_CART = MN_CART;

  window.MN_CART_OPEN = function () {
    document.getElementById('cart-overlay').classList.add('open');
    document.getElementById('cart-drawer').classList.add('open');
    document.body.style.overflow = 'hidden';
  };

  window.MN_CART_CLOSE = function () {
    document.getElementById('cart-overlay').classList.remove('open');
    document.getElementById('cart-drawer').classList.remove('open');
    document.body.style.overflow = '';
  };

  window.MN_AUTH_MODAL_CLOSE = function () {
    var overlay = document.getElementById('mn-auth-modal-overlay');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
  };

  window.MN_CHECKOUT = function () {
    if (window.MN_AUTH && window.MN_AUTH.logged) {
      /* Synchroniser le panier côté serveur puis rediriger */
      window.MN_CART._syncToServer();
      window.location.href = 'checkout.php';
      return;
    }
    window.MN_CART_CLOSE();
    var redirect = encodeURIComponent(window.location.pathname + window.location.search);
    var loginLink    = document.getElementById('mn-auth-login-link');
    var registerLink = document.getElementById('mn-auth-register-link');
    if (loginLink)    loginLink.href    = 'login.php?redirect='    + redirect;
    if (registerLink) registerLink.href = 'register.php?redirect=' + redirect;
    var overlay = document.getElementById('mn-auth-modal-overlay');
    if (overlay) {
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  };

  MN_CART.load();

  /* ── DOM ready ───────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    /* Tiroir panier */
    document.body.insertAdjacentHTML('beforeend',
      '<div class="cart-overlay" id="cart-overlay"></div>' +
      '<div class="cart-drawer" id="cart-drawer" role="dialog" aria-modal="true" aria-label="Panier">' +
        '<div class="cart-drawer-head">' +
          '<h2 class="cart-drawer-title">Mon panier</h2>' +
          '<button type="button" class="cart-close-btn" id="cart-close" aria-label="Fermer">' +
            '<i class="bi bi-x-lg"></i>' +
          '</button>' +
        '</div>' +
        '<div class="cart-items" id="cart-items-list"></div>' +
        '<div class="cart-footer" id="cart-footer">' +
          '<div class="cart-total-row">' +
            '<span class="cart-total-label">Total estimé</span>' +
            '<span class="cart-total-value" id="cart-total-value">0 FCFA</span>' +
          '</div>' +
          '<button type="button" class="btn-mn cart-checkout-btn" onclick="window.MN_CHECKOUT()">' +
            '<span>Passer commande</span><i class="bi bi-arrow-right"></i>' +
          '</button>' +
        '</div>' +
      '</div>'
    );

    /* Modale auth */
    document.body.insertAdjacentHTML('beforeend',
      '<div class="mn-auth-modal-overlay" id="mn-auth-modal-overlay">' +
        '<div class="mn-auth-modal" role="dialog" aria-modal="true" aria-label="Connexion requise">' +
          '<button type="button" class="mn-auth-modal-close" id="mn-auth-modal-close" aria-label="Fermer">' +
            '<i class="bi bi-x-lg"></i>' +
          '</button>' +
          '<div class="mn-auth-modal-icon"><i class="bi bi-bag-check"></i></div>' +
          '<h2 class="mn-auth-modal-title">Finaliser ma commande</h2>' +
          '<p class="mn-auth-modal-desc">Créez un compte ou connectez-vous pour passer votre commande.</p>' +
          '<div class="mn-auth-modal-actions">' +
            '<a href="login.php" id="mn-auth-login-link" class="btn-mn"><span>Se connecter</span><i class="bi bi-arrow-right"></i></a>' +
            '<a href="register.php" id="mn-auth-register-link" class="btn-mn-outline"><span>Créer un compte</span></a>' +
          '</div>' +
        '</div>' +
      '</div>'
    );

    document.getElementById('mn-auth-modal-overlay').addEventListener('click', function (e) {
      if (e.target === this) window.MN_AUTH_MODAL_CLOSE();
    });
    document.getElementById('mn-auth-modal-close').addEventListener('click', window.MN_AUTH_MODAL_CLOSE);
    document.getElementById('cart-overlay').addEventListener('click', window.MN_CART_CLOSE);
    document.getElementById('cart-close').addEventListener('click', window.MN_CART_CLOSE);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { window.MN_CART_CLOSE(); window.MN_AUTH_MODAL_CLOSE(); }
    });

    document.addEventListener('click', function (e) {
      if (e.target.closest('#cart-open-btn')) window.MN_CART_OPEN();
    });

    /* Charger panier serveur si connecté, sinon juste afficher */
    if (window.MN_AUTH && window.MN_AUTH.logged) {
      MN_CART.loadFromServer(function () { MN_CART._updateUI(); });
    } else {
      MN_CART._updateUI();
    }
  });
})();
