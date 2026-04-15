<fieldset>
    <legend>Sektions-Header</legend>
    <div class="form-group">
        <label>Eyebrow</label>
        <input type="text" name="sectionEyebrow" value="<?= htmlspecialchars($data['sectionEyebrow'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Überschrift</label>
        <input type="text" name="headerTitle" value="<?= htmlspecialchars($data['headerTitle'] ?? '') ?>">
    </div>
</fieldset>

<p><small>Das SVG-Diagramm selbst wird im Code verwaltet. Hier können nur die Texte und die Legende bearbeitet werden.</small></p>

<h3>Legende</h3>
<div id="legend-container">
    <?php foreach ($data['legend'] ?? [] as $i => $l): ?>
    <div class="repeater-item repeater-inline">
        <div class="form-row">
            <div class="form-group">
                <label>Typ</label>
                <select name="legend[<?= $i ?>][type]">
                    <option value="dot" <?= ($l['type'] ?? '') === 'dot' ? 'selected' : '' ?>>● Punkt</option>
                    <option value="dash" <?= ($l['type'] ?? '') === 'dash' ? 'selected' : '' ?>>┄ Strich</option>
                </select>
            </div>
            <div class="form-group">
                <label>Farbe</label>
                <input type="color" name="legend[<?= $i ?>][color]" value="<?= htmlspecialchars($l['color'] ?? '#FF6B1A') ?>">
            </div>
            <div class="form-group">
                <label>Bezeichnung</label>
                <input type="text" name="legend[<?= $i ?>][label]" value="<?= htmlspecialchars($l['label'] ?? '') ?>">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<button type="button" class="btn btn-secondary" onclick="addRepeaterItem('legend-container','legend-template')">+ Legende hinzufügen</button>

<template id="legend-template">
    <div class="repeater-item repeater-inline">
        <div class="form-row">
            <div class="form-group">
                <label>Typ</label>
                <select name="legend[__INDEX__][type]">
                    <option value="dot">● Punkt</option>
                    <option value="dash">┄ Strich</option>
                </select>
            </div>
            <div class="form-group">
                <label>Farbe</label>
                <input type="color" name="legend[__INDEX__][color]" value="#FF6B1A">
            </div>
            <div class="form-group">
                <label>Bezeichnung</label>
                <input type="text" name="legend[__INDEX__][label]" value="">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
    </div>
</template>
