# The infrastructure layer

The infrastructure layer sits at the outside border of the application. All communication between external systems and the application, happen through the infrastructure layer. We can further divide the components from the infrastructure layer in 2 categories, namely UI, and Business service implementations.

## UI

The UI category consists of components used by external actors (both systems or humans) to communicate with our system. This interaction can be in different forms: through a command line client, a HTML web interface, web services, etc.

At the moment, the application offers RESTful web services. RESTful web services are typically made out of controllers and deserializers.

Controllers translate a HTTP request, to a call to a Business service. If the HTTP request contains a body with JSON encoded data, the controller often uses a deserializer to construct a typed object from the data contained in the body.

Examples:

* [UiTPAS\UiTPASController](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/UiTPAS/UiTPASController.php)
* [UiTPAS\Registration\RegistrationJsonDeserializer](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/UiTPAS/Registration/RegistrationJsonDeserializer.php)


## Business service implementations

Implementations of interfaces defined in the business layer that require external systems, among which:
* all implementations relying on external web services like the UiTPAS API, UiTID API or Search API;
* concrete implementations that store data on the file system.

Examples:

* [Help\FileStorage](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/Help/FileStorage.php)
* [UiTPAS\UiTPASService](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/UiTPAS/UiTPASService.php)
