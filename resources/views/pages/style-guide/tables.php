<?php declare(strict_types=1); ?>

<section id="tables-uebersicht" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Übersicht: tables.css</h1>

            <p>
                Diese Seite zeigt die wichtigsten Tabellen-Strukturen aus <code>tables.css</code>:
                Tabellenblöcke, Wrapper, Varianten, Toolbars, Status-Badges,
                sortierbare Spalten, Zellhelfer, responsive Card-Tabellen,
                Empty States und Pagination.
            </p>

            <p>
                Grundregel: Die Seite besteht aus <code>section</code>-Blöcken.
                Tabellenbereiche selbst werden mit <code>.table-block</code>,
                <code>.table-wrapper</code> und <code>.table</code> aufgebaut.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#tables-inhaltsverzeichnis" class="btn btn--primary btn--md">
                Zum Inhaltsverzeichnis
            </a>

            <a href="#tables-komplettbeispiel" class="btn btn--primary-light btn--md">
                Zum Kopierbeispiel
            </a>

            <a href="#tables-entscheidungshilfe" class="btn btn--primary-transp btn--md">
                Zur Entscheidungshilfe
            </a>
        </div>
    </div>
</section>


<section id="tables-inhaltsverzeichnis">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>Inhaltsverzeichnis</h2>

            <p>
                <a href="#tables-grundstruktur">1. Grundstruktur einer Tabelle</a><br>
                <a href="#tables-table-block">2. Table-Block mit Header und Aktionen</a><br>
                <a href="#tables-varianten">3. Tabellenvarianten</a><br>
                <a href="#tables-toolbar">4. Toolbar mit Suche und Filtern</a><br>
                <a href="#tables-badges">5. Status-Badges</a><br>
                <a href="#tables-sortierung">6. Sortierbare Spalten</a><br>
                <a href="#tables-zellen">7. Zellen-Helfer</a><br>
                <a href="#tables-responsive">8. Responsive Card-Tabelle</a><br>
                <a href="#tables-empty-pagination">9. Empty State und Pagination</a><br>
                <a href="#tables-komplettbeispiel">10. Komplettes Kopierbeispiel</a><br>
                <a href="#tables-entscheidungshilfe">11. Entscheidungshilfe</a>
            </p>
        </div>
    </div>
</section>


<section id="tables-grundstruktur" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>1. Grundstruktur einer Tabelle</h2>

            <p>
                Eine Tabelle besteht aus einem Wrapper und der eigentlichen
                Tabelle. Der Wrapper ist wichtig, weil breite Tabellen auf
                kleinen Screens horizontal scrollen können.
            </p>

            <pre><code>&lt;div class="table-wrapper"&gt;
    &lt;table class="table"&gt;
        &lt;thead&gt;
            &lt;tr&gt;
                &lt;th&gt;Name&lt;/th&gt;
                &lt;th&gt;Status&lt;/th&gt;
                &lt;th&gt;Aktion&lt;/th&gt;
            &lt;/tr&gt;
        &lt;/thead&gt;

        &lt;tbody&gt;
            &lt;tr&gt;
                &lt;td&gt;Max Mustermann&lt;/td&gt;
                &lt;td&gt;Aktiv&lt;/td&gt;
                &lt;td&gt;
                    &lt;a href="#" class="btn btn--primary btn--sm"&gt;
                        Öffnen
                    &lt;/a&gt;
                &lt;/td&gt;
            &lt;/tr&gt;
        &lt;/tbody&gt;
    &lt;/table&gt;
