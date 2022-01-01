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
class TagBlock extends PrayBlock {
	/// @cond INTERNAL_DOCS

	/** @var string[] */
	private $stringTags;

	/** @var int[] */
	private $intTags;


	/**
	 * Creates a new TagBlock
	 *
	 * This should be called by all subclasses from their constructors.
	 * @param PRAYFile|array|null $prayFile The prayFile this block is contained in, or for TagBlocks being created from scratch, the initial tags array. Can be null.
	 * @param string $name The name of the block. Cannot be null.
	 * @param string|null $content The binary data this block contains. Can be null.
	 * @param int $flags The flags relating to this block. Should be zero or real flags.
	 * @param string $type The block's 4-character type. Must be defined.
	 * @throws Exception
	 */
	public function __construct($prayFile, string $name, ?string $content, int $flags, string $type) {
		parent::__construct($prayFile instanceof PRAYFile ? $prayFile : NULL, $name, $content, $flags, $type);
		if (!empty($prayFile) && is_array($prayFile)) {
			foreach ($prayFile as $tag => $value) {
				if (is_int($value)) {
					$this->intTags[$tag] = $value;
				} else if (is_string($value)) {
					$this->stringTags[$tag] = $value;
				} else if (is_object($value)) {
					throw new Exception("Cannot initialize tags array with a non-int and non-string value. Found: " . get_class($value));
				} else {
					throw new Exception("Cannot initialize tags array with a non-int and non-string value. Found: " . serialize($value));
				}
			}
		}
	}
	/// @endcond

	/**
	 * Gets the tag with the given name or nothing if it does not exist
	 * @param string $key
	 * @param mixed|null $default the default value to return if not value with tag exists
	 * @return string|int|null
	 */
	public function getTag(string $key, $default = NULL) {
		$this->ensureDecompiled();
		return $this->intTags[$key] ?? ($this->stringTags[$key] ?? $default);
	}

	/**
	 * Gets a string tag value with the given name
	 *
	 * @param string $key
	 * @param mixed|null $default the default value to return if not value with tag exists
	 * @return string|null
	 */
	public function getStringTag(string $key, ?string $default = NULL) {
		$this->ensureDecompiled();
		return $this->stringTags[$key] ?? $default;
	}

	/**
	 * Gets a string tag value with the given name
	 *
	 * @param string $key
	 * @param mixed|null $default the default value to return if not value with tag exists
	 * @return int|null
	 */
	public function getIntTag(string $key, ?int $default = NULL) {
		$this->ensureDecompiled();
		return $this->intTags[$key] ?? $default;
	}

	/**
	 * Gets all the int tags from this block as an array of tags.
	 *
	 * This is mainly useful for people writing subclasses of TagBlock.
	 * If you have to write code that uses GetTags in your application,
	 * please file a bug report!
	 * @return int[]
	 */
	public function getIntTags() {
		$this->ensureDecompiled();
		return $this->intTags;
	}

	/**
	 * Gets all the string tags from this block as an array of tags.
	 *
	 * This is mainly useful for people writing subclasses of TagBlock.
	 * If you have to write code that uses GetTags in your application,
	 * please file a bug report!
	 * @return int[]
	 */
	public function getStringTags() {
		$this->ensureDecompiled();
		return $this->stringTags;
	}

	/**
	 * Sets an int tag
	 *
	 * Sets the tag with the given value, overwriting or creating the tag as needed.
	 * Generally setters ought to be used in subclasses of TagBlock.
	 *
	 * @param string $tag Name of the tag to set
	 * @param int|null $value The value to set the tag to
	 */
	public function setIntTag(string $tag, ?int $value) {
		$this->ensureDecompiled();
		if (is_null($value)) {
			unset($this->intTags[$tag]);
		} else {
			$this->intTags[$tag] = $value;
		}
	}

	/**
	 * Sets a string tag
	 *
	 * Sets the tag with the given value, overwriting or creating the tag as needed.
	 * Generally setters ought to be used in subclasses of TagBlock.
	 *
	 * @param string $tag Name of the tag to set
	 * @param int|null $value The value to set the tag to
	 */
	public function setStringTag(string $tag, ?int $value) {
		$this->ensureDecompiled();
		if (is_null($value)) {
			unset($this->stringTags[$tag]);
		} else {
			$this->stringTags[$tag] = $value;
		}
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
		$ints = $this->intTags;
		$strings = $this->stringTags;
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
			$this->intTags[$name] = $int;
		}


		$numStrings = $blockReader->readInt(4);
		for ($i = 0; $i < $numStrings; $i++) {
			$nameLength = $blockReader->readInt(4);
			$name = $blockReader->read($nameLength);
			$stringLength = $blockReader->readInt(4);
			$string = $blockReader->read($stringLength);
			$this->stringTags[$name] = $string;
		}
	}
	/// @endcond
}
