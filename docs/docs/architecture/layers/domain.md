# The domain layer

The domain layer is the heart of the application. Components in the domain layer can be further divided into these categories:

* concrete classes modeling business concepts and rules
* interfaces defining needed business functionality, but without a concrete implementation
 
## Concrete classes modeling business concepts and rules

This category contains classes that model concepts important to the business, like UiTPAS, Passholder, Group, Activity, Price and Coupon.

## Interfaces defining needed business functionality

There are a lot of business needs, and how they are technically accomplished may depend on things outside of the application, like a particular database or on the filesystem, or external web services. The needs are captured by means of interfaces in the domain layer. Concrete implementations however belong in the infrastructure layer.