<?php

namespace C2ePhp\Agents\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/EXPCBlock.php');
require_once(dirname(__FILE__) . '/TagBlock.php');

/**
 * Class for DSEX (DS Export) blocks
 *
 * DSEX and EXPC seem to contain identical tags, so this class
 * simple extends EXPC.
 */
class DSEXBlock extends EXPCBlock {

    /**
     * Instantiates a DSEXBlock
     *
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile|null $prayFile The PRAYFile that this DFAM block belongs to.
     * @param string $name The block's name.
     * @param string $content The binary data of this block. May be null.
     * @param int $flags The block's flags
     * @param string $blockType
     * @throws Exception
     */
    public function __construct(?PRAYFile $prayFile, string $name, string $content, int $flags, string $blockType = PRAY_BLOCK_DSEX) {
        parent::__construct($prayFile, $name, $content, $flags, $blockType);
    }
}
