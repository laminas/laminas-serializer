## Quick Start

Serializing adapters can either be created from the provided
`Laminas\Serializer\AdapterPluginManager#build` method, or by instantiating one of the
`Laminas\Serializer\Adapter\*` classes.

```php
use Laminas\Serializer\Adapter;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\Exception;
use Laminas\Serializer\Serializer;

$plugins = new AdapterPluginManager();

// Via plugin manager:
$serializer = $plugins->build(Adapter\PhpSerialize::class);

// Alternately:
$serializer = new Adapter\PhpSerialize();

// Now $serializer is an instance of Laminas\Serializer\Adapter\AdapterInterface,
// specifically Laminas\Serializer\Adapter\PhpSerialize

try {
    $serialized = $serializer->serialize($data);
    // now $serialized is a string

    $unserialized = $serializer->unserialize($serialized);
    // now $data == $unserialized
} catch (Exception\ExceptionInterface $e) {
    echo $e;
}
```

The method `AdapterInterface#serialize` generates a storable string. To regenerate this
serialized data, call the method `AdapterInterface#unserialize`.

Any time an error is encountered serializing or unserializing, the adapter will
throw a `Laminas\Serializer\Exception\ExceptionInterface`.

## Basic Configuration Options

To configure a serializer adapter, you can optionally use an instance of
`Laminas\Serializer\Adapter\AdapterOptions`, an instance of one of the adapter
specific options class, an `array`, or a `Traversable` object. The adapter
will convert it into the adapter specific options class instance (if present) or
into the basic `Laminas\Serializer\Adapter\AdapterOptions` class instance.

Options can be passed as the second argument to the provided via the
adapter's `setOptions` method, or as constructor arguments when directly
instantiating an adapter.

## Available Methods

Each serializer implements the interface `Laminas\Serializer\Adapter\AdapterInterface`.

This interface defines the following methods:

| Method signature                     | Description                                       |
|--------------------------------------|---------------------------------------------------|
| `serialize(mixed $value) : string`   | Generates a storable representation of a value.   |
| `unserialize(string $value) : mixed` | Creates a PHP value from a stored representation. |

## Project Defaults

To configure a default serializer (other than `PhpSerializer`, which is already pre-configured), you can override the
dependency configuration in your project by implementing the following file `config/autoload/laminas-serializer.global.php`:

```php
use Laminas\Serializer\Adapter\AdapterInterface;
use Laminas\Serializer\Adapter\Json;
use Laminas\Serializer\GenericSerializerFactory;

// Adapter options can hold adapter specific options, please refer to the adapter configuration documentation section 
$adapterOptions = ['cycle_check' => true];

return [
    // mezzio projects
    'dependencies' => [
        'factories' => [
            AdapterInterface::class => new GenericSerializerFactory(Json::class, $adapterOptions),                
        ],
    ],
    // laminas-mvc projects
    'service_manager' => [
        'factories' => [
            AdapterInterface::class => new GenericSerializerFactory(Json::class, $adapterOptions),
        ],
    ],
];
```

INFO: **Defaults for PHP Serializer**
The PHP serializer does not have any defaults configured. If you want to modify the options of the `PhpSerializer` default, you will have to provide the config as shown above but with the `PhpSerializer` class and the options you want to use.
