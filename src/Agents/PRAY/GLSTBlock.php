<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\PRAY;

use C2ePhp\CreatureHistory\CreatureHistory;
use C2ePhp\CreatureHistory\CreatureHistoryEvent;
use C2ePhp\Support\IReader;
use C2ePhp\Support\StringReader;
use Exception;

require_once(dirname(__FILE__) . '/../CreatureHistory/constants.php');
require_once(dirname(__FILE__) . '/../CreatureHistory/CreatureHistory.php');
require_once(dirname(__FILE__) . '/../CreatureHistory/CreatureHistoryEvent.php');

/**
 * PRAY Block for Creature History Data.
 *
 * @package C2ePhp\Agents\PRAY
 */
class GLSTBlock extends CreaturesArchiveBlock {

    /// @cond INTERNAL_DOCS

	/** @var CreatureHistory  */
    private $history;
	/** @var int  */
    private $format = GLST_FORMAT_UNKNOWN;

    /// @endcond

    /**
     * Creates a new GLSTBlock
     *
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile|CreatureHistory $object The PRAYFile this FILEBlock belongs to, or the
     * CreatureHistory object to store. <b>Cannot</b> be null.
     * @param string $name The name of this block. I think it's usually the
     * creature's moniker with .GLST appended.
     * @param string $content The binary data of this file block.
     * @param int $flags The block's flags. See PrayBlock.
     * @throws Exception
     */
    public function __construct($object, string $name, string $content, int $flags) {
		parent::__construct($object instanceof PRAYFile ? $object : NULL, $name, $content, $flags, PRAY_BLOCK_GLST);
        if ($object instanceof CreatureHistory) {
            $this->history = $object;
        } else if (!($object instanceof PRAYFile)) {
            throw new Exception('Couldn\'t create a GLST block. :(');
        }
    }

    /**
     * Tries to work out which game this GLSTBlock is from
     *
     * @return string
     */
    private function guessFormat() {
        //if I don't know
        if ($this->format == GLST_FORMAT_UNKNOWN) {
            $prayFile = $this->getPrayFile();
            //ask $prayFile if it exists. (look for DSEX, otherwise C3)
            if ($prayFile != null) {
                //prayFile should know
                if (sizeof($prayFile->getBlocks(PRAY_BLOCK_DSEX)) > 0) {
                    $format = GLST_FORMAT_DS;
                } else {
                    $format = GLST_FORMAT_C3;
                }
            } else {
                //history will know. (Though it could be wrong)
                $format = $this->history->guessFormat();
            }
            //cache so I don't need to ask again :)
            $this->format = $format;
        }
        return $this->format;
    }


    /**
     * Gets the CreatureHistory this block stores.
     *
     * @return CreatureHistory
     */
    public function getHistory() {
        $this->ensureDecompiled();
        return $this->history;
    }

    /**
     * Gets the name of the PHOTBlock corresponding to the event given.
     *
     * Useful for getting a photo-taken event's photo. \n
     * e.g. if $block is a GLSTBlock, $prayFile a PRAYFile, and
     * $event a CreatureHistoryEvent for an event with a photo,
     * one can use this function thusly: \n
     * <pre>$photoBlock = $prayFile->getBlockByName($block->getPHOTBlockName($event))
     * $photo = $photBlock->getS16File();</pre>
     * $photo is now an S16File ready for use.
     * @param CreatureHistoryEvent $event
     * @return string|null
     */
    public function getPhotoBlockName(CreatureHistoryEvent $event) {
        $photoName = $event->getPhotograph();
        if (empty($photoName)) {
            return null;
        }
        if ($this->format == GLST_FORMAT_DS) {
            return $photoName . '.DSEX.photo';
        } else {
            return $photoName . '.photo';
        }
    }

    /**
     * Gets a PHOTBlock corresponding to the given CreatureHistoryEvent.
     *
     * This only works in a GLSTBlock that has been read from a
     * PRAYFile - and even then it may throw exceptions. Be cautious
     * and handle errors.
     * @param CreatureHistoryEvent $event
     * @return PrayBlock|null
     * @throws Exception
     */
    public function getPhotoBlock(CreatureHistoryEvent $event) {
        if ($this->getPrayFile() instanceof PRAYFile) {
            return $this->getPrayFile()->getBlockByName($this->getPhotoBlockName($event));
        } else {
            throw new Exception('This GLSTBlock is not connected to a PRAYFile.');
        }
    }

