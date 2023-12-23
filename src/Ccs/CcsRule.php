<?php

namespace iHTML\Ccs;

use Exception;
use iHTML\Document\DocumentModifier;

abstract class CcsRule
{

    // es: text-transform
    abstract public static function rule(): string;

    abstract public static function method(): string;

    //abstract function isValid(...$params): bool;
    
    public static function constants(): array
    {
        return [
            'display' => DocumentModifier::DISPLAY,
            'content' => DocumentModifier::CONTENT,
            'none'    => DocumentModifier::NONE,
            'inherit' => DocumentModifier::INHERIT,
        ];
    }

    public static function exec($query, $values)
    {
        $method = static::method();
        
        $values = array_map(fn ($value) =>
            $value instanceof \Sabberworm\CSS\Value\CSSString         ? $value->getString() : (
                is_string($value) && isset(static::constants()[ $value ]) ? static::constants()[ $value ] : (
                (function () {
                throw new Exception("Value $value is not defined.");
            })()
            )
            ), $values);

        $query->$method(...$values);
    }
}
