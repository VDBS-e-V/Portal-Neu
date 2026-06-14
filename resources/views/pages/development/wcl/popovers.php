<?php declare(strict_types=1); ?>

<section id="popovers-uebersicht" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Übersicht: popover.css</h1>

            <p>
                Diese Seite zeigt das Popup-System für Dialoge, Alerts, Drawer,
                native Popover, Toasts, Tooltips und Consent-Banner.
            </p>

            <p>
                Grundregel: Für echte Modals verwendest du möglichst
                <code>&lt;dialog&gt;</code> mit <code>.popup</code> und einer
                <code>.popup__surface</code>. Für kleine kontextuelle Hinweise
                verwendest du <code>.popup-popover</code> mit dem nativen
                <code>popover</code>-Attribut.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="/styleguide/popovers/generator" class="btn btn--primary btn--md">
                Zum Popover Generator
            </a>

            <a href="#popovers-inhaltsverzeichnis" class="btn btn--primary-light btn--md">
                Zum Inhaltsverzeichnis
            </a>

            <a href="#popovers-entscheidungshilfe" class="btn btn--primary-transp btn--md">
                Zur Entscheidungshilfe
            </a>
        </div>
    </div>
</section>

<section id="popovers-inhaltsverzeichnis">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>Inhaltsverzeichnis</h2>

            <p>
                <a href="/styleguide/popovers/generator">Popover Generator öffnen</a><br>
                <a href="#popup-grundstruktur">1. Grundstruktur eines Dialogs</a><br>
                <a href="#popup-typen">2. Popup-Typen</a><br>
                <a href="#popup-groessen-positionen">3. Größen und Positionen</a><br>
                <a href="#popup-statusfarben">4. Status- und Farbvarianten</a><br>
                <a href="#popup-native-popover">5. Native Popover</a><br>
                <a href="#popup-toast">6. Toasts</a><br>
                <a href="#popup-tooltip">7. Tooltips</a><br>
                <a href="#popup-consent">8. Consent-Banner</a><br>
                <a href="#popup-barrierefreiheit">9. Barrierefreiheit</a><br>
                <a href="#popovers-entscheidungshilfe">10. Entscheidungshilfe</a>
            </p>
        </div>
    </div>
</section>

<section id="popover-generator-hinweis" class="section--surface">
    <div class="container container--text-buttons">
        <div class="container__text">
            <h2>Popover Generator</h2>

            <p>
                Mit dem Generator kannst du Dialoge, Popover, Toasts, Tooltips
                und Consent-Banner konfigurieren und den fertigen HTML-Code
                direkt übernehmen.
            </p>

            <p>
                Besonders hilfreich ist der Generator, wenn Typ, Größe, Status,
                Button-Aktionen und barrierefreie Attribute kombiniert werden sollen.
            </p>

            <p>
                Typische Kombination für ein Modal:
                <code>.popup</code>, <code>.popup--modal</code>, <code>.popup--md</code>
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm">
            <a href="/styleguide/popovers/generator" class="btn btn--primary btn--md">
                Popover Generator öffnen
            </a>

            <a href="#popup-grundstruktur" class="btn btn--primary-transp btn--md">
                Erst Grundlagen ansehen
            </a>
        </div>
    </div>
</section>

