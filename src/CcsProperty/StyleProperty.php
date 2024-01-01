<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMElement;
use Exception;
use iHTML\DOM\DOMDocument;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class StyleProperty extends Property
{
    public const NONE = 2005;
    // public const VISIBLE = 2006;
    // public const HIDDEN = 2007;
    public const CCS = [
        'none' => self::NONE,
        // 'visible' => self::VISIBLE,
        // 'hidden' => self::HIDDEN,
    ];

    public static function apply(Crawler $list, array $params): void
    {
        $styles = self::declarationParamsToMap($params);
        // TODO: Implement apply() method.
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $styleAttribute = $element->getAttribute('style');
            $stylesMap = self::parseStyleAttribute($styleAttribute);
            foreach ($styles as $styleProperty => $styleValue) {
                if ($styleValue === self::NONE) {
                    unset($stylesMap[$styleProperty]);
                    continue;
                }
                $stylesMap[$styleProperty] = $styleValue;
            }
            if (empty($stylesMap)) {
                $element->removeAttribute('style');
            } else {
                $styleAttribute = self::renderStyleAttribute($stylesMap);
                $element->setAttribute('style', $styleAttribute);
            }
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
        // TODO: Implement render() method.
    }

    private static function parseStyleAttribute($style): array
    {
        $style = trim($style, " \t\n\r\0\x0B" . ';');
        if (!$style) {
            return [];
        }
        $style = explode(';', $style);
        $style = array_map(function ($rule) {
            return explode(':', $rule, 2);
        }, $style);
        $rules = [];
        foreach ($style as [$rule, $value]) {
            $rules[trim($rule)] = trim($value);
        }
        return $rules;
    }

    private static function renderStyleAttribute($rules): string
    {
        $style = '';
        foreach ($rules as $rule => $value) {
            $style .= "$rule:$value;";
        }
        return $style;
    }

    private static function declarationParamsToMap(array $params): array
    {
        if (count($params) % 2 > 0) {
            throw new Exception('Wrong `style` property values count');
        }
        $params = array_chunk($params, 2);
        $styles = [];
        foreach ($params as [$styleProperty, $styleValue]) {
            $styles[$styleProperty] = $styleValue;
        }
        return $styles;
    }

    // public const CCS = [
    //     'align-content',
    //     'align-items',
    //     'align-self',
    //     'all',
    //     'animation',
    //     'animation-delay',
    //     'animation-direction',
    //     'animation-duration',
    //     'animation-fill-mode',
    //     'animation-iteration-count',
    //     'animation-name',
    //     'animation-play-state',
    //     'animation-timing-function',
    //     'backface-visibility',
    //     'background',
    //     'background-attachment',
    //     'background-blend-mode',
    //     'background-clip',
    //     'background-color',
    //     'background-image',
    //     'background-origin',
    //     'background-position',
    //     'background-repeat',
    //     'background-size',
    //     'border',
    //     'border-bottom',
    //     'border-bottom-color',
    //     'border-bottom-left-radius',
    //     'border-bottom-right-radius',
    //     'border-bottom-style',
    //     'border-bottom-width',
    //     'border-collapse',
    //     'border-color',
    //     'border-image',
    //     'border-image-outset',
    //     'border-image-repeat',
    //     'border-image-slice',
    //     'border-image-source',
    //     'border-image-width',
    //     'border-left',
    //     'border-left-color',
    //     'border-left-style',
    //     'border-left-width',
    //     'border-radius',
    //     'border-right',
    //     'border-right-color',
    //     'border-right-style',
    //     'border-right-width',
    //     'border-spacing',
    //     'border-style',
    //     'border-top',
    //     'border-top-color',
    //     'border-top-left-radius',
    //     'border-top-right-radius',
    //     'border-top-style',
    //     'border-top-width',
    //     'border-width',
    //     'bottom',
    //     'box-shadow',
    //     'box-sizing',
    //     'caption-side',
    //     'clear',
    //     'clip',
    //     'color',
    //     'column-count',
    //     'column-fill',
    //     'column-gap',
    //     'column-rule',
    //     'column-rule-color',
    //     'column-rule-style',
    //     'column-rule-width',
    //     'column-span',
    //     'column-width',
    //     'columns',
    //     'content',
    //     'counter-increment',
    //     'counter-reset',
    //     'cursor',
    //     'direction',
    //     'display',
    //     'empty-cells',
    //     'filter',
    //     'flex',
    //     'flex-basis',
    //     'flex-direction',
    //     'flex-flow',
    //     'flex-grow',
    //     'flex-shrink',
    //     'flex-wrap',
    //     'float',
    //     'font',
    //     '@font-face',
    //     'font-family',
    //     'font-size',
    //     'font-size-adjust',
    //     'font-stretch',
    //     'font-style',
    //     'font-variant',
    //     'font-weight',
    //     'hanging-punctuation',
    //     'height',
    //     'justify-content',
    //     '@keyframes',
    //     'left',
    //     'letter-spacing',
    //     'line-height',
    //     'list-style',
    //     'list-style-image',
    //     'list-style-position',
    //     'list-style-type',
    //     'margin',
    //     'margin-bottom',
    //     'margin-left',
    //     'margin-right',
    //     'margin-top',
    //     'max-height',
    //     'max-width',
    //     '@media',
    //     'min-height',
    //     'min-width',
    //     'nav-down',
    //     'nav-index',
    //     'nav-left',
    //     'nav-right',
    //     'nav-up',
    //     'opacity',
    //     'order',
    //     'outline',
    //     'outline-color',
    //     'outline-offset',
    //     'outline-style',
    //     'outline-width',
    //     'overflow',
    //     'overflow-x',
    //     'overflow-y',
    //     'padding',
    //     'padding-bottom',
    //     'padding-left',
    //     'padding-right',
    //     'padding-top',
    //     'page-break-after',
    //     'page-break-before',
    //     'page-break-inside',
    //     'perspective',
    //     'perspective-origin',
    //     'position',
    //     'quotes',
    //     'resize',
    //     'right',
    //     'tab-size',
    //     'table-layout',
    //     'text-align',
    //     'text-align-last',
    //     'text-decoration',
    //     'text-decoration-color',
    //     'text-decoration-line',
    //     'text-decoration-style',
    //     'text-indent',
    //     'text-justify',
    //     'text-overflow',
    //     'text-shadow',
    //     'text-transform',
    //     'top',
    //     'transform',
    //     'transform-origin',
    //     'transform-style',
    //     'transition',
    //     'transition-delay',
    //     'transition-duration',
    //     'transition-property',
    //     'transition-timing-function',
    //     'unicode-bidi',
    //     'vertical-align',
    //     'visibility',
    //     'white-space',
    //     'width',
    //     'word-break',
    //     'word-spacing',
    //     'word-wrap',
    //     'z-index',
    // ];
}