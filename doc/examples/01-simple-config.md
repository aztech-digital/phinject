# Simple configuration example

## 01-simple-config.json :

```js
{
	"classes": {
		"Dependency": {
			"class": "\\stdClass",
			"properties": {
				"injected": "injected value"
			}
		},
		"SimpleObject": {
			"class": "\\stdClass", 
			"properties": {
				"dependentProperty": "@dependency",
				"property": "value"
			}
		}
	}
}
```

## 01-simple-config.php :

```php
<?php

use Aztech\Phinject\ContainerFactory;

require_once __ DIR__ . '/../../vendor/autoload.php';
    
$container = ContainerFactory::create(__DIR__ . '/01-simple-config.json');
$obj = $container->get('SimpleObject');
    
echo $obj->property . PHP_EOL;
echo $obj->dependentProperty->injected . PHP_EOL;
```
