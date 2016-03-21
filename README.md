[![Build Status](https://travis-ci.org/cultuurnet/uitpas-beheer-silex.svg?branch=master)](https://travis-ci.org/cultuurnet/uitpas-beheer-silex)
[![Coverage Status](https://coveralls.io/repos/github/cultuurnet/uitpas-beheer-silex/badge.svg?branch=master)](https://coveralls.io/github/cultuurnet/uitpas-beheer-silex?branch=master)

![Swagger Validator](http://online.swagger.io/validator/?url=https://raw.githubusercontent.com/cultuurnet/uitpas-beheer-silex/master/web/swagger.json)

Read the [documentation for developers](https://cultuurnet.gitbooks.io/uitpas-beheer-silex/content/) 
to get started.

# Git hooks

For development purposes, we advice you to install the included git hooks with the following command:

    ./vendor/bin/phing githooks

One of the hooks will try to validate the project's Swagger file. This requires you to install some node dependencies.
 Make sure npm is available and install them by running this command at the root of the project:

    npm install
