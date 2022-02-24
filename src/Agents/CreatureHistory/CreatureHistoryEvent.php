<?php /** @noinspection PhpUnused */

namespace C2ePhp\CreatureHistory;
require_once(realpath(dirname(__FILE__) . '/constants.php'));

/**
 * Class to represent events in a creature's life
 *
 * @package C2ePhp\CreatureHistory
 */
class CreatureHistoryEvent {

    /// @cond INTERNAL_DOCS

    /** @var int */
    private $eventType;
    /** @var int */
    private $worldTime;
    /** @var int */
    private $creatureAge;
    /** @var int */
    private $timestamp;
    /** @var int */
    private $lifeStage;
    /** @var string */
    private $moniker1;
    /** @var string */
    private $moniker2;
    /** @var string */
    private $userText;
    /** @var string photograph id */
    private $photograph;
    /** @var string */
    private $worldName;
    /** @var int */
    private $worldUID;
    /** @var int DS Only */
    private $dockingStationUser;
    /** @var int (DS Only)*/
    private $unknown1; ///
    /** @var int (DS ONLY) */
    private $unknown2;

    /// @endcond

	/**
	 * Instantiates a new CreatureHistoryEvent.
	 *
	 * @param int $eventType The event number as defined by the CREATUREHISTORY_EVENT_* constants.
	 * @param int $worldTime The world's age in ticks at the time of this
	 * event.
	 * @param int $creatureAge The age of the creature in ticks at the
	 * time of this event
	 * @param int $timestamp The time of this event as a unix timestamp.
	 * (number of seconds passed since 1st Jan, 1970)
	 * @param int $lifeStage The life stage this creature had achieved at
	 * the time of this event.
	 * @param string|null $moniker1 The first moniker associated with this event.
	 * @param string|null $moniker2 The second moniker associated with this event.
	 * @param string|null $userText The user text associated with this event.
	 * @param string|null $photograph The photograph associated with this event.
	 * @param string|null $worldName The name of the world the creature was inhabiting at the time of this event
	 * @param string|null $worldUID The UID of the world the creature was inhabiting at the the time of this event
	 * @see getMoniker1(), getMoniker2(), getUserText,
	 * GetPhotograph()
	 */
    public function __construct(int $eventType, int $worldTime, int $creatureAge, int $timestamp, int $lifeStage, ?string $moniker1, ?string $moniker2, ?string $userText, ?string $photograph, ?string $worldName, ?string $worldUID) {
        $this->eventType = $eventType;
        $this->worldTime = $worldTime;
        $this->creatureAge = $creatureAge;
        $this->timestamp = $timestamp;
        $this->lifeStage = $lifeStage;
        $this->moniker1 = $moniker1;
        $this->moniker2 = $moniker2;
        $this->userText = $userText;
        $this->photograph = $photograph;
        $this->worldName = $worldName;
        $this->worldUID = $worldUID;
    }

    /**
     * Add DS-specific information to the CreatureHistoryEvent
     *
     * @param string|null $dsUserID The UID of the Docking Station user whose world the creature was in at the time of the event
     * @param mixed $unknown1 I don't know! But it comes right after the DS-UID in the GLST format.
     * @param mixed $unknown2 I don't know! But it comes right after unknown1.
     */
    public function addDSInfo(?string $dsUserID, $unknown1, $unknown2) {
        $this->dockingStationUser = $dsUserID;
        $this->unknown1 = $unknown1;
        $this->unknown2 = $unknown2;
    }

    /**
     * Compiles the data into the correct format for the game specified.
     *
     * This is called automatically by CreatureHistory, most users
     * should have no need to use this function themselves.
     * @param int $format Which game to compile it for (a GLST_FORMAT_* constant)
     * @return string binary string containing GLST data ready to be put into a GLST history.
     */
    public function compile(int $format) {
        /** @noinspection SpellCheckingInspection */
        $data = pack('VVVVV', $this->eventType, $this->worldTime, $this->creatureAge, $this->timestamp, $this->lifeStage);
        $data .= pack('V', strlen($this->moniker1)) . $this->moniker1;
        $data .= pack('V', strlen($this->moniker2)) . $this->moniker2;
        $data .= pack('V', strlen($this->userText)) . $this->userText;
        $data .= pack('V', strlen($this->photograph)) . $this->photograph;
        $data .= pack('V', strlen($this->worldName)) . $this->worldName;
        $data .= pack('V', strlen($this->worldUID)) . $this->worldUID;
        if ($format == GLST_FORMAT_DS) {
            $data .= pack('V', strlen($this->dockingStationUser)) . $this->dockingStationUser;
            $data .= pack('VV', $this->unknown1, $this->unknown2);
        }
        return $data;
    }

