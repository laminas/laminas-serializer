# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.7.0 - 2016-04-06

### Added

- [zendframework/zend-serializer#14](https://github.com/zendframework/zend-serializer/pull/14) exposes the
  package as a Laminas component and/or generic configuration provider, by adding the
  following:
  - `AdapterPluginManagerFactory`, which can be consumed by container-interop /
    laminas-servicemanager to create and return a `AdapterPluginManager` instance.
  - `ConfigProvider`, which maps the service `SerializerAdapterManager` to the above
    factory.
  - `Module`, which does the same as `ConfigProvider`, but specifically for
    laminas-mvc applications. It also provices a specification to
    `Laminas\ModuleManager\Listener\ServiceListener` to allow modules to provide
    serializer configuration.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.6.1 - 2016-02-03

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-serializer#13](https://github.com/zendframework/zend-serializer/pull/13) updates the
  laminas-stdlib dependency to `^2.7 || ^3.0`, as it can work with either version.

## 2.6.0 - 2016-02-02

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-serializer#2](https://github.com/zendframework/zend-serializer/pull/2) updates the component
  to use laminas-servicemanager v3. This involves updating the `AdapterPluginManager`
  to follow changes to `Laminas\ServiceManager\AbstractPluginManager`, and updating
  the `Serializer` class to inject an empty `ServiceManager` into instances of
  the `AbstractPluginManager` that it creates.