<section id="popup-grundstruktur" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>1. Grundstruktur eines Dialogs</h2>

            <p>
                Ein Dialog besteht aus dem äußeren <code>.popup</code>, einer
                sichtbaren <code>.popup__surface</code> und den Bereichen
                <code>.popup__header</code>, <code>.popup__body</code> und
                <code>.popup__footer</code>.
            </p>

            <h3>Native Dialog-Struktur</h3>

            <p>
                <button class="btn btn--primary btn--md" type="button" data-popup-open="demo-modal">
                    Beispiel-Dialog öffnen
                </button>
            </p>

            <dialog class="popup popup--modal popup--md popup--info" id="demo-modal" aria-labelledby="demo-modal-title">
                <div class="popup__surface">
                    <header class="popup__header">
                        <div>
                            <p class="popup__kicker">Hinweis</p>
                            <h2 class="popup__title" id="demo-modal-title">Dialogtitel</h2>
                        </div>

                        <form method="dialog">
                            <button class="popup__close" aria-label="Dialog schließen">
                                <svg class="vdb-icon" aria-hidden="true">
                                    <use href="/assets/icons/vdb-icons.svg#icon-close"></use>
                                </svg>
                            </button>
                        </form>
                    </header>

                    <div class="popup__body">
                        <p class="popup__text">
                            Hier steht der Inhalt des Dialogs. Nutze kurze,
                            klare Texte und eindeutige Aktionen.
                        </p>
                    </div>

                    <footer class="popup__footer">
                        <form method="dialog" class="btn-group btn-group--horizontal btn-group--main-end">
                            <button class="btn btn--primary-transp btn--md" value="cancel">Abbrechen</button>
                            <button class="btn btn--primary btn--md" value="confirm">Bestätigen</button>
                        </form>
                    </footer>
                </div>
            </dialog>

            <pre><code>&lt;dialog class="popup popup--modal popup--md popup--info" id="example-modal"&gt;
    &lt;div class="popup__surface"&gt;
        &lt;header class="popup__header"&gt;
            &lt;div&gt;
                &lt;p class="popup__kicker"&gt;Hinweis&lt;/p&gt;
                &lt;h2 class="popup__title"&gt;Dialogtitel&lt;/h2&gt;
            &lt;/div&gt;

            &lt;form method="dialog"&gt;
                &lt;button class="popup__close" aria-label="Dialog schließen"&gt;
                    ...
                &lt;/button&gt;
            &lt;/form&gt;
        &lt;/header&gt;

        &lt;div class="popup__body"&gt;
            &lt;p class="popup__text"&gt;Inhalt des Dialogs.&lt;/p&gt;
        &lt;/div&gt;

        &lt;footer class="popup__footer"&gt;
            ...
        &lt;/footer&gt;
    &lt;/div&gt;
&lt;/dialog&gt;</code></pre>

            <h3>Öffnen per JavaScript</h3>

            <pre><code>document.querySelector('#example-modal').showModal();</code></pre>
        </div>
    </div>
</section>

<section id="popup-typen">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>2. Popup-Typen</h2>

            <p>
                Die Typklassen bestimmen die Grundform: normales Modal,
                fokussierter Alert, Drawer, Fullscreen-Dialog oder Bottom-Sheet.
            </p>
        </div>
    </div>

    <div class="container container--wide">
        <div class="grid grid--3col grid--stretch">
            <article class="tile-card tile-card--no-media tile-card--surface">
                <div class="tile-card__body">
                    <p class="tile-card__meta">Standard</p>
                    <h3 class="tile-card__title">.popup--modal</h3>
                    <p class="tile-card__text">Für normale Dialogfenster mit Text, Formular oder Aktionen.</p>
                </div>
            </article>

            <article class="tile-card tile-card--no-media tile-card--surface">
                <div class="tile-card__body">
                    <p class="tile-card__meta">Kurz und wichtig</p>
                    <h3 class="tile-card__title">.popup--alert</h3>
                    <p class="tile-card__text">Für Bestätigungen, Warnungen und kurze Entscheidungen.</p>
                </div>
            </article>

            <article class="tile-card tile-card--no-media tile-card--surface">
                <div class="tile-card__body">
                    <p class="tile-card__meta">Seitenpanel</p>
                    <h3 class="tile-card__title">.popup--drawer</h3>
                    <p class="tile-card__text">Für Filter, Einstellungen, Navigation oder längere Panels.</p>
                </div>
            </article>

            <article class="tile-card tile-card--no-media tile-card--surface">
                <div class="tile-card__body">
                    <p class="tile-card__meta">Großes Interface</p>
                    <h3 class="tile-card__title">.popup--fullscreen</h3>
                    <p class="tile-card__text">Für umfangreiche Inhalte, die fast den ganzen Viewport nutzen.</p>
                </div>
            </article>

            <article class="tile-card tile-card--no-media tile-card--surface">
                <div class="tile-card__body">
                    <p class="tile-card__meta">Mobile Pattern</p>
                    <h3 class="tile-card__title">.popup--sheet</h3>
                    <p class="tile-card__text">Für Bottom-Sheet-artige Popups, vor allem auf kleineren Screens.</p>
                </div>
            </article>

            <article class="tile-card tile-card--no-media tile-card--surface">
                <div class="tile-card__body">
                    <p class="tile-card__meta">Kontext</p>
                    <h3 class="tile-card__title">.popup-popover</h3>
                    <p class="tile-card__text">Für kleine Hilfen, Menüs oder Zusatzinfos mit nativem Popover.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section id="popup-groessen-positionen" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>3. Größen und Positionen</h2>

            <p>
                Die Größenklassen setzen die maximale Breite der
                <code>.popup__surface</code>. Positionen und Drawer-Richtungen
                werden ergänzend gesetzt.
            </p>

            <h3>Größen</h3>

            <pre><code>&lt;dialog class="popup popup--modal popup--xs"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--modal popup--sm"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--modal popup--md"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--modal popup--lg"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--modal popup--xl"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--modal popup--full"&gt;...&lt;/dialog&gt;</code></pre>

            <h3>Positionen und Drawer</h3>

            <pre><code>&lt;dialog class="popup popup--modal popup--top"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--modal popup--bottom"&gt;...&lt;/dialog&gt;

