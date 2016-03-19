# bootstrap

The `bootstrap` directory contains additional PHP files that can be loaded before the application runs. These files can be used to override or extend existing services. Some cases that might be accomplished this way:

* Log directly to a SAAS or self-hosted central log management tool (like Loggly, Logentries, Logstash, ...) instead of log files
* Collect performance metrics of requests to the UiTPAS API


You need to activate additional bootstrap files in in [`config.yml`](./config-yml.md).

Out of the box, there is only one additional bootstrap file `logging.php` which
facilitates logging to files in the [`log` ](./log.md) directory.
