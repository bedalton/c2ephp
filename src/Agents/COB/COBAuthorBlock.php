<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\COB;

/// @brief Defines Author information about the COB
use C2ePhp\Support\IReader;
use Exception;

class COBAuthorBlock extends COBBlock {

    /// @cond INTERNAL_DOCS

    private $creationTime;
    private $version;
    private $revision;
    private $authorName;
    private $authorEmail;
    private $authorURL;
    private $authorComments;

    /// @endcond

    /// @brief Creates a new COBAuthorBlock

    /**
     * @param string $authorName the name of the author of this COB
     * @param string $authorEmail the author's email address
     * @param string $authorURL The author's website address
     * @param string $authorComments Any comments the author had about this COB
     * @param int $creationTime the time this COB was compiled as a UNIX timestamp
     * @param string $version The version number of this COB (integer)
     * @param int $revision the COB's revision number (integer)
     * @throws Exception
     */
    public function __construct($authorName, $authorEmail, $authorURL, $authorComments, $creationTime, $version, $revision) {
        parent::__construct(COB_BLOCK_AUTHOR);
        $this->authorName = $authorName;
        $this->authorEmail = $authorEmail;
        $this->authorURL = $authorURL;
        $this->authorComments = $authorComments;
        $this->creationTime = $creationTime;
        $this->version = $version;
        $this->revision = $revision;
    }

    /**
     * Supposedly compiles the block into binary. Throws an error to say it's not implemented.
     *
     * @return string
     * @throws Exception
     */
    public function compile() {
        // TODO: implement
        throw new Exception("COBAgentBlock::Compile not implemented");
    }

    /**
     * Gets the name of the author
     * @return string Author's name
     */
    public function getAuthorName() {
        return $this->authorName;
    }

    /**
     * Gets the author's email address
     *
     * @return string
     */
    public function getAuthorEmail() {
        return $this->authorEmail;
    }

    /**
     * Gets the author's web address
     *
     * @return string
     */
    public function getAuthorURL() {
        return $this->authorURL;
    }

    /**
     * Gets comments from the author
     *
     * @return string
     */
    public function getAuthorComments() {
        return $this->authorComments;
    }

    /**
     * Gets the time this COB was created
     *
     * @return int A UNIX timestamp representing the time this COB was created.
     */
    public function getCreationTime() {
        return $this->creationTime;
    }

    /**
     * Gets the COB's version number
     *
     * @return int
     * @see getRevision()
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Gets the COB's revision number
     *
     * The revision number is less significant than the version
     * number.
     * @return int
     */
    public function getRevision() {
        return $this->revision;
    }

    /// @cond INTERNAL_DOCS

    /**
     * Creates the COBAuthorBlock from an IReader.
     *
     * @param IReader $reader The IReader, currently at the position of the author block
     * @return COBAuthorBlock
     * @throws Exception
     */
    public static function createFromReader(IReader $reader) {
        $creationDay = $reader->readInt(1);
        $creationMonth = $reader->readInt(1);
        $creationYear = $reader->readInt(2);
        $creationTime = mktime(0, 0, 0, $creationMonth, $creationDay, $creationYear);
        $version = $reader->readInt(1);
        $revision = $reader->readInt(1);
        $authorName = $reader->readCString();
        $authorEmail = $reader->readCString();
        $authorURL = $reader->readCString();
        $authorComments = $reader->readCString();
        return new COBAuthorBlock($authorName, $authorEmail, $authorURL, $authorComments, $creationTime, $version, $revision);
    }
    /// @endcond
}