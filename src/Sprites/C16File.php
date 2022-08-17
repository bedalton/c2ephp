<?php /** @noinspection PhpUnused */

namespace C2ePhp\Sprites;

use C2ePhp\Support\IReader;
use Exception;

/**
 * Class representing a C16 sprite file.
 *
 * @package C2ePhp\Sprites
 */
class C16File extends SpriteFile {
    /// @cond INTERNAL_DOCS

    private $encoding;
    /// @endcond

    /**
     * Creates a new C16File object.
     * If $reader is null, creates an empty C16File ready to add sprites to.
     * @param IReader|null $reader The reader to read the sprites from. Can be null.
     * @throws Exception
     */
    public function __construct(IReader $reader = null, ?string $encoding = NULL) {
		parent::__construct('C16');
        if ($reader != null) {
            $buffer = $reader->readInt(4);
            if (($buffer & 1) == 1) {
                $this->encoding = '565';
            } else {
                $this->encoding = '555';
            }

            if (($buffer & 2) == 0) { //buffer & 2 == 2 => RLE. buffer & 2 == 0 => non-RLE (same as s16 but not supported here because it's complex dude.
                throw new Exception('This file is probably a S16 masquerading as a C16!');
            } else if ($buffer > 3) {
                throw new Exception("File encoding not recognised. ($buffer)");
            }

            $buffer = $reader->readInt(2);
            if ($buffer < 1) {
                throw new Exception('Sprite file appears to contain less than 1 frame.');
            }
            $frameCount = $buffer;
            for ($x = 0; $x < $frameCount; $x++) {
                $this->addFrame(new C16Frame($reader, $this->encoding));
            }
        } else {
			$this->encoding = $encoding === '555' ? '555' : '565';
		}
    }

    /**
     * Sets the encoding for this file
     * @param $encoding '565' or '555', anything else will be treated as '555'
     */
    public function setEncoding($encoding) {
        $this->encoding = $encoding;
    }
    /**
     * Compiles the file's data into a C16 binary string
     * @return string binary string containing the C16File's data.
     */
    public function compile() {
        $data = '';
        $flags = 2; // 0b00 => 555 S16, 0b01 => 565 S16, 0b10 => 555 C16, 0b11 => 565 C16
        if ($this->encoding == '565') {
            $flags = $flags | 1;
        }
        $data .= pack('V', $flags);
        $data .= pack('v', $this->getFrameCount());
        $iData = '';
        $offset = 6 + (8 * $this->getFrameCount());
        foreach ($this->getFrames() as $frame) {
            $offset += ($frame->getHeight() - 1) * 4;
        }

        foreach ($this->getFrames() as $frame) {
            $data .= pack('V', $offset);
            $data .= pack('vv', $frame->getWidth(), $frame->getHeight());

            $frameData = $frame->encode();
            $frameBin = $frameData['data'];
            foreach ($frameData['lineOffsets'] as $lineOffset) {
                $data .= pack('V', $lineOffset + $offset);
            }
            $offset += strlen($frameBin);
            $iData .= $frameBin;
        }
        return $data . $iData;
    }

}