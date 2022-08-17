<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpUnused */
/// @cond INTERNAL_DOCS

namespace C2ePhp\CAOS\Highlight\DS;
use C2ePhp\CAOS\Highlight\TokenSets\HasTokens;

/**
 * DS CAOS commands dictionary
 */
class DSCAOSCommands implements HasTokens {

    /**
     * Returns an array of tokens.
     * @return string[]
     */
    public static function getTokens() {
        return array(
            'alph',
            'anim',
            'amms',
            'call',
            'cati',
            'cato',
            'catx',
            'core',
            'dcor',
            'disq',
            'drop',
            'dsee',
            'frat',
            'gait',
            'kill',
            'mesg writ',
            'mesg wrt+',
            'ncls',
            'nohh',
            'pcls',
            'puhl',
            'pupt',
            'rtar',
            'seee',
            'show',
            'star',
            'tcor',
            'tino',
            'tint',
            'totl',
            'touc',
            'tran',
            'ttar',
            'twin',
            'ucln',
            'visi',
            'wild',

            //brain
            'adin',
            'brn: dmpb',
            'brn: dmpd',
            'brn: dmpl',
            'brn: dmpn',
            'brn: dmpt',
            'brn: setd',
            'brn: setl',
            'brn: setn',
            'brn: sett',
            'doin',

            //camera
            'bkgd',
            'brmi',
            'cmra',
            'cmrp',
            'cmrt',
            'frsh',
            'line',
            'loft',
            'scam',
            'snap',
            'snax',
            'zoom',

            //cd player
            '_cd_ ejct',
            '_cd_ init',
            '_cd_ paws',
            '_cd_ play',
            '_cd_ shut',
            '_cd_ stop',

            //compounds
            'fcus',
            'frmt',
            'grpl',
            'grpv',
            'part',
            'pat: butt',
            'pat: cmra',
            'pat: dull',
            'pat: fixd',
            'pat: grph',
            'pat: kill',
            'pat: move',
            'pat: text',
            'pnxt',

            //creation
            'new: simp',
            'new: comp',
            'new: crag',
            'new: crea',
            'new: vhcl',
            'newc',

            //creatures
            'ages',
            'appr',
            'body',
            'boot',
            'born',
            'calg',
            'chem',
            'crea',
            'done',
            'driv',
            'forf',
            'hair',
            'injr',
            'like',
            'limb',
            'loci',
            'ltcy',
            'mate',
            'mvft',
            'nude',
            'ordr shou',
            'ordr sign',
            'ordr tact',
            'ordr writ',
            'orgf',
            'orgi',
            'plmd',
            'plmu',
            'sayn',
            'seen',
            'soul',
            'spnl',
            'step',
            'stim shou',
            'stim sign',
            'stim tact',
            'stim writ',
            'sway shou',
            'sway sign',
            'sway tact',
            'sway writ',
            'touc',
            'urge shou',
            'urge sign',
            'urge tact',
            'urge writ',
            'vocb',
            'walk',
            'wear',

            //debug
            'agnt',
            'apro',
            'bang',
            'dbg#',
            'dbg: asrt',
            'dbg: cpro',
            'dbg: flsh',
            'dbg: html',
            'dbg: outs',
            'dbg: outv',
            'dbg: paws',
            'dbg: play',
            'dbg: poll',
            'dbg: prof',
            'dbg: tack',
            'dbg: tock',
            'dbg: wtik',
            'dbga',
            'head',
            'help',
            'mann',
            'memx',

            //files
            'file glob',
            'file iclo',
            'file iope',
            'file jdel',
            'file oclo',
            'file oflu',
            'file oope',
            'fvwm',
            'outs',
            'outv',
            'outx',
            'webb',

            //genetics
            'gene clon',
            'gene cros',
            'gene kill',
            'gene load',
            'gene move',
            'gtos',
            'mtoa',
            'mtoc',

            //history
            'hist cage',
            'hist coun',
            'hist cros',
            'hist evnt',
            'hist find',
            'hist finr',
            'hist foto',
            'hist gend',
            'hist gnus',
            'hist mon1',
            'hist mon2',
            'hist mute',
            'hist name',
            'hist netu',
            'hist next',
            'hist prev',
            'hist rtim',
            'hist tage',
            'hist type',
            'hist utxt',
            'hist vari',
            'hist wipe',
            'hist wnam',
            'hist wtik',
            'hist wuid',
            'ooww',

            //input
            'clac',
            'clik',
            'imsk',
            'keyd',
            'mous',
            'tran',

            //map
            'addb',
            'addm',
            'addr',
            'altr',
            'bkds',
            'cacl',
            'calc',
            'delm',
            'delr',
            'dmap',
            'doca',
            'door',
            'emit',
            'erid',
            'gmap',
            'grap',
            'grid',
            'hirp',
            'link',
            'lorp',
            'mapd',
            'mapk',
            'prop',
            'rate',
            'rloc',
            'room',
            'rtyp',
            'torx',
            'tory',

            //motion
            'angl',
            'flto',
            'frel',
            'mvby',
            'mvsf',
            'mvto',
            'obst',
            'relx',
            'rely',
            'rotn',
            'tmvb',
            'tmvf',
            'tmvt',
            'vecx',
            'vecy',
            'velo',

            //ports
            'prt: bang',
            'prt: frma',
            'prt: from',
            'prt: inew',
            'prt: izap',
            'prt: join',
            'prt: kraf',
            'prt: name',
            'prt: onew',
            'prt: ozap',
            'prt: send',

            //resources
            'pray agti',
            'pray agts',
            'pray back',
            'pray coun',
            'pray deps',
            'pray expo',
            'pray file',
            'pray fore',
            'pray garb',
            'pray impo',
            'pray injt',
            'pray kill',
            'pray make',
            'pray next',
            'pray prev',
            'pray refr',
            'pray test',

            //caos
            'caos',
            'gids fmly',
            'gids gnus',
            'gids root',
            'inst',
            'ject',
            'lock',
            'scrx',
            'slow',
            'sorc',
            'sorq',
            'stop',
            'stpt',
            'unlk',
            'wait',

            //sounds
            'fade',
            'mclr',
            'midi',
            'mmsc',
            'mute',
            'rclr',
            'rmsc',
            'sezz',
            'sndc',
            'snde',
            'sdnl',
            'sndq',
            'stpc',
            'strk',
            'voic',
            'volm',

            //time
            'hist date',
            'hist sean',
            'hist time',
            'hist year',
            'rtif',
            'scol',
            'wolf',

            //variables
            'absv',
            'acos',
            'adds',
            'addv',
            'andv',
            'asin',
            'atan',
            'avar',
            'char',
            'cos_',
            'dele',
            'delg',
            'deln',
            'divv',
            'eamn',
            'ftoi',
            'gamn',
            'itof',
            'lowa',
            'modv',
            'mulv',
            'namn',
            'negv',
            'notv',
            'orrv',
            'rand',
            'read',
            'reaf',
            'rean',
            'reaq',
            'seta',
            'sets',
            'setv',
            'sins',
            'sin_',
            'sqrt',
            'stof',
            'stoi',
            'strl',
            'subs',
            'subv',
            'tan_',
            'type',
            'uppa',
            'vtos',

            //vehicles
            'cabn',
            'cabw',
            'dpas',
            'gpas',
            'rpas',
            'spas',
            'psed',
            'quit',
            'rgam',
            'save',
            'tntw',
            'wnti',
            'wrld',
            'wtnt',

            //netbabel
            'net: expo',
            'net: from',
            'net: head',
            'net: hear',
            'net: ruso',
            'net: stat',
            'net: ulin',
            'net: unik',
            'net: whod',
            'net: whof',
            'net: whon',
            'net: whoz',
            'net: writ',
        );
    }
}

/// @endcond

