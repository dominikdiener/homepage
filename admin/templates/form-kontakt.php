<fieldset>
    <legend>Seitenkopf</legend>
    <div class="form-group">
        <label>Eyebrow</label>
        <input type="text" name="heroEyebrow" value="<?= htmlspecialchars($data['heroEyebrow'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Titel</label>
        <input type="text" name="heroTitle" value="<?= htmlspecialchars($data['heroTitle'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Untertitel</label>
        <textarea name="heroSub" rows="2"><?= htmlspecialchars($data['heroSub'] ?? '') ?></textarea>
    </div>
</fieldset>

<fieldset>
    <legend>Unternehmensinformationen</legend>
    <div class="form-group">
        <label>Firmenname</label>
        <input type="text" name="companyName" value="<?= htmlspecialchars($data['companyName'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Beschreibung</label>
        <textarea name="companyDescription" rows="3"><?= htmlspecialchars($data['companyDescription'] ?? '') ?></textarea>
    </div>
</fieldset>

<fieldset>
    <legend>Kontaktdaten</legend>
    <div class="form-group">
        <label>Adresse</label>
        <textarea name="address" rows="3"><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>E-Mail</label>
            <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Telefon (Anzeige)</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
        </div>
    </div>
    <div class="form-group">
        <label>Telefon (Link, z.B. tel:+490000)</label>
        <input type="text" name="phoneHref" value="<?= htmlspecialchars($data['phoneHref'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Hinweis zur Antwortzeit</label>
        <input type="text" name="responseNote" value="<?= htmlspecialchars($data['responseNote'] ?? '') ?>">
    </div>
</fieldset>
