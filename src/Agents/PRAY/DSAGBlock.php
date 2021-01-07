<?php /** @noinspection PhpUnused */

namespace C2ePhp\PRAY;

use C2ePhp\Sprites\C16File;
use C2ePhp\Sprites\S16File;
use C2ePhp\Sprites\SpriteFrame;
use C2ePhp\Support\StringReader;
use Exception;

require_once(dirname(__FILE__) . '/AGNTBlock.php');
require_once(dirname(__FILE__) . '/TagBlock.php');

/**
 * Docking Station agent description block
 *
 * Defines everything about an agent for docking station
 */
class DSAGBlock extends AGNTBlock {
    /**
     * Instantiates a new DSAGBlock
     *
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile that this DSAG block belongs to.
     * @param string $name The block's name.
     * @param string $content The binary data of this block. May be null.
     * @param int $flags The block's flags
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags) {
        parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_DSAG);
    }

    /**
     * Gets the label used on the web button.
     *
     * @return string
     */
    public function getWebLabel() {
        return $this->getTag('Web Label');
    }

    /**
     * Gets the URL of the web site
     *
     * @return string
     */
    public function getWebURL() {
        return $this->getTag('Web URL');
    }

    /**
     * Gets the file used for the web button's icon
     *
     * @return string
     */
    public function getWebIcon() {
        return $this->getTag('Web Icon');
    }
    /**
     * Gets the number of the sprite to use as the base for the web icon.
     *
     * @see AGNTBlock::GetAgentAnimationFirstImage()
     * @return int
     */
    public function getWebIconBase() {
        return $this->getTag('Web Icon Base');
    }

    /**
     * Gets the animation string for the web icon.
     *
     * In theory, the web button would animate.
     * In reality, DS actually only uses the first image of the
     * animation.
     * @return string
     */
    public function getWebIconAnimationString() {
        return $this->getTag('Web Icon Animation String');
    }

    /**
     * Gets the web button image as an SpriteFrame
     *
     * @return SpriteFrame a SpriteFrame that contains the web icon.
     * @throws Exception
     */
    public function getWebIconAsSpriteFrame() {
        $webIcon = $this->getWebIcon();
        if ($webIcon == '') {
            throw new Exception('No web icon!');
        }
        $webIconBase = $this->getWebIconBase();
        $webIconAnimationString = $this->getWebIconAnimationString();
        if ($webIconBase == '') {
            $webIconBase = 0;
        }
        if ($webIconAnimationString == '') {
            $webIconAnimationString = 0;
        }
        if (($position = strpos($webIconAnimationString, ' ')) !== false) {
            $webIconAnimationString = substr($webIconAnimationString, 0, $position);
        }
        $prayFile = $this->getPrayFile();
        if ($prayFile == null) {
            throw new Exception('No PRAY file to get the icon from!');
        }
        $iconBlock = $prayFile->getBlockByName($webIcon);
        if ($iconBlock->getType() != 'FILE') {
            throw new Exception('The block with the web icon\'s filename is not a file block!');
        }
        $type = strtolower(substr($webIcon, -3));
        $icon = null;
        if ($type == 'c16') {
            $icon = new C16File(new StringReader($iconBlock->getData()));
        } else if ($type == 's16') {
            $icon = new S16File(new StringReader($iconBlock->getData()));
        }
        if ($icon == null) {
            throw new Exception("For one reason or another, couldn't make a sprite file for the web icon.");
        }
        return $icon->getFrame($webIconBase+$webIconAnimationString);

    }

}
