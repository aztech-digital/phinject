# References

**Navigation**: [Injection types](./02-Injection-types.md) - References - [Activation strategies](./04-Activators.md)

As we saw in the [Getting started](01-Getting-started.md) tutorial, dependency injection really boils down to being able store scalar values and object instances as dependencies ready to be used by another class.

Phinject uses a reference system to query the dependencies of an object. Each reference type has a specific prefix that will tell the object container the type of reference you querying.

## Parameter references

The first type of references is parameter references. They are quite simple, any value defined in the parameters section of your configuration is accessible by its name, prefixed by a `%` sign.

Here are some examples of valid parameter definitions and references:

```yaml
parameters:
    integerValue: 10
    stringValue: "some string"
    orderered-list:
        - first value
        - second value
        - ...
    set:
        key: value
        nextKey: nextValue
```

Injecting those values in objects is pretty simple:


```yaml
classes:
    myObject:
        class: \stdClass
        properties:
            integerProperty: '%integerValue'
            stringProperty: '%stringValue'
            # This next one gets injected as an array... neat, uh ?
            arrayProperty: '%ordered-list'
            # And this one too
            setProperty: '%set'
            # With sets, you can actually reference a specific item
            myProperty: '%set.key'
```

## Object references

### Simple references

To inject objects, you can use object references. Those are simply an object's name as defined in your configuration, prefixed by the `@` symbol:

```yaml
classes:
    myObject:
        class: \stdClass
    myObjectSet:
        class: \SplObjectStorage
        call:
            - attach(@myObject)
    myCompositeObject:
        class: \stdClass
        properties:
            someProperty: '@myObject'
```

Getting the hang of it ? There's more.

### Injecting object arrays

Sometimes, you may want to build a list of objects and inject that list as an array. Phinject uses the special `@ns:` prefix to handle those cases:

```yaml
classes:
    myList:firstObject:
        class: \stdClass
    myList:secondObject:
        class: \stdClass
    myObject:
        class: \stdClass
        properties:
            myArrayProperty: '@ns:myList'
```

### Working with factories

Some objects will definitely have more complex build processes, and generally, libraries provide factories to help creating those objects. In order to make your life easier, Phinject handles those as well through method invocation references.

With method invocation references, you can either call static methods on a type, or instance methods on an object.

```yaml
classes:
    firstObject:
        class: \stdClass
        properties:
            myProperty: \My\Factory\Type::factoryMethod()
            # You can also pass arguments to the method
            mySecondProperty: \My\Factory\Type::otherFactoryMethod(@aDependencyObject, %someParameterValue)
```

## Built-in references

Phinject also provides access to some special references, namely the container, environment variables, and constants.

### Injecting the container

If you ever need to inject the container itself into an object, it's possible via the special `$container` reference:

```yaml
classes:
    containerAwareObject:
        class: \My\Class
        arguments: [ '$container' ]
```

**Important notice** We highly discourage you to inject the container. Your application code should *never ever be aware* of the dependency injection container, and the cases justifying to do this are extremly limited. Phinject believes that if you need to inject the container, you are doing it wrong.

### Injecting environment variables

Phinject provides a special reference to access the environment variables, `$env`. It allows you to inject the value of an environment variable either in the parameters section, or in any section where a reference is allowed:

```yaml
parameters:
    myParam: $env.VAR_NAME
classes:
    myObject:
        class: \My\Class
        arguments: [ '$env.VAR_NAME' ]
```

### Injecting constants

Just like environment variable, you can inject defined global constants in your definitions using the `$const` reference either in the parameters section, or in any section where a reference is allowed:

```yaml
parameters:
    myParam: $const.CONST_NAME
classes:
    myObject:
        class: \My\Class
        arguments: [ '$const.CONST_NAME' ]
```

### Escaping values in the parameters section

If you do happen to have a parameter whose value actually starts with `$env.` or `$const.`, you'll run into an issue, as Phinject will try to resolve the value as a reference.

In that case, you simply need to escape the value with a backslash, which tells Phinject to not resolve the value as a reference:

```yaml
parameters:
    myParam: \$const.something
```

**Next**: [Activation strategies](./04-Activators.md)