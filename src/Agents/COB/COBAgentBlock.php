<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\COB;

use C2ePhp\Sprites\S16Frame;
use C2ePhp\Sprites\SPRFrame;
use C2ePhp\Sprites\SpriteFrame;
use C2ePhp\Support\IReader;
use Exception;

/**
 * @relates COBAgentBlock
 * @name string Dependency Types
 * The two types of dependency available to C1/C2 COBs
 */
//@{
/** Sprite dependency - 'sprite' */
define('DEPENDENCY_SPRITE', 'sprite');
/** Sound dependency - 'sound' */
define('DEPENDENCY_SOUND', 'sound');
//@}


/**
 * COB Agent Block for C1 and C2
 *
 * For Creatures 1, this block contains all the useful data in a typical COB and will be the only block.\n
 * For Creatures 2, this block contains the scripts and metadata about the actual object.
 */
class COBAgentBlock extends COBBlock {
    /// @cond INTERNAL_DOCS

    /** @var string */
    private $agentName;

    /** @var string  */
    private $agentDescription;

    /** @var int unix timestamp */
    private $lastUsageDate;

    /** @var int reuse interval in seconds */
    private $reuseInterval;

    /** @var int */
    private $quantityAvailable;

    /** @var int unix timestamp*/
    private $expiryDate;

    //'reserved' - never officially used by CL
    private $reserved1;
    private $reserved2;
    private $reserved3;

    /** @var COBDependency[] */
    private $dependencies = [];

    /** @var SpriteFrame */
    private $thumbnail;

    /** @var string */
    private $installScript;
    /** @var string */
    private $removeScript;
    /** @var string[] */
    private $eventScripts;
    /// @endcond

    /**
     * Initialises a new COBAgentBlock with the given name and description.
     *
     * As defaults can be made for everything else these are the only non-optional
     * parts of a COB file in my opinion. Even then they could just be '' if you
     * really felt like it.
     * @param string $agentName The name of the agent (as displayed in the C2 injector)
     * @param string $agentDescription The description of the agent (as displayed in the C2 injector)
     * @throws Exception
     */
    public function __construct($agentName, $agentDescription) {
        parent::__construct(COB_BLOCK_AGENT);
        $this->agentName = $agentName;
        $this->agentDescription = $agentDescription;
    }

    /**
     * Supposedly compiles the block into binary. Throws an error to say it's not implemented
     *
     * @return string
     * @throws Exception
     */
    public function compile() {
        // TODO: implement
        throw new Exception("COBAgentBlock::Compile not implemented");
    }

    /**
     * Gets the agent's name
     * @return string
     */
    public function getAgentName() {
        return $this->agentName;
    }
    /**
     * Gets the agent's description
     *
     * @return string
     */
    public function getAgentDescription() {
        return $this->agentDescription;
    }

    /**
     * @return int
     */
    public function getReuseInterval() {
        return $this->reuseInterval;
    }

    /**
     * Gets the quantity available
     * @return int
     */
    public function getQuantityAvailable() {
        return $this->quantityAvailable;
    }

    /**
     * @return int
     */
    public function getLastUsageDate() {
        return $this->lastUsageDate;
    }

    /**
     * Gets the expiry date
     * @return int
     */
    public function getExpiryDate() {
        return $this->expiryDate;
    }

    /**
     * Gets the agent's install script
     * @return string
     */
    public function getInstallScript() {
        return $this->installScript;
    }

    /**
     * Gets the agent's remove script
     *
     * @return string
     */
    public function getRemoveScript() {
        return $this->removeScript;
    }

    /**
     * Gets the number of event scripts
     *
     * @return int
     */
    public function getEventScriptCount() {
        return sizeof($this->eventScripts);
    }
    /**
     * Gets the agent's event scripts
     * @return string[] array of strings, each string is an event script
     */
    public function getEventScripts() {
        return $this->eventScripts;
    }

    /**
     * Gets an event script
     *
     * Event scripts are not necessarily in any order, so you have to work out what each script is for yourself.
     * @param int $whichScript Index of script in event scripts array
     * @return string A string containing the event script. Each line is separated by a comma typically.
     */
    public function getEventScript($whichScript) {
        return $this->eventScripts[$whichScript];
    }
    /**
     * Gets the thumbnail of this agent as would be shown in the Injector
     *
     * @return SpriteFrame SpriteFrame of the thumbnail
     */
    public function getThumbnail() {
        return $this->thumbnail;
    }

    /**
     * Gets dependencies of the given type
     *
     * If type is null, will get all dependencies.
     * @param string $type one of the COB_DEPENDENCY_* constants, defined above.
     * @return COBDependency[] An array of COBDependency objects
     */
    public function getDependencies($type = null) {
        $dependenciesToReturn = array();
        foreach ($this->dependencies as $dependency) {
            if ($type == null || $type == $dependency->getType()) {
                $dependenciesToReturn[] = $dependency;
            }
        }
        return $dependenciesToReturn;
    }

