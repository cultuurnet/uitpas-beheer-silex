# Continuous Integration

[Travis CI](https://travis-ci.org/cultuurnet/uitpas-beheer-angular), a hosted continuous integration platform, executes all unit tests, coding standard checks and other validations whenever a commit is pushed to the central source code repository at GitHub. When you create pull requests on GitHub, links to Travis' status report will be visible in the pull request.

While Travis runs the unit tests, PHPUnit also generates a code coverage report. Travis pushes the data from this report to [Coveralls](https://coveralls.io/github/cultuurnet/uitpas-beheer-silex), a hosted code coverage reporting platform. Just like Travis, Coveralls.io will  link its status report into new pull requests on GitHub.

Consult the [Travis CI documentation](https://docs.travis-ci.com/) and the [Coveralls documentation](https://coveralls.zendesk.com/hc/en-us) for more info.