<?php


const SELECTOR_NAME_REGEX = '-?[_a-zA-Z]+[_a-zA-Z0-9-]*';

function selector_weight(string $selector, bool $important = false)
{
    $important = $important ? 1 : 0; // !important
    $style    = 0; // STYLE (e.g. style="rules")
    $ids      = 0; // #LABEL (e.g. #elementId)
    $classes  = 0; // .LABEL, [LABEL] and :LABEL (e.g. .class , [attr="value"])
    $elements = 0; // LABEL (e.g. H1)

    // removing :not selector (useless)
    $selector = str_replace(':not(', '', $selector);

    // remove labels
    $selector = preg_replace(['/'.SELECTOR_NAME_REGEX.'/', '/\s/'], ['L', ' '], $selector);

    // parsing...
    for ($i = 0; $i < strlen($selector); $i++) {
        $token = $selector[ $i ];
        switch ($token) {
        
            case '#':      $ids++; $i++; break;
            case '.':  $classes++; $i++; break;
            case '[':  $classes++; $i = strpos($selector, ']', $i); break;
            case ':':  $classes++; $i++; break;
            case 'L': $elements++; break;
            case '*':
            case ')':
            default: break;
        
        }
    }
    
    return $important.'.'.$style.'.'.$ids.'.'.$classes.'.'.$elements;
}

function weight_compare_gt(string $weight1, string $weight2)
{
    return version_compare($weight1, $weight2, '>');
}

function selectors_weight(string ...$selectors)
{
    $selector_weight = '0.0.0.0.0'; // starts with lowest
    
    foreach ($selectors as $selector) {
        $current_weight = selector_weight($selector);
        
        if (weight_compare_gt($current_weight, $selector_weight)) {
            $selector_weight = $current_weight;
        }
    }

    return $selector_weight;
}

function getMimetype($file)
{
    return [
        'css' => 'text/css',
        'js' => 'text/javascript',
    ][ pathinfo($file, PATHINFO_EXTENSION) ]
    ?? mime_content_type($file);
}