&lt;/div&gt;</code></pre>

            <h3>Wichtige Basisklassen</h3>

            <p>
                <code>.table-wrapper</code><br>
                Äußerer Scroll-Container für die Tabelle.
            </p>

            <p>
                <code>.table</code><br>
                Grundklasse für alle Datentabellen.
            </p>

            <p>
                <code>.table-wrapper--bordered</code><br>
                Ergänzt einen Rahmen um den Tabellenwrapper.
            </p>

            <p>
                <code>.table-wrapper--surface</code><br>
                Nutzt eine Surface-Hintergrundfläche.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>E-Mail</th>
                        <th>Status</th>
                        <th class="table__cell--actions">Aktion</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Max Mustermann</td>
                        <td>max@example.org</td>
                        <td>Aktiv</td>
                        <td class="table__cell--actions">
                            <a href="#" class="btn btn--primary btn--sm">Öffnen</a>
                        </td>
                    </tr>

                    <tr>
                        <td>Erika Musterfrau</td>
                        <td>erika@example.org</td>
                        <td>Inaktiv</td>
                        <td class="table__cell--actions">
                            <a href="#" class="btn btn--primary-light btn--sm">Ansehen</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section id="tables-table-block">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>2. Table-Block mit Header und Aktionen</h2>

            <p>
                Für Tabellen mit Überschrift, Beschreibung und Aktionsbuttons
                nutzt du <code>.table-block</code>. Der Header enthält Titel,
                Untertitel und Aktionen.
            </p>

            <pre><code>&lt;div class="table-block"&gt;
    &lt;div class="table-block__header"&gt;
        &lt;div&gt;
            &lt;p class="table-block__kicker"&gt;Verwaltung&lt;/p&gt;
            &lt;h2 class="table-block__title"&gt;Mitglieder&lt;/h2&gt;
            &lt;p class="table-block__subtitle"&gt;
                Übersicht aller Mitglieder.
            &lt;/p&gt;
        &lt;/div&gt;

        &lt;div class="table-block__actions btn-group btn-group--horizontal"&gt;
            &lt;a href="#" class="btn btn--primary btn--md"&gt;
                Neues Mitglied
            &lt;/a&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;div class="table-wrapper"&gt;
        ...
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <p class="table-block__kicker">Verwaltung</p>
                    <h2 class="table-block__title">Mitglieder</h2>
                    <p class="table-block__subtitle">
                        Übersicht aller aktuell registrierten Mitglieder.
                    </p>
                </div>

                <div class="table-block__actions btn-group btn-group--horizontal btn-group--gap-sm">
                    <a href="#" class="btn btn--primary btn--md">Neues Mitglied</a>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="table table--striped table--hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Rolle</th>
                            <th>Status</th>
                            <th class="table__cell--actions">Aktion</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <strong>Max Mustermann</strong>
                                <span class="table__subtext">max@example.org</span>
                            </td>
                            <td>Mitglied</td>
                            <td>
                                <span class="table-badge table-badge--success">Aktiv</span>
                            </td>
                            <td class="table__cell--actions">
                                <a href="#" class="btn btn--primary-light btn--sm">Öffnen</a>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Erika Musterfrau</strong>
                                <span class="table__subtext">erika@example.org</span>
                            </td>
                            <td>Team</td>
                            <td>
                                <span class="table-badge table-badge--warning">Prüfung</span>
                            </td>
                            <td class="table__cell--actions">
                                <a href="#" class="btn btn--primary-light btn--sm">Öffnen</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<section id="tables-varianten">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>3. Tabellenvarianten</h2>

            <p>
                Tabellenvarianten werden direkt auf <code>.table</code> gesetzt.
                Sie können kombiniert werden.
            </p>

            <p>
                <code>.table--striped</code> — jede zweite Zeile dezent hinterlegt<br>
                <code>.table--hover</code> — Hover-Hervorhebung für Zeilen<br>
                <code>.table--bordered</code> — Rahmen um Zellen<br>
                <code>.table--compact</code> — reduzierte Abstände<br>
                <code>.table--comfortable</code> — größere Abstände<br>
                <code>.table--fixed</code> — feste Tabellenlayout-Logik<br>
                <code>.table--no-wrap</code> — verhindert Zeilenumbrüche<br>
                <code>.table--dense-header</code> — kompakter Tabellenkopf
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--compact table--bordered table--hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titel</th>
                        <th>Erstellt am</th>
                        <th>Status</th>
                        <th class="table__cell--actions">Aktion</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="table__cell--muted">#1024</td>
                        <td>Supportanfrage</td>
                        <td>10.06.2026</td>
                        <td>
                            <span class="table-badge table-badge--warning">Offen</span>
                        </td>
                        <td class="table__cell--actions">
                            <a href="#" class="btn btn--primary btn--sm">Bearbeiten</a>
                        </td>
                    </tr>

                    <tr>
                        <td class="table__cell--muted">#1025</td>
                        <td>Mitgliedsantrag</td>
                        <td>11.06.2026</td>
                        <td>
                            <span class="table-badge table-badge--success">Erledigt</span>
                        </td>
                        <td class="table__cell--actions">
                            <a href="#" class="btn btn--primary-light btn--sm">Ansehen</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section id="tables-toolbar">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>4. Toolbar mit Suche und Filtern</h2>

            <p>
                Eine <code>.table-toolbar</code> liegt oberhalb der Tabelle.
                Sie eignet sich für Suche, Filter oder kleine Aktionen.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <h2 class="table-block__title">Toolbar-Beispiel</h2>
                    <p class="table-block__subtitle">
                        Tabelle mit Suche und Filterbuttons.
                    </p>
                </div>
            </div>

            <div class="table-toolbar">
                <div class="table-toolbar__search">
                    <label class="visually-hidden" for="tables-toolbar-search">Suche</label>
                    <input class="form__control" id="tables-toolbar-search" type="search" placeholder="Tabelle durchsuchen">
                </div>

                <div class="table-toolbar__filters btn-group btn-group--horizontal btn-group--gap-sm">
                    <button class="btn btn--primary-light btn--sm" type="button">Alle</button>
                    <button class="btn btn--primary-transp btn--sm" type="button">Aktiv</button>
                    <button class="btn btn--primary-transp btn--sm" type="button">Archiviert</button>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="table table--striped table--hover">
                    <thead>
                        <tr>
                            <th>Bereich</th>
                            <th>Status</th>
                            <th class="table__cell--right">Einträge</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Mitglieder</td>
                            <td><span class="table-badge table-badge--success">Aktiv</span></td>
                            <td class="table__cell--right">128</td>
                        </tr>

                        <tr>
                            <td>Archiv</td>
                            <td><span class="table-badge table-badge--neutral">Archiviert</span></td>
                            <td class="table__cell--right">312</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<section id="tables-badges">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>5. Status-Badges</h2>

            <p>
                Badges markieren Statuswerte innerhalb von Tabellenzellen.
                Sie sollten semantisch eingesetzt werden und nicht nur als Schmuckfarbe.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--comfortable">
                <thead>
                    <tr>
                        <th>Badge</th>
                        <th>Klasse</th>
                        <th>Verwendung</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td><span class="table-badge table-badge--primary">Primary</span></td>
                        <td><code>.table-badge--primary</code></td>
                        <td>Hauptstatus oder wichtige Markierung</td>
                    </tr>

                    <tr>
                        <td><span class="table-badge table-badge--secondary-cta">CTA</span></td>
                        <td><code>.table-badge--secondary-cta</code></td>
                        <td>Handlungsorientierter Status</td>
                    </tr>

                    <tr>
                        <td><span class="table-badge table-badge--secondary-highlight">Highlight</span></td>
                        <td><code>.table-badge--secondary-highlight</code></td>
                        <td>Starke Hervorhebung</td>
                    </tr>

                    <tr>
                        <td><span class="table-badge table-badge--success">Aktiv</span></td>
                        <td><code>.table-badge--success</code></td>
                        <td>Erfolg, aktiv, bestätigt</td>
                    </tr>

                    <tr>
                        <td><span class="table-badge table-badge--warning">Prüfung</span></td>
                        <td><code>.table-badge--warning</code></td>
                        <td>Offen, Prüfung, ausstehend</td>
                    </tr>

                    <tr>
                        <td><span class="table-badge table-badge--error">Fehler</span></td>
                        <td><code>.table-badge--error</code></td>
                        <td>Fehler, gesperrt, abgelehnt</td>
                    </tr>

                    <tr>
                        <td><span class="table-badge table-badge--info">Info</span></td>
                        <td><code>.table-badge--info</code></td>
                        <td>Hinweis oder Bearbeitungsstatus</td>
                    </tr>

                    <tr>
                        <td><span class="table-badge table-badge--neutral">Archiv</span></td>
                        <td><code>.table-badge--neutral</code></td>
                        <td>Archiviert, inaktiv, Entwurf</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section id="tables-sortierung">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>6. Sortierbare Spalten</h2>

            <p>
                Sortierbare Spalten nutzen im Tabellenkopf einen
                <code>button.table-sort</code>. Die aktive Sortierung wird mit
                <code>.is-active</code> markiert. Die Richtung steuerst du mit
                <code>.table-sort--asc</code> oder <code>.table-sort--desc</code>.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--hover">
                <thead>
                    <tr>
                        <th>
                            <button class="table-sort table-sort--asc is-active" type="button">
                                Name
                            </button>
                        </th>

                        <th>
                            <button class="table-sort" type="button">
                                Rolle
                            </button>
                        </th>

                        <th>
                            <button class="table-sort table-sort--desc" type="button">
                                Datum
                            </button>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Max Mustermann</td>
                        <td>Admin</td>
                        <td>10.06.2026</td>
                    </tr>

                    <tr>
                        <td>Erika Musterfrau</td>
                        <td>Mitglied</td>
                        <td>11.06.2026</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section id="tables-zellen">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>7. Zellen-Helfer</h2>

            <p>
                Zellen-Helfer steuern Ausrichtung, Breite und Gewichtung einzelner
                Tabellenzellen.
            </p>

            <p>
                <code>.table__cell--right</code> — rechtsbündig, gut für Zahlen<br>
                <code>.table__cell--center</code> — zentriert<br>
                <code>.table__cell--nowrap</code> — kein Umbruch<br>
                <code>.table__cell--muted</code> — zurückhaltender Text<br>
                <code>.table__cell--strong</code> — hervorgehobener Text<br>
                <code>.table__cell--actions</code> — schmale Aktionsspalte<br>
                <code>.table__cell--min</code> — minimale Breite<br>
                <code>.table__cell--sm</code>, <code>.table__cell--md</code>, <code>.table__cell--lg</code> — feste Breiten<br>
                <code>.table__subtext</code> — kleine Zusatzzeile in einer Zelle
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped">
                <thead>
                    <tr>
                        <th>Bereich</th>
                        <th class="table__cell--right">Anzahl</th>
                        <th class="table__cell--right">Anteil</th>
                        <th class="table__cell--actions">Aktion</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <strong>Mitglieder</strong>
                            <span class="table__subtext">Registrierte Nutzer:innen</span>
                        </td>
                        <td class="table__cell--right">128</td>
                        <td class="table__cell--right">42 %</td>
                        <td class="table__cell--actions">
                            <a href="#" class="btn btn--primary btn--sm">Öffnen</a>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Support</strong>
                            <span class="table__subtext">Offene und geschlossene Tickets</span>
                        </td>
                        <td class="table__cell--right">42</td>
                        <td class="table__cell--right">14 %</td>
                        <td class="table__cell--actions">
                            <a href="#" class="btn btn--primary btn--sm">Öffnen</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section id="tables-responsive">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>8. Responsive Card-Tabelle</h2>

            <p>
                Für mobile Darstellungen nutzt du <code>.table--stack</code>.
                Wichtig: Jedes <code>td</code> braucht dann ein
                <code>data-label</code>, damit auf kleinen Screens die
                Feldbezeichnung angezeigt werden kann.
            </p>

            <pre><code>&lt;td data-label="Name"&gt;Max Mustermann&lt;/td&gt;
