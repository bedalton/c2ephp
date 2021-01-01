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

    /// @endcond

    /**
     * Instantiate a new SPRFrame
     *
     * If you're creating your own SPRFrame.
     * @see http://php.net/image
     * @param IReader|resource $reader Either an IReader or a GD Image resource
     * @param int $width Ignored when creating an SPRFrame from a GD image.
     * @param int $height Ignored when creating an SPRFrame from a GD image.
     * @param int $offset How far through the IReader the SPRFrame is. May not ever be used.
     * @throws Exception
     */
    public function __construct($reader, $width = 0, $height = 0, $offset = false) {
        if ($reader instanceof IReader) {
            $this->reader = $reader;
            parent::__construct($width, $height);
            if ($offset !== false) {
                $this->offset = $offset;
            } else {
                $this->offset = $this->reader->getPosition();
            }

            //initialise palette if necessary.
            if (empty(self::$sprToRGB)) {
                $paletteReader = new FileReader(dirname(__FILE__).'/palette.dta');
                for ($i = 0; $i < 256; $i++) {
                    self::$sprToRGB[$i] = array('r'=>$paletteReader->readInt(1)*4, 'g'=>$paletteReader->readInt(1)*4, 'b'=>$paletteReader->readInt(1)*4);
                }
                unset($paletteReader);
            }
        } else if (get_resource_type($reader) == 'gd') {
            parent::__construct(imagesx($reader), imagesy($reader), true);
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

    /// @brief Decodes the SPRFrame into a gd image.
    protected function decode() {
        $image = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        $this->reader->seek($this->offset);
        for ($y = 0; $y < $this->getHeight(); $y++)
        {
            for ($x = 0; $x < $this->getWidth(); $x++)
            {
                $colour = self::$sprToRGB[$this->reader->readInt(1)];
                imagesetpixel($image, $x, $y, imagecolorallocate($image, $colour['r'], $colour['g'], $colour['b']));
            }
        }
        $this->gdImage = $image;
    }

    /// @endcond


    /// @brief Encodes the SPRFrame.
    /**
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
                $data .= pack('C', $this->rgbToSPR($color['red'], $color['green'], $color['blue']));
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
    private function rgbToSPR($r, $g, $b) {
        //start out with the maximum distance.
        $minDistance = ($r ^ 2)+($g ^ 2)+($b ^ 2);
        $minKey = 0;
        foreach (self::$sprToRGB as $key => $colour) {
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
}