<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */

/// @cond INTERNAl_DOCS

namespace C2ePhp\CAOS\Highlight\C2;

use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * C2 CAOS built-in variables dictionary
 *
 * OBVx, OVxx and VAxx are handled by the CAOS highlighter internally
 */
class C2CAOSVariables implements HasTokens {

    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
            'ownr',
            'from',
            'pntr',
            '_it_',
            'cls2',
            'carr',
            'tcar',
            'wdth',
            'hght',
            'accg',
            'aero',
            'rest',
            'size',
            'rnge',
            'attr',
            'norn',
            'objp',

            '_p1_',
            '_p2_',
            'unid',
            'grav',
            'wall',
            'relx',
            'rely',
            'frzn',
            'posx',
            'posy',
            'posl',
            'posr',
            'posb',
            'post',
            'liml',
            'limr',
            'limb',
            'limt',
            'fmly',
            'gnus',
            'spcs',
            'movs',
            'actv',
            'neid',
            'velx',
            'vely',
            'temp',
            'lite',
            'radn',
            'ontr',
            'intr',
            'pres',
            'wndx',
            'wndy',
            'hrsc',
            'psrc',
            'lsrc',
            'rsrc',
            'rmno',
            'rtyp',
            'wldw',
            'wldh',
            'flor',
            'rms#',
            'sean',
            'seav',
            'tmod',
            'year',
            'eggl',
            'hatl',
            'lacb',
            'xvec',
            'yvec',
            'bump',
            'cmrx',
            'cmry',
            'thrt',
            'drv!',
            'baby',
            'dead',
            'uncs',
            'ins#',
            'dirn',
            'monk',
            'orgn',
            'camn',
            'cage',
            'paus',
            'game',
            'hour',
            'mins',
            'rndr',
            'lang',
            'lng+',

        );
    }
}

/// @endcond

