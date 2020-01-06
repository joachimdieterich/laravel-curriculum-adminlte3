# Generating PDFs (Certificates,...)

---

- [Configuration](#section-1)
- [Generate PDFs](#section-2)


<a name="section-1"></a>
## Configuration

Curriculum uses [barryvdh/laravel-snappy] (https://github.com/barryvdh/laravel-snappy)

Check that wkhtmltopdf binaries are present. (Further information on [barryvdh/laravel-snappy] (https://github.com/barryvdh/laravel-snappy))
Binaries for linux are included in the package, those for macs can be found under [profburial/wkhtmltopdf-binaries-osx] (https://github.com/profburial/wkhtmltopdf-binaries-osx)

Set up the `.env` to get it working. Example:
```bash
SNAPPY_PDF_BINARY="/absolute_path_to/vendor/bin/wkhtmltopdf-amd64-osx"
SNAPPY_IMAGE_BINARY="/absolute_path_to/vendor/bin/wkhtmltoimage-amd64-osx"
```

<a name="section-2"></a>
## Generate PDFs
Example: 
```bash
return SnappyPdf::loadFile('http://curriculumonline.de')->inline('cur.pdf');
```