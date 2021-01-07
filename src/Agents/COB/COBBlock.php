<?php

namespace C2ePhp\Agents\COB;

use Exception;

/*
 * @relates COBBlock
 * @name Cob Block Types
 */
/** Agent Block - 'agnt' */
define('COB_BLOCK_AGENT', 'agnt');
/** File Block - 'file' */
define('COB_BLOCK_FILE', 'file');
/** Author Block - 'auth' */
define('COB_BLOCK_AUTHOR', 'auth');


/**
 * Base class for parsed blocks inside a C1/C2 COB
 *
 * @package C2ePhp\Agents\COB
 */
abstract class COBBlock {
    /// @cond INTERNAL_DOCS

    /** @var string */
    private $type;


    /**
     * Instantiates a new COBBlock
     *
     * This function must be called from all COBBlock parents
     * @param string $type What type of COBBlock it is. Must be a 4-character string.
     * @throws Exception
     */
    public function __construct($type) {
        if (strlen($type) != 4) {
            throw new Exception('Invalid COB block type: '.$type);
        }
        $this->type = $type;
    }
    /// @endcond

    /**
     * Gets the type of this COB block
     * @return string One of the COB_BLOCK_* defines.
     */
    public function getType() {
        return $this->type;
    }
    /**
     * Compiles this COB Block and returns COB file as a binary string.
     *
     * @return string
     */
    public abstract function compile();
}