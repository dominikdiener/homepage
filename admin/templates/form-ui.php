<fieldset>
    <legend>Navigation</legend>
    <div class="form-group">
        <label>So funktioniert's</label>
        <input type="text" name="nav_how" value="<?= htmlspecialchars($data['nav']['how'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Ihr Nutzen</label>
        <input type="text" name="nav_value" value="<?= htmlspecialchars($data['nav']['value'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Technik</label>
        <input type="text" name="nav_technik" value="<?= htmlspecialchars($data['nav']['technik'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>News</label>
        <input type="text" name="nav_news" value="<?= htmlspecialchars($data['nav']['news'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Kontakt-Button</label>
        <input type="text" name="nav_kontakt" value="<?= htmlspecialchars($data['nav']['kontakt'] ?? '') ?>">
    </div>
</fieldset>

<fieldset>
    <legend>Footer</legend>
    <div class="form-group">
        <label>Impressum</label>
        <input type="text" name="footer_impressum" value="<?= htmlspecialchars($data['footer']['impressum'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Datenschutz</label>
        <input type="text" name="footer_datenschutz" value="<?= htmlspecialchars($data['footer']['datenschutz'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Kontakt</label>
        <input type="text" name="footer_kontakt" value="<?= htmlspecialchars($data['footer']['kontakt'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Copyright-Text</label>
        <input type="text" name="footer_copyright" value="<?= htmlspecialchars($data['footer']['copyright'] ?? '') ?>">
    </div>
</fieldset>
