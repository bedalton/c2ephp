<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\PRAY;

use C2ePhp\Sprites\C16File;
use C2ePhp\Sprites\S16File;
use C2ePhp\Sprites\SpriteFrame;
use C2ePhp\Support\StringReader;
use Exception;

/**
 * Represents an AGNT block in a PRAY file, used for C3 Agent scripts and metadata.
 */
class AGNTBlock extends TagBlock {

	/** @var int */
	private $scriptCount;

	/**
	 * Instantiate a new AGNTBlock
	 *
	 * Makes a new AGNTBlock. \n
	 * If $prayFile is not null, all the data about this AGNTBlock
	 * will be read from the PRAYFile.
	 * @param PRAYFile|null $prayFile The PRAYFile associated with this AGNT block.
	 * It is allowed to be null.
	 * @param string $name The name of this block.
	 * @param string $content This block's content.
	 * @param int $flags Any flags this block may have. I think this is a
	 * single byte. Check http://www.creatureswiki.net/wiki/PRAY
	 * @param string $blockType
	 * @throws Exception
	 */
	public function __construct(?PRAYFile $prayFile, string $name, string $content, int $flags, string $blockType = PRAY_BLOCK_AGNT) {
		parent::__construct($prayFile, $name, $content, $flags, $blockType);
	}

	/**
	 * Gets the agent's name.
	 *
	 * Sometimes this ends in .agnt or .dsag, because some people
	 * don't do their PRAYING properly.
	 * @return string The agent name
	 */
	public function getAgentName() {
		return $this->getName();
	}

	/**
	 * Gets the agent's description in the specified language
	 *
	 * If the description doesn't exist in the language specified, falls back on English rather than returning nothing.
	 * @param string|null $localisation The two-letter language code (e.g. de, fr) to get. If not specified, english is used.
	 * @return string|null
	 */
	public function getAgentDescription(?string $localisation = NULL) {
		if (empty($localisation)) {
			return $this->getStringTag('Agent Description');
		} else {
			$description = $this->getStringTag('Agent Description-' . $localisation);
			if (empty($description)) {
				$description = $this->getStringTag('Agent Description');
			}
			return $description;
		}
	}

	/**
	 * Gets the agent's type
	 *
	 * I honestly have no idea what this is. It seems to always be 0.
	 * @return int
	 */
	public function getAgentType() {
		return $this->getIntTag('Agent Type');
	}

	/**
	 * Gets the number of scripts stored in this block
	 *
	 * @return int
	 */
	public function getScriptCount() {
		if (!isset($this->scriptCount)) {
			return $this->scriptCount = $this->getIntTag('Script Count') ?? 0;
		}
		return $this->scriptCount;
	}

	/**
	 * Gets the specified script by index in array
	 *
	 * Gets the first script, or if you specified which script, that one.
	 * @param int $script Which script to get as an integer. The first script is script 1.
	 * @return string
	 * @throws Exception
	 */
	public function getScript(int $script = 1) {
		if ($script > 0 && $script <= $this->getScriptCount()) {
			$key = 'Script ' . $script;
			if (array_key_exists($key, $this->getStringTags())) {
				return $this->getStringTag($key);
			}
		}
		throw new Exception("Script doesn't exist!");
	}

	/**
	 * Attempts to get all scripts in this agent
	 *
	 * @return array
	 */
	public function getScripts() {
		$numScripts = $this->getScriptCount();
		$out = [];
		for ($i = 1; $i <= $numScripts; $i++) {
			$out[] = $this->getStringTag('Script ' . $i);
		}
		return $out;
	}

	/**
	 * Get the number of files this agent depends on.
	 *
	 * @return int
	 */
	public function getDependencyCount() {
		return $this->getIntTag('Dependency Count');
	}

	/**
	 * Gets the dependency specified
	 *
	 * @param int $dependency The number of the dependency to get. 1-based.
	 * return A PrayDependency object describing the file the agent depends on
	 * @return PrayDependency
	 */
	public function getDependency(int $dependency) {
		$file = $this->getIntTag('Dependency ' . $dependency);
		$category = $this->getIntTag('Dependency Category ' . $dependency);
		return new PrayDependency($category, $file);
	}

