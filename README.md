Phinject
========
   
[![Build Status](https://travis-ci.org/aztech-digital/phinject.png?branch=master)](https://travis-ci.org/aztech-digital/phinject)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aztech-digital/phinject/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aztech-digital/phinject/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/aztech-digital/phinject/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aztech-digital/phinject/?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/aztech/phinject.png)](http://hhvm.h4cc.de/package/aztech/phinject)

Phinject is a simple dependency injection container, with extensible activation & injection strategies.


## Setup

The recommended setup is to create a configuration folder at the root of your repository. All configuration is based on YAML files. JSON and native PHP files are also supported using the same data structure.

Sample YAML file :

```
parameters:
    MyParameter: 'Some parameter value'
    MyOtherParameter: 42
    MyParameters:
        nested_level:
            foo: foo
            bar: bar
            baz: foobar
        late_bound:
            # This will inject the value of MyOtherParameter (42 in this case)
            param_foo: %MyOtherParameter
            # This will inject the value of environment variable FOO
            env_foo: $env.FOO
            const_baz: $const.BAZ
		coerce_string_value:
			# This will inject "$env.FOO" instead of the value of FOO environment variable.
			forced_var: \$env.FOO
classes:
    MyServiceName:
        class: \Fully\Qualified\ClassName
        arguments: [ @MyDependency, %MyParameter, %MyParameters.nested_level, 'Hard-coded value', $container, $env.ENV, $const.ROOT_PATH ]
    MyOtherServiceWithSingleArgument:
    	class: \Fully\Qualified\ClassName
    	arguments: @MyDependency
    MyDependency:
        class: \Fully\Qualified\DependencyClassName
        props:
            MyProperty: %MyOtherParameter
    DependencyBuildFromStaticMethodCall:
		builder:  \Fully\Qualified\BuilderClassName::buildMethod(@MethodArgument, %param.value)
    DependencyBuildFromInstanceMethodCall:
		builder:  MyDependency->buildMethod(@MethodArgument, %param.value)
```

Bootstrapping the container and fetching an object :

```
$container = Aztech\Phinject\ContainerFactory::createFromYaml('config/dependencies.yml');
$service = $container->get('MyServiceName');

// Do stuff with your service ...

```

## References

You can inject different kind of references inside class definitions. You can get other service instances, parameters, the container itself, env variables, and constant values.

### Named references
- @ServiceName : fetch an instance of that definition
- %param : fetch a parameter defined in the container
- $container : fetch the container itself
- $env.ENV_NAME : fetch an environment variable. Allowed as a parameter value.
- $const.CONST_NAME : fetch a global defined constant value. Allowed as a parameter value.

### Anonymous references

Dependencies can also be expressed as anonymous dependencies. This avoids binding a dependency to the container, and can be useful to omit certain dependencies from being accessible via the container.

So the following definitions :

```
classes:
    MyDependency:
        class: \My\Dependency
        call: 
            someMethod: [ "arg" ]
    MyService:
        class: \My\Class
        args:
            [ @MyDependency ]
...
```

Can now be rewritten as :

```
classes:
    MyService:
        class: \My\Class
        args:
            - class: \My\Dependency
              isClass: true
              call: 
                  someMethod: [ "arg" ]
...
```

While not strictly mandatory, it is recommended to include the `isClass` parameter in anonymous definitions. Without that extra key, the dependency resolver is unable to determine what causes the definition to fail building, and automatically falls back to returning the definition as a parameter array, which can cause misleading error messages.

## Semi-automatic injections

If you find yourself calling the same method on multiple classes that share a common interface or base class, you can define global method injections that will apply to all types deriving from the base type defined in your global injection :

```
global:
    injections:
        "\Psr\Log\LoggerAwareInterface":
            setLogger: [ @Logger ]
classes:
    Logger: 
        class: \Psr\Log\NullLogger
    MyService:
        class: \My\ServiceImplementingLoggerAwareInterface
```

Whenever 'MyService' is instantiated, it's `setLogger` method will be invoked since it implements `LoggerAwareInterface`.

## Template pre-processor.
aztech-dev/dic-it
To help maintain configuration files, DIC-IT provides a lightweight configuration preprocessor which can be used to define templates when you have to initialize multiple objects graphs that share the same build pattern. At the time being, only full service definition templates are supported.

A sample template definition and use follows :

```
templates:
    MyTemplate: 
        class: "{{template-class-var}}"
        args:
            - "{{template-ctor-var}}"    
    
apply-templates:
    TemplatedService:
        template: MyTemplate
        apply:
            template-class-var: "\\MyTemplate\\Class"     
            template-ctor-var: "Some arg value"
```

This example will declare an object definition named "TemplatedService" and register it in the container.

Note that templated definitions cannot be bound to the container manually, they have to be processed into actual object definitions first.

## Using includes

The configuration can be split into multiple files to ease management of your dependencies :

```
includes:
    - relative/file.yml
    - relative/another-file.yml
    
classes:
    ...
```

This allows you to separate parameters from service definitions for example.

## Default object life-cycle

By default, all objects are created as non-singleton (this will definitely change) objects, so every time a reference is resolved by the container, a new instance of the requested object is created.

## Managing circular dependencies

By default, circular dependencies are not handled well (stack overflow...) due the default object life-cycle. To enable circular dependencies for a given object, at least one of the two objects must be defined as a singleton. This however will not yield the expected results, so it is *highly* recommended to define both objects involved in the circular dependency as singletons.

## Credits

This library is originally a fork on `oliviermadre/dic-it`, available [here](https://github.com/oliviermadre/dic-it).

Most of the core features have however been refactored or rewritten, enough that I felt it was time to re-brand this package, in order to both prevent confusion with the original package, and because I did not like the name.
