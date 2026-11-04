<?php

namespace App\Services\CertificateDesigner;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JsonException;

/** Data only: no HTML, PHP, Blade, arbitrary URLs, or executable expressions. */
class DesignValidator
{
    /**
     * @throws JsonException
     */
    public function validate(array $document, array $objectiveIds): array
    {
        Validator::make($document, [
            'schemaVersion'           => 'required|integer|in:1',
            'title'                   => 'required|string|max:180',
            'pageCount'               => 'required|integer|between:1,30',
            'grid'                    => 'required|numeric|in:1,2.5,5,10',
            'snap'                    => 'required|boolean',
            'assessmentSource'        => 'required|in:teacher,self',
            'selectedIds'             => 'present|array|max:2000',
            'selectedIds.*'           => 'required|string|max:100',
            'translations'            => 'required|array',
            'translations.global'     => 'required|array',
            'translations.individual' => 'present|array|max:2000',
            'elements'                => 'present|array|max:200',
            'elements.*.id'           => ['required', 'string', 'distinct', 'regex:/^[a-zA-Z0-9_-]{1,80}$/'],
            'elements.*.name'         => 'required|string|max:180',
            'elements.*.type'         => 'required|in:text,image,line,table,competency',
            'elements.*.page'         => 'required|integer|min:1|max:' . ($document['pageCount'] ?? 1),
            'elements.*.x'            => 'required|numeric|between:0,210',
            'elements.*.y'            => 'required|numeric|between:0,297',
            'elements.*.w'            => 'required|numeric|between:1,210',
            'elements.*.h'            => 'required|numeric|between:0.2,297',
            'elements.*.fontSize'     => 'required|numeric|between:6,72',
            'elements.*.align'        => 'required|in:left,center,right',
            'elements.*.color'        => ['required', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'elements.*.background'   => ['required', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'elements.*.bold'         => 'required|boolean',
            'elements.*.italic'       => 'required|boolean',
            'elements.*.border'       => 'required|boolean',
            'elements.*.repeat'       => 'required|boolean',
            'elements.*.condition'    => 'nullable|array',
        ])->validate();

        $ids    = array_map('strval', $objectiveIds);
        $levels = ['gray', 'red', 'yellow', 'green'];
        $assert = static function ($condition, $message) {
            if (! $condition) {
                throw ValidationException::withMessages(['document' => $message]);
            }
        };
        $assert(strlen(json_encode($document, JSON_THROW_ON_ERROR)) <= 10000000, 'Die Vorlage darf höchstens 10 MB groß sein.');
        $assert(! array_diff($document['selectedIds'], $ids), 'Ein ausgewählter Baustein gehört nicht zum Curriculum.');
        $assert(is_bool($document['snap']), 'Ungültiger Wahrheitswert.');
        foreach ((['global' => $document['translations']['global']] + $document['translations']['individual']) as $id => $map) {
            $assert($id === 'global' || in_array($id, $ids, true), 'Die Übersetzung verweist auf einen fremden Baustein.');
            $assert(is_array($map), 'Ungültige Übersetzung.');
            foreach ($map as $level => $text) {
                $assert(in_array($level, $levels, true) && (is_string($text) || $text === null) && mb_strlen($text ?? '') <= 4000, 'Ungültiger Übersetzungstext.');
            }
        }
        // ConvertEmptyStringsToNull is part of Curriculum's global middleware.
        foreach ($document['translations']['global'] as &$text) {
            $text ??= '';
        }
        unset($text);
        foreach ($document['translations']['individual'] as &$map) {
            foreach ($map as &$text) {
                $text ??= '';
            }
            unset($text);
        }
        unset($map);
        foreach ($document['elements'] as &$element) {
            $assert($element['x'] + $element['w'] <= 210.1 && $element['y'] + $element['h'] <= 297.1, 'Ein Element liegt außerhalb der Seite.');
            foreach (['bold', 'italic', 'border', 'repeat'] as $key) {
                $assert(is_bool($element[$key]), 'Ungültige Formatierung.');
            }
            if (array_key_exists('tableStyle', $element)) {
                $assert(in_array($element['type'], ['table', 'competency'], true) && is_array($element['tableStyle']), 'Ungültige Tabellengestaltung.');
                $booleans = ['areaBackgroundEnabled', 'outerBorder', 'innerBorders'];
                $colors   = ['areaBackground', 'areaTextColor', 'outerBorderColor', 'innerBorderColor'];
                $widths   = ['outerBorderWidth', 'innerBorderWidth'];
                foreach ($element['tableStyle'] as $key => $value) {
                    $assert(in_array($key, array_merge($booleans, $colors, $widths), true), 'Unbekannte Tabellenoption.');
                    if (in_array($key, $booleans, true)) {
                        $assert(is_bool($value), 'Ungültiger Tabellenschalter.');
                    } elseif (in_array($key, $colors, true)) {
                        $assert(is_string($value) && preg_match('/^#[a-fA-F0-9]{6}$/D', $value), 'Ungültige Tabellenfarbe.');
                    } else {
                        $assert((is_int($value) || is_float($value)) && $value >= 0.25 && $value <= 6, 'Linienstärken müssen zwischen 0,25 und 6 pt liegen.');
                    }
                }
                $element['tableStyle'] = (object) $element['tableStyle'];
            }
            if (! empty($element['condition'])) {
                Validator::make($element['condition'], [
                    'objectiveId' => 'required|string', 'operator' => 'required|in:eq,gte',
                    'level'       => 'required|in:gray,red,yellow,green', 'effect' => 'required|in:show,hide',
                ])->validate();
                $c = $element['condition'];
                $assert(in_array($c['objectiveId'], $ids, true), 'Der Baustein der Bedingung gehört nicht zum Curriculum.');
                $assert($c['operator'] !== 'gte' || $c['level'] !== 'gray', 'Grau ist keine Mindeststufe.');
            }
            $texts = [];
            switch ($element['type']) {
                case 'text':
                    $element['text'] ??= '';
                    $assert(is_string($element['text']) && mb_strlen($element['text']) <= 20000, 'Ungültiger Text.');
                    $texts[] = $element['text'];
                    break;
                case 'image':
                    $element['src'] ??= '';
                    $src = $element['src'];
                    $assert(is_string($src) && strlen($src) <= 2800000, 'Bild zu groß.');
                    if ($src !== '') {
                        $assert(preg_match('~^data:image/(png|jpeg|webp);base64,([A-Za-z0-9+/]+=*)$~D', $src, $match) === 1, 'Nur eingebettete Rasterbilder sind erlaubt.');
                        $bytes = base64_decode($match[2], true);
                        $image = $bytes === false ? false : @getimagesizefromstring($bytes);
                        $assert($bytes !== false && strlen($bytes) <= 2000000 && $image && ($image['mime'] ?? '') === 'image/' . $match[1], 'Ungültige Bilddatei.');
                    }
                    break;
                case 'table':
                    $rows = $element['cells'] ?? null;
                    $assert(is_array($rows) && count($rows) > 0 && count($rows) <= 100, 'Ungültige Tabelle.');
                    $width = is_array($rows[0] ?? null) ? count($rows[0]) : 0;
                    $assert($width > 0 && $width <= 20 && is_bool($element['headerRow'] ?? null), 'Ungültige Tabellenspalten.');
                    foreach ($element['cells'] as &$row) {
                        $assert(is_array($row) && count($row) === $width, 'Ungleichmäßige Tabellenzeilen.');
                        foreach ($row as &$cell) {
                            $cell ??= '';
                            $assert(is_string($cell) && mb_strlen($cell) <= 4000, 'Ungültiger Zellinhalt.');
                            $texts[] = $cell;
                        }
                        unset($cell);
                    }
                    unset($row);
                    break;
                case 'competency':
                    Validator::make($element, [
                        'columns'           => 'required|array|size:4',
                        'columns.*.id'      => 'required|distinct|in:gray,red,yellow,green',
                        'columns.*.title'   => 'nullable|string|max:150',
                        'columns.*.visible' => 'required|boolean',
                        'mark'              => 'nullable|string|max:10', 'headerTitle' => 'nullable|string|max:150',
                    ])->validate();
                    $element['mark'] ??= '';
                    $element['headerTitle'] ??= '';
                    foreach ($element['columns'] as &$column) {
                        $column['title'] ??= '';
                        $assert(is_bool($column['visible']), 'Ungültige Spaltensichtbarkeit.');
                    }
                    unset($column);
                    foreach (['verticalHeaders', 'groupByArea', 'showTranslation', 'autoContinue'] as $key) {
                        $assert(is_bool($element[$key] ?? null), 'Ungültige Tabellenoption.');
                    }
                    break;
            }
            foreach ($texts as $text) {
                preg_match_all('/\{\{objective:([^:}]+):(text|title|level)}}/', $text, $matches);
                $assert(! array_diff($matches[1], $ids), 'Eine Variable verweist auf einen fremden Baustein.');
            }
        }
        unset($element);
        // JSON object maps must remain objects even when empty or keyed by numeric IDs.
        $document['translations']['individual'] = (object) array_map(static fn ($map) => (object) $map, $document['translations']['individual']);

        return $document;
    }
}
