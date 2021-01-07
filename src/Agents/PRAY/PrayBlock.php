<?php /** @noinspection PhpUnused */

namespace C2ePhp\PRAY;


use Exception;

include_once dirname(__FILE__) . './pray_constants.php';

/**
 * Abstract class to represent PRAY blocks
 *
 * @package C2ePhp\PRAY
 */
abstract class PrayBlock {
    /// @cond INTERNAL_DOCS

    /** @var PRAYFile|null  */
    private $prayFile;
    /** @var string  */
    private $content;
    /** @var string  */
    private $name;
    /** @var string  */
    private $type;
    /** @var bool */
    private $decompiled;
    /** @var string */
    private $rawContents;
    /** @var int */
    private $flags;

    /// @endcond
    //
    /// @cond INTERNAL_DOCS

    /**
     * Constructs a new PrayBlock, setting the PrayBlock's name, content, flags, and type.
     *
     * @param PRAYFile $prayFile If constructing by reading a PRAY file, is a PRAYFile object. Otherwise is allowed to be anything (it's assumed the subclasses take care of it)
     * @param string   $name     The name of the block. Must be unique within the PRAYFile. This will be checked by the PRAYFile.
     * @param string   $content  If constructing the PrayBlock by reading a PRAY file, must be a binary string. Otherwise, should be null (but doesn't have to be).
     * @param int      $flags    A 1-byte int containing the flags this PrayBlock has set
     * @param string   $type     The type of the PrayBlock as a PRAY_BLOCK_* constant. Must be a four-character string.
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags, $type) {
        if (strlen($type) != 4) {
            throw new Exception('Invalid PRAY block type: '.$type);
        }
        if ($prayFile instanceof PRAYFile) {
            $this->prayFile = $prayFile;
            $this->decompiled = false;
        } else {
            $this->prayFile = null;
            $this->decompiled = true;
        }
        $this->name = $name;
        $this->rawContents = $content;
        $this->content = $content;
        $this->flags = $flags;
        $this->type = $type;
    }

    /**
     * Encodes the block header for attaching to the front of the block binary data.
     *
     * @param int $length length of the data that will be written to the block
     * @param int $uncompressedLength length of the data when uncompressed, etc.
     * @return string
     */
    protected function encodeBlockHeader($length, $uncompressedLength = false) {
        $compiled = $this->getType();
        $compiled .= substr($this->getName(), 0, 128);
        $len = 128-strlen($this->getName());

        for ($i = 0; $i < $len; $i++) {
            $compiled .= pack('x');
        }
        if ($uncompressedLength === false) {
            $uncompressedLength = $length;
        }
        $compiled .= pack('VVV', $length, $uncompressedLength, $this->flags);
        return $compiled;
    }
    /// @endcond

    /// @cond INTERNAL_DOCS

    /**
     * Performs flag functions, e.g. compression, just before a compile is done.
     *
     * Called automatically during Compile
     * @param string $data the data to perform the function on
     * @return boolean|string data, having been transformed.
     */
    protected function performFlagOperations($data) {
        if ($this->isFlagSet(PRAY_FLAG_ZLIB_COMPRESSED)) {
            $data = gzcompress($data);
        }
        return $data;
    }

    /// @endcond

    /**
     * Gets the PRAY block's name
     *
     * @return string PRAY block's name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the PRAY block's binary data if the PRAYBlock is decompiled.
     *
     * It will decompress automatically if necessary, then unset the compressed flag.
     * TODO: I'm not 100% sure I should keep this public...
     * @return string the PRAY block's binary data.
     * @throws Exception
     */
    public function getData() {
        if ($this->decompiled) {
            throw new Exception('Can\'t get data on a decompiled PRAYBlock. It must be compiled first');
        }
        if ($this->isFlagSet(PRAY_FLAG_ZLIB_COMPRESSED)) {
            $this->content = gzuncompress($this->content);
            $this->setFlagsOff(PRAY_FLAG_ZLIB_COMPRESSED);
        }
        return $this->content;
    }

    /**
     * Gets the initial content for this pray block
     * @return string
     */
    function getRawContent() {
        return $this->rawContents;
    }

    /**
     * Gets the type of PrayBlock this is.
     *
     * Gives the type as one of the PRAY_BLOCK_* constants - a
     * four-character string, all in caps. These are defined above.
     * @return string One of the PRAY_BLOCK_* constants
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns the flag bitfield used to determine flags.
     *
     * Prefer using IsFlagSet. (This may be deprecated/removed in future releases)
     * Least Significant Bit determines whether the block is compressed (1) or not.
     * All other bits are 0 in all c2e-compatible PRAY blocks.
     * @return int 0-255
     */
    public function getFlags() {
        return $this->flags;
    }

    /**
     * Tells you whether $flag is set on this PRAY block
     *
     * @param int $flag the bitfield to compare $flags to. As such can be multiple flags OR'd together.
     * @return boolean or false.
     */
    public function isFlagSet($flag) {
        return (($this->flags & $flag) === $flag);
    }

    /// @cond INTERNAL_DOCS

    /**
     * Gets the length of this block's content.
     * Only useful on blocks that came from a PRAYFile.
     * @returns int corresponding to the length of this block's binary data.
     */
    protected function getLength() {
        return strlen($this->content);
    }

