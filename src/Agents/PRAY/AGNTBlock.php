<?php /** @noinspection PhpUnused */

namespace C2ePhp\PRAY;

/// @brief Represents an AGNT block in a PRAY file, used for C3 Agent scripts and metadata.
use C2ePhp\Sprites\C16File;
use C2ePhp\Sprites\S16File;
use C2ePhp\Support\StringReader;
use Exception;

class AGNTBlock extends TagBlock {

    /**
     * Instantiate a new AGNTBlock
     *
     * Makes a new AGNTBlock. \n
     * If $prayFile is not null, all the data about this AGNTBlock
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile associated with this AGNT block.
     * It is allowed to be null.
     * @param string $name The name of this block.
     * @param string $content This block's content.
     * @param int $flags Any flags this block may have. I think this is a
     * single byte. Check http://www.creatureswiki.net/wiki/PRAY
     * @param string $blockType
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags, $blockType = PRAY_BLOCK_AGNT) {
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
     * @param string $localisation The two-letter language code (e.g. de, fr) to get. If not specified, english is used.
     * @return string
     */
    public function getAgentDescription($localisation = '') {
        if ($localisation == '') {
            return $this->getTag('Agent Description');
        } else {
            $description = $this->getTag('Agent Description-'.$localisation);
            if ($description == '') {
                $description = $this->getTag('Agent Description');
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
        return $this->getTag('Agent Type');
    }

    /**
     * Gets the number of scripts stored in this block
     *
     * @return int
     */
    public function getScriptCount() {
        return $this->getTag('Script Count');
    }

    /**
     * Gets the specified script by index in array
     *
     * Gets the first script, or if you specified which script, that one.
     * @param string $script Which script to get as an integer. The first script is script 1.
     * @return string
     * @throws Exception
     */
    public function getScript($script = 1) {
        if ($script > 0 && $script <= $this->getScriptCount()) {
            return $this->getTag('Script '.$script);
        }
        throw new Exception("Script doesn't exist!");

    }

    /**
     * Get the number of files this agent depends on.
     *
     * @return int
     */
    public function getDependencyCount() {
        return $this->getTag('Dependency Count');
    }

    /**
     * Gets the dependency specified
     *
     * @param int $dependency The number of the dependency to get. 1-based.
     * return A PrayDependency object describing the file the agent depends on
     * @return PrayDependency
     */
    public function getDependency($dependency) {
        $file = $this->getTag('Dependency '.$dependency);
        $category = $this->getTag('Dependency Category '.$dependency);
        return new PrayDependency($category, $file);
    }
    /**
     * Gets all the files this agent depends on.
     *
     * return An array of PrayDependency objects.
     */
    public function getDependencies() {
        $dependencies = array();
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
        return $this->getTag('Remove Script');
    }

    /**
     * Gets the file used for the animation of the agent on the C3 Creator/DS injector
     *
     * Only the name, not the path. Includes file extension.
     */
    public function getAgentAnimationFile() {
        return $this->getTag('Agent Animation File');
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
        return $this->getTag('Agent Animation Gallery');
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
        return $this->getTag('Animation Sprite First Image');
    }
    /**
     * Gets the animation displayed on the C3 creator/DS injector
     *
     * @return string A space-delimited set of numbers, corresponding to the indices of the sprites in the animation file.
     */
    public function getAgentAnimationString() {
        return $this->getTag('Agent Animation String');
    }
    /// @brief Gets the image used on the creator

    /**
     * Since I have no desire to bring GIF files back to the internet
     * this function will ONLY support single-frame animations.
     * If you really, REALLY, want to make a GIF, it's totally
     * possible so do it yourself.
     * You can use this function as a basis. After all, that's what
     * FOSS software is for.
     *
     * This function tries pretty hard to get the sprite out,
     * and throws an exception if it can't.
     * @throws Exception
     */
    public function getAgentAnimationAsSpriteFrame() {
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
        if ($iconBlock->getType() != 'FILE') {
            throw new Exception('The block with the animation\'s filename is not a file block!');
        }
        $type = strtolower(substr($animationFile, -3));
        $icon = null;
        if ($type == 'c16') {
            $icon = new C16File(new StringReader($iconBlock->getData()));
        } else if ($type == 's16') {
            $icon = new S16File(new StringReader($iconBlock->getData()));
        }
        if ($icon == null) {
            throw new Exception("For one reason or another, couldn't make a sprite file for the agent.");
        }
        return $icon->getFrame($animationFirstImage+$animationString);
    }
}
