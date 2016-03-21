# config.yml

`config.yml` allows for some configuration of the application.

By default this file is not present. During initial setup, you should copy
[`config.dist.yml`](./config-dist-yml.md) to `config.yml`.

You should never commit this file to the source code repository[^1].

[^1] An entry in `.gitignore` excludes config.yml from version control.
