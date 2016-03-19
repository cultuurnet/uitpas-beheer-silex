# Layered architecture

The application follows a layered architecture, comparable to the different layers of an onion skin. This architecture is often referred to as an "Onion Architecture" or "Hexagonal architecture". Each layer has its own responsibilities, which must not be broadened in order to keep the code base clean, easy to extend and adapt to changes outside of the application. We consider the following 2 big layers in the application, ordered from outside to inside:

* [Infrastructure](layers/infrastructure.md)
* [Domain](layers/domain.md)

Components of one layer must only depend on components of a more inbound layer. Any direct communication from outside with the application, or from the application with outside systems, always happens through the outer layer. Databases, a file system, external web services, ... are considered to be outside of the application.

Currently, the separation in layers is not reflected in the PHP namespace & file system layout.

Read more about the Onion Architecture in a [blog post of Jeffrey Palermo](http://jeffreypalermo.com/blog/the-onion-architecture-part-1/).