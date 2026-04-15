<div class="form-group">
    <label>
        <input type="checkbox" name="displayMode" value="1" <?= ($data['displayMode'] ?? 'accordion') === 'accordion' ? 'checked' : '' ?>>
        Akkordeon-Modus (aufklappbar)
    </label>
    <small>Wenn deaktiviert, werden alle Schritte statisch angezeigt.</small>
</div>

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
    <div class="form-group">
        <label>Untertitel</label>
        <input type="text" name="sectionSub" value="<?= htmlspecialchars($data['sectionSub'] ?? '') ?>">
    </div>
</fieldset>

<h3>Schritte</h3>
<div id="steps-container">
    <?php foreach ($data['steps'] ?? [] as $i => $step): ?>
    <div class="repeater-item">
        <div class="repeater-header">
            <span class="repeater-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Nummer</label>
                <input type="text" name="steps[<?= $i ?>][number]" value="<?= htmlspecialchars($step['number'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Tag</label>
                <input type="text" name="steps[<?= $i ?>][tag]" value="<?= htmlspecialchars($step['tag'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Titel</label>
            <input type="text" name="steps[<?= $i ?>][title]" value="<?= htmlspecialchars($step['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Kurzbeschreibung</label>
            <textarea name="steps[<?= $i ?>][description]" rows="2"><?= htmlspecialchars($step['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Detail-Text (aufklappbar)</label>
            <textarea name="steps[<?= $i ?>][detail]" rows="4"><?= htmlspecialchars($step['detail'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Bild 1 (Pfad)</label>
                <input type="text" name="steps[<?= $i ?>][image1]" value="<?= htmlspecialchars($step['image1'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Bild 1 Alt-Text</label>
                <input type="text" name="steps[<?= $i ?>][image1Alt]" value="<?= htmlspecialchars($step['image1Alt'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Bild 2 (Pfad)</label>
                <input type="text" name="steps[<?= $i ?>][image2]" value="<?= htmlspecialchars($step['image2'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Bild 2 Alt-Text</label>
                <input type="text" name="steps[<?= $i ?>][image2Alt]" value="<?= htmlspecialchars($step['image2Alt'] ?? '') ?>">
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<button type="button" class="btn btn-secondary" onclick="addRepeaterItem('steps-container','step-template')">+ Schritt hinzufügen</button>

<template id="step-template">
    <div class="repeater-item">
        <div class="repeater-header">
            <span class="repeater-num">00</span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Nummer</label>
                <input type="text" name="steps[__INDEX__][number]" value="">
            </div>
            <div class="form-group">
                <label>Tag</label>
                <input type="text" name="steps[__INDEX__][tag]" value="">
            </div>
        </div>
        <div class="form-group">
            <label>Titel</label>
            <input type="text" name="steps[__INDEX__][title]" value="">
        </div>
        <div class="form-group">
            <label>Kurzbeschreibung</label>
            <textarea name="steps[__INDEX__][description]" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label>Detail-Text</label>
            <textarea name="steps[__INDEX__][detail]" rows="4"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Bild 1 (Pfad)</label>
                <input type="text" name="steps[__INDEX__][image1]" value="">
            </div>
            <div class="form-group">
                <label>Bild 1 Alt-Text</label>
                <input type="text" name="steps[__INDEX__][image1Alt]" value="">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Bild 2 (Pfad)</label>
                <input type="text" name="steps[__INDEX__][image2]" value="">
            </div>
            <div class="form-group">
                <label>Bild 2 Alt-Text</label>
                <input type="text" name="steps[__INDEX__][image2Alt]" value="">
            </div>
        </div>
    </div>
</template>
