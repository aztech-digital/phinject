# Lifecycle of objects

**Navigation**: [Activators](./04-Activators.md) - Lifecycle of objects

## Default behavior

By default, when an object is requested from the container, a single instance is created and stored, and the same instance is always returned.

This has many benefits, including full-support for circular dependencies. However, there may be cases where you the container to act as a factory, and return a new instance of an object instead of the same instance. You can use the `singleton` key to change that behavior.

```yaml
classes:
    myObject:
        class: \My\Class
        singleton: false
        properties: [ ... ]
```

### A bit about circular dependencies

**TL;DR** avoid them.

#### Between singleton'ed objects

You can define circular dependencies between singleton'ed objects, provided at least one of the sides uses a setter injection. However, remember that circular dependencies can often be refactored by extracting a class out of the two inter-dependant classes, and that may prove to be a better solution.

If you however chose to not refactor your circular dependencies, you can do some pretty twisted things with Phinject and a bit of imagination... like circular dependencies using only constructor injections and other follies. I'll leave it up as an exercise to the reader.

#### Between non singleton'ed objects

Do not define circular dependencies with objects having the singleton property set to false. This will lead to a stack overflow exception. Assuming you have a circular dependency between A and B, creating A will request a new B, which will in turn request a new A, which will in turn... you get the idea. 

#### Between a singleton'ed and a non singleton'ed object

While creating a circular dependency between a singleton'ed object and a non singleton'ed one works, it is not recommended, since the order of creation cannot be guaranteed. Again, assumming you have a circular dependency between A (singleton) and B (non singleton), the behavior will vary depending on which object you request first. If A is requested first, it will be injected with a new instance of B (which will itself be injected with the singleton'ed A instance). On the other hand, if you request B first, it will be injected with A, but A will request a new B instance. This may or may not be desirable. 

## Life and death of objects

### For singleton'ed objects

Since all objects are stored in a registry once created, they exist for as long as the container exist.

There are two ways to destroy an object instantiated by the container:

- Destroy the container
- Bind a null value to the object's key in the container

```php
// Assuming $container is setup and contains an object named myObject

$container->bind('myObject', null);
```

Provided that you hold no other references to the original object, it will be garbage collection on the next GC cycle.

### For non singleton'ed objects

Given that the container does not store any reference to these objects, they are garbage collected as any object would when their reference count falls to 0.