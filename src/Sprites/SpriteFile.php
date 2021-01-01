<?php /** @noinspection PhpUnused */

namespace C2ePhp\Sprites;

/// @brief Superclass for all SpriteFile types
use Exception;

abstract class SpriteFile {

    /// @cond INTERNAL_DOCS

    /** @var SpriteFrame[] */
    private $frames = [];
    private $spriteFiletype;
    /// @endcond

    /// @cond INTERNAL_DOCS

    /**
     * @param string $filetype
     */
    public function __construct($filetype) {
        $this->spriteFiletype = $filetype;
    }
    /// @endcond

    /**
     * Gets a SpriteFrame from the SpriteFile.
     * @param int $frame The 0-based index of the frame to get.
     * @return SpriteFrame A SpriteFrame
     */
    public function getFrame($frame) {
        return $this->frames[$frame];
    }

    /**
     * Gets the entire frame array.
     * @return SpriteFrame[] An array of SpriteFrames
     */
    public function getFrames() {
        return $this->frames;
    }

    /**
     * Compiles the SpriteFile into a binary string
     * @return string A binary string containing the SpriteFile's data and frames.
     */
    public abstract function compile();

    /**
     * Adds a SpriteFrame to the SpriteFile
     * If necessary, this function converts the SpriteFrame to the
     * correct format.
     * At the moment, this can only add a SpriteFrame to the end of the
     * SpriteFile. TODO: I aim to fix this by the CCSF 2011.
     * @param SpriteFrame $frame A SpriteFrame
     * @throws Exception
     * @internal
     * This process uses some magic which require all types of
     * SpriteFile and SpriteFrame to use 3-character identifiers.
     * This means that if you want to make your own sprite formats
     * you'll need to override this function and provide all our magic
     * plus your own.
     * @endinternal
     */
    public function addFrame(SpriteFrame $frame) {
        if ($this->spriteFiletype == substr(get_class($frame), 0, 3)) {
            //$this->frames[$position] = $frame;
            $this->frames[] = $frame;
        } else {
            //$this->frames[$position] = $frame->toSpriteFrame($this->spriteFiletype);
            $this->frames[] = $frame->toSpriteFrame($this->spriteFiletype);
        }
    }
    /// @brief Replaces a frame in the SpriteFile

    /**
     * Replaces the frame in the given position
     * Uses the same magic as AddFrame
     * @param SpriteFrame $frame A SpriteFrame of any type.
     * @param int $position Which frame to replace. If negative, counts
     * backwards from the end of the frames array.
     * @throws Exception
     */
    public function replaceFrame(SpriteFrame $frame, $position) {
        if ($position < 0) {
            $position = sizeof($this->frames) - $position;
        }
        $this->frames[$position] = $frame->toSpriteFrame($this->spriteFiletype);
    }
    /// @brief Gets the number of frames currently stored in this SpriteFile.

    /**
     * @return int The number of frames
     */
    public function getFrameCount() {
        return sizeof($this->frames);
    }
    /// @brief Deletes the frame in the given position.

    /**
     * @param int $frame The 0-based index of the frame to delete.
     */
    public function deleteFrame($frame) {
        unset($this->frames[$frame]);
    }
    /// @brief Converts the given frame to PNG. <strong>Deprecated.</strong>

    /**
     * May be removed in a future release.
     * Use GetFrame($frame)->toPNG() instead.
     * @param int $frame The 0-based index of the frame to delete.
     * @return string A binary string containing a PNG.
     */
    public function toPNG($frame) {
        return $this->frames[$frame]->toPNG();
    }
}