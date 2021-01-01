<?php

namespace C2ePhp\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/CreaturesArchiveBlock.php');

/**
 * Block for defining a Creature's current state.
 *
 * The binary format of this block is completely un-understood.
 */
class CREABlock extends CreaturesArchiveBlock {
    /// @brief Instantiate a new CREABlock
    /**
     * If $prayFile is not null, all the data about this CREABlock
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile associated with this CREA block.
     * It is allowed to be null.
     * @param string $name The name of this block.
     * @param string $content
     * @param int $flags Any flags this block may have
     * @throws Exception
     * @params string $content This block's content.
     */
    public function __construct($prayFile, $name, $content, $flags) {
        parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_CREA);
    }
    /// @cond INTERNAL_DOCS

    /**
     * @return string
     * @throws Exception
     * @see PrayBlock::compileBlockData()
     * TODO: undocumented.
     */
    protected function compileBlockData() {
        return $this->getData();
    }

    /**
     * @throws Exception
     * @see PrayBlock::decompileBlockData()
     */
    protected function decompileBlockData() {
        throw new Exception('I don\'t know how to decompile CREA blocks!');
    }
    /// @endcond
}
