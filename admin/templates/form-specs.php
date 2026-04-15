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

<h3>Technische Daten</h3>
<div id="items-container">
    <?php foreach (($data['specs'] ?? $data['items'] ?? []) as $i => $item): ?>
    <div class="repeater-item repeater-inline">
        <div class="form-row">
            <div class="form-group">
                <label>Wert</label>
                <input type="text" name="items[<?= $i ?>][value]" value="<?= htmlspecialchars($item['value'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Bezeichnung</label>
                <input type="text" name="items[<?= $i ?>][label]" value="<?= htmlspecialchars($item['label'] ?? '') ?>">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<button type="button" class="btn btn-secondary" onclick="addRepeaterItem('items-container','item-template')">+ Datenpunkt hinzufügen</button>

<template id="item-template">
    <div class="repeater-item repeater-inline">
        <div class="form-row">
            <div class="form-group">
                <label>Wert</label>
                <input type="text" name="items[__INDEX__][value]" value="">
            </div>
            <div class="form-group">
                <label>Bezeichnung</label>
                <input type="text" name="items[__INDEX__][label]" value="">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
    </div>
</template>
