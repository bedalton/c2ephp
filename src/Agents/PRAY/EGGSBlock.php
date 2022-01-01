<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/TagBlock.php');

/**
 * Class for egg description block which is used to provide eggs for Muco and the C3 egg layer.
 */
class EGGSBlock extends TagBlock {


	/**
	 * Instantiate a new EGGSBlock
	 *
	 * Makes a new EGGSBlock. \n
	 * If $prayFile is not null, all the data about this AGNTBlock
	 * will be read from the PRAYFile.
	 * @param PRAYFile|null $prayFile The PRAYFile associated with this AGNT block.
	 * It is allowed to be null.
	 * @param string $name The name of this block.
	 * @param string $content This block's content.
	 * @param int $flags Any flags this block may have. I think this is a
	 * single byte. Check http://www.creatureswiki.net/wiki/PRAY
	 * @throws Exception
	 */
	public function __construct(?PRAYFile $prayFile, string $name, string $content, int $flags) {
		parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_EGGS);

	}

	/**
	 * Gets the agent's type.
	 *
	 * This method is identical to that in EXPCBlock.
	 * The type seems to always be 0 for EGGSBlocks.
	 */
	public function getAgentType() {
		return $this->getTag('Agent Type');
	}

	/**
	 * Gets the dependency count
	 *
	 * The number of sprites, etc that the eggs depend on.
	 */
	public function getDependencyCount() {
		return $this->getTag('Dependency Count');
	}

	/**
	 * Gets the animation string used for the egg.
	 *
	 * It seems to always be a single pose.
	 */
	public function getEggAnimationString() {
		return $this->getTag('Egg Animation String');
	}

	/**
	 * Gets the gallery file for the female egg.
	 *
	 * At least for bruin and bengal norns, this is the
	 * same as getGlyphFile2() with the extension removed.
	 */
	public function getEggGalleryFemale() {
		return $this->getTag('Egg Gallery female');
	}

	/**
	 * Gets the gallery file for the male egg.
	 *
	 * At least for bruin and bengal norns, this is the
	 * same as GetGlyphFile1() with the extension removed.
	 */
	public function getEggGalleryMale() {
		return $this->getTag('Egg Gallery male');
	}

	/**
	 * Gets the glyph filename for the male eggs.
	 *
	 * This includes the file extension.
	 */
	public function getEggGlyphFile1() {
		return $this->getTag('Egg Glyph File');
	}

	/**
	 * Gets the glyph filename for the female eggs.
	 *
	 * This includes the file extension.
	 */
	public function getEggGlyphFile2() {
		return $this->getTag('Egg Glyph File 2');
	}

	/**
	 * Gets the genetics file for the eggs.
	 *
	 * Doesn't include the .gen file extension.
	 * @return string
	 */
	public function getGeneticsFile() {
		return $this->getTag('Genetics File');
	}

}
