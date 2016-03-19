# Git workflow

If you follow the agreed git branching model, smooth [peer reviews](./peer-review.md)
and painless deployments are guaranteed.

## Permanent branches
### `master`
* This branch is used on the production environment, and is released periodically.

### `hotfix/hotfix`
* Gets updated with `master` when possible and needed (after each release to production).
* This branch should always kept clean for emergencies.

## Temporary branches
### Feature branches
* Each issue gets its own feature branch.
* All feature branches should be branched from the `master` branch.
* All feature branch pull requests should have `master` as base branch.
* The naming for the feature branches is:
```
   feature/{Jira issue-number}
```

Examples are:
```
  feature/UBR-100
  feature/UBR-330
```

Only when the [Definition of Done](./definition-of-done.md) is entirely met, code is merged from the
feature branch to the master branch.

### Hotfix branches
 An issue might require a hotfix that needs to be brought to production as soon as possible. All hotfix branches should be branched from the `hotfix/hotfix` branch.

The naming for the hotfix branches is:
 ```
    hotfix/{Jira issue-number}
 ```

Examples are:
```
  hotfix/UBR-325
  hotfix/UBR-466
```
