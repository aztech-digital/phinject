# Activation strategies

**Navigation**: [References](./03-References.md) - Activation strategies

So far, all examples given only use the standard activation strategy, which simply invokes an object's constructor with arguments.

However, you will most likely come across cases where you need to use a factory class or method to create your object, or you might want to defer activation of your object until the very moment it is actually used.

Phinject is easily extensible (more on that later), and provides built-in support for the following activation methods:

- Constructor invocation: equivalent of calling `new Class()`. This is the default method, as shown in previous examples.
- Method invocation: equivalent of calling a factory method.
- Lazy activation: creates a proxy to the actual object, so that instanciation is deferred until the object is actually used.

Additionally, you can define whether an object is loaded as a singleton or a new instance is created on every resolve.

## Constructor-based activation

You already know this one if you've read the previous chapters. Simply define the class name and the arguments required by the constructor:

```yaml
classes:
    myObject:
        class: \MyClass
        arguments: [ ... ]
```

## Method invocation based activation

This strategy is useful to invoke a factory method which builds complex objects for you instead of manually defining all the build process in Phinject's configuration.

### Calling a static factory method

```yaml
classes:
    myObject:
        builder: \My\Factory\Type::factoryMethod
        arguments: [ ... ]
    # Alternate, inline syntax
    myAltObject:
        builder: \My\Factory\Type::factoryMethod(@dependency, ...)
```

### Calling a factory method on an object instance

```yaml
classes:
    myFactory:
        class: \My\Factory\Type
        arguments: [ ... ]
    myObject:
        builder: @myFactory->createObject
        arguments: [ ... ]
    myAltObject:
        builder: @myFactory->createObject(@dependency, ...)
```

## Lazy activation of objects

(TODO: Document cases where lazy activation may be needed)

It can be desirable to defer the activation of an object until it is actually used. However, this feature is not provided by Phinject itself, but by the `proxy-manager` package written by Ocramius, so you will need to require it first:

`composer require ocramius/proxy-manager`

Once that is done, you will need to change the initialization code of your container by passing an option array telling it explicitely to enable lazy activation:

```php
$container = \Aztech\Phinject\ContainerFactory::create('./phinject.yml', [
    'deferred' => true
]);
```

Once lazy-activation is enabled, you can now use the `lazy` key in your object definitions to tell Phinject that the object must be lazily loaded:

```yaml
classes:
    myObject:
        class: \MyClass
        lazy: true
        arguments [ ... ]
```

Lazy-objects are returned as proxies to the actual object, and the build process is only triggered once you use your object in your code. Using your object is defined as either getting or setting a property value, or calling a method on it.

If your object is never used by your code, it will never be created.

