# Adapters

laminas-serializer adapters handle serialization to and deserialization from
specific representations.

Each adapter has its own strengths. In some cases, not every PHP datatype (e.g.,
objects) can be converted to a string representation. In most such cases, the
type will be converted to a similar type that is serializable.

As an example, PHP objects will often be cast to arrays. If this fails, a
`Laminas\Serializer\Exception\ExceptionInterface` will be thrown.

## The PhpSerialize Adapter

The `Laminas\Serializer\Adapter\PhpSerialize` adapter uses the built-in
[serialize()](https://php.net/serialize)/[unserialize()](https://php.net/unserialize)
functions, and is a good default adapter choice.

Available options include:

| Option                      | Data Type         | Default Value | Description                                                                                                                                        |
|-----------------------------|-------------------|---------------|----------------------------------------------------------------------------------------------------------------------------------------------------|
| unserialize_class_whitelist | `array` or `bool` | `true`        | The allowed classes for unserialize(), see [unserialize()](https://php.net/unserialize) for more information. Only available on PHP 7.0 or higher. |

## The IgBinary Adapter

[Igbinary](htts://pecl.php.net/package/igbinary) was originally released by
Sulake Dynamoid Oy and since 2011-03-14 moved to [PECL](https://pecl.php.net) and
maintained by Pierre Joye. It's a drop-in replacement for the standard PHP
serializer. Instead of using a costly textual representation, igbinary stores
PHP data structures in a compact binary form. Savings are significant when using
memcached or similar memory based storages for serialized data.

You need the igbinary PHP extension installed on your system in order to use
this adapter.

There are no configurable options for this adapter.

## The Json Adapter

The [JSON](https://wikipedia.org/wiki/JavaScript_Object_Notation) adapter provides a bridge to the
[laminas-json](https://docs.laminas.dev/laminas-json) component.

Available options include:

| Option                    | Data Type                   | Default Value                   |
|---------------------------|-----------------------------|---------------------------------|
| `cycle_check`             | `boolean`                   | `false`                         |
| `object_decode_type`      | `Laminas\Json\Json::TYPE_*` | `Laminas\Json\Json::TYPE_ARRAY` |
| `enable_json_expr_finder` | `boolean`                   | `false`                         |

## The PhpCode Adapter

The `Laminas\Serializer\Adapter\PhpCode` adapter generates a parsable PHP code
representation using [var_export()](https://php.net/var_export). To restore,
the data will be executed using [eval](https://php.net/eval).

There are no configuration options for this adapter.

WARNING: **Unserializing Objects**
Objects will be serialized using the [__set_state](https://php.net/language.oop5.magic#language.oop5.magic.set-state) magic method.
If the class doesn't implement this method, a fatal error will occur during execution.

WARNING: **Uses `eval()`**
The `PhpCode` adapter utilizes `eval()` to unserialize. This introduces both a performance and potential security issue as a new process will be executed.
Typically, you should use the `PhpSerialize` adapter unless you require human-readability of the serialized data.
