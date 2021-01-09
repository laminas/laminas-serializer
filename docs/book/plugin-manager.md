# Plugin Manager

## AdapterPluginManager

The `AdapterPluginManager` extends the laminas-servicemanager
`AbstractPluginManager`, and has the following behaviors:

- It will only return `Laminas\Serializer\Adapter\AdapterInterface` instances.
- It defines short-name aliases for all shipped serializers (the class name minus
  the namespace), in a variety of casing combinations.
- All services are shared by default; a new instance will only be created once and shared each time you call `get()`.

### AdapterPluginManagerFactory

`Laminas\Serializer\AdapterPluginManager` is mapped to the factory
`Laminas\Serializer\AdapterPluginManagerFactory` when wired to the dependency
injection container.

The factory will look for the `config` service, and use the `serializers`
configuration key to seed it with additional services. This configuration key
should map to an array that follows [standard laminas-servicemanager configuration](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/).
