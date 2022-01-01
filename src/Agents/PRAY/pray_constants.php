<?php /** @noinspection SpellCheckingInspection */
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */

include_once dirname(__FILE__) . '/../CreatureHistory/constants.php';

/**
 * @relates PrayBlock
 * @name string Block Types
 * Constants for the various PRAY block types
 */
//@{
/** Value: 'AGNT' */
define('PRAY_BLOCK_AGNT', 'AGNT');
/** Value: 'CREA' */
define('PRAY_BLOCK_CREA', 'CREA');
/** Value: 'DFAM' */
define('PRAY_BLOCK_DFAM', 'DFAM');
/** Value: 'DSAG' */
define('PRAY_BLOCK_DSAG', 'DSAG');
/** Value: 'DSEX' */
define('PRAY_BLOCK_DSEX', 'DSEX');
/** Value: 'EGG' */
define('PRAY_BLOCK_EGGS', 'EGGS');
/** Value: 'EXPC' */
define('PRAY_BLOCK_EXPC', 'EXPC');
/** Value: 'FILE' */
define('PRAY_BLOCK_FILE', 'FILE');
/** Value: 'GENE' */
define('PRAY_BLOCK_GENE', 'GENE');
/** Value: 'GLST' */
define('PRAY_BLOCK_GLST', 'GLST');
/** Value: 'LIVE' */
define('PRAY_BLOCK_LIVE', 'LIVE');
/** Value: 'PHOT' */
define('PRAY_BLOCK_PHOT', 'PHOT');
/** Value: 'SFAM' */
define('PRAY_BLOCK_SFAM', 'SFAM');

//@}


/** @name int Flags
 * Flags used to specify how the block's data is stored.
 */
///@{
/** Value: 1*/
///Whether or not the block is zLib compressed.
define('PRAY_FLAG_ZLIB_COMPRESSED', 1);
///@}
///
/* 0 = Main directory
1 = Sounds directory
2 = Images directory
3 = Genetics Directory
4 = Body Data Directory (ATT files)
5 = Overlay Directory
6 = Backgrounds Directory
7 = Catalogue Directory
8 = Bootstrap Directory (Denied)
9 = Worlds Directory (Denied)
10 = Creatures Directory
11 = Pray Files Directory (Denied) */

/**
 * @relates PrayDependency
 * PRAY Dependency Types
 *
 * All the possible types of Pray Dependency.
 */

///@{
/// Value: 1
define('PRAY_DEPENDENCY_SOUND', 1);
/// Value: 2
define('PRAY_DEPENDENCY_IMAGE', 2);
/// Value: 3
define('PRAY_DEPENDENCY_GENE', 3);
/// Value: 4
define('PRAY_DEPENDENCY_BODYDATA', 4);
/// Value: 5
define('PRAY_DEPENDENCY_OVERLAY', 5);
/// Value: 6
define('PRAY_DEPENDENCY_BACKGROUND', 6);
/// Value: 7
define('PRAY_DEPENDENCY_CATALOGUE', 7);
/// Value: 10
define('PRAY_DEPENDENCY_CREATURE', 10);
//@}