    /**
     * Gets the value of reserved1
     *
     * Reserved values weren't ever officially used by CL,
     * but someone might find them useful for something else.
     * @return int
     */
    public function getReserved1() {
        return $this->reserved1;
    }

    /**
     * Gets the value of reserved2
     *
     * Reserved values weren't ever officially used by CL,
     * but someone might find them useful for something else.
     * @return int
     */
    public function getReserved2() {
        return $this->reserved2;
    }

    /**
     * Gets the value of reserved3
     *
     * Reserved values weren't ever officially used by CL,
     * but someone might find them useful for something else.
     * @return int
     */
    public function getReserved3() {
        return $this->reserved3;
    }

    /**
     * Adds a dependency to this agent
     *
     * @param COBDependency $dependency The COBDependency to add.
     */
    public function addDependency(COBDependency $dependency) {
        if (!in_array($dependency->getName(), $this->dependencies)) {
            $this->dependencies[] = $dependency;
        }
    }

    /**
     * Sets the install script
     *
     * @param string $installScript the text of the script to add
     */
    public function setInstallScript($installScript) {
        $this->installScript = $installScript;
    }

    /**
     * Sets the remover script
     *
     * @param string $removeScript The text of the script to add
     */
    public function setRemoveScript($removeScript) {
        $this->removeScript = $removeScript;
    }

    /**
     * Adds an event script
     *
     * @param string $eventScript The text of the script to add
     */
    public function addEventScript($eventScript) {
        $this->eventScripts[] = $eventScript;
    }


    /**
     * Sets the date this agent was last injected
     * @param int $time The date this agent was last injected as a UNIX timestamp
     * @return bool
     */
    public function setLastUsageDate($time) {
        if ($time > time()) {
            return false;
        } else {
            $this->lastUsageDate = $time;
        }
        return true;
    }

    /**
     * Sets the date this agent will expire
     *
     * @param int $time The date this agent will expire as a UNIX timestamp
     */
    public function setExpiryDate($time) {
        $this->expiryDate = $time;
    }

    /**
     * Sets the quantity of the agent available
     *
     * @param int $quantity The quantity available, an integer. 0xFF means infinite.
     */
    public function setQuantityAvailable($quantity) {
        $this->quantityAvailable = $quantity;
    }
    /**
     * Sets the interval required between re-use.
     *
     * @param int $interval The interval in seconds, between re-use of this agent.
     */
    public function setReuseInterval($interval) {
        $this->reuseInterval = $interval;
    }

    /**
     * Adds the reserved variables to this agent
     *
     * These variables have no meaning to Creatures 2 and don't appear in Creatures 1.
     * They're all integers.
     * @param int $reserved1 The first reserved variable
     * @param int $reserved2 The second reserved variable
     * @param int $reserved3 The third reserved variable
     */
    public function setReserved($reserved1, $reserved2, $reserved3) {
        $this->reserved1 = $reserved1;
        $this->reserved2 = $reserved2;
        $this->reserved3 = $reserved3;
    }

    /**
     * Add the thumbnail to this agent.
     *
     * @param SpriteFrame $frame The thumbnail as a SpriteFrame
     * @throws Exception
     */
    public function setThumbnail(SpriteFrame $frame) {
        if ($this->thumbnail != null) {
            throw new Exception('Thumbnail already added');
        }
        $this->thumbnail = $frame;
    }


    /// @cond INTERNAL_DOCS

    /**
     * Adds a remover script by reading from an RCB file.
     * @param IReader $reader A StringReader or FileReader for the RCB
     * @throws Exception
     */
    public function addC1RemoveScriptFromRCB(IReader $reader) {
        if ($this->removeScript != '') {
            throw new Exception('Script already added!');
        }
        $rcb = new COB($reader);
        /** @var COBAgentBlock[] $agentBlocks */
        $agentBlocks = $rcb->getBlocks(COB_BLOCK_AGENT);
        $this->removeScript = $agentBlocks[0]->getInstallScript();
    }

