<?php
namespace C2ePhp\CAOS\Highlight\DS;
use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/** @noinspection PhpUnused */
/// @cond INTERNAL_DOCS

/**
 * DS CAOS built-in variables dictionary
 *
 * OVxx, VAxx and MVxx are handled by the CAOS highlighter internally
 */
class DSCAOSVariables implements HasTokens {

    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
            //agents
            'abba',
            'carr',
            'cata',
            'clac',
            'fltx',
            'flty',
            'fmly',
            'from',
            'gnus',
            'held',
            'hght',
            'iitt',
            'imsk',
            'mows',
            'mthx',
            'mthy',
            'null',
            'ownr',
            'pnts',
            'posb',
            'posl',
            'posr',
            'post',
            'posx',
            'posy',
            'spcs',
            'wdth',
            '_it_',

            //camera
            'cmrx',
            'cmry',
            'wndb',
            'wndh',
            'wndl',
            'wndr',
            'wndt',
            'wndw',

            //cd player
            '_cd_ frqh',
            '_cd_ frql',
            '_cd_ frqm',

            //compound
            'npgs',

            //creatures
            'attn',
            'bvar',
            'byit',
            'cage',
            'decn',
            'dftx',
            'dfty',
            'drv!',
            'expr',
            'hhld',
            'orgn',
            'tage',
            'uftx',
            'ufty',

            //debug
            'code',
            'codf',
            'codg',
            'codp',
            'cods',
            'paws',
            'tack',
            'unid',

            //files
            'innf',
            'inni',
            'innl',
            'inok',

            //input
            'hotp',
            'hots',
            'mopx',
            'mopy',
            'movx',
            'movy',
            'down',
            'emid',
            'left',
            'maph',
            'mapw',
            'mloc',
            'rght',
            '_up_',

            //motion
            'fall',
            'movs',
            'velx',
            'vely',
            'wall',

            //ports
            'prt: itot',
            'prt: otot',

            //resources

            //scripts

            //time
            'date',
            'dayt',
            'etik',
            'mont',
            'msec',
            'pace',
            'race',
            'rtim',
            'sean',
            'time',
            'wtik',
            'year',

            //variables
            'gnam',
            'modu',
            'eame',
            'name',
            'game',
            'ufos',
            'vmjr',
            'vmnr',
            '_p1_',
            '_p2_',
            'null',

            //vehicles
            'cabb',
            'cabl',
            'cabr',
            'cabt',

            //world
            'nwld',
            'wnam',
            'wuid',

            //net
            'net: erra',
            'net: host',
            'net: rawe',
            'net: user',
            'net: what'
        );
    }
}

/// @endcond


