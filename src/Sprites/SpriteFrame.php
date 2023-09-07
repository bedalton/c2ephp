<?php /** @noinspection PhpUnused */

namespace C2ePhp\Sprites;

use Exception;
use GdImage;
use ReflectionClass;

/**
 * Class representing a single frame of a sprite.
 *
 * All SpriteFrame classes may also be used in the absence of a
 * parent SpriteFile.
 */
abstract class SpriteFrame {

    /// @cond INTERNAL_DOCS

    private $decoded;
    protected $gdImage;
    private $width;
    private $height;

    /// @endcond

    /// @cond INTERNAL_DOCS

    /**
     * Initialises a SpriteFrame
     *
     * Width and height must both be non-zero.
     * @param int $width
     * @param int $height
     * @param bool $decoded
     * @throws Exception
     * @see C16Frame::C16Frame()
     */
    public function __construct(int $width, int $height, bool $decoded = false) {
        if ($width == 0) {
            throw new Exception('Zero width');
        } else if ($height == 0) {
            throw new Exception('Zero height');
        }
        $this->width = $width;
        $this->height = $height;
        $this->decoded = $decoded;
    }

	protected static function isGD($resource) {
		return $resource instanceof \GdImage || (is_resource($resource) && get_resource_type($resource) == 'gd');
	}

    protected function hasBeenDecoded() {
        return $this->decoded;
    }

    /// @endcond

    /**
     * Gets the GD Image resource for this sprite frame.
     * @return resource|GdImage GD image resource. See http://php.net/image
     */
    public function getGDImage() {
        $this->ensureDecoded();
        return $this->gdImage;
    }

    /**
     * Gets the width of the frame in pixels
     */
    public function getWidth() {
        return $this->width;
    }
    /**
     * Gets the height of the frame in pixels
     * @return integer
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Gets the colour of the pixel at the given position.
     * Returns an array like the following:
     * <pre> Array
     * (
     *    [red] => 226
     *    [green] => 222
     *    [blue] => 252
     *    [alpha] => 0
     * )</pre>
     * @see http://php.net/imagecolorsforindex
     * @param int $x The x-coordinate of the pixel to get.
     * @param int $y The y-coordinate of the pixel to get.
     * @return int[] An associative array containing the keys 'red','green','blue','alpha'.
     */
    public function getPixel(int $x, int $y) {
        $this->ensureDecoded();
        $colorIndex = imagecolorat($this->gdImage, $x, $y);
        return imagecolorsforindex($this->gdImage, $colorIndex);
    }

    /**
     * Sets the colour of a pixel.
     *
     * @param int $x The x-coordinate of the pixel to change.
     * @param int $y The y-coordinate of the pixel to change.
     * @param int $r The red component of the pixel. 0-255.
     * @param int $g The green component of the pixel. 0-255.
     * @param int $b The blue component of the pixel. 0-255.
     */
    public function setPixel(int $x, int $y, int $r, int $g, int $b) {
        $this->ensureDecoded();
        imagesetpixel($this->gdImage, $x, $y, imagecolorallocate($this->gdImage, $r, $g, $b));
    }

    /// @cond INTERNAL_DOCS

    /**
     * Ensures that the SpriteFrame has been decoded
     * This causes $gdImage to point to a usable GD Image resource
     * if it doesn't already.
     */
    function ensureDecoded() {
        if (!$this->decoded) {
            $this->decode();
        }

        $this->decoded = true;
    }

    /// @endcond

    /// @cond INTERNAL_DOCS

    /**
     * Decodes the SpriteFrame and creates gdImage
     */
    protected abstract function decode();

    /**
     * Encodes the SpriteFrame and returns a binary string.
     * @return mixed
     */
    public abstract function encode();
    /// @endcond

    /**
     * Converts this SpriteFrame into one of another type.
     *
     * This is called internally by SpriteFile, and is not really
     * for public use. A way of converting I'd approve of more is to
     * create a SpriteFile of the right type and then call AddFrame.
     * @param string $type The type of SpriteFrame to convert this to.
     * @return C16Frame|S16Frame|SPRFrame|SpriteFrame
     * @throws Exception
     * @see SpriteFile::addFrame()
     * @internal
     * If you create your own SpriteFrame and SpriteFile formats, and
     * they use names longer than 3 characters, you will need to
     * override this function in your class to provide extra magic.
     * @endinternal
     */
    public function toSpriteFrame(string $type) {
        $this->ensureDecoded();
		$thisType = (new ReflectionClass($this))->getShortName();
        if (substr($thisType, 0, 3) == $type && substr($thisType, 3) == 'Frame') {
            return $this;
        }
        switch ($type) {
            case 'C16':
                return new C16Frame($this->getGDImage());
            case 'S16':
                return new S16Frame($this->getGDImage());
            case 'SPR':
                return new SPRFrame($this->getGDImage());
            default:
                throw new Exception('Invalid sprite type ' . $type . '.');
        }
    }

    /**
     * Converts this SpriteFrame into a PNG.
     *
     * @return string binary string in PNG format, ready for output! :)
     */
    public function toPNG() {
        $this->ensureDecoded();
        ob_start();
        imagepng($this->getGDImage());
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }
}