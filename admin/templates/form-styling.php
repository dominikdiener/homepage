<!-- Hinweis -->
<div style="background:#FFF3E0;border:1px solid #FF9800;border-radius:8px;padding:14px 20px;margin-bottom:20px;color:#E65100;font-size:14px;">
    ⚠️ <strong>Achtung:</strong> CSS-Änderungen beeinflussen das gesamte Erscheinungsbild der Webseite.
    Nutzen Sie die Vorschau (<code>?rev=draft</code>), um Änderungen vor der Veröffentlichung zu prüfen.
</div>

<!-- Beschreibung -->
<div class="form-group">
    <label class="form-label">Beschreibung (optional)</label>
    <input type="text" name="description" class="form-input" value="<?= htmlspecialchars($data['description'] ?? '') ?>" placeholder="z.B. Farbschema Sommer 2026">
</div>

<!-- Layout: Baukasten links, Editor rechts -->
<div style="display:flex;gap:20px;align-items:flex-start;">

<!-- ═══ BAUKASTEN ═══ -->
<div id="css-builder" style="width:380px;flex-shrink:0;max-height:700px;overflow-y:auto;border:1px solid #ddd;border-radius:8px;background:#fafafa;">
    <div style="padding:12px 16px;background:#333;color:#fff;font-weight:600;border-radius:8px 8px 0 0;font-size:14px;">
        🎨 CSS-Baukasten
    </div>

    <!-- Farben -->
    <div class="cb-section" data-open="true">
        <div class="cb-header" onclick="toggleCbSection(this)">🎨 Farben</div>
        <div class="cb-body">
            <div class="cb-item" data-selector=":root" data-prop="--orange" data-default="#FF6B1A">
                <span class="cb-label">Hauptfarbe (Orange)</span>
                <span class="cb-desc">Buttons, Links, Badges, CTA</span>
                <input type="color" class="cb-color" value="#FF6B1A">
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--teal" data-default="#1A7A6E">
                <span class="cb-label">Akzentfarbe (Teal)</span>
                <span class="cb-desc">Highlights, Unterüberschriften</span>
                <input type="color" class="cb-color" value="#1A7A6E">
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--dark" data-default="#0F1A1C">
                <span class="cb-label">Hintergrund (Dunkel)</span>
                <span class="cb-desc">Seiten-Hintergrund</span>
                <input type="color" class="cb-color" value="#0F1A1C">
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--mid" data-default="#1C2E32">
                <span class="cb-label">Hintergrund (Mittel)</span>
                <span class="cb-desc">Karten, Sektions-Hintergrund</span>
                <input type="color" class="cb-color" value="#1C2E32">
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--mid2" data-default="#243438">
                <span class="cb-label">Hintergrund (Hell)</span>
                <span class="cb-desc">Hover-Effekte, Kartenrahmen</span>
                <input type="color" class="cb-color" value="#243438">
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--light" data-default="#E8EEF0">
                <span class="cb-label">Textfarbe (Hell)</span>
                <span class="cb-desc">Haupttext, Überschriften</span>
                <input type="color" class="cb-color" value="#E8EEF0">
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--accent" data-default="#FFD166">
                <span class="cb-label">Akzent (Gelb)</span>
                <span class="cb-desc">Hervorhebungen</span>
                <input type="color" class="cb-color" value="#FFD166">
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--grey" data-default="#8BA5AB">
                <span class="cb-label">Grau</span>
                <span class="cb-desc">Untertitel, Labels</span>
                <input type="color" class="cb-color" value="#8BA5AB">
            </div>
        </div>
    </div>

    <!-- Schriftarten -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">🔤 Schriftarten</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector=":root" data-prop="--font-body" data-default="'DM Sans', sans-serif">
                <span class="cb-label">Fließtext-Schrift</span>
                <span class="cb-desc">Text, Beschreibungen, Navigation</span>
                <select class="cb-select">
                    <option value="">Standard (DM Sans)</option>
                    <option value="'Inter', sans-serif">Inter</option>
                    <option value="'Roboto', sans-serif">Roboto</option>
                    <option value="'Open Sans', sans-serif">Open Sans</option>
                    <option value="'Lato', sans-serif">Lato</option>
                    <option value="'Poppins', sans-serif">Poppins</option>
                    <option value="'Montserrat', sans-serif">Montserrat</option>
                    <option value="Arial, sans-serif">Arial</option>
                </select>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--font-mono" data-default="'Space Mono', monospace">
                <span class="cb-label">Mono-Schrift (Labels/Badges)</span>
                <span class="cb-desc">Badges, Nummern, techn. Werte</span>
                <select class="cb-select">
                    <option value="">Standard (Space Mono)</option>
                    <option value="'JetBrains Mono', monospace">JetBrains Mono</option>
                    <option value="'Fira Code', monospace">Fira Code</option>
                    <option value="'Source Code Pro', monospace">Source Code Pro</option>
                    <option value="'IBM Plex Mono', monospace">IBM Plex Mono</option>
                    <option value="'Courier New', monospace">Courier New</option>
                    <option value="'DM Sans', sans-serif">DM Sans (wie Fließtext)</option>
                </select>
            </div>
            <div class="cb-item" data-selector=".hero h1, .section-title" data-prop="font-weight">
                <span class="cb-label">Überschriften Dicke</span>
                <span class="cb-desc">Standard: 700 (Bold)</span>
                <select class="cb-select">
                    <option value="">Standard (700)</option>
                    <option value="300">Light (300)</option>
                    <option value="400">Normal (400)</option>
                    <option value="500">Medium (500)</option>
                    <option value="600">Semibold (600)</option>
                    <option value="700">Bold (700)</option>
                    <option value="800">Extra Bold (800)</option>
                    <option value="900">Black (900)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Schriftgrößen -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">📏 Schriftgrößen</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector=":root" data-prop="--fs-nav" data-default="13px">
                <span class="cb-label">Navigation</span>
                <span class="cb-desc">Menü-Links (Standard: 13px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="10" max="20" value="13" step="1">
                    <span class="cb-range-val">13px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-hero-sub" data-default="18px">
                <span class="cb-label">Hero Untertitel</span>
                <span class="cb-desc">Text unter der Hauptüberschrift (Standard: 18px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="14" max="26" value="18" step="1">
                    <span class="cb-range-val">18px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-section-sub" data-default="18px">
                <span class="cb-label">Sektions-Untertitel</span>
                <span class="cb-desc">Beschreibungstext unter Sektionstiteln (Standard: 18px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="14" max="26" value="18" step="1">
                    <span class="cb-range-val">18px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-eyebrow" data-default="11px">
                <span class="cb-label">Eyebrow / Badge</span>
                <span class="cb-desc">Kleine Labels über Überschriften (Standard: 11px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="8" max="16" value="11" step="1">
                    <span class="cb-range-val">11px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-card-title" data-default="20px">
                <span class="cb-label">Karten-Titel</span>
                <span class="cb-desc">Überschriften in Karten (Standard: 20px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="14" max="30" value="20" step="1">
                    <span class="cb-range-val">20px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-card-text" data-default="15px">
                <span class="cb-label">Karten-Text</span>
                <span class="cb-desc">Fließtext in Karten (Standard: 15px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="12" max="22" value="15" step="1">
                    <span class="cb-range-val">15px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-btn" data-default="15px">
                <span class="cb-label">Buttons</span>
                <span class="cb-desc">Schriftgröße in Buttons (Standard: 15px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="12" max="22" value="15" step="1">
                    <span class="cb-range-val">15px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-spec-value" data-default="26px">
                <span class="cb-label">Techn. Daten Werte</span>
                <span class="cb-desc">Große Zahlenwerte in Specs (Standard: 26px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="18" max="40" value="26" step="1">
                    <span class="cb-range-val">26px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-spec-label" data-default="13px">
                <span class="cb-label">Techn. Daten Label</span>
                <span class="cb-desc">Beschriftung unter Spec-Werten (Standard: 13px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="10" max="18" value="13" step="1">
                    <span class="cb-range-val">13px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-label" data-default="10px">
                <span class="cb-label">Kleine Labels</span>
                <span class="cb-desc">Legende, Badges, Tags (Standard: 10px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="8" max="16" value="10" step="1">
                    <span class="cb-range-val">10px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-footer" data-default="13px">
                <span class="cb-label">Footer</span>
                <span class="cb-desc">Footer-Links (Standard: 13px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="10" max="18" value="13" step="1">
                    <span class="cb-range-val">13px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-footer-meta" data-default="12px">
                <span class="cb-label">Footer Meta</span>
                <span class="cb-desc">Copyright, Kleingedrucktes (Standard: 12px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="8" max="16" value="12" step="1">
                    <span class="cb-range-val">12px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-page-heading" data-default="22px">
                <span class="cb-label">Seiten-Überschrift</span>
                <span class="cb-desc">Unterseiten wie Impressum, Datenschutz (Standard: 22px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="16" max="32" value="22" step="1">
                    <span class="cb-range-val">22px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-page-text" data-default="16px">
                <span class="cb-label">Seiten-Text</span>
                <span class="cb-desc">Fließtext auf Unterseiten (Standard: 16px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="12" max="22" value="16" step="1">
                    <span class="cb-range-val">16px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-form-label" data-default="12px">
                <span class="cb-label">Formular-Label</span>
                <span class="cb-desc">Labels über Formularfeldern (Standard: 12px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="10" max="18" value="12" step="1">
                    <span class="cb-range-val">12px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=":root" data-prop="--fs-form-input" data-default="15px">
                <span class="cb-label">Formular-Eingabe</span>
                <span class="cb-desc">Text in Eingabefeldern (Standard: 15px)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="12" max="22" value="15" step="1">
                    <span class="cb-range-val">15px</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">🧭 Navigation</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector="nav" data-prop="background">
                <span class="cb-label">Nav-Hintergrund</span>
                <span class="cb-desc">Navigationsleiste</span>
                <input type="color" class="cb-color" value="#0F1A1C">
            </div>
            <div class="cb-item" data-selector=".nav-cta" data-prop="background">
                <span class="cb-label">CTA-Button Farbe</span>
                <span class="cb-desc">"Kontakt aufnehmen" Button</span>
                <input type="color" class="cb-color" value="#FF6B1A">
            </div>
            <div class="cb-item" data-selector=".nav-cta" data-prop="border-radius">
                <span class="cb-label">CTA-Button Rundung</span>
                <span class="cb-desc">Standard: 6px</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="0" max="30" value="6" step="1">
                    <span class="cb-range-val">6px</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero-Bereich -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">🏠 Hero-Bereich</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector=":root" data-prop="--fs-hero-title" data-default="clamp(36px, 4.5vw, 60px)">
                <span class="cb-label">Überschrift Größe (max)</span>
                <span class="cb-desc">Standard: 60px (skaliert responsiv)</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="32" max="80" value="60" step="2" data-template="clamp(36px, 4.5vw, {val}px)">
                    <span class="cb-range-val">60px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=".hero h1 span" data-prop="color">
                <span class="cb-label">Hervorgehobene Wörter</span>
                <span class="cb-desc">"trockenen" in der Überschrift</span>
                <input type="color" class="cb-color" value="#FF6B1A">
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">🔘 Buttons</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector=".btn-primary, .hero-actions .btn:first-child" data-prop="background">
                <span class="cb-label">Primärer Button</span>
                <span class="cb-desc">Hauptaktionen</span>
                <input type="color" class="cb-color" value="#FF6B1A">
            </div>
            <div class="cb-item" data-selector=".btn-primary, .hero-actions .btn:first-child" data-prop="border-radius">
                <span class="cb-label">Button Rundung</span>
                <span class="cb-desc">Standard: 6px</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="0" max="30" value="6" step="1">
                    <span class="cb-range-val">6px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=".btn-primary, .hero-actions .btn:first-child" data-prop="padding">
                <span class="cb-label">Button Innenabstand</span>
                <span class="cb-desc">Standard: 14px 28px</span>
                <input type="text" class="cb-text" placeholder="14px 28px" value="">
            </div>
        </div>
    </div>

    <!-- Karten & Sektionen -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">📦 Karten & Sektionen</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector=".value-card, .how-step, .audience-card" data-prop="border-radius">
                <span class="cb-label">Karten Rundung</span>
                <span class="cb-desc">Alle Inhaltskarten</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="0" max="24" value="12" step="2">
                    <span class="cb-range-val">12px</span>
                </div>
            </div>
            <div class="cb-item" data-selector=".value-card, .how-step, .audience-card" data-prop="background">
                <span class="cb-label">Karten Hintergrund</span>
                <span class="cb-desc">Standard: --mid</span>
                <input type="color" class="cb-color" value="#1C2E32">
            </div>
            <div class="cb-item" data-selector="section" data-prop="padding">
                <span class="cb-label">Sektions-Abstand</span>
                <span class="cb-desc">Standard: 100px oben/unten</span>
                <input type="text" class="cb-text" placeholder="100px 0" value="">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">📄 Footer</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector="footer" data-prop="background">
                <span class="cb-label">Footer Hintergrund</span>
                <span class="cb-desc">Seitenfuß</span>
                <input type="color" class="cb-color" value="#0F1A1C">
            </div>
            <div class="cb-item" data-selector="footer a" data-prop="color">
                <span class="cb-label">Footer Link-Farbe</span>
                <span class="cb-desc">Impressum, Datenschutz etc.</span>
                <input type="color" class="cb-color" value="#8BA5AB">
            </div>
        </div>
    </div>

    <!-- Abstände & Layout -->
    <div class="cb-section">
        <div class="cb-header" onclick="toggleCbSection(this)">📐 Abstände & Layout</div>
        <div class="cb-body" style="display:none;">
            <div class="cb-item" data-selector=".value-grid, .how-steps" data-prop="gap">
                <span class="cb-label">Karten-Abstand</span>
                <span class="cb-desc">Abstand zwischen Karten</span>
                <div class="cb-range-wrap">
                    <input type="range" class="cb-range" min="0" max="40" value="20" step="2">
                    <span class="cb-range-val">20px</span>
                </div>
            </div>
            <div class="cb-item" data-selector="body" data-prop="max-width">
                <span class="cb-label">Maximale Seitenbreite</span>
                <span class="cb-desc">Standard: 1200px</span>
                <input type="text" class="cb-text" placeholder="1200px" value="">
            </div>
        </div>
    </div>
</div>

<!-- ═══ CSS EDITOR ═══ -->
<div style="flex:1;min-width:0;">
    <div class="form-group" style="margin:0;">
        <label class="form-label">CSS-Code</label>
        <textarea name="css" id="css-editor" style="width:100%;min-height:500px;font-family:monospace;font-size:14px;"><?= htmlspecialchars($data['css'] ?? '') ?></textarea>
    </div>
</div>

</div><!-- /layout -->

<!-- Baukasten Styles -->
<style>
.cb-header {
    padding: 10px 16px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    background: #f5f5f5;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cb-header::after { content: '▸'; transition: transform 0.2s; }
.cb-section[data-open="true"] > .cb-header::after { transform: rotate(90deg); }
.cb-body { padding: 0; }
.cb-item {
    padding: 10px 16px;
    border-bottom: 1px solid #f0f0f0;
    cursor: default;
}
.cb-item:hover { background: #f8f8f8; }
.cb-label { display: block; font-weight: 600; font-size: 13px; color: #333; }
.cb-desc { display: block; font-size: 11px; color: #999; margin: 2px 0 6px; }
.cb-color {
    width: 100%;
    height: 32px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    padding: 2px;
}
.cb-select {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 13px;
    background: #fff;
}
.cb-text {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 13px;
    font-family: monospace;
}
.cb-range-wrap { display: flex; align-items: center; gap: 8px; }
.cb-range { flex: 1; }
.cb-range-val { font-size: 12px; font-family: monospace; color: #666; min-width: 40px; }
</style>

<!-- CodeMirror CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.18/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.18/theme/monokai.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.18/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.18/mode/css/css.min.js"></script>

<script>
var cmEditor;

document.addEventListener('DOMContentLoaded', function() {
    // CodeMirror initialisieren
    var textarea = document.getElementById('css-editor');
    cmEditor = CodeMirror.fromTextArea(textarea, {
        mode: 'css',
        theme: 'monokai',
        lineNumbers: true,
        lineWrapping: true,
        matchBrackets: true,
        indentUnit: 2,
        tabSize: 2,
    });
    cmEditor.setSize('100%', '600px');
    cmEditor.on('change', function() { cmEditor.save(); });

    // Baukasten: Event-Listener
    document.querySelectorAll('.cb-item').forEach(function(item) {
        var selector = item.dataset.selector;
        var prop = item.dataset.prop;

        // Farb-Picker
        var colorInput = item.querySelector('.cb-color');
        if (colorInput) {
            colorInput.addEventListener('input', function() {
                insertOrUpdateRule(selector, prop, this.value);
            });
        }

        // Select-Dropdown
        var selectInput = item.querySelector('.cb-select');
        if (selectInput) {
            selectInput.addEventListener('change', function() {
                if (this.value) {
                    insertOrUpdateRule(selector, prop, this.value);
                }
            });
        }

        // Range-Slider
        var rangeInput = item.querySelector('.cb-range');
        if (rangeInput) {
            var rangeVal = item.querySelector('.cb-range-val');
            rangeInput.addEventListener('input', function() {
                var template = this.dataset.template;
                var val;
                if (template) {
                    val = template.replace('{val}', this.value);
                    rangeVal.textContent = this.value + 'px';
                } else {
                    val = this.value + 'px';
                    rangeVal.textContent = val;
                }
                insertOrUpdateRule(selector, prop, val);
            });
        }

        // Text-Input
        var textInput = item.querySelector('.cb-text');
        if (textInput) {
            textInput.addEventListener('change', function() {
                if (this.value.trim()) {
                    insertOrUpdateRule(selector, prop, this.value.trim());
                }
            });
        }
    });
});

// Akkordeon-Sektionen
function toggleCbSection(header) {
    var section = header.parentElement;
    var body = section.querySelector('.cb-body');
    var isOpen = section.dataset.open === 'true';
    section.dataset.open = isOpen ? 'false' : 'true';
    body.style.display = isOpen ? 'none' : 'block';
}

// CSS-Regel im Editor einfügen oder aktualisieren
function insertOrUpdateRule(selector, prop, value) {
    var code = cmEditor.getValue();

    // Regex: Finde den Selector-Block
    var selectorEsc = selector.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    var blockRegex = new RegExp('(' + selectorEsc + '\\s*\\{)([^}]*)(\\})', 'g');
    var match = blockRegex.exec(code);

    if (match) {
        // Block existiert → Property aktualisieren oder hinzufügen
        var blockContent = match[2];
        var propRegex = new RegExp('(\\s*' + prop.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*:)[^;]*(;)', 'g');
        var propMatch = propRegex.exec(blockContent);

        if (propMatch) {
            // Property existiert → Wert ersetzen
            var newBlock = blockContent.replace(propRegex, '$1 ' + value + '$2');
            var newCode = code.replace(blockRegex, '$1' + newBlock + '$3');
            cmEditor.setValue(newCode);
        } else {
            // Property hinzufügen
            var newBlock = blockContent.trimEnd() + '\n  ' + prop + ': ' + value + ';\n';
            blockRegex.lastIndex = 0;
            var newCode = code.replace(blockRegex, '$1' + newBlock + '$3');
            cmEditor.setValue(newCode);
        }
    } else {
        // Block existiert nicht → neuen Block anfügen
        var newRule = '\n' + selector + ' {\n  ' + prop + ': ' + value + ';\n}\n';
        if (code.trim().length > 0) {
            cmEditor.setValue(code.trimEnd() + '\n' + newRule);
        } else {
            cmEditor.setValue(newRule.trim() + '\n');
        }
    }

    cmEditor.save();
}
</script>
