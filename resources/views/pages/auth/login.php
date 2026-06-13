<?php
$errors = $errors ?? [];
$email = (string) ($email ?? '');
$csrfToken = (string) ($csrfToken ?? '');
?>

<section class="section--surface">
    <div class="container container--text-only container--narrow">
        <form method="post" action="/login" class="form form--card" novalidate>
            <input
                type="hidden"
                name="_csrf"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
            >

            <div class="form__grid form__grid--1">
                <div class="form__title form__title--center">
                    <h1>Login</h1>
                    <p>Melden Sie sich mit Ihrer E-Mail-Adresse und Ihrem Passwort im VDBS Portal an.</p>
                </div>

                <?php if ($errors !== []): ?>
                    <div class="form__notice form__notice--error" role="alert">
                        <strong>Login fehlgeschlagen:</strong>

                        <?php foreach ($errors as $error): ?>
                            <p class="form__message form__message--error">
                                <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="form__field <?= $errors !== [] ? 'is-invalid' : '' ?>">
                    <label class="form__label" for="email">
                        E-Mail-Adresse <span class="form__required">*</span>
                    </label>

                    <input
                        class="form__control"
                        id="email"
                        name="email"
                        type="email"
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="email"
                        required
                        aria-required="true"
                        <?= $errors !== [] ? 'aria-invalid="true"' : '' ?>
                    >

                    <p class="form__hint">
                        Verwenden Sie die E-Mail-Adresse Ihres Portal-Zugangs.
                    </p>
                </div>

                <div class="form__field <?= $errors !== [] ? 'is-invalid' : '' ?>">
                    <label class="form__label" for="password">
                        Passwort <span class="form__required">*</span>
                    </label>

                    <input
                        class="form__control"
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        aria-required="true"
                        <?= $errors !== [] ? 'aria-invalid="true"' : '' ?>
                    >
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary btn--md" type="submit">
                        Einloggen
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>