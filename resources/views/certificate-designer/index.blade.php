<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Curriculum · Zertifikatseditor</title>
    <link rel="stylesheet" href="{{ asset('certificate-designer/designer.css') }}">
</head>
<body>
    <div id="app"></div>
    <script type="application/json" id="certificate-designer-config">{!! json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script src="{{ asset('certificate-designer/engine.js') }}" defer></script>
    <script src="{{ asset('certificate-designer/designer.js') }}" defer></script>
</body>
</html>
