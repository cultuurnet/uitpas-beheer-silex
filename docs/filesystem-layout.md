# File system layout

## Directories
* [vendor](filesystem-layout/vendor.md)
* [src](filesystem-layout/src.md)
* [test](filesystem-layout/test.md)
* [app](filesystem-layout/app.md)
* [bootstrap](filesystem-layout/bootstrap.md)
* [web](filesystem-layout/web.md)
* [var](filesystem-layout/var.md)
* [log](filesystem-layout/log.md)

## Files
* [bootstrap.php](filesystem-layout/bootstrap-php.md)

We won't go into detail for other files in the root of the project. All you need to know is that they contain configuration for command line tools [^1] and hosted services [^2] that support the [development process](./../../development_process.md). To know more about their usage, consult the documentation of these projects.

[^1] Phing, PHPUnit and PHP_CodeSniffer

[^2] Travis CI, Coveralls, and the tool generating this documentation, GitBook
