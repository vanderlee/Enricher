Enricher
========
Version 1.0.1

[![Build Status](https://travis-ci.org/vanderlee/Enricher.svg?branch=master)](https://travis-ci.org/vanderlee/Enricher)

Copyright &copy; 2016 Martijn van der Lee.
MIT Open Source license applies.

Introduction
------------
Backfill (add only if missing), overwrite and remove classes, style rules and
attributes in HTML by using selectors.

Basic but fast HTML attribute and style modifier for PHP.
Similar to Emogrifier with only basic selectors, but added attribute support.
It's primary purpose is to change HTML for use in emails, but it is not in any
way limited to this particular use case.

Features
--------
*	Change styles, attributes and classes.
*	Add (if missing), overwrite and remove in one run.
*	Select based on tag names, class names, id's and/or presence of attributes.
*	Multiple selectors in one run.
*	Fast.

Comparison to Emogrifier
------------------------
Enricher's functionality and purpose has significant overlap with Emogrifier
(https://github.com/jjriv/emogrifier), but is different in a few key ways.

*	Both have full unittests.
*	Both support element selectors. i.e. `a`
*	Both support ID selectors. i.e. `#logo`.
*	Both support class selectors. i.e. `table.mobile`.
*	Both support attribute selectors. i.e. `a[name]`.
*	Both support composite selectors. i.e. `a[name].mobile`.
*	(+) Enricher is ___much___ faster than Emogrifier. Upto several orders of
	magnitude.
*	(+) Enricher supports adding and removing attributes.
*	(+) Enricher supports adding and removing classes.
*	(+) Enricher supports adding, overwriting and removing in one go.
*	(-) Emogrifier supports "path" selectors. i.e. `#list a.item`.
*	(-) Emogrifier supports pseudoselectors. i.e. `:nth-child`.
*	(-) Emogrifier supports attribute value selectors. i.e. `[name=anchor]`.
*	(/) Emogrifier requires valid HTML, which may or may not be an issue,
	depending on usage scenario.

In general; if you need complex selectors such as paths, use Emogrifier.
If you need class or attribute changing or complex CSS rewriting, use Enricher.
In other cases Enricher will do the same job, but faster.

Future plans
------------
*	Support `*` as an "all-elements"  wildcard.
*	Support more complex selectors.

Documentation
=============
Here you will find basic instructions to get you started.
You can find complete PHPDoc in the /doc directory.

Installation
------------
Get the latest version here: https://github.com/vanderlee/Enricher

Enricher requires atleast PHP 5.3 or HHVM.

Either include the autoloader (for forwards compatibility) or `include` the
`Enricher.php` file directly or through your own autoloader.

Constructor
-----------
The constructor does nothing.

Methods
-------
### `reset()`
Clear out all selectors, attribute, style and class settings.

### `addSelector($selector)`/`addSelectors($selectors)`
Add a selectors to specify which elements may be selected.
*	Select elements by specifying the tag. i.e. `div`.
*	Select by class by specifying the class. i.e. `div.blue`.
*	Select by id by specifying the id. i.e. `div#body`.
*	Select by attribute by specifying the attribute. i.e. `a[href]`.
*	Combine these to form selectors. i.e. `a[href][target].blue`.
*	You may specify multiple selectors by separating them using a comma (,).

### `addAttribute($name, $value)`/`addAttributes($attributes)`
### `overwriteAttribute($name, $value)`/`overwriteAttributes($attributes)`
### `removeAttribute($name)`/`removeAttributes($names)`
Add (if missing), overwrite or remove attributes.

### `addStyle($name, $value)`/`addStyles($styles)`
### `overwriteStyle($name, $value)`/`overwriteStyles($styles)`
### `removeStyle($name)`/`removeStyles($names)`
Add (if missing), overwrite or remove style rules.

### `addClass($name)`/`addClasses($names)`
### `removeClass($name)`/`removeClasses($names)`
Add or remove classes.

### `enrich($html)`
Enrich the HTML according the the settings.
If settings are missing, an empty HTML will be returned.

Example
-------
```php
require_once 'Enricher.php';

$enricher = new Enricher();

$enricher->addSelector('a[href]');

$enricher->addStyle('color', 'black');
$enricher->addAttribute('title', 'I'm a hyperlink');

echo $enricher->enrich($html);
```