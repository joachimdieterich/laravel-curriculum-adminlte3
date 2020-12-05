# PDFs erstellen

---

- [Überblick](#section-1)
- [PDF-Vorlage erstellen](#section-2)
- [PDF generieren](#section-3)


<a name="section-1"></a>
## Überblick
-

<a name="section-2"></a>
## PDF-Vorlage erstellen

```HTML
<img  src="/media/1120"  style="position: absolute;top:0;right:0;" width="250"/>
<img alt="" src="/media/1121"  width="100%"  style="padding-top:200;" >

<div style="text-align:center;"><h2><strong>{ {$firstname} } { {$lastname} }</strong></h2></div>
<p style="text-align:center;font-size:18px;">hat erfolgreich die folgenden Module des MedienkomP@sses Sek. I <br>(Kompetenzerwartungen Ende Pflichtschulzeit) abgeschlossen.</p>

<p style="float:left;">
<div style="float:left;position:relative;width:140px;padding-left:10px;padding-right:65px;"><progress reference_type="App\TerminalObjective" reference_id="883,884,885,886" min_value="60"><img alt="" src="/media/1115" style="width:140px;"></progress></div>
<div style="float:left;position:relative;width:140px;padding-right:65px;"><progress reference_type="App\TerminalObjective" reference_id="887,888,889,890" min_value="60"><img alt="" src="/media/1117" style="width:140px;"></progress></div>
<div style="float:left;position:relative;width:140px;"><progress reference_type="App\TerminalObjective" reference_id="891,892,893,894" min_value="60"><img alt="" src="/media/1116" style="width:140px;"></progress></div>
</p>
<p style="float:left;margin-top:-65px;">
<div style="float:left;position:relative;width:140px;padding-left:120px;padding-right:65px;"><progress reference_type="App\TerminalObjective" reference_id="903,904,905,906" min_value="60"><img alt="" src="/media/1114" style="width:140px;"></progress></div>
<div style="float:left;position:relative;width:140px;padding-right:65px;"><progress reference_type="App\TerminalObjective" reference_id="895,896,897,898" min_value="60"><img alt="" src="/media/1112" style="width:140px;"></progress></div>
<div style="float:left;position:relative;width:140px;"><progress reference_type="App\TerminalObjective" reference_id="899,900,901,902" min_value="60"><img alt="" src="/media/1113" style="width:140px;"></progress></div>
</p>

<p style="clear:both; padding-top:24px;">
<strong>{ {$organization_title} }, { {$organization_street} }, { {$organization_postcode} } { {$organization_city} }</strong>
</p>
<p>{ {$organization_city} }, { {$date} }</p>
<p>&nbsp;</p>
<p>
<span style="float:left;">__________________________________</span><span style="float:right;">__________________________________</span>
<br>
<span style="float:left;">(Klassenleitung)</span><span style="float:right;">(Schulleitung)</span>
</p>

<p style="clear:both; padding-top:20px;text-align:right;">
<small>Weitere Informationen zu diesem Zertifikat erhalten Sie auf  <a href="https://medienkompass.bildung-rp.de">https://medienkompass.bildung-rp.de</a>.</small>
</p>
```

<a name="section-3"></a>
## PDF generieren
