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
        <input type="text" name="heroSub" value="<?= htmlspecialchars($data['heroSub'] ?? '') ?>">
    </div>
</fieldset>

<h3>Abschnitte</h3>
<div id="sections-container">
    <?php foreach ($data['sections'] ?? [] as $i => $s): ?>
    <div class="repeater-item">
        <div class="repeater-header">
            <span class="repeater-num"><?= $i + 1 ?></span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
        <div class="form-group">
            <label>Überschrift</label>
            <input type="text" name="sections[<?= $i ?>][heading]" value="<?= htmlspecialchars($s['heading'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Inhalt (HTML erlaubt)</label>
            <textarea name="sections[<?= $i ?>][html]" rows="6" class="code"><?= htmlspecialchars($s['html'] ?? '') ?></textarea>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<button type="button" class="btn btn-secondary" onclick="addRepeaterItem('sections-container','section-template')">+ Abschnitt hinzufügen</button>

<template id="section-template">
    <div class="repeater-item">
        <div class="repeater-header">
            <span class="repeater-num">0</span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
        <div class="form-group">
            <label>Überschrift</label>
            <input type="text" name="sections[__INDEX__][heading]" value="">
        </div>
        <div class="form-group">
            <label>Inhalt (HTML erlaubt)</label>
            <textarea name="sections[__INDEX__][html]" rows="6" class="code"></textarea>
        </div>
    </div>
</template>
