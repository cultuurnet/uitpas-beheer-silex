# web

The `web` directory contains publicly accessible resources. It should be set as the document root when configuring a virtual host on your webserver.

Inside the `web` directory, you will find the front controller `index.php`. If the webserver supports it, you should put the proper rewrite rules in place to handle most of the requests with this file. A `.htaccess` file is present with the necessary rewrite rules for the Apache HTTP server.

Next to `index.php`, you will find `swagger.json`. This file contains [Swagger documentation](./../development-process/swagger-documentation.md) for the RESTful webservices the application provides.
