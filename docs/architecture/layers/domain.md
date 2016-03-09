# The domain layer

The domain layer is the heart of the application. Components in the domain layer can be further divided into these categories:

* Concrete classes: modeling business concepts and rules
* Interfaces: defining needed business functionality, without a concrete implementation
 
## Concrete classes

This category contains classes that model existing concepts important to the business. Some of these are: UiTPAS, Passholder, Group, Activity, Price and Coupon.

Examples:

* [UiTPAS\UiTPAS](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/UiTPAS/UiTPAS.php)
* [UiTPAS\UiTPASType](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/UiTPAS/UiTPASType.php)
* [UiTPAS\UiTPASNumber](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/UiTPAS/UiTPASNumber.php)

## Interfaces

There are a lot of business needs.How these are technically accomplished may depend on things outside of the application (like a particular database, the file system or external web services). The needs are captured by means of interfaces in the domain layer. Concrete implementations however belong in the infrastructure layer.

Examples:

* [Help\StorageInterface](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/Help/StorageInterface.php)
* [UiTPAS\UiTPASServiceInterface](https://github.com/cultuurnet/uitpas-beheer-silex/blob/master/src/UiTPAS/UiTPASServiceInterface.php)