	/**
	 * Gets all the files this agent depends on.
	 *
	 * return An array of PrayDependency objects.
	 */
	public function getDependencies() {
		$dependencies = [];
		for ($i = 1; $i <= $this->getDependencyCount(); $i++) {
			$dependencies[] = $this->getDependency($i);
		}
		return $dependencies;
	}

	/**
	 * Gets the script used to remove this agent.
	 *
	 * If not specified, most likely a removal script is included in the agent's scripts,
	 * however, if this isn't specified the game won't know how to remove the agent
	 */
	public function getRemoveScript() {
		return $this->getStringTag('Remove script');
	}

	/**
	 * Gets the file used for the animation of the agent on the C3 Creator/DS injector
	 *
	 * Only the name, not the path. Includes file extension.
	 */
	public function getAgentAnimationFile() {
		return $this->getStringTag('Agent Animation File');
	}

	/**
	 * Gets the filename (excluding extension) used for the
	 * animation of the agent on the C3 Creator/DS injector
	 *
	 * For all agent files I've seen, it's functionally identical
	 * to substr(AGNTBlock::GetAgentAnimationFile(),0,-4)
	 * I have no idea why anyone would use this.
	 */
	public function getAgentAnimationGallery() {
		return $this->getStringTag('Agent Animation Gallery');
	}

	/**
	 * Gets the number of the first image of the animation
	 * displayed on the C3 Creator/DS injector
	 *
	 * This is used as the basis for the animation string.
	 * For example, an AGNT block with \n
	 * 'Animation Sprite First Image' = 4 \n
	 * and 'Agent Animation String' = 0 0 3 4 \n
	 * Would show the same image as one with \n
	 * 'Animation Sprite First Image' = 0 \n
	 * and 'Agent Animation String' = 4 4 7 8 \n
	 */
	public function getAgentAnimationFirstImage() {
		return $this->getIntTag('Animation Sprite First Image');
	}

	/**
	 * Gets the animation displayed on the C3 creator/DS injector
	 *
	 * @return string A space-delimited set of numbers, corresponding to the indices of the sprites in the animation file.
	 */
	public function getAgentAnimationString() {
		return $this->getStringTag('Agent Animation String');
	}

	/**
	 * Gets the image used on the creator
	 *
	 * Since I have no desire to bring GIF files back to the internet
	 * this function will ONLY support single-frame animations.
	 * If you really, REALLY, want to make a GIF, it's totally
	 * possible so do it yourself.
	 * You can use this function as a basis. After all, that's what
	 * FOSS software is for.
	 *
	 * This function tries pretty hard to get the sprite out,
	 * and throws an exception if it can't.
	 * @return SpriteFrame|string
	 * @throws Exception
	 */
	public function getAgentAnimationAsSpriteFrame(&$fileName = NULL) {
		$animationFile = $this->getAgentAnimationFile();
		if ($animationFile == '') {
			$animationFile = $this->getAgentAnimationGallery();
			if ($animationFile == '') {
				throw new Exception('No animation file!');
			}
			$animationFile .= '.c16';
		}
		$animationFirstImage = $this->getAgentAnimationFirstImage();
		$animationString = $this->getAgentAnimationString();
		if ($animationFirstImage == '') {
			$animationFirstImage = 0;
		}
		if ($animationString == '') {
			$animationString = 0;
		}
		if (($position = strpos($animationString, ' ')) !== false) {
			$animationString = substr($animationString, 0, $position);
		}

		$prayFile = $this->getPrayFile();
		if ($prayFile == null) {
			throw new Exception('No PRAY file to get the icon from!');
		}
		$iconBlock = $prayFile->getBlockByName($animationFile);
		$frame = $animationFirstImage + $animationString;
		if ($iconBlock == null || $iconBlock->getType() != 'FILE') {
			$fileName = "$animationFile[$frame]";
			return $fileName;
		}
		$type = strtolower(substr($animationFile, -3));
		$icon = null;
		$spriteData = new StringReader($iconBlock->getData());
		if ($type == 'c16') {
			$icon = new C16File($spriteData);
		} else if ($type == 's16') {
			$icon = new S16File($spriteData);
		}
		if ($icon == null) {
			throw new Exception("For one reason or another, couldn't make a sprite file for the agent.");
		}
		$frame = $icon->getFrame($frame);
		$frame->ensureDecoded();
		unset($spriteData);
		unset($icon);
		return $frame;
	}

}
