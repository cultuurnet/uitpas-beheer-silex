# Unit tests

Unit tests are written with the [PHPUnit](https://phpunit.de/) framework.

## Location

Tests are placed in the [test](../docs/filesystem-layout/test.md) directory, within additional subdirectories conform [PSR-4](http://www.php-fig.org/psr/psr-4/).

## Naming conventions
Tests are named after the class they are testing, with the suffix **Test**.

Test methods use [snake case](https://en.wikipedia.org/wiki/Snake_case) naming, as opposed to the rest of the code base. This makes it easier to generate meaningful, human readable descriptions from the test cases with [PHPUnit's TestDox feature](https://phpunit.de/manual/current/en/other-uses-for-tests.html).

## More info

Consult the PHPUnit manual for more information.
