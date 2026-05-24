<?php declare(strict_types=1); ?>
<footer class="footer site-footer" role="contentinfo">
    <hr class="footer-divider--thick">
	<div class="footer-top">
		<div class="container container--wide">
			<div class="footer-grid">
				<div class="footer-col">
					<h4>Informationen für</h4>
					<ul class="footer-list">
						<li><a href="#" class="link--no-style">Teamer:innen</a></li>
						<li><a href="#" class="link--no-style">Vereinsmitglieder:innen</a></li>
						<li><a href="#" class="link--no-style">Verwaltung</a></li>
						<li><a href="#" class="link--no-style">Schüler:innen</a></li>
						<li><a href="#" class="link--no-style">Lehrkräfte</a></li>
						<li><a href="#" class="link--no-style">Partner:innen</a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h4>Service-Portal</h4>
					<ul class="footer-list">
						<li><a href="/" class="link--no-style">Startseite</a></li>
						<li><a href="/kontakt" class="link--no-style">Kontakt</a></li>
						<li><a href="/impressum" class="link--no-style">Impressum</a></li>
						<li><a href="/datenschutz" class="link--no-style">Datenschutz</a></li>
						<li><a href="/barrierefreiheit" class="link--no-style">Barrierefreiheit</a></li>
						<li><a href="/beratung" class="link--no-style">Beratung</a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h4>Soziale Medien</h4>
					<ul class="footer-list footer-social">
						<li><a href="#" class="link--no-style"><span class="social-icon">📷</span> Instagram</a></li>
						<li><a href="#" class="link--no-style"><span class="social-icon">▶️</span> Homo Politicus</a></li>
					</ul>
				</div>

				<div class="footer-col">
					<h4>Kontakt</h4>
					<ul class="footer-list">
						<li><a href="mailto:kontakt@vdb.schule" class="link--no-style">kontakt@vdb.schule</a></li>
						<li><a href="tel:+491234567890" class="link--no-style">+49 1234 567890</a></li>
						<li><a href="/kontakt" class="link--no-style">Kontaktformular</a></li>
						<li><a href="/anfahrt" class="link--no-style">Anfahrt</a></li>
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