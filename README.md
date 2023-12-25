# ihtml
iHTML - incremental HTML

A template engine

## Installation

```sudo curl -JL https://clue.engineering/phar-composer-latest.phar -o /usr/local/bin/phar-composer

sudo phar-composer build https://github.com/incremental-html/ihtml.git:dev-master /usr/local/bin/ihtml
```

## Functionalities
* Template engine
* StaticGen (See [JAMStack](https://jamstack.org/))
* Server

## Advantages
* One language for everything (se `use cases` below)
* consistency with the rest of environment - one language everywhere, JAVASCRIPT, CSS AND HERE
* No need one more (maybe) language, SELECTORS EVERYWHERE, DOM EVERYWHERE
* Layout TOTALLY separated from code, a designer can manage the final html file
* No need to prepare an HTML for integration
* No need to prepare HTML for new block - inheritance is EVERYWHERE, customizability is EVERYWHERE
* Better HTML files, more readable. *"Lorem ipsum" is the way*.

## Use cases
* HTML Inheritance
* Site Structure (pages, sections, etc...)
* Modularization (separate ads, sidebar, ecc...)
* Data compiling
and then
* Multilanguage support (site multilanguage structure and labels system)
* Template engines code injection (Twig, Smarty, etc...)
* CMS (title and content, Markdown, BBcode, HTML, text plain, etc...)
and again
* Sanitization (removing every not-allowed content in a tag)
* Minify (WTF?!!)

## Integration
* **SASS/SCSS**, to structure your code
* **PurgeCSS**, to Purge unused content modifications

## Getting started
TODO

## TODO
* add property `attribute`:
  * `attribute: "title" "some text";` to change content
  * `attribute: "title" "";` to empty
  * `attribute: "title" none;` to remove the attribute
  * `attribute: "title" visibile/hidden;` to hide (may be shown again)
  * `attribute: "title" "My new content" content;` to concatenate
* add property `class`:
  * `class: "my-class" visibile;` to add the class
  * `class: "my-class" hidden;` to remove it
* add property `ldjson`:
  * `ldjson: "./title" "My title"` to set path
* add property `style`:
  * `style: background-color "black"` to set style
  * `style: background-color none`
* functionalities
  * add CSS vars(--var) function support
  * support for rules: border, margin, padding, wikitext
  * add javascript on* attributes support
    * .element { onclick = "execMe();" }
  * add support for rule `attributes: A B, C D`
  * add `content` attribute support
  * add `code` rule support
* internal selector navigation supports
  * add `<style>` support
  * add `[srcset]` support
  * add `ld+json` navigation support
  * add `url` parts support (Valid URL attributes - https://www.w3.org/TR/2017/REC-html52-20171214/fullindex.html#attributes-table)
    * link[href],
    * script[src],
    * a[href],
    * img[src],
    * source[src],
    * video[poster]
    * and other URI
* integration
  * add **SASS/SCSS** example
  * add **PurgeCSS** integration
* refactor
  * move to a REAL html5 parser (like the Chrome one)
  * add white-space support for inline CSSs and JSs
  * add full website example to be used as unit test
    * add `blog posts` example
  * add incremental caching (after benchmarks and choice of platform)
  * add dependency tool on project
  * add check @import loop

## Usage

Applies ccs on template and outputs to file:
```shell
ihtml <template> <ccs> [-o <file>]
```

Applies code on template and outputs to file:
```shell
ihtml <template> -r "<code>" [-o <file>]
```

Applies stdin on template and outputs to file:
```shell
ihtml <template> [-o <file>]
<code>
```

Compiles the project:
```shell
ihtml -p <project> [-o <directory>]
```

Opens a server on a project:
```shell
ihtml -p <project> -s [<PORT>] [-t <static files dir>]
```

## Examples
```css
/*
 *
 * Examples of CCS functions
 * (<SELECTOR> is a valid CSS3 selector)
 *
 */

/* Updates content of a tag with new html */
<SELECTOR> {
  content: "HTML CONTENT EXCLUDED TAGS";
}

/* Removes an element */
<SELECTOR> {
  display: none;
}

/* Replaces an element with new html */
<SELECTOR> {
  display: "<div>HTML CONTENT INCLUDING TAGS</div>";
}

/**/
<SELECTOR> {
  text: "TEXT CONTENT (URL ENTITY ENCODED)";
}

/**/
<SELECTOR> {
  display: '<div>' content '</div>';
}

/**/
<SELECTOR> {
  display: '<div>' display display display '</div>';
}

/**/
@import url(local/file.ccs);

/**/
<SELECTOR> {
  content: url(content.html);
}

/**/
<SELECTOR> {
  content: " \
<div> \
  Content \
</div> \
";
}

/**/
<SELECTOR> {
  /**/
  text-transform: uppercase;
  /**/
  text-transform: lowercase;
  /**/
  text-transform: capitalize;
  /**/
  text-transform: none;
}
/**/
<SELECTOR> {
  /**/
  visibility: visible;
  /**/
  visibility: hidden;
}
/**/
<SELECTOR> {
  /**/
  white-space: normal;
  /**/
  white-space: nowrap;
  /**/
  white-space: pre;
  /**/
  white-space: pre-line;
  /**/
  white-space: pre-wrap;
}

/**/
<SELECTOR> {
  bbcode: "[b]BB CODE[/b] ENCODED TO [i]HTML[/i]";
}

/**/
<SELECTOR> {
  markdown: url(content.md);
}

/**/
<SELECTOR> {
  attr-<ATTRIBUTE>-content: "STRING";
  /**/
  attr-<ATTRIBUTE>-display: none | "STRING";
  /**/
  attr-<ATTRIBUTE>-visibility: visible | hidden;
  /**/
  attr-<ATTRIBUTE>[-content]: "STRING";
}
/**/
<SELECTOR> {
  style-<RULE>-content: "CSS VALUE STRING";
  /**/
  style-<RULE>-literal: css value literal;
  /**/
  style-<RULE>-display: "<div> Content </div>";
  /**/
  style-<RULE>-visibility: visible | hidden;
  /**/
  style-<RULE>[-literal]: css value literal;
  /**/
  [style-]CSSRULENAME[-literal]: css value literal;
}
/**/
<SELECTOR> {
  class-<CLASSNAME>-visibility: visible | hidden;
  /**/
  class-<CLASSNAME>[-visibility]: visible | hidden;
  /**/
  [class-]GENERICNAME[-visibility]: visible | hidden;
}
```

See docs/examples/ for other examples.

