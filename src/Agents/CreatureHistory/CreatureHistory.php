<?php /** @noinspection PhpUnused */

namespace C2ePhp\CreatureHistory;

include_once dirname(__FILE__) . '/constants.php';

/**
 * Class representing a creature's history.
 *
 * As used in the C3 crypt as well as the creature's in-game info.
 */
class CreatureHistory {

	/// @cond INTERNAL_DOCS

	/** @var CreatureHistoryEvent[] */
	private $events = [];
	/** @var string|null */
	private $moniker;
	/** @var string|null */
	private $name;
	/** @var int|null */
	private $gender;
	/** @var int|null */
	private $genus;
	/** @var int|null */
	private $species;
	/** @var bool */
	private $warpVeteran;


	/** @var int|null */
	private $mutations;
	/** @var int|null  */
	private $crossoverPoints;
	//TODO: find out what unknowns are`
	/** @var int (DS only) */
	private $unknown1;
	/** @var string (DS only) */
	private $unknown2;

	/// @endcond

	/**
	 * Construct a CreatureHistory object.
	 *
	 * @param null|string $moniker The moniker of the creature
	 * @param null|string $name The creature's name
	 * @param int|null $gender The creature's gender
	 * @param int|null $genus The creature's genus (Grendel, ettin, norn, geat)
	 * @param int|null $species The creature's species (unsure of purpose)
	 */
	public function __construct(?string $moniker, ?string $name, ?int $gender, ?int $genus, ?int $species) {
		$this->moniker = $moniker;
		$this->name = $name;
		$this->gender = $gender;
		$this->genus = $genus;
		$this->species = $species;
	}

	/**
	 * Compiles the CreatureHistory into CreaturesArchive data.
	 *
	 * @param int $format GLST_FORMAT_C3 or GLST_FORMAT_DS.
	 * @return string binary string ready for archiving and putting in a GLST block.
	 */
	public function compile(int $format = GLST_FORMAT_C3) {
		if ($format != GLST_FORMAT_C3 && $format != GLST_FORMAT_DS) {
			$format = $this->guessFormat();
		}
		if ($format == GLST_FORMAT_DS) {
			$data = pack('V', 0x27);
		} else {
			$data = pack('V', 0x0C);
		}
		$data .= pack('V', 1);
		$data .= pack('V', 32) . $this->moniker;
		$data .= pack('V', 32) . $this->moniker; //yeah, twice. Dunno why, CL are bonkers.
		$data .= pack('V', strlen($this->name)) . $this->name;
		/** @noinspection SpellCheckingInspection */
		$data .= pack('VVVV', $this->gender, $this->genus, $this->species, count($this->events));
		foreach ($this->events as $event) {
			$data .= $event->compile($format);
		}
		$data .= pack('V', $this->mutations);
		$data .= pack('V', $this->crossoverPoints);
		if ($format == GLST_FORMAT_DS) {
			$data .= pack('V', $this->unknown1);
			$data .= pack('V', strlen($this->unknown2)) . $this->unknown2;
		}
		return $data;
	}

	/**
	 * Try to work out which game this CreatureHistory is for
	 *
	 * This is done by working out whether any DS-specific variables
	 * are set.
	 * @return int or GLST_FORMAT_C3.
	 */
	public function guessFormat() {
		return (isset($this->unknown1)) ? GLST_FORMAT_DS : GLST_FORMAT_C3;
	}

	/**
	 * Adds an event to the end of a history
	 *
	 * @param CreatureHistoryEvent $event A CreatureHistoryEvent to add to this
	 * CreatureHistory object.
	 */
	public function addEvent(CreatureHistoryEvent $event) {
		$this->events[] = $event;
	}

	/**
	 * Gets an event from the history
	 *
	 * Simply gets the nth event that happened in this history
	 * @param int $n the event number to get
	 * @return CreatureHistoryEvent|null $nth CreatureHistoryEvent
	 */
	public function getEvent(int $n) {
		return $this->events[$n] ?? null;
	}

