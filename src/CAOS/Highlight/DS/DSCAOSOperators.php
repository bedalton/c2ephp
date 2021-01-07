<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */
/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\DS;
use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * Valid operators for DS CAOS
 */
class DSCAOSOperators  implements HasTokens {

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

