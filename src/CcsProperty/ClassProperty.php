<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use Exception;
use iHTML\DOM\DOMDocument;
use iHTML\DOM\DOMElement;
use iHTML\iHTML\DocumentQuery;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class ClassProperty extends Property
{
    public const VISIBLE = 1003;
    public const HIDDEN = 1004;

    public const CCS = [
        'visible' => VisibilityProperty::VISIBLE,
        'hidden' => VisibilityProperty::HIDDEN,
    ];

    /**
     * @param DocumentQuery $context
     * @throws Exception
     */
    public static function apply(Crawler $list, array $params, DocumentQuery $context): void
    {
        $classesVisibility = self::declarationParamsToMap($params);
        foreach ($list as $element) {
            /** @var DOMElement $element */
            $classAttribute = $element->getAttribute('class');
            $classesMap = self::classAttributeToMap($classAttribute);
            foreach ($classesVisibility as $class => $visibility) {
                switch ($visibility) {
                    case self::VISIBLE:
                        $classesMap[$class] = true;
                        break;
                    case self::HIDDEN:
                        unset($classesMap[$class]);
                        break;
                }
            }
            $classAttribute = self::classMapToAttribute($classesMap);
            $element->setAttribute('class', $classAttribute);
        }
    }

    public static function declarationParamsToMap(array $params): array
    {
        if (count($params) % 2 > 0) {
            throw new Exception('Wrong `class` property values count');
        }
        $params = array_chunk($params, 2);
        $classesVisibility = [];
        foreach ($params as [$class, $visibility]) {
            if (!is_string($class)) {
                throw new Exception("Wrong `class` name ($class)");
            }
            if (!in_array($visibility, [self::VISIBLE, self::HIDDEN])) {
                throw new Exception("Wrong `class` visibility ($class)");
            }
            $classesVisibility[$class] = $visibility;
        }
        return $classesVisibility;
    }

    private static function classAttributeToMap(string $classList): array
    {
        $classList = (array)preg_split('/\s+/', $classList);
        $classList = array_filter($classList);
        $classList = array_flip($classList);
        return $classList;
    }

    private static function classMapToAttribute(array $classesMap): string
    {
        $classList = array_keys($classesMap);
        $classList = implode(' ', $classList);
        return $classList;
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}