<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */

namespace C2ePhp\CAOS\Highlight\DS;
/// @cond INTERNAL_DOCS
use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * DS CAOS dictionary of tokens that can act like commands or variables
 */
class DSCAOSCommandVariables implements HasTokens {

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
            'load',

            //net
            'net: line',
            'net: pass'
        );
    }
}

/// @endcond


