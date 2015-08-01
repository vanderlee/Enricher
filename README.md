Enricher
========
Version 0.1 (incomplete unittests and readme)

[![Build Status](https://travis-ci.org/vanderlee/Enricher.svg?branch=master)](https://travis-ci.org/vanderlee/Enricher)

Copyright &copy; 2015 Martijn van der Lee.
MIT Open Source license applies.

Introduction
------------
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
*	Emogrifier allows "path" selectors, i.e. `#list a.item`. Enricher does not.
*	Emogrifier allows pseudoselectors such as ":nth-child". Enricher does not.
*	Enricher lets you add and remove attributes. Emogrifier does not.
*	Enricher lets you add and remove classes. Emogrifier does not.
*	Enricher lets you add, overwrite and remove in one run. Emogrifier does not.
*	Enricher is faster than Emogrifier.
In general; if you need complex selectors, use Emogrifier. In other cases
Enricher will do the same but faster.

Documentation
=============
TODO: Intro
TODO: Examples

Installation
------------
Get the latest version here: https://github.com/vanderlee/Enricher

Enricher requires atleast PHP 5.3 or HHVM.

Either include the autoloader (for forwards compatibility) or `include` the
`Enricher.php` file directly or through your own autoloader.

Constructor
-----------
TODO: Not yet implemented

Methods
-------
TODO: List all methods in short form
TODO: Link to generated PHPDoc

### `reset()`
TODO

### `addSelector($selector)`/`addSelectors($selectors)`
TODO

### `enrich($html)`
TODO

Selectors
---------
Add a selector to specify which elements may be selected.
*	Select elements by specifying the tag. i.e. `div`.
*	Select by class by specifying the class. i.e. `div.blue`.
*	Select by id by specifying the id. i.e. `div#body`.
*	Select by attribute by specifying the attribute. i.e. `a[href]`.
*	Combine these to form selectors. i.e. `a[href][target].blue`.
*	You may specify multiple selectors by separating them using a comma (,).

Changes
-------
Will