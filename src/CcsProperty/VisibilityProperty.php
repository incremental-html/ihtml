<?php

namespace iHTML\CcsProperty;

class VisibilityProperty extends Property
{
    const VISIBLE = 1003;
    const HIDDEN  = 1004;

    public static function ccsConstants(): array
    {
        parent::ccsConstants();
        return [
            'visible' => VisibilityProperty::VISIBLE,
            'hidden' => VisibilityProperty::HIDDEN,
        ];
    }

    public static function queryMethod(): string
    {
        return 'visibility';
    }
    
    public static function isValid(...$params): bool
    {
        return in_array($params[0], [self::VISIBLE, self::HIDDEN]);
    }

    public function apply(\DOMElement $element)
    {
        $this->applyLater($element, self::VISIBLE);
    }

    public function render()
    {
        foreach ($this->lates as $late) {
            $late->element->parentNode->removeChild($late->element);
        }
    }
}
