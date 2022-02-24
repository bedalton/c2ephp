<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */

/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\C3;

use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * C3 CAOS flow control (doif, else, loop, etc) dictionary
 */
class C3CAOSFlowControls implements HasTokens {

    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(

            'doif',
            'econ',
            'elif',
            'else',
            'enum',
            'endi',
            'endm',
            'epas',
            'esee',
            'etch',
            'ever',
            'goto',
            'gsub',
            'inst',
            'iscr',
            'loop',
            'next',
            'over', //wait until current agent anim is over...sounds like a flow control to me.
            'repe',
            'reps',
            'retn',
            'rscr',
            'scrp',
            'slow',
            'subr',
            'untl',

        );
    }
}

/// @endcond

