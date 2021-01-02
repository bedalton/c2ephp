<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\COB;


use C2ePhp\Support\IReader;
use C2ePhp\Support\StringReader;
use Exception;


///@{
/**
 * @name string C1 format cob
 * Value: C1
 */
define('COB_FORMAT_C1', 'C1');
/**
 * @name string C2 format COB
 * Value: C2
 */
define('COB_FORMAT_C2', 'C2');
///@}

/**
 * Class that interacts with COB files (c1 and c2 formats)
 * @package C2ePhp
 */
class COB {

    /// @cond INTERNAL_DOCS

    /** @var string Creatures variant. (ie. 'C1' or 'C2') */
    private $format;
    /** @var COBBlock[] */
    private $blocks;

    /// @endcond

    /**
     * Creates a new COB file
     *
     * If you want to create a COB file from scratch, simply don't
     * pass anything to the constructor. \n\n
     * Alternatively, if you know which kind of COB file you are
     * reading, or only want to deal with a specific kind of COB
     * file, you can call the LoadC1COB and LoadC2COB functions
     * after creating a blank cob file. E.g. ($reader is a IReader) \n\n
     * $cob = new COB; \n
     * $cob->loadC1COB($reader); \n
     * This code will only parse C1 cobs.
     * @param IReader|null $reader The reader which contains the cob to read from. Can be null.
     * @throws Exception
     */
    public function __construct(IReader $reader = null) {
        if ($reader != null) {
            $this->loadCOB($reader);
        }
    }

    /// @cond INTERNAL_DOCS

    /**
     * Loads the COB from the IReader.
     * Used internally, this function is not for the general public! \n
     * This function first identifies which type of COB is in the IReader
     * Then decompresses if necessary, then calls LoadC1COB or LoadC2COB.
     * @param IReader $reader The reader to read from
     * @throws Exception
     */
    private function loadCOB(IReader $reader) {
        if ($reader->read(4) == 'cob2') {
            $reader->seek(0);
            $this->loadC2COB($reader);
        } else {
            $string = $reader->getSubString(0);
            $data = @gzuncompress($string);
            if ($data === false) {
                $reader->seek(0);
                $this->loadC1COB($reader);
            } else {
                $this->loadC2COB(new StringReader($data));
            }
        }
    }

    /**
     * @return string
     */
    public function getFormat() {
        return $this->format;
    }

    /// @endcond

    /**
     * Loads a C2 COB from the IReader given
     *
     * C2 COBs have multiple blocks, which are accessible via the
     * COB::GetBlocks function.
     * @param IReader|null $reader The IReader to load from
     * @throws Exception if file is not C2 COB
     */
    public function loadC2COB(IReader $reader) {
        $this->format = COB_FORMAT_C2;
        if ($reader->read(4) == 'cob2') {
            while ($block = $this->readBlock($reader)) {
                $this->blocks[] = $block;
            }
        } else {
            throw new Exception('Not a valid C2 COB file!');
        }
    }

    /**
     * Loads a C1 COB from the specified reader
     * C1 COBs have just one block, which is a COBAgentBlock.
     * This is accessible by calling COB::GetBlocks
     * @param IReader|null $reader the reader to load from
     * @throws Exception if file is not a valid C1 COB
     */
    public function loadC1COB(IReader $reader) {
        $this->format = COB_FORMAT_C1;
        $version = $reader->readInt(2);
        if ($version > 4) {
            throw new Exception('Invalid cob file.');
        } else {
            $this->blocks[] = COBAgentBlock::createFromReaderC1($reader);
        }
    }

    /**
     * Adds a COBBlock to this COB
     *
     * @param COBBlock $block the block to add.
     */
    public function addBlock(COBBlock $block) {
        //TODO: Check that this block works for this COB type?
        $this->blocks[] = $block;
    }

    /// @cond INTERNAL_DOCS

    /**
     * Underlying block reader used by C2 COBs
     *
     * Reads a block from the specified reader, then instantiates
     * a representative COBBlock, and returns it.
     * @param IReader $reader
     * @return COBAgentBlock|COBAuthorBlock|COBFileBlock|COBUnknownBlock|false
     * @throws Exception
     */
    private function readBlock(IReader $reader) {
        if (!($type = $reader->read(4))) {
            return false;
        }
        $size = $reader->readInt(4);
        switch ($type) {
            case 'agnt':
                //we read the entire thing so that if there are errors we can still go on with the other blocks.
                return COBAgentBlock::createFromReaderC2($reader);
            case 'auth':
                return COBAuthorBlock::createFromReader(new StringReader($reader->read($size)));
            case 'file':
                return COBFileBlock::createFromReader(new StringReader($reader->read($size)));
            default:
                //throw new Exception('Invalid block type: Probably a bug or an invalid COB file: '.$type);
                //simply ignore unknown block types, in case people add their own
                return new COBUnknownBlock($type, $reader->read($size));
        }
    }

    /// @endcond

    /**
     * Accessor method to get blocks of the given type
     * If $type is false, will return all blocks in this agent. \n
     * In a C1 COB, there is only one block and it is of the agnt
     * type.
     * @param string $type The type of block to get (agnt, auth, file). False by default.
     * @return COBBlock[] An array of COBBlocks.
     */
    public function getBlocks($type = false) {
        $blocks = array();
        foreach ($this->blocks as $block) {
            if ($type === false || $type == $block->getType()) {
                $blocks[] = $block;
            }
        }
        return $blocks;
    }

    /**
     * Compiles the COB in the given format
     * @param string $format The format of the COB. If null, assumed it's a creatures 2 COB
     * @return string A binary string containing the COB.
     * @throws Exception
     */
    public function compile($format = null) {
        if ($format == null) {
            $format = $this->getFormat();
        }
        if ($format != FORMAT_C1) {
            $format = FORMAT_C2;
        }
        switch ($format) {
            case FORMAT_C1:
                return $this->compileC1();
            case FORMAT_C2:
                return $this->compileC2();
            default:
                throw new Exception('Non-understood COB format - sorry!');
        }
    }

    /**
     * Compiles to a C1 COB. <b>Unimplemented</b>
     * @return string
     * @throws Exception
     */
    public function compileC1() {
        throw new Exception('C1 COB Compilation not yet ready.');
    }

    /**
     * Compiles a C2 COB. <b>May not actually work.</b>
     * TODO: Check accuracy
     * @return string
     */
    public function compileC2() {
        $data = 'cob2';
        foreach ($this->blocks as $block) {
            $data .= $block->compile();
        }
        return $data;
    }
}

