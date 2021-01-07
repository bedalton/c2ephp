<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */

namespace C2ePhp\CAOS\Highlight\DS;
/// @cond INTERNAL_DOCS
use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * DS CAOS flow control (doif, else, loop, etc) dictionary
 */
class DSCAOSFlowControls implements HasTokens {

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