&lt;td data-label="E-Mail"&gt;max@example.org&lt;/td&gt;
&lt;td data-label="Status"&gt;
    &lt;span class="table-badge table-badge--success"&gt;Aktiv&lt;/span&gt;
&lt;/td&gt;</code></pre>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-wrapper">
            <table class="table table--stack table--striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>E-Mail</th>
                        <th>Status</th>
                        <th>Aktion</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td data-label="Name">Max Mustermann</td>
                        <td data-label="E-Mail">max@example.org</td>
                        <td data-label="Status">
                            <span class="table-badge table-badge--success">Aktiv</span>
                        </td>
                        <td data-label="Aktion">
                            <a href="#" class="btn btn--primary btn--sm">Öffnen</a>
                        </td>
                    </tr>

                    <tr>
                        <td data-label="Name">Erika Musterfrau</td>
                        <td data-label="E-Mail">erika@example.org</td>
                        <td data-label="Status">
                            <span class="table-badge table-badge--neutral">Archiviert</span>
                        </td>
                        <td data-label="Aktion">
                            <a href="#" class="btn btn--primary-light btn--sm">Ansehen</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section id="tables-empty-pagination">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>9. Empty State und Pagination</h2>

            <p>
                Wenn keine Daten vorhanden sind, nutzt du <code>.table-empty</code>.
                Für Seitennavigation unter der Tabelle nutzt du
                <code>.table-pagination</code>.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-empty">
                <h3 class="table-empty__title">Keine Einträge gefunden</h3>
                <p class="table-empty__text">
                    Passe die Filter an oder erstelle einen neuen Eintrag.
                </p>

                <div class="btn-group btn-group--horizontal btn-group--gap-sm">
                    <a href="#" class="btn btn--primary btn--md">Neu erstellen</a>
                    <a href="#" class="btn btn--primary-transp btn--md">Filter zurücksetzen</a>
                </div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-wrapper">
                <table class="table table--striped">
                    <thead>
                        <tr>
                            <th>Eintrag</th>
                            <th>Status</th>
                            <th class="table__cell--actions">Aktion</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Eintrag 1</td>
                            <td><span class="table-badge table-badge--success">Aktiv</span></td>
                            <td class="table__cell--actions">
                                <a href="#" class="btn btn--primary btn--sm">Öffnen</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-pagination">
                <p class="table-pagination__info">
                    Zeige 1–1 von 24 Einträgen
                </p>

                <div class="table-pagination__actions btn-group btn-group--horizontal btn-group--gap-sm">
                    <button class="btn btn--primary-transp btn--sm" type="button">Zurück</button>
                    <button class="btn btn--primary btn--sm" type="button">Weiter</button>
                </div>
            </div>
        </div>
    </div>
