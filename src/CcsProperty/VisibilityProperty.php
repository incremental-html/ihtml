<?php

namespace iHTML\CcsProperty;

use Exception;
use Symfony\Component\DomCrawler\Crawler;

/** @noinspection PhpUnused */

class VisibilityProperty extends Property
{
    const VISIBLE = 1003;
    const HIDDEN = 1004;

    public static function ccsConstants(): array
    {
        parent::ccsConstants();
        return [
            'visible' => VisibilityProperty::VISIBLE,
            'hidden' => VisibilityProperty::HIDDEN,
        ];
    }

    /**
     * @throws Exception
     */
    public static function apply(Crawler $list, array $params): void
    {
        if (!self::isValid(...$params)) {
            throw new Exception("Bad parameters: " . json_encode($params));
        }
        $later = Property::applyLater($list, $params, self::VISIBLE);
        foreach ($later as $late) {
            $late->element->parentNode->removeChild($late->element);
        }
    }

    private static function isValid(...$params): bool
    {
        return in_array($params[0], [self::VISIBLE, self::HIDDEN]);
    }
}
