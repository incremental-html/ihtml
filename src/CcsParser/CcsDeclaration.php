<?php
declare(strict_types=1);

namespace iHTML\CcsParser;

use Exception;
use iHTML\CcsProperty\Property;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;
use iHTML\iHTML\DocumentQuery;
use Sabberworm\CSS;
use function Symfony\Component\String\u;

class CcsDeclaration
{
    public string $property;
    public string $rawValue;
    public array $values;

    public function __construct($oRule, FileDirectoryExistent $root)
    {
        $this->property = $oRule->getRule();
        $this->rawValue = (string)$oRule->getValue();
        $this->values =
            $oRule->getValue() instanceof CSS\Value\RuleValueList ?
                $oRule->getValue()->getListComponents() :
                [$oRule->getValue()];
        /**
         * @throws Exception
         */
        $this->values = array_map(
            fn($value) => match (true) {
                $value instanceof CSS\Value\URL =>
                new CSS\Value\CSSString(
                    (new FileRegularExistent($value->getURL()->getString(), $root))
                        ->contents(),
                ),
                default => $value,
            },
            $this->values,
        );
    }

    public function executeOn(DocumentQuery $query): void
    {
        $method = $this->getMethod();
        $arguments = $this->getValues();
        $query->$method(...$arguments);
    }

    private function getMethod(): string
    {
        return (string)u($this->property)->camel();
    }

    private function getValues(): array
    {
        $propertyClass = '\\iHTML\\CcsProperty\\' . u($this->property)->camel()->title() . 'Property';
        if (!class_exists($propertyClass)) {
            throw new Exception("Class `$propertyClass` not implemented for property `$this->property`.");
        }
        /** @var Property $propertyClass */
        return $propertyClass::convertPropertyValues($this->values);
    }
}