    /// @cond INTERNAL_DOCS

    /**
     * Decompiles the GLST data into a CreatureHistory object, then stores that object.
     *
     * @return bool
     * @throws Exception
     */
    protected function decompileBlockData() {
        $reader = new StringReader($this->getData());
        $firstChar = $reader->read(1);
        if ($firstChar == chr(0x27)) { //apostrophe thing
            //ds
            $this->format = GLST_FORMAT_DS;
        } else if ($firstChar == chr(0x0C)) { //control character
            //c3
            $this->format = GLST_FORMAT_C3;
        } else {
            throw new Exception("Couldn't guess the format.");
        }
        //the first four bytes including $firstChar are probably one integer used to identify the game used.
        //We read the first one above and now we're skipping the next three.
        $reader->skip(3); // 3 nulls.
        if ($reader->readInt(4) != 1) { //Always 1, don't know why.
            throw new Exception('Either the GLST Block is corrupt or I don\'t understand what the 2nd set of 4 bytes mean.');
        }
        $moniker = $reader->read($reader->readInt(4));
        $reader->skip($reader->readInt(4)); //second moniker is always identical and never necessary.
        $name = $reader->read($reader->readInt(4));
        $gender = $reader->readInt(4);
        $genus = $reader->readInt(4); //0 for norn, 1 for grendel, 2 for ettin
        $species = $reader->readInt(4);
        $eventsLength = $reader->readInt(4);

        $this->history = new CreatureHistory($moniker, $name, $gender, $genus, $species);
        if (!isset($eventsLength)) {
            return false;
        }
        for ($i = 0; $i < $eventsLength; $i++) {
            $this->decodeEvent($reader);
        }

        //reading the footer
        $mutations = $reader->readInt(4);
        $crossovers = $reader->readInt(4);

        $this->history->setMutationsAndCrossovers($mutations, $crossovers);

        if ($this->format == GLST_FORMAT_DS) {
            $unknown1 = $reader->readInt(4);
            $warpVeteran = (($reader->readInt(4) == 1) ? 1 : 0);
            $unknown2 = $reader->read($reader->readInt(4));
            $this->history->setDSUnknowns($unknown1, $unknown2);
            $this->history->setWarpVeteran($warpVeteran);
        }
        return TRUE;
    }


    /**
     * Decodes an event. Used by DecompileBlockData.
     *
     * @param IReader $reader
     * @return bool
     */
    private function decodeEvent(IReader $reader) {
        $eventNumber = $reader->readInt(4);
        //echo 'Event '.$eventNumber."\n";
        if ($eventNumber < 18) {
            $worldTime = $reader->readInt(4);
            $creatureAge = $reader->readInt(4);
            $timestamp = $reader->readInt(4);
            $lifeStage = $reader->readInt(4);
            $moniker = $reader->read($reader->readInt(4));
            $moniker2 = $reader->read($reader->readInt(4));
            $userText = $reader->read($reader->readInt(4));
            $photograph = $reader->read($reader->readInt(4));
            $worldName = $reader->read($reader->readInt(4));
            $worldUID = $reader->read($reader->readInt(4));
            $event = new CreatureHistoryEvent(
                $eventNumber,
                $worldTime,
                $creatureAge,
                $timestamp,
                $lifeStage,
                $moniker,
                $moniker2,
                $userText,
                $photograph,
                $worldName,
                $worldUID
            );
            if ($this->format == GLST_FORMAT_DS) {
                $DSUser = $reader->read($reader->readInt(4));
                $unknown1 = $reader->readInt(4);
                $unknown2 = $reader->readInt(4);
                $event->addDSInfo($DSUser, $unknown1, $unknown2);
            }
            $this->history->addEvent($event);
            return true;
        }
        return false;
    }

    /**
     * Compiles the block into binary for PrayBlock
     *
     * @param int $format One of the GLST_FORMAT_* constants
     * @return false|string compiled block data for Compile()
     */
    protected function compileBlockData(int $format = GLST_FORMAT_UNKNOWN) {
        //if you don't know
        if ($format == GLST_FORMAT_UNKNOWN) {
            $format = $this->guessFormat();
        }
        return archive($this->history->compile($format));
    }

    /// @endcond
}
