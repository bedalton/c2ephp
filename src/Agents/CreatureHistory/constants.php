<?php /** @noinspection PhpUnused */
/** @noinspection SpellCheckingInspection */


/// @relates GLSTBlock
/// @name History Formats
///@{

/** Value: 0 */
const GLST_FORMAT_UNKNOWN = 0;
/** Value: 1 */
const GLST_FORMAT_C3 = 1;
/** Value: 2 */
const GLST_FORMAT_DS = 2;
//@}


/**
 * @relates CreatureHistory
 * @name int Gender
 * CreatureHistory-specific gender constants
 */
///@{
/** Value: 1 */
const CREATUREHISTORY_GENDER_MALE = 1;

/** Value: 2 */
const CREATUREHISTORY_GENDER_FEMALE = 2;
///@}


/**
 * @relates CreatureHistoryEvent
 * @name int Event Numbers
 * All creatures are either CONCEIVED, SPLICED, ENGINEERED, or
 * IAMCLONED.\n
 * Then CONCEIVED creatures are MUMLAIDMYEGG. \n
 * Then they are HATCHED except maybe ENGINEERED creatures. \n
 * Then they have a PHOTOTAKEN. \n
 * Then there's not a specific order of events that always happen
 * - they go on and live their own lives ;)
 */
//@{
/** I was conceived by kisspopping or artificial insemination. \n
 * Value: 0 */
const CREATUREHISTORY_EVENT_CONCEIVED = 0;
/** I was spliced from two other creatures \n
 * Value: 1 */
const CREATUREHISTORY_EVENT_SPLICED = 1;
/** I was created by someone with a genetics kit \n
 * Value: 2 */
const CREATUREHISTORY_EVENT_ENGINEERED = 2;
/** I hatched out of my egg. \n
 * Value: 3 */
const CREATUREHISTORY_EVENT_HATCHED = 3;
/**
 * CreatureHistoryEvent::GetLifestage will tell you what lifestage I
 * am now. \n\n
 * Value: 4
 */
const CREATUREHISTORY_EVENT_AGED = 4;
/** I was exported from this world \n
 * Value: 5 */
const CREATUREHISTORY_EVENT_EXPORTED = 5;
/** I joined this world \n
 * Value: 6 */
const CREATUREHISTORY_EVENT_IMPORTED = 6;
/** My journey through life ended. \n
 * Value: 7 */
const CREATUREHISTORY_EVENT_DIED = 7;
/** I became pregnant. \n
 * Value: 8 */
const CREATUREHISTORY_EVENT_BECAMEPREGNANT = 8;
/** I made someone else pregnant.  \n
 * Value: 9 */
const CREATUREHISTORY_EVENT_IMPREGNATED = 9;
/** My child hatched from its egg! \n
 * Value: 10 */
const CREATUREHISTORY_EVENT_CHILDBORN = 10;
/** My mum laid my egg. \n
 * Value: 11 */
const CREATUREHISTORY_EVENT_MUMLAIDMYEGG = 11;
/** I laid an egg I was carrying. \n
 * Value: 12 */
const CREATUREHISTORY_EVENT_LAIDEGG = 12;
/** A photo was taken of me. \n
 * Value: 13 */
const CREATUREHISTORY_EVENT_PHOTOTAKEN = 13;
/**
 * I was made by cloning another creature \n
 * This happens when you export a creature then import it more than
 * once. \n
 * Value: 14
 */
const CREATUREHISTORY_EVENT_IAMCLONED = 14;
/** Another creature was made by cloning me. \n
 * This happens when you export a creature then import it more than
 * once. \n
 * Value: 15
 */
const CREATUREHISTORY_EVENT_CLONEDME = 15;
/** I left this world through the warp \n
 * Value: 16 */
const CREATUREHISTORY_EVENT_WARPEDOUT = 16;
/** I entered this world through the warp \n
 * Value: 17 */
const CREATUREHISTORY_EVENT_WARPEDIN = 17;
//@}