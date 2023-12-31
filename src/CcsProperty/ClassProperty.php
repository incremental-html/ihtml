<?php
declare(strict_types=1);

namespace iHTML\CcsProperty;

use DOMDocument;
use DOMElement;
use Exception;
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
     * @throws Exception
     */
    public static function apply(Crawler $list, array $params): void
    {
        foreach ($list as $element) {
            /** @var DOMElement $element */
            if (count($params) % 2 > 0) {
                throw new Exception('Wrong `class` property values count');
            }
            $params = array_chunk($params, 2);
            foreach ($params as [$class, $visibility]) {
                if (!is_string($class)) {
                    throw new Exception("Wrong `class` name ($class)");
                }
                if (!in_array($visibility, [self::VISIBLE, self::HIDDEN])) {
                    throw new Exception("Wrong `class` visibility ($class)");
                }
                $classList = $element->getAttribute('class');
                $classList = (array)preg_split('/\s+/', $classList);
                $classList = array_filter($classList);
                $classList = array_flip($classList);
                switch ($visibility) {
                    case self::VISIBLE:
                        $classList[$class] = true;
                        break;
                    case self::HIDDEN:
                        unset($classList[$class]);
                        break;
                    default:
                }
                $classList = array_keys($classList);
                $classList = implode(' ', $classList);
                $element->setAttribute('class', $classList);
            }
        }
    }

    public static function render(DOMDocument $domDocument): void
    {
    }
}