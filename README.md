# ar-js-php-trainer
If you need to create a Hiro Marker server-side with PHP, you are lucky

### Install
Get Marker.class.php into your project and require it.

```php
require_once ('Marker.class.php');
```

### Composer installation
It is also possible to call it with composer (recommended)
```
composer require lucaasleaal/ar-js-php-trainer
```

### Instance
```php
$i = new Marker('images/silviosantos.png');
if ($i->status == 'error'){
	die($i->msg);
}
```

### Save jpg-marker and patt file
```php
$i = new Marker('images/silviosantos.png');
if ($i->status == 'error'){
	die($i->msg);
}
$i->saveMarker('images/silviosantos-marker.jpg');
$i->savePatt('images/silviosantos.patt');
```
