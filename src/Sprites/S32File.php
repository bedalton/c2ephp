<?php /** @noinspection PhpUnused */

namespace C2ePhp\Sprites;

use C2ePhp\Support\IReader;
use Exception;

/**
 * Class representing a C16 sprite file.
 *
 * @package C2ePhp\Sprites
 */
class S32File extends SpriteFile {
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
		parent::__construct('S32');
        if ($reader != null) {
            $buffer = $reader->readInt(4);
            if (($buffer & 4) == 0) { //buffer & 2 == 2 => RLE. buffer & 2 == 0 => non-RLE (same as s16 but not supported here because it's complex dude.
                throw new Exception('This file is probably a C16 masquerading as a S32!');
            } else if ($buffer > 4) {
                throw new Exception("File encoding not recognised. ($buffer)");
            } else if ($buffer & 2) {
                throw new Exception('This file is probably a C16 masquerading as a S32!');
            } else if ($buffer != 4) {
                throw new Exception('This file is probably a S16 masquerading as a S32!');
            }

            $buffer = $reader->readInt(2);
            if ($buffer < 1) {
                throw new Exception('Sprite file appears to contain less than 1 frame.');
            }
            $frameCount = $buffer;
            for ($x = 0; $x < $frameCount; $x++) {
                $this->addFrame(new S32Frame($reader, $this->encoding));
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
        $offset = 6 + (8 * $this->getFrameCount());
        /** @var [int, int, string][] $images */
        $images = array_map(function (SpriteFrame $frame) {
            return [$frame->getWidth(), $frame->getHeight(), $frame->encode()];
        }, $this->getFrames());
        foreach ($images as $image) {
            $data .= pack('V', $offset);
            $offset += strlen($image[2]);
            $data .= pack('v', strlen($image[0]));
            $data .= pack('v', strlen($image[1]));
        }

        foreach ($images as $image) {
            $data .= $image[2];
        }
        return $data;
    }

}