	/**
	 * Removes an event from history
	 *
	 * Removes the nth event from this history
	 * @param int $n the event number to remove
	 */
	public function removeEvent(int $n) {
		unset($this->events[$n]);
	}

	/**
	 * Counts the events in the history
	 *
	 * @return int How many events there currently are in this history
	 */
	public function countEvents() {
		return sizeof($this->events);
	}

	/**
	 * Gets all events matching the given event type
	 *
	 * @param int $type one of the Event Type constants.
	 * @return CreatureHistoryEvent[] an array of CreatureHistoryEvents.
	 * @see agents/CreatureHistory/CreatureHistoryEvent.php Event Types
	 */
	public function getEventsByType(int $type) {
		$matchingEvents = [];
		foreach ($this->events as $event) {
			if ($event->getEventType() == $type) {
				$matchingEvents[] = $event;
			}
		}
		return $matchingEvents;
	}

	/**
	 * Gets all the events in this history
	 *
	 * @return CreatureHistoryEvent[] array of CreatureHistoryEvents
	 */
	public function getEvents() {
		return $this->events;
	}

	/**
	 * Gets the moniker of the creature this history is attached to.
	 *
	 * @return null|string
	 */
	public function getCreatureMoniker() {
		return $this->moniker;
	}

	/**
	 * Gets the generation of the creature
	 *
	 * I cannot guarantee that this function works. However, it does use the
	 * same method as the Creatures 3 in-game creature information viewer,
	 * so it should work on all creatures made in-game.
	 * @return int 0 for failure or the generation of the creature otherwise.
	 */
	public function getCreatureGenerationNumber() {
		$pos = strpos('_', $this->moniker);
		if ($pos >= 0) {
			$firstBit = substr($this->moniker, 0, $pos);
			if (is_numeric($firstBit)) {
				return $firstBit + 0;
			}
		}
		return 0;
	}

	/**
	 * Gets the name of the creature this history is attached to.
	 * @return string
	 */
	public function getCreatureName() {
		return $this->name;
	}

	/**
	 * Gets the gender of the creature this history is attached to.
	 * @return int
	 */
	public function getCreatureGender() {
		return $this->gender;
	}

	/**
	 * Gets the genus of the creature this history is attached to.
	 * @return int
	 */
	public function getCreatureGenus() {
		return $this->genus;
	}

	/**
	 * Gets the species of the creature this history is attached to.
	 * @return int
	 */
	public function getCreatureSpecies() {
		return $this->species;
	}

	/**
	 * Gets whether the creature this history is attached to has been through the warp.
	 * @return bool
	 */
	public function isWarpVeteran() {
		return $this->warpVeteran;
	}

	/**
	 * Gets the number of mutation points during conception
	 * @return int
	 */
	public function getCreatureMutations() {
		return $this->mutations;
	}


	/**
	 * Gets the number of crossover points during conception
	 * @return int|null
	 */
	public function getCreatureCrossoverPoints() {
		return $this->crossoverPoints;
	}

	/**
	 * @param int $mutations
	 * @param int $crossovers
	 */
	public function setMutationsAndCrossovers(int $mutations, int $crossovers) {
		$this->mutations = $mutations;
		$this->crossoverPoints = $crossovers;
	}


	/**
	 * Set variables that are currently unknown, specific to DS
	 *
	 * This calls SetC3Unknown
	 * @param int $unknown1 First unknown variable
	 * @param false|string|null $unknown2 Second unknown variable
	 */
	public function setDSUnknowns(int $unknown1, ?string $unknown2) {
		$this->unknown1 = $unknown1;
		$this->unknown2 = $unknown2;
	}

	/**
	 * Sets whether or not the creature is a veteran of the warp (DS only)
	 *
	 * @param int $warpVeteran A boolean (I think!)
	 */
	public function setWarpVeteran(int $warpVeteran) {
		$this->warpVeteran = !empty($warpVeteran);
	}
}