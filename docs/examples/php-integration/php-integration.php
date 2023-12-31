#!/usr/bin/php
<?php
declare(strict_types=1);

use iHTML\CcsProperty\DisplayProperty;
use iHTML\CcsProperty\Property;
use iHTML\Filesystem\FileRegularExistent;
use iHTML\iHTML\Document;

require_once __DIR__ . '/../../../vendor/autoload.php';

$documentFile = new FileRegularExistent(__DIR__ . '/../static-html-bundles/html5up-stellar/generic.html');
$document = new Document($documentFile);

$testForeach = [
    (object)[
        'prop1' => 'a',
        'prop2' => 'b',
        'prop3' => 'c',
    ],
    (object)[
        'prop1' => 'd',
        'prop2' => 'e',
        'prop3' => 'f',
    ],
    (object)[
        'prop1' => 'g',
        'prop2' => 'h',
        'prop3' => 'i',
    ],
];

foreach ($testForeach as $each) {
    $document('.site-inner .content .entry-content:last-child')
        ->display(
            Property::getVar('--display'),
            Property::getVar('--display'),
        )
    ;
    $document('.site-inner .content .entry-content:nth-last-child(2) h2')
        ->text($each->prop1)
    ;
    $document('.site-inner .content .entry-content:nth-last-child(2)')
        ->attr('data-attr2')
        ->content($each->prop2)
    ;
    $document('.site-inner .content .entry-content:nth-last-child(2) p')
        ->content($each->prop3)
    ;
}
$document('.site-inner .content .entry-content:last-child')
    ->display(DisplayProperty::NONE)
;

$document->print();
