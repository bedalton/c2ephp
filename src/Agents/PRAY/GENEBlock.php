<?php

namespace C2ePhp\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/PrayBlock.php');

/**
 * The class for a GENE block
 *
 * The contents are identical to that of a .gen file
 * This class doesn't have any useful functions yet - I don't
 * actually know how to decode genetics at the moment.
 */
class GENEBlock extends PrayBlock {

    /// @brief Creates a new GENEBlock
    /**
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile object this block belongs to. Can be null.
     * @param string $name The block's name. This is a creature's moniker with .genetics appended.
     * @param string $content The block's binary data. Used when constructing from a PrayFile
     * @param int $flags The block's flags, which apply to the binary data as-is.
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags) {
        parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_GENE);

    }

    /// @cond INTERNAL_DOCS

    /**
     * Compiles the block's data
     *
     * Does nothing really, just pipes the original binary data along
     * TODO: Make code for creation of GENE blocks.
     * @throws Exception
     */
    protected function compileBlockData() {
        return $this->getData();
    }

    /**
     * Decompiles the block's data
     * Called automatically when necessary.
     * TODO: This currently does nothing as I don't know how to decompile a gene file.
     * @throws Exception
     */
    protected function decompileBlockData() {
        throw new Exception('I don\'t know how to decompile a GENE.');
    }

    /// @endcond
}
