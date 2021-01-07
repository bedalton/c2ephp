<?php /** @noinspection PhpUnused */

namespace C2ePhp\Sprites;



use C2ePhp\Support\IReader;
use Exception;

/**
 * Class for S16 sprite files.
 *
 * TODO: Currently doesn't support creating S16s.
 */
class S16File extends SpriteFile {

    /// @cond INTERNAL_DOCS

    private $encoding;
    private $frameCount;
    private $reader;

    /// @endcond

    /**
     * Instantiates a new S16File
     * @param IReader $reader an IReader to create the S16File from.
     * @throws Exception
     */
    public function __construct(IReader $reader)
    {
        parent::__construct('S16');
        $this->reader = $reader;
        $buffer = $this->reader->readInt(4);
        if ($buffer == 1) {
            $this->encoding = "565";
        } else if ($buffer == 0) {
            $this->encoding = "555";
        } else {
            throw new Exception("File encoding not recognised. (".$buffer.'|'.$this->reader->getPosition().')');
        }
        $this->frameCount = $this->reader->readInt(2);
        for ($i = 0; $i < $this->frameCount; $i++)
        {
            $this->addFrame(new S16Frame($this->reader, $this->encoding));
        }
    }

    /**
     * Sets the encoding (555 or 565) of this s16 file.
     *
     * This only affects compiling the S16File. You cannot
     * accidentally (or deliberately) read a 555 sprite file in 565
     * format or vise versa.
     * @param string $encoding '555', '565'
     */
    public function setEncoding($encoding) {
        $this->encoding = $encoding;
    }

    /**
     * Returns the number of frames in this Sprite
     * @return int
     */
    public function getFrameCount() {
        return $this->frameCount;
    }

    /**
     * Compiles the S16 file into a binary string.
     *
     * @return string binary string containing s16 data of whatever the
     * current encoding set by SetEncoding() is. If you haven't set
     * it, it's most likely 565.
     */
    public function compile() {
        $data = '';
        /* S16 and C16 are actually the same format....
         * C16 just has RLE. But they're different classes.
         * not very DRY, I know. This is better for magic though. */
        $flags = 0;
        // 0b00 => 555 S16,
        // 0b01 => 565 S16,
        // 0b10 => 555 C16,
        // 0b11 => 565 C16
        if ($this->encoding == '565') {
            $flags = $flags | 1;
        }
        $data .= pack('V', $flags);
        $data .= pack('v', $this->getFrameCount());
        $iData = '';
        $offset = 6+(8*$this->getFrameCount());
        foreach ($this->getFrames() as $frame) {
            $data .= pack('V', $offset);
            $data .= pack('vv', $frame->getWidth(), $frame->getHeight());

            $frameBin = $frame->encode();
            $offset += strlen($frameBin);
            $iData .= $frameBin;
        }
        return $data.$iData;
    }
}