&lt;dialog class="popup popup--drawer popup--drawer-right"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--drawer popup--drawer-left"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--drawer popup--drawer-top"&gt;...&lt;/dialog&gt;
&lt;dialog class="popup popup--drawer popup--drawer-bottom"&gt;...&lt;/dialog&gt;</code></pre>
        </div>
    </div>
</section>

<section id="popup-statusfarben">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>4. Status- und Farbvarianten</h2>

            <p>
                Statusklassen steuern Akzentlinie, Kicker-Farbe und Status-Icon.
                Nutze sie semantisch und nicht nur als Schmuckfarbe.
            </p>

            <div class="grid grid--2col grid--compact">
                <button class="btn btn--success btn--md" type="button" data-popup-open="popup-success-demo">Success</button>
                <button class="btn btn--error btn--md" type="button" data-popup-open="popup-error-demo">Error</button>
                <button class="btn btn--warning btn--md" type="button" data-popup-open="popup-warning-demo">Warning</button>
                <button class="btn btn--info btn--md" type="button" data-popup-open="popup-info-demo">Info</button>
            </div>

            <pre><code>.popup--primary
.popup--secondary-cta
.popup--secondary-highlight
.popup--plum
.popup--success
.popup--error
.popup--danger
.popup--warning
.popup--info
.popup--surface
.popup--ink</code></pre>
        </div>
    </div>

    <?php
        $statusDemos = [
            'popup-success-demo' => ['popup--success', 'icon-success', 'Success', 'Die Aktion wurde erfolgreich abgeschlossen.'],
            'popup-error-demo' => ['popup--error', 'icon-error', 'Error', 'Beim Speichern ist ein Fehler aufgetreten.'],
            'popup-warning-demo' => ['popup--warning', 'icon-warning', 'Warning', 'Bitte prüfe diese Entscheidung vor dem Fortfahren.'],
            'popup-info-demo' => ['popup--info', 'icon-info', 'Info', 'Hier steht eine ergänzende Information.'],
        ];
    ?>

    <?php foreach ($statusDemos as $id => [$statusClass, $icon, $title, $text]): ?>
        <dialog class="popup popup--alert <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" aria-labelledby="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>-title">
            <div class="popup__surface">
                <header class="popup__header">
                    <div class="popup__status-icon" aria-hidden="true">
                        <svg class="vdb-icon">
                            <use href="/assets/icons/vdb-icons.svg#<?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></use>
                        </svg>
                    </div>

                    <div>
                        <p class="popup__kicker">Status</p>
                        <h2 class="popup__title" id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>
                    </div>
                </header>

                <div class="popup__body">
                    <p class="popup__text"><?= htmlspecialchars($text, ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <footer class="popup__footer">
                    <form method="dialog" class="btn-group btn-group--horizontal btn-group--main-end">
                        <button class="btn btn--primary btn--md" value="ok">OK</button>
                    </form>
                </footer>
            </div>
        </dialog>
    <?php endforeach; ?>
</section>

<section id="popup-native-popover" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>5. Native Popover</h2>

            <p>
                Native Popover eignen sich für kleine kontextuelle Hinweise,
                Hilfetexte oder Menüs. Der Button verweist mit
                <code>popovertarget</code> auf das Popover-Element.
            </p>

            <p>
                <button class="btn btn--primary btn--md" type="button" popovertarget="help-popover-demo">
                    Hilfe anzeigen
                </button>
            </p>

            <div class="popup-popover popup-popover--info" id="help-popover-demo" popover>
                <div class="popup-popover__header">
                    <strong>Hinweis</strong>
                </div>

                <p>
                    Hier steht ein kurzer Hilfetext, der kontextuell eingeblendet wird.
                </p>
            </div>

            <pre><code>&lt;button class="btn btn--primary btn--md" popovertarget="help-popover"&gt;
    Hilfe anzeigen
&lt;/button&gt;

&lt;div class="popup-popover popup-popover--info" id="help-popover" popover&gt;
    &lt;div class="popup-popover__header"&gt;
        &lt;strong&gt;Hinweis&lt;/strong&gt;
    &lt;/div&gt;

    &lt;p&gt;Hier steht ein kurzer Hilfetext.&lt;/p&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>

<section id="popup-toast">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>6. Toasts</h2>

            <p>
                Toasts sind kurze Benachrichtigungen. Eine Seite braucht eine
                <code>.toast-region</code> mit <code>aria-live</code>, darin liegen
                einzelne <code>.toast</code>-Elemente.
            </p>

            <div class="toast toast--success is-visible" style="position: relative; max-width: 26rem;">
                <div class="toast__icon" aria-hidden="true">
                    <svg class="vdb-icon">
                        <use href="/assets/icons/vdb-icons.svg#icon-success"></use>
                    </svg>
                </div>

                <div class="toast__content">
                    <p class="toast__title">Gespeichert</p>
                    <p class="toast__text">Die Änderungen wurden erfolgreich gespeichert.</p>
                </div>

                <button class="toast__close" type="button" aria-label="Meldung schließen">×</button>
                <span class="toast__progress" aria-hidden="true"></span>
            </div>

            <pre><code>&lt;div class="toast-region toast-region--bottom-right" aria-live="polite" aria-label="Benachrichtigungen"&gt;
    &lt;div class="toast toast--success is-visible"&gt;
        &lt;div class="toast__icon" aria-hidden="true"&gt;...&lt;/div&gt;

        &lt;div class="toast__content"&gt;
            &lt;p class="toast__title"&gt;Gespeichert&lt;/p&gt;
            &lt;p class="toast__text"&gt;Die Änderungen wurden gespeichert.&lt;/p&gt;
        &lt;/div&gt;

        &lt;button class="toast__close" aria-label="Meldung schließen"&gt;×&lt;/button&gt;
        &lt;span class="toast__progress" aria-hidden="true"&gt;&lt;/span&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>

<section id="popup-tooltip" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>7. Tooltips</h2>

            <p>
                Tooltips werden über <code>data-tooltip</code> aktiviert.
                Sie sind für Zusatzinformationen geeignet, aber nicht für
                wichtige Inhalte, die ausschließlich im Tooltip stehen.
            </p>

            <div class="btn-group btn-group--horizontal btn-group--gap-sm">
                <button class="btn btn--primary btn--md" type="button" data-tooltip="Tooltip oberhalb des Buttons.">
                    Tooltip oben
                </button>

                <button class="btn btn--primary-light btn--md" type="button" data-tooltip="Tooltip rechts." data-tooltip-position="right">
                    Tooltip rechts
                </button>

                <button class="btn btn--primary-transp btn--md" type="button" data-tooltip="Tooltip unten." data-tooltip-position="bottom">
                    Tooltip unten
                </button>
            </div>

            <pre><code>&lt;button
    class="btn btn--primary btn--md"
    type="button"
    data-tooltip="Dieser Button öffnet das Serviceportal."
&gt;
    Serviceportal öffnen
&lt;/button&gt;</code></pre>
        </div>
    </div>
</section>

<section id="popup-consent">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>8. Consent-Banner</h2>

            <p>
                Consent-Banner nutzen <code>.popup-consent</code> und werden mit
                <code>.is-open</code> sichtbar gemacht. Für die Position gibt es
                <code>.popup-consent--bottom</code> und <code>.popup-consent--center</code>.
            </p>

            <div class="form__notice">
                <div class="popup-consent__content">
                    <div>
                        <h3 class="popup-consent__title">Datenschutzhinweis</h3>
                        <p class="popup-consent__text">
                            Wir verwenden notwendige Cookies, um diese Website bereitzustellen.
                        </p>
                    </div>

                    <div class="popup-consent__actions btn-group btn-group--horizontal btn-group--gap-sm">
                        <button class="btn btn--primary-transp btn--md" type="button">Einstellungen</button>
                        <button class="btn btn--primary btn--md" type="button">Verstanden</button>
                    </div>
                </div>
            </div>

            <pre><code>&lt;div class="popup-consent popup-consent--bottom is-open"&gt;
    &lt;div class="popup-consent__content"&gt;
        &lt;div&gt;
            &lt;h2 class="popup-consent__title"&gt;Datenschutzhinweis&lt;/h2&gt;
            &lt;p class="popup-consent__text"&gt;...&lt;/p&gt;
        &lt;/div&gt;

        &lt;div class="popup-consent__actions btn-group btn-group--horizontal"&gt;
            ...
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>

<section id="popup-barrierefreiheit" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>9. Barrierefreiheit</h2>

            <p>
                Für echte Modals ist <code>&lt;dialog&gt;</code> die bevorzugte
                Struktur. Öffne Modals mit <code>showModal()</code>, damit der
                Browser Fokus und Escape-Verhalten besser unterstützen kann.
            </p>

            <p>
                Dialoge brauchen eine klare Überschrift und nach Möglichkeit
                <code>aria-labelledby</code>. Close-Buttons und Icon-only-Buttons
                brauchen immer ein aussagekräftiges <code>aria-label</code>.
            </p>

            <p>
                Toast-Regionen sollten <code>aria-live="polite"</code> oder bei
                kritischen Fehlern <code>aria-live="assertive"</code> verwenden.
                Tooltips dürfen keine Informationen enthalten, die für die
                Bedienung zwingend erforderlich sind.
            </p>
        </div>
    </div>
</section>

<section id="popovers-entscheidungshilfe">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>10. Entscheidungshilfe</h2>

            <p>
                <code>.popup--modal</code><br>
                Für normale Dialoge, Formulare und mehrstufige Inhalte.
            </p>

            <p>
                <code>.popup--alert</code><br>
                Für kurze, wichtige Entscheidungen wie Bestätigen, Löschen oder Abbrechen.
            </p>

            <p>
                <code>.popup--drawer</code><br>
                Für Panels mit Navigation, Filtern oder Einstellungen.
            </p>

            <p>
                <code>.popup-popover</code><br>
                Für kleine kontextuelle Hinweise oder leichte Zusatzinfos.
            </p>

            <p>
                <code>.toast</code><br>
                Für nicht-blockierende Rückmeldungen nach einer Aktion.
            </p>

            <p>
                <code>[data-tooltip]</code><br>
                Für kurze Hilfetexte direkt an einem Element.
            </p>

            <p>
                <code>.popup-consent</code><br>
                Für Cookie-, Datenschutz- oder Consent-Hinweise.
            </p>
        </div>
    </div>
</section>

<script>
(function () {
    document.querySelectorAll('[data-popup-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.getAttribute('data-popup-open');
            const dialog = document.getElementById(id);
            if (!dialog) return;

            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            } else {
                dialog.setAttribute('open', 'open');
            }
        });
    });
})();
</script>
