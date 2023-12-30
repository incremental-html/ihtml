<?php

namespace iHTML\CcsProperty;

use Exception;
use Symfony\Component\DomCrawler\Crawler;
use function in_array;

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
        if (count($params) > 1) {
            throw new Exception("Bad parameters count: " . json_encode($params));
        }
        $valid = [
            VisibilityProperty::VISIBLE,
            VisibilityProperty::HIDDEN,
        ];
        if (!in_array($params[0], $valid)) {
            throw new Exception("Bad parameters: " . json_encode($params));
        }
        $later = Property::applyLater($list, $params, self::VISIBLE);
        foreach ($later as $late) {
            $late->element->parentNode->removeChild($late->element);
        }
    }
}
