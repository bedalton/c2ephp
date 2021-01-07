<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */
/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\C2;

use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * C2 CAOS dictionary of tokens that can act like commands or variables
 */
class C2CAOSCommandVariables implements HasTokens {
    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
            'targ',
            'edit',
            'bhvr',
            'tick',
            'pose',
            'aslp',
            'vrsn'
        );
    }
}
/// @endcond

