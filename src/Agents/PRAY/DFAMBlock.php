<?php

namespace C2ePhp\PRAY;

require_once(dirname(__FILE__) . '/TagBlock.php');
require_once(dirname(__FILE__) . '/DSEXBlock.php');

/**
 * Docking Station starter family description block
 *
 * The fields in a DFAM block are identical to those in a DSEX block
 * so this class simply extends DSEXBlock.
 */
class DFAMBlock extends DSEXBlock {
    /// @brief Instantiates a new DFAMBlock
    /**
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile that this DFAM block belongs to.
     * @param string $name The block's name.
     * @param string $content The binary data of this block. May be null.
     * @param int $flags The block's flags
     */
    public function __construct($prayFile, $name, $content, $flags) {
        parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_DFAM);

    }
}
