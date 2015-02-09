# Injection types

**Navigation**: [Getting started](./01-Getting-started) - Injection types - [References](./03-References.md)

When using dependency injection, you encounter two types of dependency injections:

- Constructor injections
- Setter injections

With Phinject, well it's the same, with a few minor variations to account for PHP's specifics:

- Constructor injections
- Property injections
- Method call injections

Property and method call injections are basically setter injections. They happen once after the object is constructed.

Before we go any further, it's always a good idea to try to follow these rules of thumbs:

- **Prefer constructor injections**. Your objects should work without needing any setter injections. Setter injections should be reserved for customizing the default behavior of your object.
- **Avoid over-injection**. A sensible limit is 3 arguments per constructor, though it's not an absolute, universal value.

If you find that you cannot respect these rules, you probably should review your architecture and reconsider the responsibilities of your objects, but that is outside of the scope of this document.

## Constructor injections

When you need to pass arguments to an buildable object in the container, you can use the `arguments` key in that object's definition to specify an array of [references](./03-References.md) that will be used to invoke the object's constructor:

```yaml
classes:
    myObject:
        class: \My\Class
        arguments: [ '%someParameter', '@someDependency' ]
    # You can use YAML's alternate list syntax
    myOtherObject:
        class: \My\Class
        arguments:
            - '%someParameter'
            - '@someDependency'
```

## Setter injections

As mentioned previously, with Phinject, you have access to two types of setter injections, method calls and property setters.

### Method calls

To invoke a method on an object, simpy use the `call` key of your object definition:

 ```yaml
classes:
    myObject:
        class: \My\Class
        call:
            - push(%someParameter, @someDependency)
            - push(%someParameter2, @someDependency2)
            - anotherMethod(@anotherDependency)
```

### Property injections

You can also inject values and objects directly into an object's public properties (protected and private properties are not supported for the obvious reason that you should not be doing it).

 ```yaml
classes:
    myObject:
        class: \My\Class
        properties:
            myProperty: '%someParameter'
            myOtherProperty: '@someDependency'
```

**Next**: [References](./03-References.md)