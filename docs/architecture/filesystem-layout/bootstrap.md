# Bootstrap

The `bootstrap` directory contains additional PHP files that can be loaded before the application runs. These files can be used to override or extend existing services. Some cases that might be in reach:
* Log directly to a SAAS or self-hosted central log management tool like Loggly, Logentries, Logstash, ... instead of log files
* Collect performance metrics of requests to the UiTPAS API