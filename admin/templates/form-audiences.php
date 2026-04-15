<fieldset>
    <legend>Sektions-Header</legend>
    <div class="form-group">
        <label>Eyebrow</label>
        <input type="text" name="sectionEyebrow" value="<?= htmlspecialchars($data['sectionEyebrow'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Titel</label>
        <input type="text" name="sectionTitle" value="<?= htmlspecialchars($data['sectionTitle'] ?? '') ?>">
    </div>
</fieldset>

<h3>Zielgruppen-Karten</h3>
<div id="cards-container">
    <?php foreach (($data['audiences'] ?? $data['cards'] ?? []) as $i => $card): ?>
    <div class="repeater-item">
        <div class="repeater-header">
            <span class="repeater-num"><?= $i + 1 ?></span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>CSS-Klasse</label>
                <select name="cards[<?= $i ?>][cssClass]">
                    <option value="gu" <?= ($card['cssClass'] ?? '') === 'gu' ? 'selected' : '' ?>>GU (Orange)</option>
                    <option value="her" <?= ($card['cssClass'] ?? '') === 'her' ? 'selected' : '' ?>>Hersteller (Teal)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Label</label>
                <input type="text" name="cards[<?= $i ?>][label]" value="<?= htmlspecialchars($card['label'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Titel</label>
            <input type="text" name="cards[<?= $i ?>][title]" value="<?= htmlspecialchars($card['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Beschreibung</label>
            <textarea name="cards[<?= $i ?>][description]" rows="3"><?= htmlspecialchars($card['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Vorteile (ein Vorteil pro Zeile)</label>
            <textarea name="cards[<?= $i ?>][benefits]" rows="5"><?= htmlspecialchars(implode("\n", $card['benefits'] ?? [])) ?></textarea>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<button type="button" class="btn btn-secondary" onclick="addRepeaterItem('cards-container','card-template')">+ Zielgruppe hinzufügen</button>

<template id="card-template">
    <div class="repeater-item">
        <div class="repeater-header">
            <span class="repeater-num">0</span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>CSS-Klasse</label>
                <select name="cards[__INDEX__][cssClass]">
                    <option value="gu">GU (Orange)</option>
                    <option value="her">Hersteller (Teal)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Label</label>
                <input type="text" name="cards[__INDEX__][label]" value="">
            </div>
        </div>
        <div class="form-group">
            <label>Titel</label>
            <input type="text" name="cards[__INDEX__][title]" value="">
        </div>
        <div class="form-group">
            <label>Beschreibung</label>
            <textarea name="cards[__INDEX__][description]" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label>Vorteile (ein Vorteil pro Zeile)</label>
            <textarea name="cards[__INDEX__][benefits]" rows="5"></textarea>
        </div>
    </div>
</template>
