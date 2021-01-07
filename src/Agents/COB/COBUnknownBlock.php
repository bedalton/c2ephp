<?php

namespace C2ePhp\Agents\COB;

use Exception;

/**
 * Simple class to allow for extra block types.
 *
 * @package C2ePhp\Agents\COB
 */
class COBUnknownBlock extends COBBlock {

    /// @cond INTERNAL_DOCS

    private $contents;

    /// @endcond

    /**
     * Creates a new COBUnknown block with the given type and contents
     * @param string $type The four-character type of the block
     * @param string $contents The contents of the block
     * @throws Exception
     */
    public function __construct($type, $contents) {
        parent::__construct($type);
        $this->contents = $contents;
    }

    /**
     * Returns the raw data back again
     * @return string
     */
    public function compile() {
        return $this->getType() . $this->contents;
    }

    /**
     * Gets the block's contents
     */
    public function getContents() {
        return $this->contents;
    }
}
