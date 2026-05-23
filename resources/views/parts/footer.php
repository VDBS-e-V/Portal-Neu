<?php declare(strict_types=1); ?>
<hr class="footer-divider--thick">
<footer class="footer" role="contentinfo">
	<div class="footer-top">
		<div class="container container--wide">
			<div class="footer-grid">
				<div class="footer-col">
					<h4>Informationen für</h4>
					<ul class="footer-list">
						<li><a href="#">Teamer:innen</a></li>
						<li><a href="#">Vereinsmitglieder:innen</a></li>
						<li><a href="#">Verwaltung</a></li>
						<li><a href="#">Schüler:innen</a></li>
						<li><a href="#">Lehrkräfte</a></li>
						<li><a href="#">Partner:innen</a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h4>Service-Portal</h4>
					<ul class="footer-list">
						<li><a href="/">Startseite</a></li>
						<li><a href="/kontakt">Kontakt</a></li>
						<li><a href="/impressum">Impressum</a></li>
						<li><a href="/datenschutz">Datenschutz</a></li>
						<li><a href="/barrierefreiheit">Barrierefreiheit</a></li>
						<li><a href="/beratung">Beratung</a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h4>Soziale Medien</h4>
					<ul class="footer-list footer-social">
						<li><a href="#"><span class="social-icon">📷</span> Instagram</a></li>
						<li><a href="#"><span class="social-icon">▶️</span> Homo Politicus</a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h4>Kontakt</h4>
					<ul class="footer-list">
						<li><a href="mailto:kontakt@vdb.schule">kontakt@vdb.schule</a></li>
						<li><a href="tel:+491234567890">+49 1234 567890</a></li>
						<li><a href="/kontakt">Kontaktformular</a></li>
						<li><a href="/anfahrt">Anfahrt</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<hr class="footer-divider--soft">

	<div class="footer-bottom">
		<div class="container container--wide footer-bottom-inner">
			<div class="footer-brand" onclick="window.location.href='/';">
				<img src="/assets/images/bildmarken/logo_icon.png" alt="Logo" class="footer-logo">
			</div>

			<div class="footer-meta">
				<h3 class="footer-org">Verband für Demokratiebildung und Bibliotheken an Schulen e.V.</h3>
				<p class="footer-tagline">Weil Schule uns alle angeht!</p>
			</div>

			<div class="footer-legal">
				<small>&copy; <?= date('Y') ?> <?= htmlspecialchars(getenv('APP_NAME') ?: 'App', ENT_QUOTES, 'UTF-8') ?></small>
			</div>
		</div>
	</div>
</footer>