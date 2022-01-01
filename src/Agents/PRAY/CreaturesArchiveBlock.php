<?php

namespace C2ePhp\Agents\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/PrayBlock.php');
require_once(realpath(dirname(__FILE__) . '/../../Support/Archiver.php'));

/**
 * Abstract class for easy de-archiving CreaturesArchives
 *
 * Used by CREABlock and GLSTBlock, this class is not for end-users.
 */
abstract class CreaturesArchiveBlock extends PrayBlock {
    /// @cond INTERNAL_DOCS

    /**
     * Instantiates a new CreaturesArchiveBlock
     *
     * @param PRAYFile|null $prayFile The PRAYFile this CreaturesArchive belongs to. May be null.
     * @param string $name The name of this block
     * @param string $content This block's binary data.
     * @param int $flags the flags of this block
     * @param string $type This block's type as one of the PRAY_BLOCK_* constants
     * @throws Exception
     */
    public function __construct(?PRAYFile $prayFile, string $name, string $content, int $flags, string $type) {
        parent::__construct($prayFile, $name, $content, $flags, $type);
        if ($prayFile instanceof PRAYFile) {
            if (!$this->deArchive()) {
                throw new Exception("De-Archiving failed, block probably wasn't a CreaturesArchive type");
            }
        }
    }
    /// @endcond

    /**
     * DeArchives this block
     * @return bool
     * @throws Exception
     */
    private function deArchive() {
        $content = $this->getData();
        if ($content[0] == 'C') {
            $content = deArchive($content);
            if ($content !== false) {
                $this->setData($content);
                return true;
            }
//          echo 'Invalid CreaturesArchive';
            return false;
        }
        return true;
    }

}
