<?php /** @noinspection PhpUnused */

namespace C2ePhp\Agents\PRAY;

include_once dirname(__FILE__) . '/pray_constants.php';

/**
 * Dependency class used in various PrayBlocks.
 *
 * Known classes that use it: AGNTBlock, DSAGBlock, EGGSBlock
 */
class PrayDependency {
    /// @cond INTERNAL_DOCS

    /** @var int One of the PRAY_DEPENDENCY_* constants */
    private $category;
    /** @var string */
    private $filename;

    /// @endcond

    /**
     * Initialise a new PrayDependency
     *
     * @param int $category One of the PRAY_DEPENDENCY_* constants
     * @param string $filename The name of the file this dependency relates to.
     */
    public function __construct($category, $filename) {
        $this->category = $category;
        $this->filename = $filename;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getFileName() {
        return $this->filename;
    }
}
