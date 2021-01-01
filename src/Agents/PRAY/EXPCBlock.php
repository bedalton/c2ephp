<?php /** @noinspection SpellCheckingInspection */

/** @noinspection PhpUnused */

namespace C2ePhp\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/TagBlock.php');
/**
 * Defines the class for EXPC (Creatures 3 exported creatures)
 *
 * @internal
 * Here's an example of the tags inside an EXPC block. \n
 * By the way, this is real data taken from a norn available for
 * download from Helen's site (http://www.creaturesvillage.com/helen/)
 * so don't blame the immature world name on me ;) \n
 * <pre>Array
 * (
 *    [Creature Age In Ticks] => 69524
 *    [Creature Life Stage] => 4
 *    [Exported At Real Time] => 998726076
 *    [Exported At World Time] => 203183
 *    [Gender] => 2
 *    [Genus] => 1
 *    [Pregnancy Status] => 0
 *    [Variant] => 4
 *    [Creature Name] => lilo
 *    [Exported From World Name] => stinky
 *    [Exported From World UID] => ship-pcnfc-evaq5-2zmqz-45yy2
 *    [Head Gallery] => A40a
 * )</pre>
 */

class EXPCBlock extends TagBlock {

    /**
     * Creates a new EXPCBlock
     *
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile that this DFAM block belongs to.
     * @param string $name The block's name.
     * @param string $content The binary data of this block. May be null.
     * @param int $flags The block's flags
     * @param string $blockType
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags, $blockType = PRAY_BLOCK_EXPC) {
        parent::__construct($prayFile, $name, $content, $flags, $blockType);
    }

    /**
     * Gets the age of the creature
     *
     * @return int The creature's age in ticks
     */
    public function getAgeInTicks() {
        return $this->getTag('Creature Age In Ticks');
    }

    /**
     * Gets the life stage of the creature
     *
     * @return int The life stage of the creature.
     */
    public function getLifeStage() {
        return $this->getTag('Creature Life Stage');
    }

    /**
     * Gets the time the creature was exported
     * @return int The time the creature was exported as a UNIX timestamp. (Seconds since 00:00 1st Jan 1970)
     */
    public function getExportUNIXTime() {
        return $this->getTag('Exported At Real Time');
    }

    /**
     * Gets the world-time when the creature was exported
     *
     * @return int The age of the world, in ticks, when the creature was exported.
     */
    public function getExportWorldTime() {
        return $this->getTag('Exported At World Time');
    }

    /*
     * Gets the gender of the creature
     *
     * 1 = male \n
     * 2 = female
     */
    public function getGender() {
        return $this->getTag('Gender');
    }

    /**
     * Gets the genus of the creature
     *
     * I.e. whether it's a norn, grendel, ettin, or geat. \n
     * Most likely in that order. I think 1 is Norn.
     */
    public function getGenus() {
        return $this->getTag('Genus');
    }

    /**
     * Gets whether the creature is pregnant
     *
     * 0 = not pregnant, 1 = pregnant.
     */
    public function getPregnancyStatus() {
        return $this->getTag('Pregnancy Status');
    }

    /**
     * Gets the variant (breed) of the creature
     *
     * @return string A single alphabetical character.
     * Unsure if it's upper or lowercase.
     */
    public function getVariant() {
        return $this->getTag('Variant');
    }

    /**
     * Gets the creature's name
     *
     * @return string
     */
    public function getCreatureName() {
        return $this->getTag('Creature Name');
    }

    /**
     * Gets the name of the world the creature was exported from.
     *
     * @return string
     */
    public function getWorldName() {
        return $this->getTag('Exported From World Name');
    }

    /**
     * Gets the UID of the world the creature was exported from.
     *
     * @return int|string
     */
    public function getWorldUID() {
        return $this->getTag('Exported From World UID');
    }

    /**
     * Gets the gallery for the creature's head sprites.
     *
     * This does not include the file extension.
     * @return string
     */
    public function getHeadGallery() {
        return $this->getTag('Head Gallery');
    }
}
