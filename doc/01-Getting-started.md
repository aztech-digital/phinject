# Getting started

Getting started - [Injection types](./02-Injection-types.md)

Phinject is designed to be easy to use.

The core philosophy behind Phinject is that dependency injection should be absolutely transparent to the context in which it is used. All configuration takes place in stand-alone configuration files, and except for the bootstrap phase of the container, you should not need to reference the container anywhere in your code.

## Hello world

This contrived example will show you how to create a simple dependency injection container and how to use it.

Create file named `HelloWorld.php` and add the following code:

```php
<?php

require_once 'vendor/autoload.php';

class HelloWorld
{

    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
```

Without a container, you probably would use the following code to create the class and print a message to console:

```php

$helloWorld = new HelloWorld('hello world');

echo $helloWorld->getMessage() . PHP_EOL;

```

While this approach is fine, as your application increases in complexity, it will get very cumbersome to inject all parameters in all classes. Additionnaly, it requires extra logic to customize the injected values depending on your environment.

In order to solve this problem, developers use a dependency injection container that contains all the parameters for your application, as well as the creation logic of your objects.

### Using scalar values as dependencies

In order to use Phinject, you need a configuration file. So go ahead and add the file `phinject.yml` to your project, in the same folder as `HelloWorld.php`:

```yaml
parameters:
    helloWorld: 'Hello DI world !'

classes:
    helloWorldObject:
        class: \HelloWorld
        arguments: [ '%helloWorld' ]
```

The first section, `parameters`, defines all scalar values that are parameters to be injected in your classes. Here we only have a single parameter, named `helloWorld` with the value `Hello DI world !`.

The second section, `classes`, defines the objects you want to create. Here, we create an object that will be referenced by the key `helloWorldObject`, and we define its class and constructor arguments.

Notice the `%` in front of `helloWorld` in the arguments array. The `%` tells our container that `helloWorld` is a **parameter reference**, and that it must use the value we previously defined in parameters. But we'll get back to that later.

Now add the following code to your `HelloWord.php` file, at the of the file:

```php

$container = \Aztech\Phinject\ContainerFactory::create('./phinject.yml');
$helloWorld = $container->get('helloWorldObject');

echo $helloWorld->getMessage() . PHP_EOL;
```

When you run the `HelloWorld.php` file, your code will print:

```
Hello DI world !
```

And voilà, you've succesfully an object container with Phinject.

### Using objects as dependencies

So now that you've created your HelloWorld class, you want to add a printer class, because you do not want all these `echo ...` statements in your code, and you create an HelloWorldPrinter class.

Go ahead and that class to your `HelloWorld.php`, right after the `HelloWorld` class:

```php
class HelloWorldPrinter
{
    private $helloWorld;

    public function __construct(HelloWorld $helloWorld)
    {
        $this->helloWorld = $helloWorld;
    }

    public function print()
    {
        echo $this->helloWorld->getMessage() . PHP_EOL;
    }
}
```

Once that is done, edit your `phinject.yml` file so it looks like this. We'll go over the changes after that:

```yaml
parameters:
    helloWorld: 'Hello DI world !'

classes:
    helloWorldObject:
        class: \HelloWorld
        arguments: [ '%helloWorld' ]
    helloWorldPrinter:
        class: \HelloWorldPrinter
        arguments: [ '@helloWorldObject' ]
```

As before, we added a new class definition, `helloWorldPrinter`, specifying its class and the arguments required to construct it.

Notice that this time we an `@` instead of a `%` to reference the dependency object. This tells the container that `helloWorldObject` is an **object reference**.

Now that our container is setup to use the printer, replace the following code:

```php
$container = \Aztech\Phinject\ContainerFactory::create('./phinject.yml');
$helloWorld = $container->get('helloWorldObject');

echo $helloWorld->getMessage() . PHP_EOL;
```

By:

```php
$container = \Aztech\Phinject\ContainerFactory::create('./phinject.yml');
$helloWorldPrinter = $container->get('helloWorldPrinter');

$helloWorldPrinter->print();
```

Once you run the code again, it will give the same output as before.

## Conclusion

We've just covered the basics of Phinject, allowing you to configure parameters and object dependencies in the configuration file.

Obviously, the example is far from real-life code, but it serves to highlight the basic functionality and concepts of Phinject.