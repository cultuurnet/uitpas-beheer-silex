# Layered architecture

The application follows a layered architecture, comparable to the different layers of an onion skin. Each layer has its own responsibilities, which must not be broadened in order to keep the code base clean and easy to extend. Components of one layer must only depend on components of a more inbound layer.

Currently, the separation in layers is not reflected in the PHP namespace & file system layout.

Read more about the Onion Architecture in a [blog post of Jeffrey Palermo](http://jeffreypalermo.com/blog/the-onion-architecture-part-1/).