    /**
     * Sets the data for this object
     *
     * Used only by the CreatureHistoryBlock class to archive/de-archive data.
     * Should ONLY be used to transform data in a way not specified by flags just before decompiling proper.
     * @param string $data
     */
    protected function setData($data) {
        $this->content = $data;
    }

    /**
     * Returns the PRAYFile this block belongs to. Only applies to PrayBlocks created with a PRAYFile.
     *
     * @returns PRAYFile
     */
    protected function getPrayFile() {
        return $this->prayFile;
    }

    /**
     * Sets flags
     *
     * @param int $flags a bitfield representing the flags to set on.
     */
    protected function setFlagsOn($flags) {
        $this->flags = $this->flags | $flags;
    }

    /**
     * Unsets flags
     *
     * @param int $flags a bitfield representing the flags to set off.
     */
    protected function setFlagsOff($flags) {
        $this->flags = $this->flags & ~$flags;

    }

    /**
     * Makes sure that the PrayBlock is decompiled.
     * Call this function at the beginning of every function that returns data in superclasses so that data decoding is automatic.
     */
    protected function ensureDecompiled() {
        if (!$this->decompiled) {
            $this->decompileBlockData();
            $this->decompiled = true;
        }
    }

    /// @endcond
    // Started above GetLength.

    /**
     * Compile this PrayBlock
     *
     * Compiles the PrayBlock's data if necessary, then adds the
     * header and returns the binary pray block. \n
     * This function is mainly intended for use by PRAYFiles.
     */
    public function compile() {
        if ($this->decompiled) {
            $data = $this->compileBlockData();
            $ucLength = strlen($data);
            $data = $this->performFlagOperations($data);
            $compiled = $this->encodeBlockHeader(strlen($data), $ucLength);
            $compiled .= $data;
            $this->content = $data;
            $this->decompiled = false;
            return $compiled;
        } else {
            $data = $this->performFlagOperations($this->content);
            $compiled  = $this->encodeBlockHeader(strlen($this->content), strlen($data));
            $compiled .= $this->content;
            return $compiled;
        }
    }

    /// @cond INTERNAL_DOCS

    /// @brief Compiles the block data
    /**
     * return The compiled block data as a string.
     */
    protected abstract function compileBlockData();

    /**
     * Decompiles the block data
     *
     * Must be implemented in subclasses!
     * This is used to read data from the PRAYFile
     * and turn it into member variables. \n
     * Called automatically by EnsureDecompiled
     *
     */
    protected abstract function decompileBlockData();

    /**
     * Creates PrayBlock objects of the correct type.
     *
     * For developer use. Called by
     * @param string $blockType The type of PRAYBlock, as one of the Block Types defines.
     * @param PRAYFile $prayFile The PRAYFile object that the PRAYBlock is a child of. This is used to allow blocks to access to each other.
     * @param string $name The name of the PRAYBlock
     * @param string $content The binary content of the PRAYBlock, uncompressed if necessary.
     * @param int $flags The flags given to this PRAYBlock as an integer.
     *   return An object that is an instance of a subclass of PrayBlock.
     * @return AGNTBlock|CREABlock|DFAMBlock|DSAGBlock|DSEXBlock|EGGSBlock|EXPCBlock|FILEBlock|GENEBlock|GLSTBlock|LIVEBlock|PHOTBlock|SFAMBlock|null
     * @throws Exception
     */
    public static function makePrayBlock($blockType, PRAYFile $prayFile, $name, $content, $flags) {
        switch ($blockType) {
            //agents
        case PRAY_BLOCK_AGNT:
            return new AGNTBlock($prayFile, $name, $content, $flags);
        case PRAY_BLOCK_DSAG:
            return new DSAGBlock($prayFile, $name, $content, $flags);
        case PRAY_BLOCK_LIVE:
            return new LIVEBlock($prayFile, $name, $content, $flags); //sea monkeys agent files.

            //egg
        case PRAY_BLOCK_EGGS:
            return new EGGSBlock($prayFile, $name, $content, $flags);

            //starter families
        case PRAY_BLOCK_DFAM:
            return new DFAMBlock($prayFile, $name, $content, $flags);
        case PRAY_BLOCK_SFAM:
            return new SFAMBlock($prayFile, $name, $content, $flags);

            //exported creatures
        case PRAY_BLOCK_EXPC:
            return new EXPCBlock($prayFile, $name, $content, $flags);
        case PRAY_BLOCK_DSEX:
            return new DSEXBlock($prayFile, $name, $content, $flags);

        case PRAY_BLOCK_CREA:
            return new CREABlock($prayFile, $name, $content, $flags);

            //creature photos
        case PRAY_BLOCK_PHOT:
            return new PHOTBlock($prayFile, $name, $content, $flags);

            //creature history
        case PRAY_BLOCK_GLST:
            return new GLSTBlock($prayFile, $name, $content, $flags);

            //creature genetics
        case PRAY_BLOCK_GENE:
            return new GENEBlock($prayFile, $name, $content, $flags);

            //files
        case PRAY_BLOCK_FILE:
            return new FILEBlock($prayFile, $name, $content, $flags);

        default:
            return null;
        }
    }
    /// @endcond
}