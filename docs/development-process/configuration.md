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
 In the example, `johndoe`and `janedoe` are uitid usernames.

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

## Feedback information
 To set where the feedback messages should be sent to.
```yaml
feedback:
  from: foo@bar.com
  to: foo@bar.com
  subject: "UiTPAS balie beheer feedback."
```

## Mailcatcher

```yaml
swiftmailer.options:
  # This connects to a mailserver at port 1025
  # This is not the standard SMTP port, but the default port of Mailcatcher,
  # a great tool for debugging mail. Check it out! http://mailcatcher.me/
  host: 127.0.0.1
  port: 1025
```

## Export

```yaml
export:
  limit_per_api_request: 50
```