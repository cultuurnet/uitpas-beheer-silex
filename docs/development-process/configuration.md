# Configuration

## Authorization

Add pairs of user names and a list of granted roles under 'roles' in config.yml:

```yaml
roles:
  johndoe:
    - ROLE_HELP_EDIT
  janedoe:
    - ROLE_HELP_EDIT
```

The following roles are available:

- ROLE_HELP_EDIT: edit the help text

## Cross-Origin Resource Sharing
 Add one entry for each path that should be allowed to access the Silex API.

```yaml
cors:
  origins:
    - http://culpas.dev
    - https://culpas.dev
    - http://culpas-app.dev
    - http://localhost:9999
```

## Debugging

```yaml
debug: false|true
```

## UitId credentials

```yaml
uitid:
  consumer:
    key: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    secret: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  base_url: https://acc2.uitid.be/uitid/rest/
```

## Search API

```yaml
search:
  base_url: https://acc2.uitid.be/uitid/rest/searchv2
```

## Logging

```yaml
bootstrap:
  logging: false|true
```
The default [directory for the log](../architecture/filesystem-layout/log.md) files is `/log`.

## 

```yaml

```

## 

```yaml

```