</section>


<section id="tables-komplettbeispiel">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h2>10. Komplettes Kopierbeispiel</h2>

            <p>
                Die ausführliche Tabellenvorlage liegt als eingeklappter Codeblock vor.
                So bleibt die Übersichtsseite kurz, aber du kannst bei Bedarf einen
                vollständigen Tabellenbereich mit allen wichtigen Funktionen kopieren.
            </p>
        </div>
    </div>
</section>


<section class="section--surface long-width">
    <div class="container container--text-only">
        <div class="container__text">
            <h3>Tabellen-Kopiervorlage als Code</h3>

            <p>
                Enthalten sind Table-Block, Header, Kicker, Toolbar, Suche, Filter,
                sortierbare Spalten, Badges, Zellhelfer, Subtexte, Aktionsspalte,
                <code>tfoot</code>, responsive <code>data-label</code>-Werte,
                Pagination und Empty State.
            </p>

            <div class="form__actions">
                <button class="btn btn--primary btn--md" type="button" data-copy-template="#tables-copy-template-code">
                    Tabellen-Code kopieren
                </button>
            </div>

            <details>
                <summary>
                    <span class="btn btn--primary-light btn--md">Kopiervorlage anzeigen</span>
                </summary>

                <pre><code id="tables-copy-template-code">&lt;div class="table-block table-block--surface"&gt;
            &lt;div class="table-block__header"&gt;
                &lt;div&gt;
                    &lt;p class="table-block__kicker"&gt;Verwaltung&lt;/p&gt;
                    &lt;h2 class="table-block__title"&gt;Ausführliche Tabellen-Kopiervorlage&lt;/h2&gt;
                    &lt;p class="table-block__subtitle"&gt;
                        Dieses Beispiel zeigt einen vollständigen Tabellenbereich mit Suche,
                        Filtern, Sortierung, Status, Zellvarianten, Aktionen und Pagination.
                    &lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="table-block__actions btn-group btn-group--horizontal btn-group--gap-sm"&gt;
                    &lt;a href="#" class="btn btn--primary btn--md"&gt;Neu erstellen&lt;/a&gt;
                    &lt;a href="#" class="btn btn--primary-light btn--md"&gt;Exportieren&lt;/a&gt;
                    &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;Archiv öffnen&lt;/a&gt;
                &lt;/div&gt;
            &lt;/div&gt;

            &lt;div class="table-toolbar"&gt;
                &lt;div class="table-toolbar__search"&gt;
                    &lt;label class="visually-hidden" for="tables-copy-search"&gt;Tabelle durchsuchen&lt;/label&gt;
                    &lt;input class="form__control" id="tables-copy-search" type="search" placeholder="Tabelle durchsuchen"&gt;
                &lt;/div&gt;

                &lt;div class="table-toolbar__filters btn-group btn-group--horizontal btn-group--gap-sm"&gt;
                    &lt;button class="btn btn--primary-light btn--sm" type="button"&gt;Alle&lt;/button&gt;
                    &lt;button class="btn btn--primary-transp btn--sm" type="button"&gt;Aktiv&lt;/button&gt;
                    &lt;button class="btn btn--primary-transp btn--sm" type="button"&gt;Prüfung&lt;/button&gt;
                    &lt;button class="btn btn--primary-transp btn--sm" type="button"&gt;Archiviert&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;

            &lt;div class="table-wrapper table-wrapper--bordered"&gt;
                &lt;table class="table table--striped table--hover table--stack table--compact table--dense-header"&gt;
                    &lt;caption&gt;
                        Kopiervorlage für eine umfangreiche Verwaltungsübersicht.
                    &lt;/caption&gt;

                    &lt;thead&gt;
                        &lt;tr&gt;
                            &lt;th class="table__cell--min"&gt;
                                &lt;button class="table-sort table-sort--asc is-active" type="button"&gt;
                                    ID
                                &lt;/button&gt;
                            &lt;/th&gt;

                            &lt;th&gt;
                                &lt;button class="table-sort" type="button"&gt;
                                    Bereich
                                &lt;/button&gt;
                            &lt;/th&gt;

                            &lt;th class="table__cell--md"&gt;Verantwortung&lt;/th&gt;

                            &lt;th class="table__cell--right"&gt;
                                &lt;button class="table-sort table-sort--desc" type="button"&gt;
                                    Einträge
                                &lt;/button&gt;
                            &lt;/th&gt;

                            &lt;th class="table__cell--center"&gt;Priorität&lt;/th&gt;
                            &lt;th&gt;Status&lt;/th&gt;
                            &lt;th class="table__cell--nowrap"&gt;Letzte Änderung&lt;/th&gt;
                            &lt;th class="table__cell--actions"&gt;Aktionen&lt;/th&gt;
                        &lt;/tr&gt;
                    &lt;/thead&gt;

                    &lt;tbody&gt;
                        &lt;tr&gt;
                            &lt;td data-label="ID" class="table__cell--muted table__cell--nowrap"&gt;#1001&lt;/td&gt;

                            &lt;td data-label="Bereich" class="table__cell--strong"&gt;
                                Mitglieder
                                &lt;span class="table__subtext"&gt;Mitgliederverwaltung und Rollenpflege&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Verantwortung"&gt;Geschäftsstelle&lt;/td&gt;

                            &lt;td data-label="Einträge" class="table__cell--right"&gt;128&lt;/td&gt;

                            &lt;td data-label="Priorität" class="table__cell--center"&gt;
                                &lt;span class="table-badge table-badge--primary"&gt;Hoch&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Status"&gt;
                                &lt;span class="table-badge table-badge--success"&gt;Aktiv&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Letzte Änderung" class="table__cell--nowrap"&gt;10.06.2026&lt;/td&gt;

                            &lt;td data-label="Aktionen" class="table__cell--actions"&gt;
                                &lt;div class="btn-group btn-group--horizontal btn-group--gap-xs"&gt;
                                    &lt;a href="#" class="btn btn--primary btn--sm"&gt;Öffnen&lt;/a&gt;
                                    &lt;a href="#" class="btn btn--primary-light btn--sm"&gt;Bearbeiten&lt;/a&gt;
                                &lt;/div&gt;
                            &lt;/td&gt;
                        &lt;/tr&gt;

                        &lt;tr&gt;
                            &lt;td data-label="ID" class="table__cell--muted table__cell--nowrap"&gt;#1002&lt;/td&gt;

                            &lt;td data-label="Bereich" class="table__cell--strong"&gt;
                                Support
                                &lt;span class="table__subtext"&gt;Anfragen, Tickets und Rückmeldungen&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Verantwortung"&gt;Portalteam&lt;/td&gt;

                            &lt;td data-label="Einträge" class="table__cell--right"&gt;42&lt;/td&gt;

                            &lt;td data-label="Priorität" class="table__cell--center"&gt;
                                &lt;span class="table-badge table-badge--secondary-cta"&gt;Mittel&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Status"&gt;
                                &lt;span class="table-badge table-badge--warning"&gt;Prüfung&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Letzte Änderung" class="table__cell--nowrap"&gt;09.06.2026&lt;/td&gt;

                            &lt;td data-label="Aktionen" class="table__cell--actions"&gt;
                                &lt;div class="btn-group btn-group--horizontal btn-group--gap-xs"&gt;
                                    &lt;a href="#" class="btn btn--primary btn--sm"&gt;Öffnen&lt;/a&gt;
                                    &lt;a href="#" class="btn btn--primary-transp btn--sm"&gt;Zuweisen&lt;/a&gt;
                                &lt;/div&gt;
                            &lt;/td&gt;
                        &lt;/tr&gt;

                        &lt;tr&gt;
                            &lt;td data-label="ID" class="table__cell--muted table__cell--nowrap"&gt;#1003&lt;/td&gt;

                            &lt;td data-label="Bereich" class="table__cell--strong"&gt;
                                Veranstaltungen
                                &lt;span class="table__subtext"&gt;Termine, Anmeldungen und Räume&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Verantwortung"&gt;Bildungsteam&lt;/td&gt;

                            &lt;td data-label="Einträge" class="table__cell--right"&gt;18&lt;/td&gt;

                            &lt;td data-label="Priorität" class="table__cell--center"&gt;
                                &lt;span class="table-badge table-badge--secondary-highlight"&gt;Termin&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Status"&gt;
                                &lt;span class="table-badge table-badge--info"&gt;In Bearbeitung&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Letzte Änderung" class="table__cell--nowrap"&gt;08.06.2026&lt;/td&gt;

                            &lt;td data-label="Aktionen" class="table__cell--actions"&gt;
                                &lt;a href="#" class="btn btn--primary-light btn--sm"&gt;Ansehen&lt;/a&gt;
                            &lt;/td&gt;
                        &lt;/tr&gt;

                        &lt;tr&gt;
                            &lt;td data-label="ID" class="table__cell--muted table__cell--nowrap"&gt;#1004&lt;/td&gt;

                            &lt;td data-label="Bereich" class="table__cell--strong"&gt;
                                Dateien
                                &lt;span class="table__subtext"&gt;Uploads, Materialien und Freigaben&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Verantwortung"&gt;Redaktion&lt;/td&gt;

                            &lt;td data-label="Einträge" class="table__cell--right"&gt;76&lt;/td&gt;

                            &lt;td data-label="Priorität" class="table__cell--center"&gt;
                                &lt;span class="table-badge table-badge--neutral"&gt;Normal&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Status"&gt;
                                &lt;span class="table-badge table-badge--error"&gt;Fehler&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Letzte Änderung" class="table__cell--nowrap"&gt;07.06.2026&lt;/td&gt;

                            &lt;td data-label="Aktionen" class="table__cell--actions"&gt;
                                &lt;div class="btn-group btn-group--horizontal btn-group--gap-xs"&gt;
                                    &lt;a href="#" class="btn btn--danger btn--sm"&gt;Prüfen&lt;/a&gt;
                                    &lt;a href="#" class="btn btn--ghost btn--sm"&gt;Log&lt;/a&gt;
                                &lt;/div&gt;
                            &lt;/td&gt;
                        &lt;/tr&gt;

                        &lt;tr&gt;
                            &lt;td data-label="ID" class="table__cell--muted table__cell--nowrap"&gt;#1005&lt;/td&gt;

                            &lt;td data-label="Bereich" class="table__cell--strong"&gt;
                                Archiv
                                &lt;span class="table__subtext"&gt;Alte Vorgänge und abgeschlossene Einträge&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Verantwortung"&gt;Verwaltung&lt;/td&gt;

                            &lt;td data-label="Einträge" class="table__cell--right"&gt;312&lt;/td&gt;

                            &lt;td data-label="Priorität" class="table__cell--center"&gt;
                                &lt;span class="table-badge table-badge--plum"&gt;Ablage&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Status"&gt;
                                &lt;span class="table-badge table-badge--neutral"&gt;Archiviert&lt;/span&gt;
                            &lt;/td&gt;

                            &lt;td data-label="Letzte Änderung" class="table__cell--nowrap"&gt;01.06.2026&lt;/td&gt;

                            &lt;td data-label="Aktionen" class="table__cell--actions"&gt;
                                &lt;a href="#" class="btn btn--outline btn--sm"&gt;Ansehen&lt;/a&gt;
                            &lt;/td&gt;
                        &lt;/tr&gt;
                    &lt;/tbody&gt;

                    &lt;tfoot&gt;
                        &lt;tr&gt;
                            &lt;th colspan="3"&gt;Gesamt&lt;/th&gt;
                            &lt;td class="table__cell--right"&gt;576&lt;/td&gt;
                            &lt;td class="table__cell--center" colspan="2"&gt;5 Bereiche&lt;/td&gt;
                            &lt;td colspan="2" class="table__cell--actions"&gt;Stand: 11.06.2026&lt;/td&gt;
                        &lt;/tr&gt;
                    &lt;/tfoot&gt;
                &lt;/table&gt;
            &lt;/div&gt;

            &lt;div class="table-pagination"&gt;
                &lt;p class="table-pagination__info"&gt;
                    Zeige 1–5 von 576 Einträgen
                &lt;/p&gt;

                &lt;div class="table-pagination__actions btn-group btn-group--horizontal btn-group--gap-sm"&gt;
                    &lt;button class="btn btn--primary-transp btn--sm" type="button"&gt;
                        Zurück
                    &lt;/button&gt;

                    &lt;button class="btn btn--primary btn--sm" type="button"&gt;
                        Weiter
                    &lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;</code></pre>
            </details>
        </div>
    </div>

    <script>
    (function () {
        document.querySelectorAll('[data-copy-template]').forEach(function (button) {
            button.addEventListener('click', function () {
                var targetSelector = button.getAttribute('data-copy-template');
                var target = document.querySelector(targetSelector);

                if (!target) {
                    return;
                }

                navigator.clipboard.writeText(target.textContent).then(function () {
                    var originalText = button.textContent;
                    button.textContent = 'Code kopiert';

                    window.setTimeout(function () {
                        button.textContent = originalText;
                    }, 1500);
                });
            });
        });
    })();
    </script>
