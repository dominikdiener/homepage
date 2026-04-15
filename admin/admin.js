/**
 * Admin – Repeater-Logik für dynamische Formularfelder
 */

function addRepeaterItem(containerId, templateId) {
    const container = document.getElementById(containerId);
    const template = document.getElementById(templateId);
    if (!container || !template) return;

    const clone = template.content.cloneNode(true);
    const items = container.querySelectorAll('.repeater-item');
    const index = items.length;

    // Platzhalter __INDEX__ ersetzen
    clone.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/__INDEX__/g, index);
    });
    clone.querySelectorAll('[id]').forEach(el => {
        el.id = el.id.replace(/__INDEX__/g, index);
    });
    clone.querySelectorAll('[for]').forEach(el => {
        el.htmlFor = el.htmlFor.replace(/__INDEX__/g, index);
    });

    // Nummer aktualisieren wenn vorhanden
    const numEl = clone.querySelector('.repeater-num');
    if (numEl) numEl.textContent = String(index + 1).padStart(2, '0');

    container.appendChild(clone);
}

function removeRepeaterItem(button) {
    const item = button.closest('.repeater-item');
    if (!item) return;

    const container = item.parentElement;
    if (confirm('Diesen Eintrag wirklich entfernen?')) {
        item.remove();
        reindexRepeater(container);
    }
}

function reindexRepeater(container) {
    const items = container.querySelectorAll('.repeater-item');
    items.forEach((item, i) => {
        // Namen neu indizieren
        item.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, '[' + i + ']');
        });
        // Nummer aktualisieren
        const numEl = item.querySelector('.repeater-num');
        if (numEl) numEl.textContent = String(i + 1).padStart(2, '0');
    });
}
