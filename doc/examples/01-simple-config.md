# Simple configuration example with parameters and classes

## YAML configuration

### 01-simple-config.yaml :

```yaml
classes:
	Dependency:
	    class: \stdClass
	    properties:
	        injected: injected value
    SimpleObject:
        class: \stdClass
        properties:
            dependentProperty: '@dependency'
            property: value
```

## JSON configuration

### 01-simple-config.json :

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

## Using in PHP

### 01-simple-config.php :

```php
<?php

use Aztech\Phinject\ContainerFactory;

require_once __ DIR__ . '/../../vendor/autoload.php';

// Update filename accordingly    
$configFile = __DIR__ . '/01-simple-config.json';

$container = ContainerFactory::create($configFile);
$obj = $container->get('SimpleObject');
    
echo $obj->property . PHP_EOL;
echo $obj->dependentProperty->injected . PHP_EOL;
```
