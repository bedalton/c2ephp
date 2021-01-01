<?php /** @noinspection PhpUnused */

namespace C2ePhp\PRAY;

/// @brief Representation of a PHOT block
use C2ePhp\Sprites\S16File;
use C2ePhp\Support\StringReader;
use Exception;

/**
* Used to store photos of creatures. \n
* For all properly exported creatures, PHOT blocks always have a
* corresponding CreatureHistoryEvent in the GLSTBlock. \n
* Support for creating your own PHOTBlocks is currently nonexistent.
*/
class PHOTBlock extends PrayBlock {

    /// @brief Instantiate a PHOTBlock
    /**
     * If $prayFile is not null, all the data for this block
     * will be read from the PRAYFile.
     * @param PRAYFile $prayFile The PRAYFile that this DSAG block belongs to.
     * @param string $name The block's name.
     * @param string $content The binary data of this block. May be null.
     * @param int $flags The block's flags
     * @throws Exception
     */
    public function __construct($prayFile, $name, $content, $flags) {
        parent::__construct($prayFile, $name, $content, $flags, PRAY_BLOCK_PHOT);
    }

    /// @cond INTERNAL_DOCS

    /**
     * @return string
     * @throws Exception
     */
    protected function compileBlockData() {
        return $this->getData();
    }

    /**
     * @throws Exception
     */
    protected function decompileBlockData() {
        throw new Exception('It\'s impossible to decompile a PHOT.');
    }

    /// @endcond

    /**
     * Returns the photo data as an s16 file. <b>Deprecated.</b>
     * @return S16File photo data as an S16File object.
     * @throws Exception
     */
    public function getS16File() {
        return new S16File(new StringReader($this->getData()));
    }

    /**
     * Returns the photo data as a PNG.
     * @return string The photo data as a binary string containing PHP data.
     * @throws Exception
     */
    public function toPNG() {
        return $this->getS16File()->toPNG(0);
    }
}
