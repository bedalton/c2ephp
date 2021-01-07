<?php

namespace C2ePhp\Agents\PRAY;


use Exception;

/**
 * Block to enable some kind of compatibility with Amazing Virtual Sea Monkeys agents
 *
 * This class will probably remain forever untested and unused -
 * Amazing Virtual Sea Monkeys was not a popular game, I doubt
 * many agents were created for it.
 */
class LIVEBlock extends AGNTBlock {

    /**
     * Creates a new LIVEBlock
     *
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile this LIVEBlock belongs to.
     * @param string $name The name of this block
     * @param string $content The binary data of this file block.
     * @param int $flags The block's flags. See PrayBlock.
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags) {
        parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_LIVE);

    }
}
