<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\PRAY;

include_once dirname(__FILE__) . '/pray_constants.php';

use C2ePhp\Support\IReader;
use Exception;

/**
 * Class representing a file that uses the PRAY format
 *
 * .creature, .family and .agents files all use this format.
 */
class PRAYFile {

    /// @cond INTERNAL_DOCS

    /** @var IReader|null */
    private $reader;
    /** @var PrayBlock[] array */
    private $blocks = [];
    private $parsed = false;

    /// @endcond

    /**
     * Creates a new PRAYFile
     *
     * @param IReader|null $reader The IReader to read from.
     * If reader is null then this is a user-generated PRAYFile.
     * @throws Exception
     */
    function __construct(?IReader $reader = null) {
        if ($reader instanceof IReader) {
            $this->reader = $reader;
            $this->parse();
        } else {
            $this->parsed = true; //make sure no one tries to parse it...
        }
    }

    /// @cond INTERNAL_DOCS

    /**
     * Reads the PRAYFile stored in the reader. Called automatically.
     *
     * @return void
	 * @throws Exception
     */
    private function parse() {
        if (!$this->parsed) {
            if ($this->parseHeader()) {
                /** @noinspection PhpStatementHasEmptyBodyInspection */
                while ($this->reader->hasNext() && $this->parseBlockHeader()) {
                }
                $this->parsed = true;
                //print_r($this->blocks);
			} else {
                echo 'Failed at block header: NOT A PRAY FILE';
			}
		}

	}

    /// @endcond


    /**
     * Compiles the PRAYFile.
     *
     * @return string binary string containing the PRAYFile's contents.
     */
    public function compile() {
        $compiled = 'PRAY';
        foreach ($this->blocks as $block) {
            $compiled .= $block->compile();
        }
        return $compiled;
    }

    /**
     * Adds a block to this PRAYFile.
     *
     * @param PrayBlock $block The PrayBlock to add.
     * @throws Exception
     */
    public function addBlock(PrayBlock $block) {
        foreach ($this->blocks as $checkBlock) {
            if ($checkBlock->getName() == $block->getName()) {
                throw new Exception('PRAY Files cannot contain multiple blocks with the same name');
            }
        }
        $this->blocks[] = $block;
    }

    /**
     * Gets the blocks of the specified type(s)
     *
     * If $type is a string, returns all blocks of that type. \n
     * If $type is an array, returns all blocks of the types in the
     * array. \n
     * @param string $type The type(s) of blocks to return, as the
     * PRAYBLOCK_TYPE_* constants. (see PrayBlock for block types)
     * @return PrayBlock[]
     */
    public function getBlocks($type = FALSE) { //gets all blocks or one type of block
        if (!$type) {
            return $this->blocks;
        } else {
            if (is_string($type)) {
                $type = [$type];
            }
            $blocksOfType = [];
            foreach ($this->blocks as $block) {
                if (in_array($block->getType(), $type)) {
                    $blocksOfType[] = $block;
                }
            }
            return $blocksOfType;
        }
    }

    /**
     * Gets a block with the specified name
     *
     * This is mainly used when you want to get the PHOTBlock
     * for a particular CreatureHistoryEvent, for example.
     * @param string $name
     * @return PrayBlock|null
     */
    public function getBlockByName(string $name) {
        foreach ($this->blocks as $block) {
            if ($block->getName() == $name) {
                return $block;
            }
        }
        return null;
    }

    /// @cond INTERNAL_DOCS

    /**
     *  Checks that this PRAYFile begins with PRAY. \n
     *  It's not much of a header, but it's a header nonetheless.
     */
    private function parseHeader() {
        if ($this->reader->read(4) == 'PRAY') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Reads a block from the reader
     *
     * Reads a block, then creates it using the
     * PrayBlock::MakePrayBlock method.
     * @returns true if the block was created successfully,
     * false otherwise.
     * @throws Exception
     */
    private function parseBlockHeader() {
        $blockType = $this->reader->read(4);
        if (empty($blockType)) {
            return false;
        }
        $name = trim($this->reader->read(128));
        if (empty($name)) {
            return false;
        }
        $length = $this->reader->readInt(4);
        if ($length === false) {
            return false;
        }
        $fullLength = $this->reader->readInt(4); //full means uncompressed
        if ($fullLength === false) {
            return false;
        }
        $flags = $this->reader->readInt(4);
        if ($flags === false) {
            return false;
        }

        $content = $this->reader->read($length);
        if (strlen($content) !== $length) {
            throw new Exception("Insufficient number of bytes returned from read in block: $blockType->$name");
        }
        $this->blocks[] = PrayBlock::makePrayBlock($blockType, $this, $name, $content, $flags);
        return true;
    }

    /// @endcond
}

