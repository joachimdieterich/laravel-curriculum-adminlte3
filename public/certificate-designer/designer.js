/* Graphical editor. No external requests in demo mode; school data only via authenticated API. */
(() => {
    'use strict';
    const E = globalThis.CertEngine, $ = s => document.querySelector(s), esc = E.esc, clone = E.clone;
    const config = JSON.parse($('#certificate-designer-config')?.textContent || 'null');
    const state = {
        data: E.sample(),
        doc: null,
        selected: null,
        tab: 'content',
        preview: false,
        zoom: .78,
        history: [],
        future: [],
        templateId: null,
        revision: null,
        dirty: false,
        translationId: 'global',
        status: '',
        warnings: [],
        results: [],
        searchKind: null
    };
    state.doc = E.standard(state.data);
    if (config) {
        state.data = {
            curriculum: {id: null, title: ''},
            student: {id: null, name: 'Name Schüler/-in'},
            organization: config.organization,
            date: new Date().toLocaleDateString('de-DE'),
            areas: [],
            achievements: {}
        };
        state.doc = E.standard(state.data);
    }
    const selected = () => state.doc.elements.find(e => e.id === state.selected);
    const opts = (values, current) => values.map(v => `<option value="${esc(v.value ?? v.id)}" ${String(v.value ?? v.id) === String(current) ? 'selected' : ''}>${esc(v.label ?? v.title)}</option>`).join('');
    const levelOpts = current => opts(E.LEVELS, current);
    const objectiveOpts = current => opts(E.allObjectives(state.data).map(o => ({
        id: String(o.id),
        title: o.title
    })), current);
    const check = (label, attr, value) => `<label class="check"><input type="checkbox" ${attr} ${value ? 'checked' : ''}>${esc(label)}</label>`;
    const field = (label, key, value, type = 'text', extra = '') => `<label>${esc(label)}<input type="${type}" data-prop="${key}" value="${esc(value)}" ${extra}></label>`;

    function notify(message, error = false) {
        $('.toast')?.remove();
        const el = document.createElement('div');
        el.className = 'toast' + (error ? ' error' : '');
        el.setAttribute('role', error ? 'alert' : 'status');
        el.textContent = message;
        document.body.append(el);
        setTimeout(() => el.remove(), 5000);
    }

    function checkpoint() {
        state.history.push(JSON.stringify(state.doc));
        if (state.history.length > 30) state.history.shift();
        state.future = [];
    }

    function changed() {
        state.dirty = true;
        state.status = 'Ungespeicherte Änderungen';
    }

    function change(fn) {
        checkpoint();
        fn();
        changed();
        render();
    }

    function restore(dir) {
        const from = dir === 'undo' ? state.history : state.future, to = dir === 'undo' ? state.future : state.history;
        if (!from.length) return;
        to.push(JSON.stringify(state.doc));
        state.doc = JSON.parse(from.pop());
        if (!selected()) state.selected = null;
        changed();
        render();
    }

    function render() {
        const sidebarScroll = $('.sidebar')?.scrollTop || 0, inspectorScroll = $('.inspector')?.scrollTop || 0,
            stageScroll = $('.stage')?.scrollTop || 0;
        document.body.classList.toggle('preview-mode', state.preview);
        $('#app').innerHTML = `<header class="app-header"><div class="brand"><div class="brand-mark">c</div><div><strong>Curriculum <span style="font-weight:400;color:#778b7f">/ Zertifikate</span></strong></div></div><div class="header-actions"><button data-action="help" title="Hilfe">?</button><button data-action="templates">Vorlagen</button><button data-action="preview" class="${state.preview ? 'active' : ''}">${state.preview ? 'Bearbeiten' : 'Vorschau'}</button><button data-action="print">PDF / Drucken</button><button data-action="save" class="primary">Speichern</button></div></header>
<div class="filebar"><input aria-label="Vorlagenname" id="document-title" value="${esc(state.doc.title)}"><span class="tag">A4 · HOCHFORMAT</span><span class="spacer"></span><button class="icon" title="Rückgängig (Strg+Z)" data-action="undo" ${!state.history.length ? 'disabled' : ''}>↶</button><button class="icon" title="Wiederholen (Strg+Umschalt+Z)" data-action="redo" ${!state.future.length ? 'disabled' : ''}>↷</button><button data-action="import">Import</button><button data-action="export">Export</button></div>
<div class="workspace"><aside class="sidebar"><nav class="tabs"><button data-tab="content" class="${state.tab === 'content' ? 'active' : ''}">Bausteine</button><button data-tab="elements" class="${state.tab === 'elements' ? 'active' : ''}">Elemente</button><button data-tab="translations" class="${state.tab === 'translations' ? 'active' : ''}">Übersetzungen</button></nav>${sidebar()}</aside>
<main class="stage"><div class="stage-toolbar"><button data-action="zoomout" aria-label="Verkleinern">−</button><span style="font-size:11px">${Math.round(state.zoom * 100)} %</span><button data-action="zoomin" aria-label="Vergrößern">+</button><span style="color:#b4c5b9">│</span>${check('Raster', 'id="snap-toggle"', state.doc.snap)}<select aria-label="Rasterweite" id="grid-size">${opts([1, 2.5, 5, 10].map(v => ({
            value: v,
            label: v + ' mm'
        })), state.doc.grid)}</select><button data-action="addpage">+ Seite</button></div><div class="warnings" hidden></div><div class="canvas-stack"></div></main><aside class="inspector">${inspector()}</aside></div>
<input id="import-file" type="file" accept="application/json,.json" hidden><input id="image-file" type="file" accept="image/png,image/jpeg,image/webp" hidden><dialog id="modal"></dialog>`;
        renderCanvas();
        $('.sidebar').scrollTop = sidebarScroll;
        $('.inspector').scrollTop = inspectorScroll;
        $('.stage').scrollTop = stageScroll;
    }

    function sidebar() {
        if (state.tab === 'elements') return `<h2>Element hinzufügen</h2><p class="muted" style="margin-top:6px">Frei auf der Seite platzieren.</p><div class="palette"><button data-add="text"><b>T</b>Text / Variable</button><button data-add="image"><b>▧</b>Bild / Logo</button><button data-add="table"><b>▦</b>Freie Tabelle</button><button data-add="line"><b>―</b>Linie</button><button data-add="competency" class="wide"><b>☷</b>Kompetenztabelle mit Kreuzen</button></div><div class="section-title"><h3>EBENEN</h3><small class="muted">${state.doc.elements.length} Elemente</small></div>${state.doc.elements.map(e => `<div tabindex="0" role="button" class="layer ${e.id === state.selected ? 'active' : ''}" data-select="${e.id}"><span>${({
            text: 'T',
            image: '▧',
            table: '▦',
            competency: '☷',
            line: '―'
        })[e.type]}</span>${esc(e.name)}<small>S. ${e.page}${e.condition ? ' · ◇' : ''}</small></div>`).join('')}<p class="hint">Ziehen zum Verschieben. Am Eckpunkt die Größe ändern. Pfeiltasten: 1 mm; Umschalt: Rasterweite. Strg+Z macht Änderungen rückgängig.</p>`;
        if (state.tab === 'translations') {
            const map = state.translationId === 'global' ? state.doc.translations.global : state.doc.translations.individual[state.translationId] || {};
            return `<h2>Stufen in Worte übersetzen</h2><p class="muted" style="margin-top:8px">Diese Texte ersetzen die Variable eines Bausteins abhängig von seiner Bewertung.</p><label>Gültig für</label><select id="translation-scope"><option value="global">Alle Bausteine · Standard</option>${objectiveOpts(state.translationId)}</select>${E.LEVELS.map(l => `<div class="translation-row"><label><span class="level-dot" style="background:${l.color}"></span>${l.short}${state.translationId !== 'global' && !Object.hasOwn(map, l.id) ? ' · Standard' : ''}</label><textarea data-translation="${l.id}" placeholder="${esc(state.doc.translations.global[l.id])}">${esc(map[l.id] ?? '')}</textarea>${state.translationId !== 'global' && Object.hasOwn(map, l.id) ? `<button data-reset-translation="${l.id}" style="font-size:10px">Standard übernehmen</button>` : ''}</div>`).join('')}<p class="hint">Individuelle Texte haben Vorrang. Ein ausdrücklich leerer Text bleibt leer. „Standard übernehmen“ stellt die globale Zuordnung wieder her.</p>`;
        }
        let source = config ? `<label>Curriculum suchen</label><input id="curriculum-search" placeholder="Titel eingeben …" autocomplete="off">${state.searchKind === 'curriculum' ? resultsHtml() : ''}<div class="hint" style="margin-top:8px">${esc(state.data.curriculum.title || 'Noch kein Curriculum ausgewählt')}</div>` : `<span class="tag">BEISPIEL-CURRICULUM</span><h2 style="margin-top:10px">${esc(state.data.curriculum.title)}</h2>`;
        return `${source}<div class="section-title"><h3>AUSWAHL</h3><small>${state.doc.selectedIds.length} Bausteine</small></div><div class="row"><button data-action="selectall" style="font-size:11px">Alle auswählen</button><button data-action="selectnone" style="font-size:11px">Keine</button></div>${state.data.areas.map(a => `<details class="area" open><summary>${esc(a.title)} <button class="area-toggle" data-area="${esc(a.id)}">Alle / keine</button></summary>${a.objectives.map(o => check(o.title, `data-objective="${esc(o.id)}"`, state.doc.selectedIds.includes(String(o.id)))).join('')}</details>`).join('')}
<hr><h3>VORSCHAU-DATEN</h3>${config ? `<label>Person suchen</label><input id="student-search" placeholder="Name eingeben …" autocomplete="off">${state.searchKind === 'student' ? resultsHtml() : ''}<p class="hint">${esc(state.data.student.name)}</p>` : `<label>Name Schüler/-in</label><input id="student-name" value="${esc(state.data.student.name)}">`}
<label>Bewertung verwenden</label><select id="assessment-source">${opts([{
            value: 'teacher',
            label: 'Lehrkrafteinschätzung'
        }, {
            value: 'self',
            label: 'Selbsteinschätzung'
        }], state.doc.assessmentSource)}</select><label>Ausstellungsdatum</label><input id="certificate-date" value="${esc(state.data.date)}">
${!config ? `<details style="margin-top:16px"><summary class="muted" style="cursor:pointer">Beispielbewertungen ändern</summary>${E.allObjectives(state.data).map(o => `<div class="assessment-row"><span>${esc(o.title)}</span><select data-assessment="${esc(o.id)}">${levelOpts(E.levelFor(state.data, o.id, state.doc.assessmentSource))}</select></div>`).join('')}</details>` : ''}<p class="hint">Grau bedeutet „nicht bewertet“. Bei „mindestens“ gilt Rot → Gelb → Grün. Grau erfüllt keine Mindeststufe.</p>`;
    }

    function resultsHtml() {
        return `<div>${state.results.map(r => `<button class="data-result" data-result="${esc(r.id)}">${esc(r.title || r.name)}</button>`).join('') || '<p class="muted">Keine Treffer</p>'}</div>`;
    }

    function tableControls(e) {
        if (!E.isTable(e)) return '';
        const s = E.tableStyle(e),
            input = (label, key, type, disabled = false) => `<label>${label}<input type="${type}" data-table-style="${key}" value="${esc(s[key])}" ${type === 'number' ? 'min="0.25" max="6" step="0.25"' : ''} ${disabled ? 'disabled' : ''}></label>`;
        return `${e.type === 'competency' ? `<hr><h3>BEREICHSÜBERSCHRIFTEN</h3>${check('Bereichshintergrund anzeigen', 'data-table-style="areaBackgroundEnabled"', s.areaBackgroundEnabled)}<div class="grid2">${input('Hintergrundfarbe', 'areaBackground', 'color', !s.areaBackgroundEnabled)}${input('Schriftfarbe', 'areaTextColor', 'color')}</div>` : ''}
 <hr><h3>TABELLENRAHMEN</h3>${check('Außenrahmen anzeigen', 'data-table-style="outerBorder"', s.outerBorder)}<div class="grid2">${input('Rahmenfarbe', 'outerBorderColor', 'color', !s.outerBorder)}${input('Stärke · pt', 'outerBorderWidth', 'number', !s.outerBorder)}</div>
 ${check('Innere Linien anzeigen', 'data-table-style="innerBorders"', s.innerBorders)}<div class="grid2">${input('Linienfarbe', 'innerBorderColor', 'color', !s.innerBorders)}${input('Stärke · pt', 'innerBorderWidth', 'number', !s.innerBorders)}</div><p class="muted">Für eine rahmenlose Tabelle beide Haken ausschalten. Die Auswahlmarkierung im Editor wird nicht gedruckt.</p>`;
    }

    function inspector() {
        const e = selected();
        if (!e || state.preview) return `<div class="empty"><div class="big">↖</div><h2>Dein Zertifikat gestalten</h2><p class="muted" style="margin-top:10px">Wähle ein Element auf der Seite aus. Hier bearbeitest du Inhalt, Position und Regeln.</p></div>`;
        return `<div class="heading"><h2>Element bearbeiten</h2><span>${esc(({
            text: 'TEXT',
            image: 'BILD',
            line: 'LINIE',
            table: 'TABELLE',
            competency: 'KOMPETENZEN'
        })[e.type])}</span></div>${field('Bezeichnung', 'name', e.name)}${tableControls(e)}
${e.type === 'text' ? `<label>Text</label><textarea data-prop="text" id="element-text">${esc(e.text)}</textarea><label>Variable einfügen</label><select id="variable-picker"><option value="">Variable wählen …</option>${opts([{
            id: '{{student.name}}',
            title: 'Name Schüler/-in'
        }, {id: '{{school.name}}', title: 'Schulname'}, {id: '{{school.city}}', title: 'Ort'}, {
            id: '{{date}}',
            title: 'Datum'
        }, {
            id: '{{curriculum.title}}',
            title: 'Curriculum'
        }], '')}${E.allObjectives(state.data).map(o => `<option value="${esc('{{objective:' + o.id + ':text}}')}">${esc(o.title)} · Stufentext</option>`).join('')}</select>` : ''}
${e.type === 'image' ? `<button data-action="uploadimage" style="width:100%;margin-top:12px">Bild auswählen …</button><p class="muted" style="margin-top:5px">PNG, JPG oder WebP · max. 2 MB</p>` : ''}
${e.type === 'competency' ? `<hr><h3>STUFENSPALTEN</h3>${e.columns.map((c, i) => `<div class="column"><input type="checkbox" aria-label="${esc(c.title)} anzeigen" data-column-visible="${i}" ${c.visible ? 'checked' : ''}><span class="level-dot" style="background:${E.LEVELS.find(l => l.id === c.id).color}"></span><input type="text" aria-label="Spaltentitel ${esc(c.id)}" data-column-title="${i}" value="${esc(c.title)}"><button data-column-move="${i}" title="Spalte nach links" ${i === 0 ? 'disabled' : ''}>↑</button></div>`).join('')}${field('Überschrift der Bausteinspalte', 'headerTitle', e.headerTitle)}${field('Zeichen für erreichte Stufe', 'mark', e.mark)}${check('Überschriften senkrecht', 'data-prop="verticalHeaders"', e.verticalHeaders)}${check('Nach Bereich gruppieren', 'data-prop="groupByArea"', e.groupByArea)}${check('Übersetzungsspalte anzeigen', 'data-prop="showTranslation"', e.showTranslation)}${check('Auf Folgeseiten fortsetzen', 'data-prop="autoContinue"', e.autoContinue)}<p class="muted">Ausgeblendete Stufen erhalten keine Kreuze in anderen Spalten. Die Höhe legt den Platz vor dem Seitenumbruch fest.</p>` : ''}
${e.type === 'table' ? `<hr><h3>TABELLENINHALT</h3><div class="table-editor"><table>${e.cells.map((r, i) => `<tr>${r.map((t, j) => `<td><textarea aria-label="Zeile ${i + 1}, Spalte ${j + 1}" data-cell="${i},${j}">${esc(t)}</textarea></td>`).join('')}</tr>`).join('')}</table></div><div class="row"><button data-action="addrow">+ Zeile</button><button data-action="addcolumn">+ Spalte</button></div><div class="row" style="margin-top:6px"><button data-action="delrow">− Zeile</button><button data-action="delcolumn">− Spalte</button></div>${check('Erste Zeile als Überschrift', 'data-prop="headerRow"', e.headerRow)}<p class="muted">Variablen funktionieren auch in Tabellenzellen.</p>` : ''}
<hr><h3>POSITION & GRÖSSE · MM</h3><div class="grid2">${field('X', 'x', e.x, 'number', 'min="0" step="0.5"')}${field('Y', 'y', e.y, 'number', 'min="0" step="0.5"')}${field('Breite', 'w', e.w, 'number', 'min="1" step="0.5"')}${field('Höhe', 'h', e.h, 'number', 'min="1" step="0.5"')}</div><label>Seite</label><select data-prop="page">${opts(Array.from({length: state.doc.pageCount}, (_, i) => ({
            value: i + 1,
            label: 'Seite ' + (i + 1)
        })), e.page)}</select><label>An der Seite ausrichten</label><div class="formatbar"><button data-align="left" title="Links am Seitenrand">⇤</button><button data-align="center" title="Horizontal zentrieren">↔</button><button data-align="right" title="Rechts am Seitenrand">⇥</button><button data-align="grid" title="Am Raster ausrichten">▦</button></div>
${!['image', 'line'].includes(e.type) ? `<div class="grid2">${field('Schriftgröße · pt', 'fontSize', e.fontSize, 'number', 'min="6" max="72"')}${field('Textfarbe', 'color', e.color, 'color')}</div><div class="formatbar"><button data-format="bold" class="${e.bold ? 'active' : ''}"><b>B</b></button><button data-format="italic" class="${e.italic ? 'active' : ''}"><i>I</i></button>${['left', 'center', 'right'].map(a => `<button data-text-align="${a}" class="${e.align === a ? 'active' : ''}" title="Text ${a}">${({
            left: '≡ ←',
            center: '≡',
            right: '→ ≡'
        })[a]}</button>`).join('')}</div>` : ''}
${field('Hintergrund', 'background', e.background, 'color')}${!E.isTable(e) ? check('Elementrahmen anzeigen', 'data-prop="border"', e.border) : ''}${check('Auf jeder Seite wiederholen', 'data-prop="repeat"', e.repeat)}
<hr><h3>SICHTBARKEITSREGEL</h3>${check('Bedingung aktivieren', 'id="condition-enabled"', !!e.condition)}${e.condition ? `<div class="condition-box"><label>Wenn Baustein</label><select data-condition="objectiveId">${objectiveOpts(e.condition.objectiveId)}</select><label>Kompetenzstufe</label><select data-condition="operator">${opts([{
            value: 'eq',
            label: 'exakt'
        }, {
            value: 'gte',
            label: 'mindestens'
        }], e.condition.operator)}</select><select style="margin-top:6px" data-condition="level">${opts(E.LEVELS.filter(l => e.condition.operator === 'eq' || l.id !== 'gray'), e.condition.level)}</select><label>Dann dieses Element</label><select data-condition="effect">${opts([{
            value: 'show',
            label: 'anzeigen (sonst ausblenden)'
        }, {value: 'hide', label: 'ausblenden (sonst anzeigen)'}], e.condition.effect)}</select></div>` : ''}
<hr><div class="row"><button data-action="duplicate">Duplizieren</button><button data-action="delete" class="danger">Löschen</button></div><div class="row" style="margin-top:8px"><button data-action="backward">Nach hinten</button><button data-action="forward">Nach vorne</button></div>`;
    }

    function elementNode(e) {
        const box = document.createElement('div');
        box.innerHTML = E.renderElement(e, state.doc, state.data, !state.preview);
        const node = box.firstElementChild;
        if (node && e.type === 'image') node.style.height = e.h + 'mm';
        if (node && e.id === state.selected) node.classList.add('selected');
        return node;
    }

// Measure actual browser line wrapping, then split rows with a repeated area heading and table header.
    function tableChunks(e) {
        const parts = E.tableParts(e, state.doc, state.data), probe = document.createElement('div');
        probe.className = 'cd-measure';
        probe.style.cssText += `width:${e.w}mm;font-size:${e.fontSize}pt;line-height:1.4;font-weight:${e.bold ? 700 : 400};font-style:${e.italic ? 'italic' : 'normal'}`;
        probe.innerHTML = `${E.tableTag(e, 'cd-competencies')}${parts.head}<tbody></tbody></table>`;
        document.body.append(probe);
        const body = probe.querySelector('tbody'), table = probe.querySelector('table'),
            pxPerMm = probe.getBoundingClientRect().width / e.w;
        const chunks = [];
        let html = '', used = 0, capacity = e.h;
        const height = (s) => {
            body.innerHTML = s;
            return table.getBoundingClientRect().height / pxPerMm;
        };
        for (const group of parts.groups) {
            let groupStarted = false;
            for (const row of group.rows) {
                let addition = (groupStarted ? '' : group.heading) + row;
                if (html && height(html + addition) > capacity) {
                    chunks.push(html);
                    html = '';
                    capacity = 237;
                    groupStarted = false;
                    addition = group.heading + row;
                }
                if (height(html + addition) > capacity) state.warnings.push(`„${e.name}“: Eine Tabellenzeile passt nicht in den verfügbaren Platz. Höhe/Schriftgröße anpassen.`);
                html += addition;
                groupStarted = true;
                used++;
            }
        }
        if (html || !used) chunks.push(html);
        probe.remove();
        return chunks.map(c => `${E.tableTag(e, 'cd-competencies')}${parts.head}<tbody>${c}</tbody></table>`);
    }

    function renderCanvas() {
        const stack = $('.canvas-stack');
        stack.innerHTML = '';
        state.warnings = [];
        const pages = [];
        const newPage = (label) => {
            const wrap = document.createElement('div');
            wrap.className = 'sheet-wrap';
            wrap.style.width = (210 * 96 / 25.4 * state.zoom) + 'px';
            wrap.style.height = (297 * 96 / 25.4 * state.zoom + 23) + 'px';
            wrap.innerHTML = `<div class="page-label">${esc(label)}</div><section class="cd-page ${state.preview ? '' : 'editing'} ${state.doc.snap && !state.preview ? 'grid' : ''}" style="transform:scale(${state.zoom});--grid:${state.doc.grid}mm"><div class="safe-area"></div></section>`;
            stack.append(wrap);
            return wrap.querySelector('.cd-page');
        };
        const append = (page, e) => {
            const n = elementNode(e);
            if (n) page.append(n);
            return n;
        };
        for (let n = 1; n <= state.doc.pageCount; n++) {
            const page = newPage('SEITE ' + n);
            page.dataset.page = n;
            pages.push(page);
            for (const e of state.doc.elements.filter(e => e.page === n || e.repeat)) append(page, e);
        }
        const continuations = [];
        for (const e of state.doc.elements.filter(e => e.type === 'competency' && e.autoContinue)) {
            if (!E.visible(e.condition, state.data, state.doc.assessmentSource) && state.preview) continue;
            const chunks = tableChunks(e);
            for (const page of pages) {
                const n = page.querySelector(`[data-element="${e.id}"]`);
                if (n) {
                    n.querySelector('.cd-table').outerHTML = chunks[0];
                    if (chunks.length > 1) continuations.push({e, chunks: chunks.slice(1)});
                }
            }
        }
        for (const {e, chunks} of continuations) {
            for (const [i, content] of chunks.entries()) {
                const page = newPage('FORTSETZUNG · ' + e.name + ' · ' + (i + 2));
                page.classList.remove('editing', 'grid');
                page.dataset.continuation = 'true';
                const copy = {...e, y: 20, h: 237, repeat: false};
                const n = append(page, copy);
                if (n) {
                    n.querySelector('.cd-table').outerHTML = content;
                    n.classList.remove('selected');
                    n.querySelector('.cd-resize')?.remove();
                }
                for (const footer of state.doc.elements.filter(f => f.repeat && f.id !== e.id)) append(page, footer);
            }
        }
        for (const page of stack.querySelectorAll('.cd-page')) {
            const pageRect = page.getBoundingClientRect();
            for (const n of page.querySelectorAll('.cd-element')) {
                if (n.classList.contains('cd-condition-hidden')) continue;
                const r = n.getBoundingClientRect();
                if (r.bottom > pageRect.bottom + 1 || r.right > pageRect.right + 1) state.warnings.push('Ein Element ragt über den Seitenrand. Position oder Größe anpassen.');
            }
        }
        const knownIds = new Set(E.allObjectives(state.data).map(o => String(o.id)));
        if (state.doc.selectedIds.some(id => !knownIds.has(id))) state.warnings.push('Die Vorlage verweist auf Bausteine eines anderen Curriculums. Auswahl und Regeln prüfen.');
        for (const e of state.doc.elements) if (e.condition && !knownIds.has(e.condition.objectiveId)) state.warnings.push(`„${e.name}“: Der Baustein der Bedingung fehlt im geladenen Curriculum.`);
        if (!config && state.preview) {
        } // Demo values are deliberately separate from template storage.
        const warnings = [...new Set(state.warnings)];
        $('.warnings').hidden = !warnings.length;
        $('.warnings').textContent = warnings.join(' ');
    }

    function clampElement(e) {
        e.w = Math.min(210, Math.max(1, e.w));
        e.h = Math.min(297, Math.max(.2, e.h));
        e.x = Math.max(0, Math.min(210 - e.w, e.x));
        e.y = Math.max(0, Math.min(297 - e.h, e.y));
        e.fontSize = Math.max(6, Math.min(72, e.fontSize));
        for (const k of ['x', 'y', 'w', 'h']) e[k] = Math.round(e[k] * 10) / 10;
    }

    function add(type) {
        change(() => {
            const defaults = E.standard(state.data).elements.find(e => e.type === type);
            let e = E.baseElement(type, {y: 45});
            if (type === 'text') e.text = 'Neuer Text';
            if (type === 'image') {
                e.src = '';
                e.w = 45;
                e.h = 25;
            }
            if (type === 'line') e.h = 1;
            if (type === 'table') {
                e.cells = [['Überschrift 1', 'Überschrift 2'], ['Text', 'Text']];
                e.headerRow = true;
                e.h = 25;
            }
            if (type === 'competency') {
                e = {...clone(defaults), id: E.uid(), y: 45, h: 190};
            }
            state.doc.elements.push(e);
            state.selected = e.id;
            state.tab = 'elements';
        });
    }

    async function api(path, method = 'GET', payload) {
        const r = await fetch(config.apiBase + path, {
            method,
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrf
            }, ...(payload ? {body: JSON.stringify(payload)} : {})
        });
        if (!r.ok) {
            const error = await r.json().catch(() => ({}));
            throw new Error(error.message || `Anfrage fehlgeschlagen (${r.status}).`);
        }
        return r.status === 204 ? null : r.json();
    }

    function download(name, body, type) {
        const url = URL.createObjectURL(new Blob([body], {type})), a = document.createElement('a');
        a.href = url;
        a.download = name;
        a.click();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    function localTemplates() {
        try {
            return JSON.parse(localStorage.getItem('curriculum-certificate-designer-demo-v1') || '[]');
        } catch {
            return [];
        }
    }

    async function save(asNew = false) {
        E.validate(state.doc);
        if (config) {
            if (!state.data.curriculum.id) throw new Error('Bitte zuerst ein Curriculum auswählen.');
            const r = await api('/templates' + (state.templateId && !asNew ? '/' + state.templateId : ''), state.templateId && !asNew ? 'PUT' : 'POST', {
                title: state.doc.title,
                curriculum_id: state.data.curriculum.id,
                document: state.doc,
                revision: state.revision
            });
            state.templateId = r.id;
            state.revision = r.revision;
        } else {
            const list = localTemplates();
            const id = state.templateId && !asNew ? state.templateId : E.uid(),
                item = {id, title: state.doc.title, document: clone(state.doc)};
            const i = list.findIndex(t => t.id === id);
            if (i < 0) list.push(item); else list[i] = item;
            localStorage.setItem('curriculum-certificate-designer-demo-v1', JSON.stringify(list));
            state.templateId = id;
        }
        state.dirty = false;
        state.status = config ? 'Vorlage gespeichert' : 'Im Browser gespeichert';
        render();
        notify('Vorlage gespeichert.');
    }

    function openDialog(html) {
        const dialog = $('#modal');
        dialog.innerHTML = '<button class="close" data-action="closemodal" aria-label="Schließen">×</button>' + html;
        dialog.showModal();
    }

    async function templates() {
        const list = config ? await api('/templates') : localTemplates();
        openDialog(`<h2>Deine Vorlagen</h2><p>Mit der Standardvorlage beginnen oder einen gespeicherten Entwurf öffnen.</p><div class="row"><button data-action="standard" class="primary">Neue Standardvorlage</button><button data-action="savecopy">Als neue Vorlage speichern</button></div><div style="margin-top:20px">${list.map(t => `<div class="saved-template"><span>${esc(t.title)}</span><div class="row"><button data-load-template="${esc(t.id)}">Öffnen</button><button class="danger" data-delete-template="${esc(t.id)}">Löschen</button></div></div>`).join('') || '<p class="muted">Noch keine Vorlage gespeichert.</p>'}</div>`);
    }

    async function loadTemplate(id) {
        if (state.dirty && !confirm('Ungespeicherte Änderungen verwerfen?')) return;
        const t = config ? await api('/templates/' + id) : localTemplates().find(t => String(t.id) === String(id));
        E.validate(t.document);
        if (config) {
            await loadCurriculum(t.curriculum_id, false);
        }
        state.doc = clone(t.document);
        state.templateId = t.id;
        state.revision = t.revision;
        state.selected = null;
        state.history = [];
        state.future = [];
        state.dirty = false;
        state.status = 'Vorlage geladen';
        state.translationId = 'global';
        render();
    }

    async function loadCurriculum(id, resetSelection = true) {
        const d = await api('/curricula/' + id);
        state.data.curriculum = d.curriculum;
        state.data.areas = d.areas;
        state.data.achievements = {};
        state.results = [];
        state.searchKind = null;
        if (resetSelection) {
            checkpoint();
            state.doc.selectedIds = E.allObjectives(state.data).map(o => String(o.id));
            state.templateId = null;
            state.revision = null;
            changed();
        }
        if (state.data.student.id) await loadAchievements();
    }

    async function loadAchievements() {
        if (!state.data.curriculum.id || !state.data.student.id) return;
        const r = await api(`/curricula/${state.data.curriculum.id}/students/${state.data.student.id}`);
        state.data.achievements = r.achievements;
        state.data.student = r.student;
    }

    async function print() {
        E.validate(state.doc);
        if (config && (!state.data.curriculum.id || !state.data.student.id)) throw new Error('Zum Erstellen bitte Curriculum und Person auswählen.');
        if (config) await loadAchievements();
        const old = state.preview;
        state.preview = true;
        render();
        await Promise.all([...document.images].map(img => img.decode().catch(() => {
        })));
        if (state.warnings.length) {
            state.preview = old;
            render();
            throw new Error('Bitte die angezeigten Hinweise vor dem Drucken beheben.');
        }
        window.print();
        state.preview = old;
        render();
    }

    const actions = {
        undo: () => restore('undo'),
        redo: () => restore('redo'),
        preview: () => {
            state.preview = !state.preview;
            render();
        },
        save: () => save(),
        savecopy: () => save(true),
        templates,
        closemodal: () => $('#modal').close(),
        print,
        zoomin: () => {
            state.zoom = Math.min(1.5, Math.round((state.zoom + .1) * 100) / 100);
            render();
        },
        zoomout: () => {
            state.zoom = Math.max(.35, Math.round((state.zoom - .1) * 100) / 100);
            render();
        },
        addpage: () => change(() => {
            if (state.doc.pageCount < 30) state.doc.pageCount++;
        }),
        selectall: () => change(() => state.doc.selectedIds = E.allObjectives(state.data).map(o => String(o.id))),
        selectnone: () => change(() => state.doc.selectedIds = []),
        delete: () => change(() => {
            state.doc.elements = state.doc.elements.filter(e => e.id !== state.selected);
            state.selected = null;
        }),
        duplicate: () => {
            const e = selected();
            if (e) change(() => {
                const copy = {...clone(e), id: E.uid(), name: e.name + ' · Kopie', x: e.x + 5, y: e.y + 5};
                clampElement(copy);
                state.doc.elements.push(copy);
                state.selected = copy.id;
            });
        },
        forward: () => change(() => {
            const i = state.doc.elements.findIndex(e => e.id === state.selected);
            if (i >= 0 && i < state.doc.elements.length - 1) [state.doc.elements[i], state.doc.elements[i + 1]] = [state.doc.elements[i + 1], state.doc.elements[i]];
        }),
        backward: () => change(() => {
            const i = state.doc.elements.findIndex(e => e.id === state.selected);
            if (i > 0) [state.doc.elements[i], state.doc.elements[i - 1]] = [state.doc.elements[i - 1], state.doc.elements[i]];
        }),
        addrow: () => change(() => {
            const e = selected();
            if (e.cells.length < 100) e.cells.push(e.cells[0].map(() => ''));
        }),
        addcolumn: () => change(() => {
            const e = selected();
            if (e.cells[0].length < 20) e.cells.forEach(r => r.push(''));
        }),
        delrow: () => change(() => {
            if (selected().cells.length > 1) selected().cells.pop();
        }),
        delcolumn: () => change(() => {
            if (selected().cells[0].length > 1) selected().cells.forEach(r => r.pop());
        }),
        import: () => $('#import-file').click(),
        uploadimage: () => $('#image-file').click(),
        export: () => {
            E.validate(state.doc);
            download((state.doc.title.replace(/[^\p{L}\p{N}_-]/gu, '_') || 'Vorlage') + '.json', JSON.stringify(state.doc, null, 2), 'application/json');
            notify('Vorlage exportiert – ohne Vorschau-Personendaten.');
        },
        standard: () => {
            if (state.dirty && !confirm('Ungespeicherte Änderungen verwerfen?')) return;
            state.doc = E.standard(state.data);
            state.templateId = null;
            state.revision = null;
            state.selected = null;
            state.history = [];
            state.future = [];
            state.dirty = true;
            render();
        },
        help: () => openDialog(`<h2>Dein Zertifikat, dein Layout</h2><p><b>1 · Inhalte wählen.</b> Bereiche und Bausteine links auswählen. Die Tabelle gruppiert sie automatisch.</p><p><b>2 · Gestalten.</b> Texte, Logos und Tabellen hinzufügen, ziehen oder millimetergenau ausrichten. Ein Klick öffnet die Eigenschaften.</p><p><b>3 · Regeln & Texte.</b> Sichtbarkeit pro Element: exakt oder mindestens eine Stufe. Stufentexte gelten global oder individuell. Die Lehrkrafteinschätzung ist vorausgewählt.</p><p><b>4 · Prüfen & ausgeben.</b> In der Vorschau werden Variablen und Regeln ausgewertet. Lange Kompetenztabellen werden fortgesetzt. „PDF / Drucken“ öffnet den Browser-Druckdialog; dort „Als PDF speichern“, A4, 100 %, keine zusätzlichen Kopf-/Fußzeilen wählen.</p><p><b>Variablen:</b> <code>{{student.name}}</code> <code>{{date}}</code> <code>{{objective:ID:text}}</code> (Stufentext), <code>{{objective:ID:title}}</code> und <code>{{objective:ID:level}}</code>. Auch in freien Tabellen verwendbar.</p><p><b>Bedienung:</b> Pfeiltasten bewegen das ausgewählte Element. Strg+Z / Strg+Umschalt+Z, Entf und Strg+D sind verfügbar. Ebenen werden von oben nach unten gezeichnet.</p><p>${config ? 'Vorlagen werden für das eigene Konto und die aktuelle Organisation gespeichert.' : 'Dies ist eine lokale Demo mit fiktiven Daten. Speichern gilt für diesen Browser. Mit Export sicherst du Vorlagen als Datei.'}</p>`)
    };
    document.addEventListener('click', async event => {
        const t = event.target.closest('button,[data-select]');
        if (!t) return;
        try {
            if (t.dataset.action) {
                await actions[t.dataset.action]?.();
                return;
            }
            if (t.dataset.tab) {
                state.tab = t.dataset.tab;
                render();
                return;
            }
            if (t.dataset.add) {
                add(t.dataset.add);
                return;
            }
            if (t.dataset.select) {
                state.selected = t.dataset.select;
                render();
                return;
            }
            const e = selected();
            if (t.dataset.align) {
                change(() => {
                    if (t.dataset.align === 'grid') {
                        e.x = Math.round(e.x / state.doc.grid) * state.doc.grid;
                        e.y = Math.round(e.y / state.doc.grid) * state.doc.grid;
                    } else e.x = t.dataset.align === 'left' ? 18 : t.dataset.align === 'right' ? 192 - e.w : (210 - e.w) / 2;
                    clampElement(e);
                });
                return;
            }
            if (t.dataset.format) {
                change(() => e[t.dataset.format] = !e[t.dataset.format]);
                return;
            }
            if (t.dataset.textAlign) {
                change(() => e.align = t.dataset.textAlign);
                return;
            }
            if (t.dataset.columnMove) {
                change(() => {
                    const i = +t.dataset.columnMove;
                    [e.columns[i], e.columns[i - 1]] = [e.columns[i - 1], e.columns[i]];
                });
                return;
            }
            if (t.dataset.resetTranslation) {
                change(() => delete state.doc.translations.individual[state.translationId][t.dataset.resetTranslation]);
                return;
            }
            if (t.dataset.area) {
                event.preventDefault();
                const ids = state.data.areas.find(a => String(a.id) === t.dataset.area).objectives.map(o => String(o.id));
                change(() => {
                    if (ids.every(id => state.doc.selectedIds.includes(id))) state.doc.selectedIds = state.doc.selectedIds.filter(id => !ids.includes(id)); else state.doc.selectedIds = [...new Set([...state.doc.selectedIds, ...ids])];
                });
                return;
            }
            if (t.dataset.loadTemplate) {
                await loadTemplate(t.dataset.loadTemplate);
                return;
            }
            if (t.dataset.deleteTemplate) {
                if (!confirm('Diese gespeicherte Vorlage löschen?')) return;
                if (config) await api('/templates/' + t.dataset.deleteTemplate, 'DELETE'); else localStorage.setItem('curriculum-certificate-designer-demo-v1', JSON.stringify(localTemplates().filter(x => String(x.id) !== t.dataset.deleteTemplate)));
                if (String(state.templateId) === t.dataset.deleteTemplate) {
                    state.templateId = null;
                    state.revision = null;
                    changed();
                }
                await templates();
                return;
            }
            if (t.dataset.result) {
                if (state.searchKind === 'curriculum') {
                    if (state.dirty && !confirm('Curriculum wechseln? Die Bausteinauswahl wird ersetzt; vorhandene Regeln und Übersetzungen müssen geprüft werden.')) return;
                    await loadCurriculum(t.dataset.result);
                } else {
                    state.data.student = {
                        id: t.dataset.result,
                        name: state.results.find(r => String(r.id) === t.dataset.result).name
                    };
                    await loadAchievements();
                }
                state.searchKind = null;
                state.results = [];
                render();
                return;
            }
        } catch (err) {
            notify(err.message, true);
        }
    });
    document.addEventListener('change', async event => {
        const t = event.target, e = selected();
        try {
            if (t.id === 'import-file') {
                const f = t.files[0];
                if (!f) return;
                if (f.size > 10000000) throw new Error('Datei zu groß (maximal 10 MB).');
                const doc = JSON.parse(await f.text());
                E.validate(doc);
                if (state.dirty && !confirm('Ungespeicherte Änderungen verwerfen?')) return;
                checkpoint();
                state.doc = clone(doc);
                state.templateId = null;
                state.revision = null;
                state.selected = null;
                state.translationId = 'global';
                changed();
                render();
                notify('Vorlage importiert. Bausteinzuordnung prüfen.');
                return;
            }
            if (t.id === 'image-file') {
                const f = t.files[0];
                if (!f || !e) return;
                if (f.size > 2000000 || !['image/png', 'image/jpeg', 'image/webp'].includes(f.type)) throw new Error('Bitte PNG, JPG oder WebP bis 2 MB wählen.');
                const src = await new Promise((resolve, reject) => {
                    const r = new FileReader();
                    r.onload = () => resolve(r.result);
                    r.onerror = reject;
                    r.readAsDataURL(f);
                });
                const img = new Image();
                img.src = src;
                await img.decode();
                change(() => {
                    e.src = src;
                    e.h = Math.min(297 - e.y, e.w * img.naturalHeight / img.naturalWidth);
                    clampElement(e);
                });
                return;
            }
            if (t.dataset.tableStyle) {
                change(() => {
                    const key = t.dataset.tableStyle;
                    e.tableStyle = E.tableStyle(e);
                    e.tableStyle[key] = t.type === 'checkbox' ? t.checked : t.type === 'number' ? Math.max(.25, Math.min(6, Number(t.value) || E.TABLE_DEFAULTS[key])) : t.value;
                });
                return;
            }
            if (t.dataset.prop) {
                change(() => {
                    e[t.dataset.prop] = t.type === 'checkbox' ? t.checked : ['x', 'y', 'w', 'h', 'fontSize', 'page'].includes(t.dataset.prop) ? Number(t.value) : t.value;
                    clampElement(e);
                });
                return;
            }
            if (t.dataset.objective) {
                change(() => {
                    const id = t.dataset.objective;
                    state.doc.selectedIds = t.checked ? [...new Set([...state.doc.selectedIds, id])] : state.doc.selectedIds.filter(x => x !== id);
                });
                return;
            }
            if (t.dataset.columnVisible) {
                change(() => e.columns[+t.dataset.columnVisible].visible = t.checked);
                return;
            }
            if (t.dataset.columnTitle) {
                change(() => e.columns[+t.dataset.columnTitle].title = t.value);
                return;
            }
            if (t.dataset.cell) {
                change(() => {
                    const [r, c] = t.dataset.cell.split(',').map(Number);
                    e.cells[r][c] = t.value;
                });
                return;
            }
            if (t.dataset.translation) {
                change(() => {
                    if (state.translationId === 'global') state.doc.translations.global[t.dataset.translation] = t.value; else {
                        state.doc.translations.individual[state.translationId] ??= {};
                        state.doc.translations.individual[state.translationId][t.dataset.translation] = t.value;
                    }
                });
                return;
            }
            if (t.dataset.condition) {
                change(() => {
                    e.condition[t.dataset.condition] = t.value;
                    if (e.condition.operator === 'gte' && e.condition.level === 'gray') e.condition.level = 'red';
                });
                return;
            }
            if (t.dataset.assessment) {
                const code = E.LEVELS.find(l => l.id === t.value).code,
                    status = (state.data.achievements[t.dataset.assessment] || '00').split('');
                status[state.doc.assessmentSource === 'self' ? 0 : 1] = code;
                state.data.achievements[t.dataset.assessment] = status.join('');
                render();
                return;
            }
            switch (t.id) {
                case 'document-title':
                    change(() => state.doc.title = t.value.slice(0, 180));
                    break;
                case 'snap-toggle':
                    change(() => state.doc.snap = t.checked);
                    break;
                case 'grid-size':
                    change(() => state.doc.grid = +t.value);
                    break;
                case 'student-name':
                    state.data.student.name = t.value;
                    render();
                    break;
                case 'certificate-date':
                    state.data.date = t.value;
                    render();
                    break;
                case 'assessment-source':
                    change(() => state.doc.assessmentSource = t.value);
                    break;
                case 'translation-scope':
                    state.translationId = t.value;
                    render();
                    break;
                case 'variable-picker':
                    if (t.value) change(() => {
                        e.text += (e.text ? ' ' : '') + t.value;
                    });
                    break;
                case 'condition-enabled':
                    if (t.checked && !E.allObjectives(state.data).length) {
                        t.checked = false;
                        throw new Error('Bitte zuerst ein Curriculum mit Bausteinen wählen.');
                    }
                    change(() => e.condition = t.checked ? {
                        objectiveId: String(E.allObjectives(state.data)[0].id),
                        operator: 'eq',
                        level: 'green',
                        effect: 'show'
                    } : null);
                    break;
            }
        } catch (err) {
            notify(err.message, true);
        }
    });
    let searchTimer, searchSerial = 0;
    document.addEventListener('input', event => {
        if (!config || !['curriculum-search', 'student-search'].includes(event.target.id)) return;
        clearTimeout(searchTimer);
        const q = event.target.value, kind = event.target.id === 'curriculum-search' ? 'curriculum' : 'student',
            serial = ++searchSerial;
        searchTimer = setTimeout(async () => {
            try {
                const r = await api('/' + (kind === 'curriculum' ? 'curricula' : 'students') + '?q=' + encodeURIComponent(q));
                if (serial !== searchSerial) return;
                state.results = r;
                state.searchKind = kind;
                const id = kind + '-search';
                const cursor = event.target.selectionStart;
                render();
                $('#' + id).value = q;
                $('#' + id).focus();
                $('#' + id).setSelectionRange(cursor, cursor);
            } catch (e) {
                notify(e.message, true);
            }
        }, 350);
    });
    let drag = null;
    document.addEventListener('pointerdown', event => {
        if (state.preview || event.button !== 0) return;
        const n = event.target.closest('.cd-element'), page = event.target.closest('.cd-page');
        if (!n) {
            if (page) {
                state.selected = null;
                $('.inspector').innerHTML = inspector();
                document.querySelectorAll('.selected').forEach(n => n.classList.remove('selected'));
            }
            return;
        }
        if (page.dataset.continuation) return;
        event.preventDefault();
        const e = state.doc.elements.find(x => x.id === n.dataset.element);
        state.selected = e.id;
        document.querySelectorAll('.cd-element').forEach(n => n.classList.toggle('selected', n.dataset.element === e.id));
        $('.inspector').innerHTML = inspector();
        const scale = page.getBoundingClientRect().width / 210;
        drag = {
            e,
            node: n,
            startX: event.clientX,
            startY: event.clientY,
            x: e.x,
            y: e.y,
            w: e.w,
            h: e.h,
            resize: event.target.classList.contains('cd-resize'),
            scale,
            changed: false
        };
    });
    document.addEventListener('pointermove', event => {
        if (!drag) return;
        const d = drag, dx = (event.clientX - d.startX) / d.scale, dy = (event.clientY - d.startY) / d.scale;
        if (!d.changed && (Math.abs(dx) + Math.abs(dy)) < .5) return;
        if (!d.changed) {
            checkpoint();
            d.changed = true;
        }
        const snap = v => state.doc.snap ? Math.round(v / state.doc.grid) * state.doc.grid : Math.round(v * 10) / 10;
        if (d.resize) {
            d.e.w = snap(d.w + dx);
            d.e.h = snap(d.h + dy);
        } else {
            d.e.x = snap(d.x + dx);
            d.e.y = snap(d.y + dy);
        }
        clampElement(d.e);
        d.node.style.left = d.e.x + 'mm';
        d.node.style.top = d.e.y + 'mm';
        d.node.style.width = d.e.w + 'mm';
        d.node.style.minHeight = d.e.h + 'mm';
        if (d.e.type === 'image') d.node.style.height = d.e.h + 'mm';
    });
    document.addEventListener('pointerup', () => {
        if (drag?.changed) {
            changed();
            render();
        }
        drag = null;
    });
    document.addEventListener('keydown', event => {
        if (event.target.closest('[data-select]') && ['Enter', ' '].includes(event.key)) {
            event.preventDefault();
            event.target.closest('[data-select]').click();
            return;
        }
        if (event.target.closest('input,textarea,select,[contenteditable]') || $('#modal')?.open) return;
        const modifier = event.ctrlKey || event.metaKey;
        if (modifier && event.key.toLowerCase() === 'z') {
            event.preventDefault();
            restore(event.shiftKey ? 'redo' : 'undo');
            return;
        }
        if (modifier && event.key.toLowerCase() === 's') {
            event.preventDefault();
            save().catch(e => notify(e.message, true));
            return;
        }
        if (state.preview || !selected()) return;
        if (event.key === 'Delete' || event.key === 'Backspace') {
            event.preventDefault();
            actions.delete();
        }
        if (modifier && event.key.toLowerCase() === 'd') {
            event.preventDefault();
            actions.duplicate();
        }
        if (event.key.startsWith('Arrow')) {
            event.preventDefault();
            change(() => {
                const e = selected(), step = event.shiftKey ? state.doc.grid : 1;
                if (event.key === 'ArrowLeft') e.x -= step;
                if (event.key === 'ArrowRight') e.x += step;
                if (event.key === 'ArrowUp') e.y -= step;
                if (event.key === 'ArrowDown') e.y += step;
                clampElement(e);
            });
        }
    });
    window.addEventListener('beforeunload', event => {
        if (state.dirty) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
    const available = Math.max(300, window.innerWidth - 614);
    state.zoom = Math.min(.85, Math.max(.35, (available - 55) / (210 * 96 / 25.4)));
    render();
})();
