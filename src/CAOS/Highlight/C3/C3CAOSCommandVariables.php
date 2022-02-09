<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */
/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\C3;

use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * C3 CAOS dictionary of tokens that can act like commands or variables
 */
class C3CAOSCommandVariables implements HasTokens {

    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
            'attr',
            'base',
            'bhvr',
            'clik', //I have no experience using this, but I think this is right.
            'gall',
            'hand',
            'mira',
            'paus',
            'plne',
            'pose',
            'rnge',
            'targ',
            'tick',

            //camera
            'meta',
            'trck',
            'wdow',

            //compound
            'page',
            'ptxt',

            //creatures
            'aslp',
            'dead',
            'dirn',
            'drea',
            'face',
            'ins#',
            'mind',
            'motr',
            'norn',
            'uncs',
            'zomb',

            //files

            //input
            'pure',

            //map
            'perm',

            //motion
            'accg',
            'admp',
            'aero',
            'avel',
            'elas',
            'fdmp',
            'fric',
            'fvel',
            'sdmp',
            'spin',
            'svel',
            'varc',

            //ports

            //resources

            //caos

            //sounds
            'vois',

            //time
            'buzz',
            'wpau',
            'targ',

            //vehicles
            'cabp',
            'cabv',

            //world
            'delw',
            'load'
        );
    }
}

/// @endcond

