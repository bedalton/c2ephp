<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */

/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\C3;

use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * Valid C3 CAOS operators
 */
class C3CAOSOperators implements HasTokens {

    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
        '=',
        '<>',
        '<',
        '<=',
        '>',
        '>=',
        'eq',
        'gt',
        'lt',
        'ne',
        'and',
        'or'
        );
    }
}

/// @endcond

