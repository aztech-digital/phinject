# Extending the container features

**Navigation**: [Lifecycle of objects](./05-Lifecycle.md) - Extending the container

## Foreword on the injection process

With Phinject, the build process of objects is decomposed into multiple logical steps, each of which ultimately will be extensible. For now, only the activation process can be extended.

### What happens when you request an object ?

First, the container checks if the object has already been built, and if it has an instance available in store (only in the case of singleton'ed objects, for more information on that, read [Lifecycle of objects](./05-Lifecycle.md)), it returns it.

When the instance has never been requested, or if a build is required because the instance is not singleton'ed, the following process goes on:

- Look in the classes configuration array for a definition matching the name of the requested service.
- Loop through all activators until one claims it can activate the object.
- The container along with the configuration node of the requested class and the service name are passed to the activator.
- The selected activator creates (however it sees fit) the instance using the available configuration, and returns that instance. If it needs to query dependencies in order to instanciate the requested oject, it can do so by querying the container that it was given. It may also perform some post-build injections in order to simplify the service configuration node of the requested object.
- The instanciated object is then returned to the parent routine, which then goes on to perform post-build injections. 
- Post-build injections are performed by injector instances, which are looped over and each is passed the object to get a chance to perform the necessary injections depending on the service configuration.
- Once the object is fully injected, it is then returned to the function that requested the object.

TODO: Document type intialization features.

### So, how does this help me ?

Weel, first of all, you don't necessarily need to extend Phinject. For most use cases, using factory methods via the `builder` key should be enough. 

However, if you want to distribute components that easily integrate with Phinject, activators may be more suitable as the build process of your shipped component may be cumbersome to users if it requires multiple dependencies to instanciate itself.

All you need to do is provide with your package an implementation of `\Aztech\Phinject\Activator`, which declares a single method, `activate`:

```php
interface Activator
{
    /**
     *
     * @param Container $container
     * @param ArrayResolver $serviceConfig
     * @param string $serviceName
     */
    public function createInstance(Container $container, ArrayResolver $serviceConfig, $serviceName);
}
```

The arguments it receives are the container, to allow you to resolve dependencies required by your build process, an ArrayResolver instance which gives you access to the configuration array of the service to build, and the name of the requested service (though you should not need to use in most cases).

Using those objects, you can then build your object as requested, and return that object instance. You can checkout this [implementation](https://github.com/aztech-digital/http-oauth-layer/blob/master/src/Phinject/OauthActivator.php) for clues of how to proceed.

Once your project ships with an activator, client projects can then use by simply declaring it in the `config.activators` section of their configuration file:

```yaml
config:
    activators:
        activatorKey: \Activator\Class
```

The activator then becomes available for use by using the defined activator in their object definition:

```yaml
classes:
    myCustomActivatedObject:
        # Note: the value associated with activatorKey can be anything, it's up to decide how to parse it.
        activatorKey:
            setting: true
            otherSetting: 42
        call: [ ... ]
        properties: [ ... ]
```

That's it folks !
