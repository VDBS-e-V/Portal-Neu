<?php declare(strict_types=1); ?>

<form action="">
    <div class="form_container">

        <div class="form_title">
            <h2>Beispiel Formular</h2>
        </div>

        <div class="form_section_title coolor__primary">
            <h4>Text Eingaben</h4>
        </div>

        <div class="form_item">
            <label for="input-text">Input Text</label>
            <input type="text" id="input-text" name="input-text">
        </div>

        <div class="form_item">
            <label for="input-email">Input E-Mail</label>
            <input type="email" id="input-email" name="input-email">
        </div>

        <div class="form_item">
            <label for="input-password">Input Passwort</label>
            <input type="password" id="input-password" name="input-password">
        </div>

        <div class="form_item">
            <label for="input-search">Input Suche</label>
            <input type="search" id="input-search" name="input-search">
        </div>

        <div class="form_item">
            <label for="input-url">Input URL</label>
            <input type="url" id="input-url" name="input-urlurl">
        </div>


        <div class="form_section_title coolor__secondary_cta">
            <h4>Nummer Eingaben</h4>
        </div>

        <div class="form_item">
            <label for="input-number">Input Nummern</label>
            <input type="number" id="input-number" name="input-number">
        </div>

        <div class="form_item">
            <label for="input-tel">Input Telefon</label>
            <input type="tel" id="input-tel" name="input-tel">
        </div>
        

        <div class="form_section_title coolor__secondary_highlight">
            <h4>Zeitpunkt Eingaben</h4>
        </div>

        <div class="form_item">
            <label for="input-date">Input Datum</label>
            <input type="date" id="input-date" name="input-date">
        </div>        

        <div class="form_item">
            <label for="input-datetime-local">Input Zeitstempel</label>
            <input type="datetime-local" id="input-datetime-local" name="input-datetime-local">
        </div>

        <div class="form_item">
            <label for="input-month">Input Monat</label>
            <input type="month" id="input-month" name="input-month">
        </div>

        <div class="form_item">
            <label for="input-time">Input Zeit</label>
            <input type="time" id="input-time" name="input-time">
        </div>

        <div class="form_item">
            <label for="input-week">Input Week</label>
            <input type="week" id="input-week" name="input-week">
        </div>

        <div class="form_section_title coolor__secondary_highlight">
            <h4>Weitere Eingaben</h4>
        </div>

        <div class="form_item">
            <label for="">Textarea</label>
            <textarea name="" id=""></textarea>
        </div>

        <div class="form_item">
            <label for="">Select</label>
            <select name="" id="">
                <option value="option-1">Option 1</option>
                <option value="option-2">Option 2</option>
                <option value="option-3">Option 3</option>
                <option value="option-4">Option 4</option>
            </select>
        </div>

        <div class="form_item">
            <label for="">Select Multi</label>
            <select name="" id="" multiple>
                <option value="option-1">Option 1</option>
                <option value="option-2">Option 2</option>
                <option value="option-3">Option 3</option>
                <option value="option-4">Option 4</option>
            </select>
        </div>

        <div class="form_item">
            <label for="">Datalist</label>
            <input type="text" list="example">
            <datalist id="example">
                <option value="option-1">Option 1</option>
                <option value="option-2">Option 2</option>
                <option value="option-3">Option 3</option>
                <option value="option-4">Option 4</option>
            </datalist>
        </div>

        <div class="form_item span__full">
            <label for="">TinyMCE</label>
            <textarea id="mytextarea" style="width: 100%;">Hello, World!</textarea>
            <script>
            tinymce.init({
                selector: '#mytextarea',
                height: 500,
                license_key: 'gpl'
            });
            </script>
        </div>

    </div>
</form>
