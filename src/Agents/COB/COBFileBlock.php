<?php

namespace C2ePhp\Agents\COB;

/// @brief Defines the bock to represent a file block in a COB file.
use C2ePhp\Support\IReader;
use Exception;

class COBFileBlock extends COBBlock {

    /// @cond INTERNAL_DOCS
    //
    /** @var string */
    private $fileType;
    /** @var string */
    private $fileName;

    /** @var int */
    private $reserved;
    /** @var string */
    private $contents;

    /// @endcond

    /**
     * @brief Constructs a new COBFileBlock
     * @param string $type The file type
     * @param string $name The file name (including extension)
     * @param string $contents The contents of the file
     * @throws Exception
     */
    public function __construct($type, $name, $contents) {
        parent::__construct(COB_BLOCK_FILE);
        $this->fileType = $type;
        $this->fileName = $name;
        $this->contents = $contents;
    }

    /**
     * Add the reserved data associated with this file block
     * @param int[] $reserved The reserved data
     */
    public function addReserved($reserved) {
        $this->reserved = $reserved;
    }

    /**
     * Supposedly compiles the block into binary. Throws an error to say it's not implemented.
     *
     * @return string
     * @throws Exception
     */
    public function compile() {
        // TODO: implement
        throw new Exception("COBAgentBlock::Compile not implemented");
    }

    /**
     * Get the name of the file
     */
    public function getName() {
        return $this->fileName;
    }

    /**
     * Get the file's type
     *
     * @return string 'sprite' or 'sound' - i.e. one of the
     * COB_DEPENDENCY_* constants in COBAgentBlock
     */
    public function getFileType() {
        return $this->fileType;
    }

    /**
     * Get the contents of the file.
     *
     * @return string
     */
    public function getContents() {
        return $this->contents;
    }

    /**
     * Get the reserved data
     *
     * Reserved data was never officially used.
     * @return int A 4-byte integer.
     */
    public function getReserved() {
        return $this->reserved;
    }

    /// @cond INTERNAL_DOCS

    /**
     * Creates a new COBFileBlock from an IReader
     *
     * @param IReader $reader The reader the data's coming from
     * @return COBFileBlock
     * @throws Exception
     */
    public static function createFromReader(IReader $reader) {
        $type = ($reader->readInt(2) == 0) ? 'sprite' : 'sound';
        $reserved = $reader->readInt(4);
        $size = $reader->readInt(4);
        $fileName = $reader->readCString();
        $contents = $reader->read($size);
        $block = new COBFileBlock($type, $fileName, $contents);
        $block->addReserved($reserved);
        return $block;
    }
    /// @endcond
}