    /**
     * Accessor method for event type
     *
     * @return int The event type as a CREATUREHISTORY_EVENT_* constant.
     */
    public function getEventType() {
        return $this->eventType;
    }

    /**
     * Accessor method for world time
     *
     * @return int The age of the world, in ticks, when this event occurred.
     */
    public function getWorldTime() {
        return $this->worldTime;
    }

    /**
     * Accessor method for creature age
     *
     * @return int The age of the creature, in ticks, when this event happened
     */
    public function getCreatureAge() {
        return $this->creatureAge;
    }

    /**
     * Accessor method for timestamp
     *
     * @return int The unix timestamp of the time at which this event occurred
     */
    public function getTimestamp() {
        return $this->timestamp;
    }


    /**
     * Accessor method for life stage
     *
     * @return int The creature's life stage (an integer, 0-6 I think.
     * 0xFF means unborn.) \n
     * TODO: Make a set of constants for this.
     */
    public function getLifeStage() {
        return $this->lifeStage;
    }


    /**
     * Accessor method for moniker 1
     *
     * Moniker 1 is the first moniker associated with this event.
     * In conception and splicing, it is one of the parent
     * creatures. \n
     * In cloning, it is the parent/child's moniker. Whichever is not
     * the current creature. \n
     * In laying an egg, it is the moniker of the egg laid. \n
     * In becoming pregnant, it's the creature that made this one
     * pregnant \n
     * In making another pregnant, it's the pregnant creature. \n
     * In a child being born, it's the other parent of the child
     * @return string The first moniker associated with this event
     */
    public function getMoniker1() {
        return $this->moniker1;
    }

    /**
     * Accessor method for moniker 2
     *
     * Moniker 2 is the second moniker associated with this event.
     * In conception and splicing, it is one of the conceiving
     * creatures.
     * In becoming pregnant, it's the child's moniker
     * In making another pregnant, it's the child's moniker
     * In a child being born, it's the child's moniker
     * @return string The first moniker associated with this event
     */
    public function getMoniker2() {
        return $this->moniker2;
    }

    /**
     * Accessor method for user text
     *
     * In theory user text can be used on any event without messing
     * it up (and it would be readable via CAOS) See
     * http://nornalbion.github.com/c2ephp/caos-guide.html#HIST%20FOTO
     * for more on reading history with CAOS. \n
     * In practice, this is only used by either the first event or
     * the hatched event (I forget which) and is used to mean the
     * text that the user enters to describe this creature in the
     * creature info dialog.
     * @return string The user text associated with this event.
     */
    public function getUserText() {
        return $this->userText;
    }

    /**
     * Accessor method for photograph
     *
     * Gets the name of the PHOT block containing the S16File
     * of the photograph for this event. \n
     * In theory this can be used on any event without messing
     * anything up, and would be readable via CAOS. See
     * http://nornalbion.github.com/c2ephp/caos-guide.html#HIST%20FOTO
     * for more on reading history with CAOS. \n
     * In practice (i.e. in all GLST blocks I've seen) this is only
     * used on photo-taken events. \n
     * @return string The identifier of the photograph (in the format myMoniker-photoNumber)
     */
    public function getPhotograph() {
        return $this->photograph;
    }
    /**
     * Accessor method for world name
     *
     * @return string The name of the world this creature was in during this event
     */
    public function getWorldName() {
        return $this->worldName;
    }

    /**
     * Accessor method for world UID
     *
     * @return int The name of the world this creature was in during this event
     */
    public function getWorldUID() {
        return $this->worldUID;
    }
}