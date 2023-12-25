#!/usr/bin/php
<?php

require_once __DIR__ . '/../src/iHTML.php';

$document = new \iHTML\iHTML\Document(__DIR__.'/example.html');

$document('.site-inner .entry-title')
    ->content('test')
;

$document('.site-inner .entry-title')
    ->display('<h1>', iHTML\Modifiers\DisplayModifier::CONTENT, '</h1>')
;

$document('footer')
    ->visibility(iHTML\Modifiers\VisibilityModifier::HIDDEN)
;

$document('.site-inner .content .entry-content')
    ->className('entry-content')->visibility(iHTML\QueryClass::HIDDEN)
    ->className('entry-content-2')->visibility(iHTML\QueryClass::VISIBLE)
    ->className('has-background')->visibility(iHTML\QueryClass::HIDDEN)
    ->className('has-background')->visibility(iHTML\QueryClass::VISIBLE)
    ->className('has-background', iHTML\QueryClass::VISIBLE) // as visibility
    ->className('has-background', iHTML\QueryClass::HIDDEN) // as visibility
;

$document('.site-inner .content .entry-content')
    ->attr('itemprop')->content('itemprop-content')
    ->attr('data-href', 'string') // as content: 'string'
    ->attr('data-href', 'none') // as content: 'none'
    ->attr('data-href', '') // as content: ''
;

$document('.site-inner .content .entry-content')
    ->attr('itemprop')->visibility(iHTML\QueryAttr::HIDDEN)
    ->attr('data-href', iHTML\QueryAttr::VISIBLE) // as visibility: VISIBLE
    ->attr('data-href', iHTML\QueryAttr::HIDDEN) // as visibility: HIDDEN
    ->attr('data-href', iHTML\QueryAttr::NONE) // as display: ''
;

$document('.site-inner .content .entry-content')
    ->style('background-image')->content('string')
    ->style('background-image', 'string') // as content: 'string'
    ->style('background-image', 'none') // as content: 'none'
    ->style('background-image', '') // as content: ''
;

$document('.site-inner .content .entry-content')
    ->style('background-image')->visibility(iHTML\QueryStyle::HIDDEN)
    ->style('background-image')->visibility(iHTML\QueryStyle::HIDDEN)
    ->style('background-image', iHTML\QueryStyle::VISIBLE) // as visibility: VISIBLE
    ->style('background-image', iHTML\QueryStyle::HIDDEN) // as visibility: HIDDEN
    ->style('background-image', iHTML\QueryStyle::NONE) // as display: ''
;

$document->render(STDOUT);
