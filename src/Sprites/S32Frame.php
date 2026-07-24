<?php

namespace C2ePhp\Sprites;

use C2ePhp\Support\IReader;
use Exception;

/**
 * Class for a single image in a C16File
 */
class S32Frame extends SpriteFrame {
	/// @cond INTERNAL_DOCS

	private $encoding;

	private array $pngBytes;

	private $reader;
	private $offset;
    private $length;

	/// @endcond


	///brief Initialise a C16Frame
	/**
	 * @see http://php.net/image
	 * @param IReader|resource $reader An IReader or GD image resource.
	 * @param string $encoding The encoding of the C16 frame (555 or 565). Defaults to 565
	 * @throws Exception
	 */
	public function __construct($reader, $encoding = '565') {
		if ($reader instanceof IReader) {
			$this->reader = $reader;
			$this->encoding = $encoding;
			$this->offset = $this->reader->readInt(4);
            $position = $this->reader->getPosition();
            $this->length = $this->reader->readInt(4) - $this->offset;
            $this->reader->seek($position);

			$width = $this->reader->readInt(2);
			$height = $this->reader->readInt(2);

			parent::__construct($width, $height);
		} else if (SpriteFrame::isGD($reader)) {
			parent::__construct(imagesx($reader), imagesy($reader), true);
			$this->gdImage = $reader;
		} else {
			throw new Exception('$reader must be an IReader or gd image resource.');
		}
	}

	/**
	 * Sets the encoding to use when compiling
	 *
	 * @param $encoding
	 */
	public function setEncoding($encoding) {
		$this->ensureDecoded();
		$this->encoding = $encoding;
	}

	/// @cond INTERNAL_DOCS

	/**
	 * Decodes the C16Frame
	 *
	 * Called automatically by EnsureDecompiled.
	 * @throws Exception
	 */
	protected function decode() {
        if ($this->gdImage) {
            return $this->gdImage;
        }
		$image = imagecreatefromstring($this->reader->getSubString($this->offset, $this->length));

// 2. IMPORTANT: Disable blending so it doesn't overwrite your alpha channel
        imagealphablending($image, false);

// 3. IMPORTANT: Tell PHP to save the alpha channel information
        imagesavealpha($image, true);
        $this->gdImage = $image;
		return $image;
	}
	/// @endcond

	/**
	 * Encodes the C16Frame into a C16 binary string
	 *
	 * Produces a string suitable for use as a PHOTO block, for example.
	 * This is called automatically by C16File's Compile function.
	 * @throws Exception
	 */
	public function encode() {
        ob_start();
        imagepng($this->getGDImage(), null, 7);
        $pngBytes = ob_get_clean();
        return $pngBytes;
	}
}
