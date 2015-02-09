# References

[Injection types](./02-Injection-types.md) - References

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

To inject object, you can use object references. Those are simply an object's name as defined in your configuration, prefixed by the `@` symbol:

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

