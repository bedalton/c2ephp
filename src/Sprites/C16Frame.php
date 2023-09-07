<?php

namespace C2ePhp\Sprites;

use C2ePhp\Support\IReader;
use Exception;
use GdImage;

/**
 * Class for a single image in a C16File
 */
class C16Frame extends SpriteFrame {
    /// @cond INTERNAL_DOCS

    private $encoding;

    private $lineOffset = [];

    private $reader;
    private $offset;

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

            $width = $this->reader->readInt(2);
            $height = $this->reader->readInt(2);

            parent::__construct($width, $height);

            for ($x = 0; $x < ($height - 1); $x++) {
                $this->lineOffset[$x] = $this->reader->readInt(4);
            }
        } else if (SpriteFrame::isGD($reader)) {
            $this->encoding = ($encoding == '555') ? '555' : '565';
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
        $image = imagecreatetruecolor($this->getWidth(),
            $this->getHeight());

        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        $this->reader->seek($this->offset);
        for ($y = 0; $y < $this->getHeight(); $y++) {
            for ($x = 0; $x < $this->getWidth();) {
                $run = $this->reader->readInt(2);
                if (($run & 0x0001) > 0) {
                    $run_type = 'colour';
                } else {
                    $run_type = 'black';
                }
                $run_length = ($run & 0x7FFF) >> 1;
                $z = $x + $run_length;
                if ($run_type == 'black') {
                    for (; $x < $z; $x++) {
                        imagesetpixel($image, $x, $y, $transparent);
                    }
                } else //colour run
                {
                    for (; $x < $z; $x++) {
                        $pixel = $this->reader->readInt(2);
                        if ($this->encoding == '565') {
                            $red = ($pixel & 0xF800) >> 8;
                            $green = ($pixel & 0x07E0) >> 3;
                            $blue = ($pixel & 0x001F) << 3;
                        } else if ($this->encoding == '555') {
                            $red = ($pixel & 0x7C00) >> 7;
                            $green = ($pixel & 0x03E0) >> 2;
                            $blue = ($pixel & 0x001F) << 3;
                        } else {
                            throw new Exception("Invalid encoding: $this->encoding");
                        }
                        if ($red + $green + $blue === 0) {
                            $colour = $transparent;
                        } else {
                            $colour = imagecolorallocate($image, $red, $green, $blue);
                        }
                        imagesetpixel($image, $x, $y, $colour);
                    }
                }
                if ($x == $this->getWidth()) {
                    $this->reader->skip(2);
                }
            }
        }
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
        $data = '';
        $lineOffsets = [];
        for ($y = 0; $y < $this->getHeight(); $y++) {
            $wasBlack = 0;
            $runLength = 0;
            if ($y > 0) {
                $lineOffsets[] = strlen($data);
            }
            $colourRunData = '';
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


                // if isBlack !== wasBlack
                if (($newPixel == 0) !== $wasBlack || $runLength > 32766) {
                    //end the run if this isn't the first run
                    if ($wasBlack !== 0) {

                        //output data.
                        $run = $runLength << 1;
                        if ($wasBlack) {
                            $data .= pack('v', $run);
                        } else {
                            $run = $run | 1;
                            $data .= pack('v', $run);
                            $data .= $colourRunData;
                        }
                    }
                    //start a new run
                    if ($newPixel == 0) {
                        $wasBlack = true;
                        $colourRunData = '';
                    } else {
                        $wasBlack = false;
                        $colourRunData = pack('v', $newPixel);
                    }
                    $runLength = 1;

                } else {
                    if (!$wasBlack) {
                        $colourRunData .= pack('v', $newPixel);
                    }
                    $runLength++;
                }

                if ($x == ($this->getWidth() - 1)) {
                    //end run and output data.
                    $run = $runLength << 1;
                    if ($wasBlack) {
                        $data .= pack('v', $run);

                    } else {
                        $run = $run | 1;
                        $data .= pack('v', $run);
                        $data .= $colourRunData;
                        $colourRunData = '';
                    }
                }
            }
            //line terminating zero tag.
            $data .= "\0\0";
        }
        //image terminating zero tag
        $data .= "\0\0";
        return ['lineOffsets' => $lineOffsets, 'data' => $data];
    }
}
