# Migration to Version 3.0

Upgrading `laminas-serializer` will require some code changes, depending on how the serializers were used.


### Static Methods in `Serializer` Implementation

The biggest breaking change in v3 relates to the `Laminas\Serializer\Serializer`. This class has been removed due to
its potential side-effects and for not following the [`S`](https://en.wikipedia.org/wiki/Single-responsibility_principle) within [`SOLID`](https://en.wikipedia.org/wiki/SOLID).

This class was:
 - a registry (by providing a method to both persist and provide a "default" serializer instance)
 - a factory (by providing methods to create objects based on arguments passed to methods)
 - a serializer (by providing `serialize` and `unserialize` which then called magically an `AdapterInterface` implementation)

To enable projects and 3rd-party libraries to provide their own serializer implementations (which are also available via the `AdapterPluginManager`), this class was removed in favor of dependency injection.

The amount of work to migrate projects consuming the `Laminas\Serializer\Serializer` class depends on the way how it was
used in projects.

#### Projects Making Use of `Serializer#setDefaultAdapter`

In case you are using this method within a `laminas-mvc` or `mezzio` project, laminas got your back. You can now configure the projects default adapter by using the [dependency configuration](../intro.md#project-defaults).
If your project uses `Serializer#setDefaultAdapter` outside of `laminas-mvc` or `mezzio` projects, you have to provide your own registry. 
You can either copy the current implementation into your project or implement an own minimal implementation such as:

```php
namespace MyNamespace;

use Laminas\Serializer\Adapter\AdapterInterface;use Laminas\Serializer\Adapter\PhpSerialize;

final class SerializerRegistry
{
     private static null|AdapterInterface $adapter = null;
     
     public static function getDefaultAdapter(): AdapterInterface
     {
        if (self::$adapter) {
            return self::$adapter;
        }
        
        return self::$adapter = new PhpSerialize();
     }
     
     public static function setDefaultAdapter(AdapterInterface $adapter): void
     {
        self::$adapter = $adapter;
     }
}
```

_It is highly recommended to use dependency injection over a registry._

#### Projects Making Use of `Serializer#getDefaultAdapter`

In case you are using this method within a `laminas-mvc` or `mezzio` project, laminas got your back. You can now retrieve the projects default adapter by using the container: `$container->get(\Laminas\Serializer\Adapter\AdapterInterface::class);`.
This will always provide an instance of `AdapterInterface` (`PhpSerialize` by-default unless configured otherwise) and therefore provides the same value as `Serializer#getDefaultAdapter`.

Outside of laminas frameworks, you the changes are depending on how the project is interacting with the `Serializer` class.
If your project does not call `Serializer#setDefaultAdapter`, code can be replaced with `new \Laminas\Serializer\Adapter\PhpSerialize()`.
If you think that is not a good solution, feel free to implement your own registry. Refer to the section above on how to do so.

#### Projects Making Use of `Serializer#serialize` or `Serializer#unserialize`

There are no replacements for these methods. You can either instantiate `PhpSerialize` adapter (which is the default) or, in case your projects uses `Serializer#setDefaultAdapter`, please refer to the [section above](#projects-making-use-of-serializersetdefaultadapter).

#### Projects Making Use of `Serializer#factory`

This method has multiple ways to get called:


##### Passing an Instance of `AdapterInterface`

```php
use Laminas\Serializer\Adapter\PhpSerialize;

$adapter = new PhpSerialize();
$adapter = Serializer::factory($adapter); // unnecessary method call
$adapter = Serializer::factory($adapter, ['unserialize_class_whitelist' => false]); // unnecessary method call
```

In both cases, the `$adapter` was already an instance of an adapter prior calling the `factory` method.
The `factory` method **always** immediately returned the `$adapter` in case it was not a string and therefore, the method calls can be simply removed.

##### Passing an Already Known `class-string`

```php
use Laminas\Serializer\Adapter\PhpSerialize;

$adapterClassName = PhpSerialize::class;
$adapter = Serializer::factory($adapterClassName);
$adapter = Serializer::factory($adapterClassName, ['unserialize_class_whitelist' => false]);
```

In this example, we obviously are aware that we want a `PhpSerialize` serializer. Please directly instantiate the serializer instead of calling the factory method.

##### Passing a Service Alias

```php
$adapterAlias = 'phpserialize';
$adapter = Serializer::factory($adapterAlias);
$adapter = Serializer::factory($adapterAlias, ['unserialize_class_whitelist' => false]);
```

Same as the [example where we pass `class-string`](#passing-an-already-known-class-string). Please refactor your code to directly instantiate the serializer instead.

##### Passing an Unknown Value

```php
use Laminas\Serializer\Adapter\AdapterInterface;

function myfunction(string|AdapterInterface $adapterAliasOrClassStringOrInstance, ?array $adapterOptions = null): string
{
    $adapter = Serializer::factory($adapterAliasOrClassStringOrInstance, $adapterOptions);
    
    return $adapter->serialize(new stdClass());
}
```

This will require you to use the `Laminas\Serializer\AdapterPluginManager`.

```php
use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\AdapterPluginManager;

function myfunction(string|AdapterInterface $adapterAliasOrClassStringOrInstance, ?array $adapterOptions = null): string
{
    $adapter = $adapterAliasOrClassStringOrInstance;
    if (!$adapter instanceof AdapterInterface) {
        $plugins = new AdapterPluginManager();
        $adapter = $plugins->build($adapterAliasOrClassStringOrInstance, $adapterOptions);
    }
    
    return $adapter->serialize(new stdClass());
}
```

## Checklist

1. `laminas-serializer` is updated to the latest version from within `2.x`
2. Search your code for the usage of `Laminas\Serializer\Serializer`, if this class is in-use, please refer to its [dedicated migration section](#static-methods-in-serializer-implementation)
3. If your project provides an implementation of `AdapterInterface` or `AbstractAdapter`, please migrate your code to comply with the latest type-additions
