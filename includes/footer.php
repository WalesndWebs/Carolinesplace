<?php $root = isset($root) ? $root : '/'; ?>
<footer class="footer">
  <div class="footer__grid">
    <div>
      <div class="footer__brand-name">Caroline's Place</div>
      <div class="footer__brand-sub">Lagos, Nigeria</div>
      <p class="footer__tagline">
        A private sanctuary built in honour of Dame Caroline Oladunni Adebutu.
        Discretion, refinement, and bespoke experiences for those who expect the finest.
      </p>
    </div>
    <div>
      <div class="footer__heading">Navigate</div>
      <a href="<?= $root ?>index.php"     class="footer__link">Home</a>
      <a href="<?= $root ?>clubhouse.php" class="footer__link">The Club House</a>
      <a href="<?= $root ?>spa.php"       class="footer__link">N Lounge &amp; Spa</a>
      <a href="<?= $root ?>spa_menu.php"  class="footer__link">Book an Experience</a>
    </div>
    <div>
      <div class="footer__heading">Contact</div>
      <span class="footer__link">Lagos, Nigeria</span>
      <span class="footer__link">By appointment only</span>
      <span class="footer__link">nlandspa@gmail.com</span>
      <a href="https://bolamatelokoh.com/" class="footer__link" target="_blank" rel="noopener noreferrer">Founder Profile</a>
    </div>
  </div>
  <div class="footer__bottom">
    <span>&copy; <?= date('Y') ?> Caroline's Place. All rights reserved.</span>
    <span>walesndwebs.com</span>
  </div>
</footer>
</body>
</html>
