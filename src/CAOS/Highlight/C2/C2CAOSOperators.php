<?php
/** @noinspection PhpUnused */

/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\C2;

use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * Valid operators for C2 CAOS
 */
class C2CAOSOperators implements HasTokens {

    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
            'eq',
            'gt',
            'lt',
            'ne',
            'ge',
            'le',
            'bt',
            'bf',
        );
    }
}

/// @endcond

