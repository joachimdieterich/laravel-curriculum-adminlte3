/* Curriculum Certificate Designer · MIT · dependency-free shared rendering core. */
(() => {
    'use strict';
    const LEVELS = [
        {id: 'gray', label: 'Noch nicht bewertet', short: 'Grau', color: '#94a3b8', rank: null, code: '0'},
        {id: 'red', label: 'Noch nicht erreicht', short: 'Rot', color: '#ce5364', rank: 0, code: '3'},
        {id: 'yellow', label: 'Mit Unterstützung', short: 'Gelb', color: '#d29c31', rank: 1, code: '2'},
        {id: 'green', label: 'Selbstständig erreicht', short: 'Grün', color: '#298366', rank: 2, code: '1'}
    ];
    const clone = x => JSON.parse(JSON.stringify(x));
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[c]));
    const uid = () => 'e' + Math.random().toString(36).slice(2, 11);
    const sample = () => ({
        curriculum: {id: 'demo', title: 'Kompetenzentwicklung · Physik'},
        student: {id: 'demo-1', name: 'Mila Schneider'},
        organization: {name: 'Musterschule', city: 'Mainz'},
        date: new Date().toLocaleDateString('de-DE'),
        areas: [
            {
                id: 'a1',
                title: 'Erkenntnisgewinnung',
                objectives: [{id: 'b1', title: 'Ich kann eine überprüfbare Vermutung formulieren.'}, {
                    id: 'b2',
                    title: 'Ich kann einen Versuch planen und sicher durchführen.'
                }, {id: 'b3', title: 'Ich kann Messwerte dokumentieren und auswerten.'}]
            },
            {
                id: 'a2',
                title: 'Kommunikation und Bewertung',
                objectives: [{id: 'b4', title: 'Ich kann Beobachtungen mit Fachbegriffen beschreiben.'}, {
                    id: 'b5',
                    title: 'Ich kann Ergebnisse verständlich präsentieren.'
                }, {id: 'b6', title: 'Ich kann mein Vorgehen reflektieren und verbessern.'}]
            }
        ],
        achievements: {b1: '11', b2: '22', b3: '00', b4: '12', b5: '33', b6: '21'}
    });
    const TABLE_DEFAULTS = Object.freeze({
        areaBackgroundEnabled: true,
        areaBackground: '#e0e9e2',
        areaTextColor: '#213b36',
        outerBorder: true,
        outerBorderColor: '#8ba296',
        outerBorderWidth: 0.75,
        innerBorders: true,
        innerBorderColor: '#8ba296',
        innerBorderWidth: 0.75
    });
    const isTable = el => ['table', 'competency'].includes(el.type);
    const tableStyle = el => ({...TABLE_DEFAULTS, ...(el.tableStyle ?? {})});

    function tableTag(el, extraClass = '') {
        const s = tableStyle(el),
            outer = s.outerBorder ? `${s.outerBorderWidth}pt solid ${s.outerBorderColor}` : '0 none transparent',
            inner = s.innerBorders ? `${s.innerBorderWidth}pt solid ${s.innerBorderColor}` : '0 none transparent';
        return `<table class="cd-table ${extraClass}" style="--cd-outer-border:${outer};--cd-inner-border:${inner};--cd-area-background:${s.areaBackgroundEnabled ? s.areaBackground : 'transparent'};--cd-area-color:${s.areaTextColor}">`;
    }

    const baseElement = (type, props = {}) => ({
        id: uid(),
        type,
        name: ({text: 'Text', image: 'Bild', table: 'Tabelle', competency: 'Kompetenztabelle', line: 'Linie'})[type],
        page: 1,
        x: 18,
        y: 100,
        w: 174,
        h: 15,
        fontSize: 11,
        bold: false,
        italic: false,
        align: 'left',
        color: '#213b36',
        background: '#ffffff',
        border: false,
        repeat: false,
        condition: null, ...(['table', 'competency'].includes(type) ? {tableStyle: clone(TABLE_DEFAULTS)} : {}), ...props
    });

    function standard(data = sample()) {
        return {
            schemaVersion: 1,
            title: 'Kompetenzzertifikat',
            pageCount: 1,
            grid: 5,
            snap: true,
            assessmentSource: 'teacher',
            selectedIds: data.areas.flatMap(a => a.objectives.map(o => String(o.id))),
            translations: {global: Object.fromEntries(LEVELS.map(l => [l.id, l.label])), individual: {}},
            elements: [
                baseElement('image', {name: 'Schullogo', x: 18, y: 17, w: 40, h: 20, src: ''}),
                baseElement('text', {
                    name: 'Schule',
                    text: '{{school.name}}',
                    x: 105,
                    y: 22,
                    w: 87,
                    h: 12,
                    fontSize: 10,
                    align: 'right'
                }),
                baseElement('text', {
                    name: 'Titel',
                    text: 'Kompetenzzertifikat',
                    x: 18,
                    y: 48,
                    w: 174,
                    h: 18,
                    fontSize: 29,
                    bold: true,
                    align: 'center'
                }),
                baseElement('text', {
                    name: 'Schülername',
                    text: '{{student.name}}',
                    x: 18,
                    y: 71,
                    w: 174,
                    h: 15,
                    fontSize: 20,
                    bold: true,
                    italic: true,
                    align: 'center'
                }),
                baseElement('text', {
                    name: 'Einleitung',
                    text: 'Dieses Zertifikat dokumentiert die persönliche Kompetenzentwicklung in den ausgewählten Bereichen.',
                    x: 18,
                    y: 93,
                    w: 174,
                    h: 16,
                    fontSize: 11
                }),
                baseElement('competency', {
                    name: 'Kompetenzübersicht',
                    x: 18,
                    y: 117,
                    w: 174,
                    h: 126,
                    fontSize: 10,
                    columns: LEVELS.map(l => ({id: l.id, title: l.label, visible: true})),
                    mark: 'X',
                    verticalHeaders: true,
                    showTranslation: false,
                    groupByArea: true,
                    headerTitle: 'Bereiche und Bausteine',
                    autoContinue: true
                }),
                baseElement('text', {
                    name: 'Ort und Datum',
                    text: '{{school.city}}, {{date}}',
                    x: 18,
                    y: 269,
                    w: 65,
                    h: 9,
                    fontSize: 10,
                    repeat: true
                }),
                baseElement('line', {name: 'Unterschriftslinie', x: 117, y: 269, w: 75, h: 1, repeat: true}),
                baseElement('text', {
                    name: 'Unterschrift',
                    text: 'Unterschrift',
                    x: 117,
                    y: 272,
                    w: 75,
                    h: 8,
                    fontSize: 9,
                    repeat: true
                })
            ]
        };
    }

    const allObjectives = data => data.areas.flatMap(a => a.objectives.map(o => ({...o, area: a.title})));

    function levelFor(data, id, source = 'teacher') {
        const status = String(data.achievements?.[id] ?? '00').padStart(2, '0');
        const code = status[source === 'self' ? 0 : 1];
        return LEVELS.find(l => l.code === code)?.id ?? 'gray';
    }

    function visible(condition, data, source) {
        if (!condition) return true;
        const actual = levelFor(data, condition.objectiveId, source);
        const a = LEVELS.find(l => l.id === actual), b = LEVELS.find(l => l.id === condition.level);
        let match = condition.operator === 'eq' ? actual === condition.level : (a?.rank != null && b?.rank != null && a.rank >= b.rank);
        return condition.effect === 'hide' ? !match : match;
    }

    function translation(doc, data, id) {
        const level = levelFor(data, id, doc.assessmentSource);
        const local = doc.translations.individual[id];
        return local && Object.prototype.hasOwnProperty.call(local, level) ? local[level] : doc.translations.global[level] ?? '';
    }

    function interpolate(text, doc, data) {
        const vars = {
            'student.name': data.student?.name ?? '',
            'school.name': data.organization?.name ?? '',
            'school.city': data.organization?.city ?? '',
            'date': data.date ?? '',
            'curriculum.title': data.curriculum?.title ?? ''
        };
        return String(text ?? '').replace(/\{\{([^{}]+)\}\}/g, (whole, key) => {
            key = key.trim();
            if (Object.hasOwn(vars, key)) return vars[key];
            const match = key.match(/^objective:([^:]+):(text|title|level)$/);
            if (!match) return whole;
            if (match[2] === 'text') return translation(doc, data, match[1]);
            if (match[2] === 'level') return LEVELS.find(l => l.id === levelFor(data, match[1], doc.assessmentSource)).label;
            return allObjectives(data).find(o => String(o.id) === match[1])?.title ?? '';
        });
    }

    function tableParts(el, doc, data) {
        const columns = el.columns.filter(c => c.visible), span = 1 + columns.length + (el.showTranslation ? 1 : 0);
        const head = `<thead><tr><th class="cd-objective-head">${esc(el.headerTitle)}</th>${columns.map(c => `<th class="cd-level-head ${el.verticalHeaders ? 'vertical' : ''}"><span>${esc(c.title)}</span></th>`).join('')}${el.showTranslation ? '<th class="cd-translation-head">Rückmeldung</th>' : ''}</tr></thead>`;
        const groups = [];
        for (const area of data.areas) {
            const objectives = area.objectives.filter(o => doc.selectedIds.includes(String(o.id)));
            if (!objectives.length) continue;
            const heading = el.groupByArea ? `<tr class="cd-area"><th colspan="${span}">${esc(area.title)}</th></tr>` : '';
            groups.push({
                heading,
                rows: objectives.map(o => `<tr><td>${esc(o.title)}</td>${columns.map(c => `<td class="cd-mark">${levelFor(data, o.id, doc.assessmentSource) === c.id ? esc(el.mark) : ''}</td>`).join('')}${el.showTranslation ? `<td>${esc(translation(doc, data, o.id))}</td>` : ''}</tr>`)
            });
        }
        return {head, groups};
    }

    function elementContent(el, doc, data) {
        if (el.type === 'text') return `<div class="cd-text">${esc(interpolate(el.text, doc, data))}</div>`;
        if (el.type === 'image') return el.src ? `<img alt="${esc(el.name)}" src="${esc(el.src)}" draggable="false">` : '<div class="cd-placeholder">+<small>Platz für Schullogo</small></div>';
        if (el.type === 'line') return '<div class="cd-line"></div>';
        if (el.type === 'table') return `${tableTag(el)}<tbody>${el.cells.map((row, i) => `<tr>${row.map(t => `<${i === 0 && el.headerRow ? 'th' : 'td'}>${esc(interpolate(t, doc, data))}</${i === 0 && el.headerRow ? 'th' : 'td'}>`).join('')}</tr>`).join('')}</tbody></table>`;
        const parts = tableParts(el, doc, data);
        return `${tableTag(el, 'cd-competencies')}${parts.head}<tbody>${parts.groups.map(g => g.heading + g.rows.join('')).join('')}</tbody></table>`;
    }

    function elementStyle(el) {
        return `left:${el.x}mm;top:${el.y}mm;width:${el.w}mm;min-height:${el.h}mm;font-size:${el.fontSize}pt;font-weight:${el.bold ? '700' : '400'};font-style:${el.italic ? 'italic' : 'normal'};text-align:${el.align};color:${el.color};background:${el.background};${el.border && !isTable(el) ? 'border:1px solid currentColor;' : ''}`;
    }

    function renderElement(el, doc, data, editing = false) {
        const show = visible(el.condition, data, doc.assessmentSource);
        if (!show && !editing) return '';
        return `<div class="cd-element ${!show ? 'cd-condition-hidden' : ''}" data-element="${esc(el.id)}" data-kind="${el.type}" style="${elementStyle(el)}">${elementContent(el, doc, data)}${editing ? '<div class="cd-resize" title="Größe ändern"></div>' : ''}</div>`;
    }

    function validate(doc) {
        const fail = msg => {
            throw new Error(msg)
        };
        if (!doc || doc.schemaVersion !== 1) fail('Unbekanntes Vorlagenformat.');
        if (typeof doc.title !== 'string' || !doc.title.trim() || doc.title.length > 180) fail('Ungültiger Vorlagentitel.');
        if (!Number.isInteger(doc.pageCount) || doc.pageCount < 1 || doc.pageCount > 30) fail('1 bis 30 Seiten sind möglich.');
        if (!['teacher', 'self'].includes(doc.assessmentSource)) fail('Ungültige Bewertungsquelle.');
        if (![1, 2.5, 5, 10].includes(doc.grid) || typeof doc.snap !== 'boolean') fail('Ungültiges Raster.');
        if (!Array.isArray(doc.selectedIds) || doc.selectedIds.length > 2000 || doc.selectedIds.some(id => typeof id !== 'string' || id.length > 100)) fail('Ungültige Bausteinauswahl.');
        if (!doc.translations?.global || !doc.translations?.individual || Array.isArray(doc.translations.global) || Array.isArray(doc.translations.individual)) fail('Übersetzungen fehlen.');
        for (const map of [doc.translations.global, ...Object.values(doc.translations.individual)]) {
            if (!map || typeof map !== 'object' || Array.isArray(map)) fail('Ungültige Übersetzung.');
            for (const [key, val] of Object.entries(map)) if (!LEVELS.some(l => l.id === key) || typeof val !== 'string' || val.length > 4000) fail('Ungültiger Übersetzungstext.');
        }
        if (!Array.isArray(doc.elements) || doc.elements.length > 200) fail('Maximal 200 Elemente sind möglich.');
        const ids = new Set();
        for (const e of doc.elements) {
            if (!e || typeof e.id !== 'string' || !/^[a-zA-Z0-9_-]{1,80}$/.test(e.id) || ids.has(e.id)) fail('Ungültige oder doppelte Element-ID.');
            ids.add(e.id);
            if (!['text', 'image', 'line', 'table', 'competency'].includes(e.type)) fail('Unbekannter Elementtyp.');
            if (typeof e.name !== 'string' || !e.name.trim() || e.name.length > 180) fail('Ungültiger Elementname.');
            if (!Number.isInteger(e.page) || e.page < 1 || e.page > doc.pageCount) fail('Ungültige Elementseite.');
            for (const k of ['x', 'y', 'w', 'h', 'fontSize']) if (!Number.isFinite(e[k])) fail('Ungültige Elementgröße.');
            if (e.x < 0 || e.y < 0 || e.w < 1 || e.h < 0.2 || e.x + e.w > 210.1 || e.y + e.h > 297.1 || e.fontSize < 6 || e.fontSize > 72) fail('Element außerhalb der A4-Seite.');
            if (!['left', 'center', 'right'].includes(e.align)) fail('Ungültige Ausrichtung.');
            for (const k of ['color', 'background']) if (!/^#[a-f0-9]{6}$/i.test(e[k])) fail('Ungültige Farbe.');
            for (const k of ['bold', 'italic', 'border', 'repeat']) if (typeof e[k] !== 'boolean') fail('Ungültige Formatierung.');
            if (Object.hasOwn(e, 'tableStyle')) {
                if (!isTable(e) || !e.tableStyle || typeof e.tableStyle !== 'object' || Array.isArray(e.tableStyle)) fail('Ungültige Tabellengestaltung.');
                for (const [key, value] of Object.entries(e.tableStyle)) {
                    if (!Object.hasOwn(TABLE_DEFAULTS, key)) fail('Unbekannte Tabellenoption.');
                    if (typeof TABLE_DEFAULTS[key] === 'boolean' && typeof value !== 'boolean') fail('Ungültiger Schalter für die Tabelle.');
                    if (typeof TABLE_DEFAULTS[key] === 'string' && (typeof value !== 'string' || !/^#[a-f0-9]{6}$/i.test(value))) fail('Ungültige Tabellenfarbe.');
                    if (typeof TABLE_DEFAULTS[key] === 'number' && (!Number.isFinite(value) || value < 0.25 || value > 6)) fail('Linienstärken müssen zwischen 0,25 und 6 pt liegen.');
                }
            }
            if (e.condition) {
                const c = e.condition;
                if (typeof c.objectiveId !== 'string' || !['eq', 'gte'].includes(c.operator) || !['show', 'hide'].includes(c.effect) || !LEVELS.some(l => l.id === c.level) || (c.operator === 'gte' && c.level === 'gray')) fail('Ungültige Bedingung. Grau ist keine erreichte Kompetenzstufe.');
            }
            if (e.type === 'text' && (typeof e.text !== 'string' || e.text.length > 20000)) fail('Ungültiger Text.');
            if (e.type === 'image' && (typeof e.src !== 'string' || e.src.length > 2800000 || (e.src && !/^data:image\/(png|jpeg|webp);base64,[A-Za-z0-9+/]+=*$/.test(e.src)))) fail('Nur eingebettete PNG-, JPEG- oder WebP-Bilder bis 2 MB sind zulässig.');
            if (e.type === 'table' && (typeof e.headerRow !== 'boolean' || !Array.isArray(e.cells) || !e.cells.length || e.cells.length > 100 || e.cells.some(r => !Array.isArray(r) || !r.length || r.length > 20 || r.length !== e.cells[0].length || r.some(t => typeof t !== 'string' || t.length > 4000)))) fail('Ungültige Tabelle.');
            if (e.type === 'competency') {
                if (!Array.isArray(e.columns) || e.columns.length !== 4 || new Set(e.columns.map(c => c.id)).size !== 4 || e.columns.some(c => !LEVELS.some(l => l.id === c.id) || typeof c.title !== 'string' || c.title.length > 150 || typeof c.visible !== 'boolean')) fail('Ungültige Stufenspalten.');
                if (typeof e.mark !== 'string' || e.mark.length > 10 || typeof e.headerTitle !== 'string' || e.headerTitle.length > 150) fail('Ungültige Tabellenbeschriftung.');
                for (const k of ['groupByArea', 'verticalHeaders', 'showTranslation', 'autoContinue']) if (typeof e[k] !== 'boolean') fail('Ungültige Tabellenoption.');
            }
        }
        if (JSON.stringify(doc).length > 10000000) fail('Die Vorlage ist größer als 10 MB.');
        return doc;
    }

    globalThis.CertEngine = {
        LEVELS,
        TABLE_DEFAULTS,
        isTable,
        tableStyle,
        tableTag,
        clone,
        esc,
        uid,
        sample,
        baseElement,
        standard,
        allObjectives,
        levelFor,
        visible,
        translation,
        interpolate,
        tableParts,
        elementContent,
        elementStyle,
        renderElement,
        validate
    };
})();
