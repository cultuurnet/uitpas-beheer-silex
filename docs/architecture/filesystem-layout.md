# File system layout

## Folders
* [Vendor](filesystem-layout/vendor.md)
* [Src](filesystem-layout/src.md)
* [Test](filesystem-layout/test.md)
* [App](filesystem-layout/app.md)
* [Bootstrap](filesystem-layout/bootstrap.md)
* [Web](filesystem-layout/web.md)
* [Var](filesystem-layout/var.md)
* [Log](filesystem-layout/log.md)

## Files
* [Bootstrap.php](filesystem-layout/bootstrap-php.md)

We won't go into detail for other files in the root of the project. All you need to know is that they contain configuration for command line tools [^1] and hosted services [^2] that support the [development process](./../../development_process.md).

[^1] Phing, PHPUnit and PHP_CodeSniffer

[^2] Travis CI, Coveralls, and the tool generating this documentation, GitBook