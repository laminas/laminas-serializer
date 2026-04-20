# Migration to Version 4.0

Upgrading `laminas-serializer` will require some code changes, depending on how the serializers were used.

## Breaking Changes

### Option name changes for PhpSerializer

The deprecated option `unserialize_class_whitelist` has been renamed to `allowed_classes` and the setter and getter for the previous option name has been removed: `setUnserializeClassWhitelist()` and `getUnserializeClassWhitelist()` respectively.
Replacement methods are named `setAllowedClasses()` and `getAllowedClasses()`.
