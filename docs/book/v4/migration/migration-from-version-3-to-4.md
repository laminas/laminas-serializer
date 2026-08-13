# Migration from Version 3 to 4

Upgrading `laminas-serializer` will require some code changes, depending on how the serializers were used.

## Breaking Changes

### Removed laminas-json Dependency

The dependency on `laminas/laminas-json` has been removed.
The `Json` adapter now uses native PHP functions ([`json_encode`](https://www.php.net/manual/function.json-encode.php) and [`json_decode`](https://www.php.net/manual/function.json-decode.php)).

#### JSON Adapter and JsonException

Since `laminas/laminas-json` is no longer used, the `Json` adapter now throws `JsonException` wrapped in `Laminas\Serializer\Exception\RuntimeException` when an error occurs during serialization or unserialization.

### Removed Options and Methods

#### JsonOptions

The following deprecated options and their corresponding getters and setters have been removed from `Laminas\Serializer\Adapter\JsonOptions`:

- `cycleCheck` (`setCycleCheck()` / `getCycleCheck()`)
- `enableJsonExprFinder` (`setEnableJsonExprFinder()` / `getEnableJsonExprFinder()`)
- `objectDecodeType` (`setObjectDecodeType()` / `getObjectDecodeType()`)

Use `setAssocArray()` and `isAssocArray()` instead of `objectDecodeType`.

#### PhpSerializeOptions

The deprecated option `unserialize_class_whitelist` has been renamed to `allowed_classes` and the setter and getter for the previous option name has been removed: `setUnserializeClassWhitelist()` and `getUnserializeClassWhitelist()` respectively.
Replacement methods are named `setAllowedClasses()` and `getAllowedClasses()`.

### Removed Module Class

The `Laminas\Serializer\Module` class has been removed.
This means that formal integration with `laminas-mvc` via the `ServiceListener` is no longer provided.
The `SerializerAdapterManager` is still available via `ConfigProvider`.

### Final Classes

The following classes are now marked as `final` because they are no longer intended to be subclassed:

- `Laminas\Serializer\AdapterPluginManagerFactory`
- `Laminas\Serializer\ConfigProvider`
