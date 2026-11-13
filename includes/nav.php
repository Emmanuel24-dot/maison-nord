<?php
require_once __DIR__ . '/auth.php';
$active = isset($active) ? $active : 'home';
$mn_user = mn_current_user();
?>
<script>
window.MN_AUTH={logged:<?= $mn_user ? 'true' : 'false' ?>,prenom:<?= $mn_user ? json_encode($mn_user['prenom']) : 'null' ?>};
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('#nav-hamburger');
    if (!btn) return;
    e.stopImmediatePropagation();
    var menu = document.getElementById('nav-mobile-menu');
    var isOpen = btn.classList.toggle('open');
    if (menu) menu.classList.toggle('open', isOpen);
    btn.setAttribute('aria-expanded', String(isOpen));
    var drop = document.getElementById('mn-user-dropdown');
    if (drop) drop.classList.remove('open');
  });
});
</script>
<nav class="mn-nav" id="main-nav">
  <div class="container mn-nav-inner">
    <div class="mn-nav-left">
      <a class="mn-nav-link<?= $active === 'home'     ? ' active' : '' ?>" href="index.php">Accueil</a>
      <a class="mn-nav-link<?= $active === 'boutique'    ? ' active' : '' ?>" href="boutique.php">Boutique</a>
      <a class="mn-nav-link<?= $active === 'nouveautes' ? ' active' : '' ?>" href="nouveautes.php">Nouveautés</a>
      <a class="mn-nav-link<?= $active === 'apropos'    ? ' active' : '' ?>" href="a-propos.php">À propos</a>
      <a class="mn-nav-link<?= $active === 'contact'  ? ' active' : '' ?>" href="contact.php">Contact</a>
    </div>
    <a class="mn-nav-brand" href="index.php">MAISON NORD</a>
    <div class="mn-nav-right">
      <button class="mn-nav-icon-btn btn-theme-toggle" aria-label="Changer le thème">
        <i class="bi bi-moon-stars theme-icon"></i>
      </button>

      <?php if ($mn_user): ?>
      <div class="mn-user-menu" id="mn-user-menu">
        <button class="mn-nav-icon-btn mn-user-btn" type="button" id="mn-user-btn" aria-expanded="false" aria-haspopup="true">
          <i class="bi bi-person-check"></i>
        
        </button>
        <div class="mn-user-dropdown" id="mn-user-dropdown" role="menu">
          <a href="profil.php" class="mn-user-dropdown-item" role="menuitem">
            <i class="bi bi-person"></i> Mon profil
          </a>
          <a href="mes-commandes.php" class="mn-user-dropdown-item" role="menuitem">
            <i class="bi bi-bag-check"></i> Mes commandes
          </a>
          <a href="favoris.php" class="mn-user-dropdown-item" role="menuitem">
            <i class="bi bi-heart"></i> Mes favoris
          </a>
          <div style="height:1px;background:var(--mn-border);margin:.25rem .75rem"></div>
          <a href="logout.php" class="mn-user-dropdown-item" role="menuitem">
            <i class="bi bi-box-arrow-right"></i> Se déconnecter
          </a>
        </div>
      </div>
      <?php else: ?>
      <a class="mn-nav-icon-btn" href="login.php" aria-label="Se connecter" title="Se connecter">
        <i class="bi bi-person"></i>
      </a>
      <?php endif; ?>

      <button class="mn-cart-btn" id="cart-open-btn" type="button" aria-label="Ouvrir le panier">
        <i class="bi bi-bag"></i>
        <span class="mn-cart-badge" id="nav-cart-count" aria-live="polite">0</span>
      </button>
      <button class="mn-hamburger" id="nav-hamburger" type="button" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
  <div class="mn-nav-mobile" id="nav-mobile-menu" role="navigation" aria-label="Menu mobile">
    <a class="mn-nav-link<?= $active === 'home'     ? ' active' : '' ?>" href="index.php">Accueil</a>
    <a class="mn-nav-link<?= $active === 'boutique'    ? ' active' : '' ?>" href="boutique.php">Boutique</a>
    <a class="mn-nav-link<?= $active === 'nouveautes' ? ' active' : '' ?>" href="nouveautes.php">Nouveautés</a>
    <a class="mn-nav-link" href="a-propos.php">À propos</a>
    <a class="mn-nav-link<?= $active === 'contact'  ? ' active' : '' ?>" href="contact.php">Contact</a>
    <?php if ($mn_user): ?>
    <a class="mn-nav-link" href="profil.php"><i class="bi bi-person me-1"></i>Mon profil</a>
    <a class="mn-nav-link" href="mes-commandes.php"><i class="bi bi-bag-check me-1"></i>Mes commandes</a>
    <a class="mn-nav-link" href="favoris.php"><i class="bi bi-heart me-1"></i>Mes favoris</a>
    <a class="mn-nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Se déconnecter</a>
    <?php else: ?>
    <a class="mn-nav-link" href="login.php"><i class="bi bi-person me-1"></i>Se connecter</a>
    <?php endif; ?>
  </div>
</nav>
