<?php /** @noinspection PhpUnused */

namespace C2ePhp\Sprites;

use C2ePhp\Support\IReader;
use Exception;

/**
 * Class for files in C1's SPR format.
 *
 * Creating new SPR files is currently unsupported.
 * TODO: Allow creating SPR files.
*/
class SPRFile extends SpriteFile {

    /**
     * Instantiates a new SPRFile
     * Reads in the IReader and creates SPRFrames as required.
     * @param IReader|null $reader An IReader to read from.
     * @throws Exception
     */
    public function __construct(?IReader $reader = NULL) {
        parent::__construct('SPR');

		if ($reader != null) {
			$frameCount = $reader->readInt(2);

			for ($i = 0; $i < $frameCount; $i++) {
				$offset = $reader->readInt(4);
				$width = $reader->readInt(2);
				$height = $reader->readInt(2);
				$this->addFrame(new SPRFrame($reader, $width, $height, $offset));
			}
		}
    }

    /**
     * Compiles the SPR file into a binary string
     * @return false|string
     */
    public function compile() {
        $data = pack('v', $this->getFrameCount());
        $offset = 2+(8*$this->getFrameCount());
        foreach ($this->getFrames() as $frame) {
        $data .= pack('V', $offset);
        $data .= pack('vv', $frame->getWidth(), $frame->getHeight());
        $offset += $frame->getWidth()*$frame->getHeight();
        }
        foreach ($this->getFrames() as $frame) {
        $data .= $frame->encode();
        }
        return $data;
    }
}