<?php
/** \file
*	Defines the PrayBlock abstract superclass, as well as the MakePrayBlock PrayBlock factory.
*/
require_once(dirname(__FILE__).'/AGNTBlock.php');
require_once(dirname(__FILE__).'/CREABlock.php');
require_once(dirname(__FILE__).'/DFAMBlock.php');
require_once(dirname(__FILE__).'/DSAGBlock.php');
require_once(dirname(__FILE__).'/DSEXBlock.php');
require_once(dirname(__FILE__).'/EGGSBlock.php');
require_once(dirname(__FILE__).'/EXPCBlock.php');
require_once(dirname(__FILE__).'/FILEBlock.php');
require_once(dirname(__FILE__).'/GENEBlock.php');
require_once(dirname(__FILE__).'/GLSTBlock.php');
require_once(dirname(__FILE__).'/LIVEBlock.php');
require_once(dirname(__FILE__).'/PHOTBlock.php');
require_once(dirname(__FILE__).'/SFAMBlock.php');

/** \name Block Types
	* Constants for the various PRAY block types
	*/
	//@{
	/**\brief Value: 'AGNT' */
	define('PRAY_BLOCK_AGNT','AGNT'); 
	/**\brief Value: 'CREA' */
	define('PRAY_BLOCK_CREA','CREA');
	/**\brief Value: 'DFAM' */
	define('PRAY_BLOCK_DFAM','DFAM');
	/**\brief Value: 'DSAG' */
	define('PRAY_BLOCK_DSAG','DSAG');
	/**\brief Value: 'DSEX' */
	define('PRAY_BLOCK_DSEX','DSEX');
	/**\brief Value: 'EGG' */
	define('PRAY_BLOCK_EGGS' ,'EGGS' );
	/**\brief Value: 'EXPC' */
	define('PRAY_BLOCK_EXPC','EXPC');
	/**\brief Value: 'FILE' */
	define('PRAY_BLOCK_FILE','FILE');
	/**\brief Value: 'GENE' */
	define('PRAY_BLOCK_GENE','GENE');
	/**\brief Value: 'GLST' */
	define('PRAY_BLOCK_GLST','GLST');
	/**\brief Value: 'LIVE' */
	define('PRAY_BLOCK_LIVE','LIVE');
	/**\brief Value: 'PHOT' */
	define('PRAY_BLOCK_PHOT','PHOT');
	/**\brief Value: 'SFAM' */
	define('PRAY_BLOCK_SFAM','SFAM');
	//@}
	/** \name Flags
	* Flags used to specify how the block's data is stored.
	*/
	//@{
	/**\brief Value: 1*/
	///Whether or not the block is zLib compressed.
	define('PRAY_FLAG_ZLIB_COMPRESSED',1);
	//@}

