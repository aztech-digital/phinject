# Simple configuration example

## 01-simple-config.json :

```js
{
	"classes": {
		"SimpleObject": {
			"class": "\\stdClass", 
			"props": {
				"property": "value"
			}
		}
	}
}
```

## 01-simple-config.php :

```php
<?php

    require_once __ DIR__ . '/../../vendor/autoload.php';
    
    $config = \Aztech\Phinject\Config\ConfigFactory::fromFile(__DIR__ . '/injections.json');
    $container = \Aztech\Phinject\ContainerFactory::create($config);
    
    $obj = $container->get('SimpleObject');
    
    echo $obj->property . PHP_EOL;
```