</section>


<section id="tables-entscheidungshilfe">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>11. Entscheidungshilfe</h2>

            <h3>Einfache Tabelle</h3>
            <pre><code>&lt;div class="table-wrapper"&gt;
    &lt;table class="table"&gt;
        ...
    &lt;/table&gt;
&lt;/div&gt;</code></pre>

            <h3>Lesbare Datentabelle</h3>
            <pre><code>&lt;table class="table table--striped table--hover"&gt;
    ...
&lt;/table&gt;</code></pre>

            <h3>Kompakte Verwaltungstabelle</h3>
            <pre><code>&lt;table class="table table--compact table--bordered table--hover"&gt;
    ...
&lt;/table&gt;</code></pre>

            <h3>Tabelle mit Titel und Aktionen</h3>
            <pre><code>&lt;div class="table-block table-block--card"&gt;
    &lt;div class="table-block__header"&gt;...&lt;/div&gt;
    &lt;div class="table-wrapper"&gt;...&lt;/div&gt;
&lt;/div&gt;</code></pre>

            <h3>Responsive Tabelle</h3>
            <pre><code>&lt;table class="table table--stack table--striped"&gt;
    &lt;td data-label="Name"&gt;Max Mustermann&lt;/td&gt;
&lt;/table&gt;</code></pre>

            <h3>Status in Tabellen</h3>
            <pre><code>&lt;span class="table-badge table-badge--success"&gt;
    Aktiv
&lt;/span&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#tables-uebersicht" class="btn btn--primary btn--md">
                Zurück nach oben
            </a>
        </div>
    </div>
</section>