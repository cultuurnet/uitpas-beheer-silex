# log

The `log` directory is used for storing log files. A log file is written here when the logging setting in the `config.yml` or `config.dist.yml` file is set to `true`

```yaml
bootstrap:
  logging: true
```

You should never commit the contents of this directory to the source code repository[^1].

[^1] An entry in `.gitignore` excludes everything in the log directory from version control.
