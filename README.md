# SilverStripe Form Elements module

![Build Status](https://github.com/lekoala/silverstripe-form-elements/actions/workflows/ci.yml/badge.svg)
[![scrutinizer](https://scrutinizer-ci.com/g/lekoala/silverstripe-form-elements/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lekoala/silverstripe-form-elements/)
[![Code coverage](https://codecov.io/gh/lekoala/silverstripe-form-elements/branch/master/graph/badge.svg)](https://codecov.io/gh/lekoala/silverstripe-form-elements)

## Intro

A set of form elements based on [Formidable Elements](https://github.com/lekoala/formidable-elements).

Available fields:

-   BS Tags (ajax)
-   BS Autocomplete (ajax)
-   Cleave (mask)
-   Coloris
-   Flatpickr
-   Growing textarea
-   InputMask (mask)
-   TelInput
-   TipTap
-   TomSelect (ajax)

Formatters:

-   Date
-   Number

Some other elements are implemented in distinct modules:

-   https://github.com/lekoala/silverstripe-filepond
-   https://github.com/lekoala/silverstripe-tabulator

## Requirements

All fields expose a `requirements` static method. This will include the requirements except if you disabled them with the `enable_requirements` config var.
This method is called (unless disabled) in the `Field` method.

## TODO

-   i18n
-   Doc
-   Tests

## Compatibility

Tested with 4.13 but should work with ^4 or ^5.

## Maintainer

LeKoala - thomas@lekoala.be
