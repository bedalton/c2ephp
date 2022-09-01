<?php /** @noinspection PhpUnused */

namespace C2ePhp\Sprites;

use C2ePhp\Support\FileReader;
use C2ePhp\Support\IReader;
use C2ePhp\Support\StringReader;
use Exception;

/**
 * Class for a single frame stored in SPR format.
 * @package C2ePhp\Sprites
 */
class SPRFrame extends SpriteFrame {

    /// @cond INTERNAL_DOCS

    private $offset;
    private $reader;
    public static $sprToRGB;
	private $keepBlack = TRUE;

    /// @endcond

    /**
     * Instantiate a new SPRFrame
     *
     * If you're creating your own SPRFrame.
     * @see http://php.net/image
     * @param IReader|resource $reader Either an IReader or a GD Image resource
     * @param int $width Ignored when creating an SPRFrame from a GD image.
     * @param int $height Ignored when creating an SPRFrame from a GD image.
     * @param int|null $offset How far through the IReader the SPRFrame is. May not ever be used.
     * @throws Exception
     */
    public function __construct($reader, int $width = 0, int $height = 0, ?int $offset = NULL) {


		//initialise palette if necessary.
		if (empty(self::$sprToRGB)) {
			$json = dirname(__FILE__).'/true-palette.json';
			if (file_exists($json)) {
				self::$sprToRGB = json_decode(file_get_contents($json), TRUE);
			} else {
				$paletteReader = new FileReader(dirname(__FILE__) . '/palette.dta');
				for ($i = 0; $i < 256; $i++) {
					self::$sprToRGB[$i] = array('r' => $paletteReader->readInt(1) * 4, 'g' => $paletteReader->readInt(1) * 4, 'b' => $paletteReader->readInt(1) * 4);
				}
				unset($paletteReader);
			}
			if (count(self::$sprToRGB) < 255) {
				throw new Exception("SPR Palette data is too shallow. Not enough color entries found. Expected 255; Found: " . count(self::$sprToRGB));
			}
		}
        if ($reader instanceof IReader) {
            $this->reader = $reader;
            parent::__construct($width, $height);
            if (!empty($offset)) {
                $this->offset = $offset;
            } else {
                $this->offset = $this->reader->getPosition();
            }
        } else if (get_resource_type($reader) == 'gd') {
            parent::__construct(imagesx($reader), imagesy($reader), true);
			if (self::hasTransparency($reader)) {
				$this->keepBlack = TRUE;
			}
            $this->gdImage = $reader;
        } else {
            throw new Exception('$reader was not an IReader or a gd image.');
        }
    }


    /**
     * Flips the image on the y-axis.
     * This is really for automated use by C1 COBAgentBlocks, but
     * feel free to use it yourself, it's not going anywhere.
     * @throws Exception
     */
    public function flip() {
        if ($this->hasBeenDecoded()) {
            throw new Exception('Too late!');
        }
        $tempData = '';
        for ($i = ($this->getHeight()-1); $i > -1; $i--) {
            $tempData .= $this->reader->getSubString($this->offset+($this->getWidth())*$i, ($this->getWidth()));
        }
        $this->reader = new StringReader($tempData);
        $this->offset = 0;

    }


    /// @cond INTERNAL_DOCS

    /**
     * Decodes the SPRFrame into a gd image.
     */
    protected function decode() {
        $image = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        imagesavealpha($image, true);
		imagealphablending($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        $this->reader->seek($this->offset);
		$this->keepBlack = TRUE;
        for ($y = 0; $y < $this->getHeight(); $y++)
        {
            for ($x = 0; $x < $this->getWidth(); $x++)
            {
                $colour = $this->reader->readInt(1);
                if ($colour === 0) {
                    $colour = $transparent;
                } else {
                    $colour = self::$sprToRGB[$colour];
                    $colour = imagecolorallocate($image, $colour['r'], $colour['g'], $colour['b']);
                }
                imagesetpixel($image, $x, $y, $colour);
            }
        }
        $this->gdImage = $image;
    }

    /// @endcond

    /**
     * Encodes the SPRFrame.
     *
     * Called automatically by SPRFile::Compile() \n
     * Generally end-users won't want a single frame of SPR data,
     * so add it to an SPRFile and call SPRFile::Compile().
     * @return string binary string of SPR data.
     */
    public function encode() {
        $data = '';
        for ($y = 0; $y < $this->getHeight(); $y++) {
            for ($x = 0; $x < $this->getWidth(); $x++) {
                $color = $this->getPixel($x, $y);
				if ($color['alpha'] >= 64) {
					$color = 0;
				} else {
					$color = $this->rgbToSPR($color['red'], $color['green'], $color['blue'], $this->keepBlack);
				}
                $data .= pack('C', $color);
            }
        }
        return $data;
    }

    /// @cond INTERNAL_DOCS

    /**
     * Chooses the nearest colour in the SPR palette.
     * Runs in O(n) time.
     * @param int $r red
     * @param int $g green
     * @param int $b blue
     * @return int|string
     */
    private function rgbToSPR(int $r, int $g, int $b, bool $keepBlack) {
        //start out with the maximum distance.
        $minDistance = ($r ^ 2)+($g ^ 2)+($b ^ 2);
        $minKey = 0;
        foreach (self::$sprToRGB as $key => $colour) {
			if ($key === 0 && $keepBlack) {
				continue;
			}
			if (array_sum(array_values(array_slice($colour, 0, 3))) + $r + $g + $b === 0) {
				return $key;
			}
			$distance = pow(($r-$colour['r']), 2)+pow(($g-$colour['g']), 2)+pow(($b-$colour['b']), 2);
            if ($distance == 0) {
                return $key;
            } else if ($distance < $minDistance) {
                $minKey = $key;
                $minDistance = $distance;
            }
        }
        return $minKey;
    }
    /// @endcond

	private static function hasTransparency($gdImage) {
		$width = imagesx($gdImage);
		$height = imagesy($gdImage);
		// Check corners first as they are most likely to be transparent
		$corners = [
			[0, 0],
			[0, $height - 1],
			[$width - 1, 0],
			[$width - 1, $height - 1]
		];
		$isTransparent = function($gdImage, $x, $y) {
			$color = imagecolorat($gdImage, $x, $y);
			$transparency = ($color >> 24) & 0x7F;
			return $transparency >= 64;
		};
		foreach ($corners as [$x, $y]) {
			if ($isTransparent($gdImage, $x, $y)) {
				return TRUE;
			}
		}

		for($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				if ($isTransparent($gdImage, $x, $y)) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}