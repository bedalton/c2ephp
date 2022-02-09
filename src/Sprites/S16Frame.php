<?php

namespace C2ePhp\Sprites;

use C2ePhp\Support\IReader;
use Exception;


/**
 * Class for frames of S16 files.
 *
 * S16Frames can be created from S16Files or from GD Image resources.
 */
class S16Frame extends SpriteFrame {

    /// @cond INTERNAL_DOCS

    private $offset;
    private $reader;
    private $decoded;
    private $encoding;

    /// @endcond

	/**
	 * Instantiate an S16Frame
	 * @param $reader
	 * @param string $encoding
	 * @param int|null $width
	 * @param int|null $height
	 * @param int|null $offset
	 * @throws Exception
	 */
    public function __construct($reader, $encoding = '565', ?int $width = NULL, ?int $height = NULL, ?int $offset = NULL) {
        if ($reader instanceof IReader) {
            $this->reader = $reader;
            $this->encoding = $encoding;
            if (is_null($width) || is_null($height) || is_null($offset)) {
                $this->offset = $this->reader->readInt(4);
                parent::__construct($this->reader->readInt(2), $this->reader->readInt(2));
            } else {
                parent::__construct($width, $height);
                $this->offset = $offset;
            }
        } else if (get_resource_type($reader) == 'gd') {
            $this->gdImage = $reader;
            $this->decoded = true;
            $this->encoding = $encoding;
            parent::__construct(imagesx($reader), imagesy($reader), TRUE);

        }
    }

    /**
     * Encodes the image into s16 frame data.
     * @param $format 555 or 565.
     * @return string image data in the format specified
     * @throws Exception
     */
    public function encode(string $format = '565') {
        $this->ensureDecoded();
        $data = '';
        for ($y = 0; $y < $this->getHeight(); $y++) {
            for ($x = 0; $x < $this->getWidth(); $x++) {

                $pixel = $this->getPixel($x, $y);
                if ($pixel['red'] > 255 || $pixel['green'] > 255 || $pixel['blue'] > 255) {
                    throw new Exception('Pixel colour out of range.');
                }
                if ($this->encoding == '555') {
                    $newPixel = (($pixel['red'] << 7) & 0xF800) | (($pixel['green'] << 2) & 0x03E0) | (($pixel['blue'] >> 3) & 0x001F);
                } else {
                    $newPixel = (($pixel['red'] << 8) & 0xF800) | (($pixel['green'] << 3) & 0x07E0) | (($pixel['blue'] >> 3) & 0x001F);
                }
                $data .= pack('v', $newPixel);
            }
        }
        return $data;
    }

    /// @cond INTERNAL_DOCS

    /**
     * Decodes the S16Frame and creates a GDImage.
     * TODO: Internal documentation S16Frame::Decode()
     * @throws Exception
     */
    protected function decode() {
        if ($this->decoded) {
            return $this->gdImage;
        }
        if ($this->getWidth() == 0 || $this->getHeight() == 0)
            throw new Exception('Cannot decode element with zero width or height');
        $image = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        if (empty($image))
            throw new Exception('Failed to create imagetruecolor in S16');
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        $this->reader->seek($this->offset);
        for ($y = 0; $y < $this->getHeight(); $y++) {
            for ($x = 0; $x < $this->getWidth(); $x++) {
                $pixel = $this->reader->readInt(2);
                $red = 0;
                $green = 0;
                $blue = 0;
                if ($this->encoding == "565") {
                    $red = ($pixel & 0xF800) >> 8;
                    $green = ($pixel & 0x07E0) >> 3;
                    $blue = ($pixel & 0x001F) << 3;
                } else if ($this->encoding == "555") {
                    $red = ($pixel & 0x7C00) >> 7;
                    $green = ($pixel & 0x03E0) >> 2;
                    $blue = ($pixel & 0x001F) << 3;
                }
                if ($red + $green + $blue === 0) {
                    $colour = $transparent;
                } else {
                    $colour = imagecolorallocate($image, $red, $green, $blue);
                }
                imagesetpixel($image, $x, $y, $colour);
            }
        }
        $this->gdImage = $image;
        $this->decoded = true;
        return $image;
    }
    /// @endcond
}