/**\brief Abstract class to represent PRAY blocks*/
/**
* This class represents a PRAY block
*/
abstract class PrayBlock {
	private $prayfile;
	private $content;
	private $name;
	private $type;
	private $decompiled;

/**\brief PrayBlock constructor*/
/**
*	Constructs a new PrayBlock, setting the PrayBlock's name, content, flags, and type.
*	\param $prayfile	If constructing by reading a PRAY file, is a PRAYFile object. Otherwise is allowed to be anything (it's assumed the subclasses take care of it)
*	\param $name		The name of the block. Must be unique within the PRAYFile. This will be checked by the PRAYFile.
*	\param $content		If constructing the PrayBlock by reading a PRAY file, must be a binary string. Otherwise, should be null (but doesn't have to be).
*	\param $flags		A 1-byte integer containing the flags this PrayBlock has set
**	\param $type		The type of the PrayBlock as a PRAY_BLOCK_* constant	
*/
	public function PrayBlock($prayfile,$name,$content,$flags,$type) {
		if($prayfile instanceof PRAYFile) {
			$this->prayfile = $prayfile;
			$this->decompiled = false;
		} else {
			$this->prayfile = null;
			$this->decompiled = true;
		}
		$this->name = $name;
		$this->content = $content;
		$this->flags = $flags;
		$this->type = $type;
	}
	/** Encodes the block header for attaching to the front of the block binary data.
	 * 
	 * \param $length length of the data that will be written to the block
	 * \param $uncompressedlength length of the data when uncompressed, etc.
	 */
	protected function EncodeBlockHeader($length,$uncompressedlength=false) {
		$compiled = $this->GetType();
		$compiled .= substr($this->GetName(),0,128);
		$len = 128 - strlen($this->GetName());
		fwrite(STDERR, $len);
		for($i=0;$i<$len;$i++) {
			$compiled .= pack('x');
		}
		if($uncompressedlength === false) {
			$uncompressedlength = $length;
		}
		$compiled .= pack('VVV',$length,$uncompressedlength,$this->flags);
		return $compiled;
	}
	/** Performs flag functions, e.g. compression, just before a compile is done.
	 * Called automatically.
	 * \param $data the data to perform the function on
	 * \return the data, having been transformed.
	 */
	protected function PerformFlagOperations($data) {
		if($this->IsFlagSet(PRAY_FLAG_ZLIB_COMPRESSED)) {
			$data = gzcompress($data);
		}
		return $data;
	}
	/** Gets the PRAY block's name
	 * \return the PRAY block's name
	 */
	public function GetName() {
		return $this->name;
	}
	/** Gets the PRAY block's binary data if the PRAYBlock is decompiled.
	 * It will decompress automatically if necessary, then unset the compressed flag.
	 * \return the PRAY block's binary data.
	 */
	public function GetData() {
		if($this->decompiled) {
			throw new Exception('Can\'t get data on a decompiled PRAYBlock. It must be compiled first');
			return;
		}
		if($this->IsFlagSet(PRAY_FLAG_ZLIB_COMPRESSED)) {
			$this->content = gzuncompress($this->content);
			$this->SetFlagsOff(PRAY_FLAG_ZLIB_COMPRESSED);
		}
		return $this->content;
	}
	/** Gives the type as one of the PRAY_BLOCK_* constants
	 * \return One of the PRAY_BLOCK_* constants
	 */
	public function GetType() {
		return $this->type;
	}
	/** Returns the flag bitfield used to determine flags. Prefer using IsFlagSet
	 * \return an integer that will either be 0 or 1 with c2e-compatible PRAY blocks.
	 */
	public function GetFlags() {
		return $this->flags;
	}
	/** Tells you whether $flag is set on this PRAY block
	 * \param $flag the bitfield to compare $flags to. As such can be multiple flags OR'd together.
	 * \return true or false.
	 */
	public function IsFlagSet($flag) {
		return (($this->flags & $flag) === $flag);
	}
	/** Gets the length of this block's content. Only useful on blocks that came from a PRAYFile.
	 * \returns an integer corresponding to the length of this block's binary data.
	 */
	protected function GetLength() {
		return strlen($this->content);
	}
	/** Sets the data for this object
	 * Used only by the CreatureHistoryBlock class to archive/de-archive data.
	 * Should ONLY be used to transform data in a way not specified by flags just before decompiling proper.
	 */
	protected function SetData($data) {
		$this->content = $data;
	}
	/** Returns the PRAYFile this block belongs to. Only applies to PrayBlocks created with a PRAYFile.
	 * \returns a PRAYFile object.
	 */
	protected function GetPrayFile() {
		return $this->prayfile;
	}
	/** Sets flags
	 * \param $flags a bitfield representing the flags to set on.
	 */
	protected function SetFlagsOn($flags) {
		$this->flags = $this->flags | $flags;
	}
	/** Unsets flags
	 * \param $flags a bitfield representing the flags to set off.
	 */
	protected function SetFlagsOff($flags) {
		$this->flags = $this->flags & ~$flags;
				
	}
	/** Makes sure that the PrayBlock is decompiled.
	 * Call this function at the beginning of every function that returns data in superclasses so that data decoding is automatic.
	 */
	protected function EnsureDecompiled() {
		if($this->decompiled) {
			return;
		} else {
			$this->DecompileBlockData();
		}
	}
	/** Compile this PrayBlock
	 * Compiles the PrayBlock's data if necessary, then adds the header and returns the binary pray block.
	 * This function is mainly intended for use by PRAYFiles. 
	 */
	public function Compile() {
		if($this->decompiled) { 
			$data = $this->CompileBlockData();
			$uclength = strlen($data);
			$data = $this->PerformFlagFunctions($data);
			$compile = $this->EncodeBlockHeader(strlen($data),$uclength);
			$compiled .= $data;
			$this->content = $data;
			$this->decompiled = false;
			return $compiled; 
		} else {
			$data = $this->PerformFlagFunctions($this->content);
			$compiled  = $this->EncodeBlockHeader(strlen($this->content),strlen($data));
			$compiled .= $this->content;
			return $compiled;
		}
	}
	/**\brief Compiles the block data
	 * \return The compiled block data as a string.
	 */
	protected abstract function CompileBlockData();
	/**\brief Decompiles the block data
	 */
	protected abstract function DecompileBlockData();
	/**\brief Creates PrayBlock objects of the correct type.
	* 	\param $blocktype	The type of PRAYBlock, as one of the Block Types defines.
	*	\param $prayfile	The PRAYFile object that the PRAYBlock is a child of. This is used to allow blocks to access to each other.
	*	\param $name		The name of the PRAYBlock
	*	\param $content		The binary content of the PRAYBlock, uncompressed if necessary.
	*	\param $flags		The flags given to this PRAYBlock as an integer.
	*	\return An object that is an instance of a subclass of PrayBlock.
	*/
	public static function MakePrayBlock($blocktype,PRAYFile $prayfile,$name,$content,$flags) {
		switch($blocktype) {
			//agents
			case PRAY_BLOCK_AGNT:
				return new AGNTBlock($prayfile,$name,$content,$flags);
			case PRAY_BLOCK_DSAG:
				return new DSAGBlock($prayfile,$name,$content,$flags);
			case PRAY_BLOCK_LIVE:
				return new LIVEBlock($prayfile,$name,$content,$flags); //sea monkeys agent files.

			//egg
			case PRAY_BLOCK_EGGS:
				return new EGGSBlock($prayfile,$name,$content,$flags);

			//starter families
			case PRAY_BLOCK_DFAM:
				return new DFAMBlock($prayfile,$name,$content,$flags);
			case PRAY_BLOCK_SFAM:
				return new SFAMBlock($prayfile,$name,$content,$flags);
		
			//exported creatures
			case PRAY_BLOCK_EXPC:
				return new EXPCBlock($prayfile,$name,$content,$flags);
			case PRAY_BLOCK_DSEX:
				return new DSEXBlock($prayfile,$name,$content,$flags);

			case PRAY_BLOCK_CREA:
				return new CREABlock($prayfile,$name,$content,$flags);

			//creature photos
			case PRAY_BLOCK_PHOT:
				return new PHOTBlock($prayfile,$name,$content,$flags);

			//creature history
			case PRAY_BLOCK_GLST:
				return new GLSTBlock($prayfile,$name,$content,$flags);

			//creature genetics
			case PRAY_BLOCK_GENE:
				return new GENEBlock($prayfile,$name,$content,$flags);
	
			//files
			case PRAY_BLOCK_FILE:
				return new FILEBlock($prayfile,$name,$content,$flags);

			default:
				return null;
		}
	}
}
?>