    /**
     * Creates a new COBAgentBlock from an IReader.
     *
     * Reads from the current position of the IReader to fill out the data required by
     * the COBAgentBlock, then creates one and adds all the fields to it.
     * @param IReader $reader The IReader, seeked to the beginning of the contents of the agent block
     * @return COBAgentBlock
     * @throws Exception
     */
    public static function createFromReaderC2(IReader $reader) {
        $quantityAvailable = $reader->readInt(2);
        if ($quantityAvailable == 0xffff) {
            $quantityAvailable = -1;
        }
        $lastUsageDate = $reader->readInt(4);
        $reuseInterval = $reader->readInt(4);

        $expiryDay = $reader->readInt(1);
        $expiryMonth = $reader->readInt(1);
        $expiryYear = $reader->readInt(2);
        $expiryDate = mktime(0, 0, 0, $expiryMonth, $expiryDay, $expiryYear);

        $reserved = array($reader->readInt(4), $reader->readInt(4), $reader->readInt(4));

        $agentName = $reader->readCString();
        $agentDescription = $reader->readCString();

        $installScript = str_replace(',', "\n", $reader->readCString());
        $removeScript = str_replace(',', "\n", $reader->readCString());

        $numEventScripts = $reader->readInt(2);

        $eventScripts = array();

        for ($i = 0; $i < $numEventScripts; $i++) {
            $eventScripts[] = str_replace(',', "\n", $reader->readCString());
        }
        $numDependencies = $reader->readInt(2);
        $dependencies = array();

        for ($i = 0; $i < $numDependencies; $i++) {
            $type = ($reader->readInt(2) == 0) ? DEPENDENCY_SPRITE : DEPENDENCY_SOUND;
            $name = $reader->readCString();
            $dependencies[] = new COBDependency($type, $name);
        }
        $thumbWidth = $reader->readInt(2);
        $thumbHeight = $reader->readInt(2);

        $thumbnail = new S16Frame($reader, '565', $thumbWidth, $thumbHeight, $reader->getPosition());
        $reader->skip($thumbHeight*$thumbWidth*2);

        //parsing finished, onto making an AgentBlock.
        $agentBlock = new COBAgentBlock($agentName, $agentDescription);
        $agentBlock->setQuantityAvailable($quantityAvailable);
        $agentBlock->setReuseInterval($reuseInterval);
        $agentBlock->setExpiryDate($expiryDate);
        $agentBlock->setLastUsageDate($lastUsageDate);
        $agentBlock->setReserved($reserved[0], $reserved[1], $reserved[2]);
        $agentBlock->setInstallScript($installScript);
        $agentBlock->setRemoveScript($removeScript);
        foreach ($eventScripts as $eventScript) {
            $agentBlock->addEventScript($eventScript);
        }
        foreach ($dependencies as $dependency) {
            $agentBlock->addDependency($dependency);
        }
        $agentBlock->setThumbnail($thumbnail);
        return $agentBlock;

    }

    /**
     * Creates a COBAgentBlock from an IReader
     *
     * Reads from the current position of the IReader to fill out the data required by
     * the COBAgentBlock, then creates one and adds all the fields to it.
     * @param IReader $reader The IReader, seeked to the beginning of the contents of the agent block
     *
     * @return COBAgentBlock
     * @throws Exception
     */
    public static function createFromReaderC1(IReader $reader) {
        $quantityAvailable = $reader->readInt(2);
        $expires_month = $reader->readInt(4);
        $expires_day = $reader->readInt(4);
        $expires_year = $reader->readInt(4);
        $expiryDate = mktime(0, 0, 0, $expires_month, $expires_day, $expires_year);

        $numObjectScripts = $reader->readInt(2);
        $numInstallScripts = $reader->readInt(2);
        $reader->skip(4); //$quantityUsed = $reader->readInt(4)
        $objectScripts = array();
        for ($i = 0; $i < $numObjectScripts; $i++) {
            $scriptSize = $reader->readInt(1);
            if ($scriptSize == 255) {
                $scriptSize = $reader->readInt(2);
            }
            $objectScripts[$i] = $reader->read($scriptSize);
        }
        $installScripts = array();
        for ($i = 0; $i < $numInstallScripts; $i++) {
            $scriptSize = $reader->readInt(1);
            if ($scriptSize == 255) {
                $scriptSize = $reader->readInt(2);
            }
            $installScripts[$i] = $reader->read($scriptSize);
        }
        $pictureWidth = $reader->readInt(4);
        $pictureHeight = $reader->readInt(4);
        $reader->skip(2);
        $sprFrame = null;
        if ($pictureWidth > 0 || $pictureHeight > 0) {
            $sprFrame = new SPRFrame($reader, $pictureWidth, $pictureHeight);
            $sprFrame->flip();
        }

        $agentName = $reader->read($reader->readInt(1));

        $agentBlock = new COBAgentBlock($agentName, '');
        $agentBlock->setQuantityAvailable($quantityAvailable);
        $agentBlock->setExpiryDate($expiryDate);
        if ($sprFrame != null) {
            $agentBlock->setThumbnail($sprFrame);
        }
        foreach ($objectScripts as $objectScript) {
            $agentBlock->addEventScript($objectScript);
        }
        $agentBlock->setInstallScript(implode("\n*c2ephp Install script separator\n", $installScripts));
        return $agentBlock;
    }
    /// @endcond
}
