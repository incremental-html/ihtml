# ihtml
iHTML - incremental HTML

A template engine

## Installation

```sudo curl -JL https://clue.engineering/phar-composer-latest.phar -o /usr/local/bin/phar-composer

phar-composer install https://github.com/incremental-html/ihtml.git
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
* Site structure (pages, sections, etc...)
* Modularization (separate ads, sidebar, ecc...)
* Content injection

and then:
* CMS (title and content, Markdown, BB-code, HTML, text plain, etc...)
* Template engines code injection (Twig, Mustache, Smarty, etc...)
* Multi-language support (site multi-language structure and labels system)

* and again:
* Sanitization (removing every not-allowed content in a tag)
* Minify

## Integration
* **SASS/SCSS**, to structure your code
* **PurgeCSS**, to Purge unused content modifications

## Getting started
TODO

## TODO
* `.ihtml` file support
  * contains .html source and .ccs to apply. Or an .html with content tag or link contentsheet
  * must replace resources and errors in project
* ccs properties:
  * `attribute`:
    * `attribute: "title" "some text";` to change content
    * `attribute: "title" "";` to empty
    * `attribute: "title" none;` to remove the attribute
    * `attribute: "title" visibile/hidden;` to hide (may be shown again)
    * `attribute: "title" "My new content" content;` to concatenate
  * `style`:
    * `style`: background-color "black"` to set style
    * `style`: background-color none`
  * `srcset`
    * `srcset`: "size" none
    * `srcset`: "size" "image.jpg"
  * `border`
  * `margin` top bottom left right
  * `padding` top bottom left right
  * `tag`
  * `wikitext`
  * `code` (with highlight_string)
  * css-white-space
    * minified, normal, compact
  * js-white-space
    * minified, normal, compact
  * `on`
    * `on: click "execMe();"`
* ccs functions:
  * content(): opening tag, closing tag, tag name ("div")
  * `<string> url(<string> file)` (done)
  * `<json> json(<string> code)`
  * `<json> yaml(<string> code)`
  * `<string> json-select(<json> data, <string> path)`
  * `<string> json-set(<json> data, <string> path, <mixed> value)`
  * `<string> uri-set(<URI> data, <string> path, <mixed> value)`
    * (Valid URL attributes - https://www.w3.org/TR/2017/REC-html52-20171214/fullindex.html#attributes-table)
    * link[href],
    * script[src],
    * a[href],
    * img[src],
    * source[src],
    * video[poster]
    * and other URI
  *  `<string> html-set(<dom> data, <string> selector)`
  * `<string> style-set(<string> content, <string> selector, <string> property, <string> value)`
  * `<mixed> var(<label> name)`
  * `<URI> uri(<string> url)`
* integration
  * add **SASS/SCSS** example
  * add **PurgeCSS** integration

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
ihtml -p <project> -s [<PORT>] [-t <static fs
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
  class: "<CLASSNAME>" visible;
  /**/
  class: "<CLASSNAME>" hidden;
   /**/
   class: "<CLASSNAME>" visible "<CLASSNAME>" hidden;
}
```

See docs/examples/ for other examples.

