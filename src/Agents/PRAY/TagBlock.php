<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\PRAY;

use C2ePhp\Support\StringReader;
use Exception;

/**
 * Base block class for working with tag-type blocks
 *
 * This includes (but may not be limited to) agents
 * (AGNTBlock,DSAGBlock), creatures (EXPCBlock,DSEXBlock) and starter
 * families (SFAMBlock,DFAMBlock) \n
 * This contains the majority of the meat of the tag block functions,
 * including compilation and de-compilation and maintaining a
 * name->value tag array. \n
 * Subclasses of tag block are good for getting data in a more
 * programmer-friendly way and for accessing data in the PRAYFile
 * based on data within the TagBlock. For example, AGNTBlock can
 * return a SpriteFrame for the image displayed on the Creator when
 * that agent is selected.
 */
abstract class TagBlock extends PrayBlock {
    /// @cond INTERNAL_DOCS

    /** @var string[]|int[] */
    private $tags;

    /**
     * Creates a new TagBlock
     *
     * This should be called by all subclasses from their constructors.
     * @param PRAYFile|PrayBlock[] $prayFile The prayFile this block is contained in, or for TagBlocks being created from scratch, the initial tags array. Can be null.
     * @param string $name The name of the block. Cannot be null.
     * @param string $content The binary data this block contains. Can be null.
     * @param int $flags The flags relating to this block. Should be zero or real flags.
     * @param string $type The block's 4-character type. Must be defined.
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags, $type) {
        parent::__construct($prayFile, $name, $content, $flags, $type);
        if (is_array($prayFile)) {
            $this->tags = $prayFile;
        }
    }
    /// @endcond

    /**
     * Gets the tag with the given name
     *
     * Returns the tag's value as a string, or
     * nothing if the tag doesn't exist.
     * @param string $key
     * @return string|int
     */
    public function getTag($key) {
        $this->ensureDecompiled();
        foreach ($this->tags as $tk => $tv) {
            if ($key == $tk) {
                return $tv;
            }
        }
        return NULL;
    }

    /**
     * Gets all the tags from this block as an array of tags.
     *
     * This is mainly useful for people writing subclasses of TagBlock.
     * If you have to write code that uses GetTags in your application,
     * please file a bug report!
     * @return string[]|int[]
     */
    public function getTags() {
        $this->ensureDecompiled();
        return $this->tags;
    }

    /**
     * Sets a tag
     *
     * Sets the tag with the given value, overwriting or creating the tag as needed.
     * Generally setters ought to be used in subclasses of TagBlock.
     * @param string $tag Name of the tag to set
     * @param string|int $value The value to set the tag to
     */
    public function setTag($tag, $value) {
        $this->ensureDecompiled();
        $this->tags[$tag] = $value;
    }
    /// @cond INTERNAL_DOCS

    /**
     * Compiles the block and returns a string
     *
     * This is called by the Compile method in PrayBlock.
     * You shouldn't have cause to use it in any of your code,
     * except for debugging purposes.
     */
    protected function compileBlockData() {
        $compiled = '';
        $ints = array();
        $strings = array();
        foreach ($this->tags as $key => $value) {
            if (is_int($value)) {
                $ints[$key] = $value;
            } else {
                $strings[$key] = $value;
            }
        }
        $compiled .= pack('V', sizeof($ints));
        foreach ($ints as $key => $value) {
            $compiled .= pack('V', strlen($key));
            $compiled .= $key;
            $compiled .= pack('V', $value);
        }
        $compiled .= pack('V', sizeof($strings));
        foreach ($strings as $key => $value) {
            $compiled .= pack('V', strlen($key));
            $compiled .= $key;
            $compiled .= pack('V', strlen($value));
            $compiled .= $value;
        }
        return $compiled;
    }

    /**
     * Decompiles the block. Called by EnsureDecompiled.
     *
     * @throws Exception
     * @see PrayBlock::decompileBlockData()
     */
    protected function decompileBlockData() {
        //use getData because it decompresses if necessary.
        $blockReader = new StringReader($this->getData());

        $numInts = $blockReader->readInt(4);
        for ($i = 0; $i < $numInts; $i++) {
            $nameLength = $blockReader->readInt(4);
            $name = $blockReader->read($nameLength);
            $int = $blockReader->readInt(4);
            $this->tags[$name] = $int;
        }


        $numStrings = $blockReader->readInt(4);
        for ($i = 0; $i < $numStrings; $i++) {
            $nameLength = $blockReader->readInt(4);
            $name = $blockReader->read($nameLength);
            $stringLength = $blockReader->readInt(4);
            $string = $blockReader->read($stringLength);
            $this->tags[$name] = $string;
        }
    }
    /// @endcond
}
