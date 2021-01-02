<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/PrayBlock.php');

/**
 * A block to allow simple, non-parsed binary content.
 *
 * This class is essentially un-necessary, but it's good for
 * debugging as you can use it to test PRAY compilation without
 * needing to test individual block types' compilation. \n
 * Additionally, it allows us to create blocks for types we don't
 * know of yet. \n
 * BinaryBlocks cannot be read from a PRAYFile.
 */
class BinaryBlock extends PrayBlock {
    /// @cond INTERNAL_DOCS

    private $binaryData;
    /// @endcond

    /**
     * Instantiate a BinaryBlock
     *
     * @param string $type The four-character code for the block.
     * @param string $name The block's name.
     * @param string $content The content of the block as a binary string.
     * @throws Exception
     */
    public function __construct($type, $name, $content) {
        parent::__construct(null, $name, '', 0, $type);
        $this->binaryData = $content;
    }

    /**
     * Compile the BinaryBlock
     *
     * @return string compiled BinaryBlock as a binary string.
     */
    public function compile() {
        return $this->encodeBlockHeader(strlen($this->binaryData)).$this->binaryData;
    }

    protected function compileBlockData() {
        return $this->compile();
    }

    protected function decompileBlockData() {

    }
}

