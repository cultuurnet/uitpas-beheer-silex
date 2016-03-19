# Vendor

The `vendor` directory contains the application's dependencies, which were installed with [Composer](https://getcomposer.org).

You should never commit the contents of this directory to the source code repository [^1]. Instead, you manage the exact versions of the dependencies with composer.json and composer.lock.

[^1] An entry in .gitignore excludes the entire vendor directory from version control.