<?php

namespace C2ePhp\PRAY;

use Exception;

/**
 * Creatures 3 starter family description block
 *
 * The tags in a SFAM block are the same as those in an
 * EXPC block so we inherit from EXPC to keep things DRY.
 *
 */
class SFAMBlock extends EXPCBlock {

    /// @brief Instantiates a new SFAMBlock
    /**
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile that this DFAM block belongs to.
     * @param string $name The block's name.
     * @param string $content The binary data of this block. May be null.
     * @param int $flags The block's flags
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags) {
        parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_SFAM);

    }
}
