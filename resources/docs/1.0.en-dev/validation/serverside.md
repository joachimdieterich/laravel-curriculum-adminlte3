# Form validation

---

- [HTML Purifier](#section-1)

<a name="section-1"></a>
## HTML Purifier
Curriculum uses [mews/purifier](https://github.com/mewebstudio/Purifier)



Include in `model`
```php
use Mews\Purifier\Casts\CleanHtml;
use Mews\Purifier\Casts\CleanHtmlInput;
use Mews\Purifier\Casts\CleanHtmlOutput;


protected $casts = [
        'content'      => CleanHtml::class, // cleans both when getting and setting the value
        'description'  => CleanHtmlInput::class, // cleans when setting the value
        'text'         => CleanHtmlOutput::class, // cleans when getting the value
        'updated_at'   => 'datetime',
        'created_at'   => 'datetime',
    ];
```
