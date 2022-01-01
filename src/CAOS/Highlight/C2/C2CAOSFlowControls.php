<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */
/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\C2;

use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * C2 CAOS flow control (doif, else, loop, etc) dictionary
 */
class C2CAOSFlowControls implements HasTokens {
    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
            'enum',
            'esee',
            'etch',
            'escn',
            'next',
            'nscn',
            'inst',
            'slow',
            'stop',
            'endm',
            'subr',
            'gsub',
            'retn',
            'reps',
            'repe',
            'loop',
            'untl',
            'ever',
            'doif',
            'else',
            'endi',
            'scrp',
            'iscr',
            'rscr'
        );
    }
}
/// @endcond

