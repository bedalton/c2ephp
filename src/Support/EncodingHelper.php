<?php

namespace C2ePhp\Support;

class EncodingHelper {


	static function decodeCreaturesEncoding($string, bool $latin = FALSE) {
		if (FileReader::CP_DECODING_DEFAULT) {
			return $string;
		}
		return mb_convert_encoding($string, 'UTF-8', $latin ? 'iso-8859-1' : 'Windows-1252');
	}


	static function replaceInvalidChars($string, $char) {
		$string = htmlspecialchars_decode(htmlspecialchars($string, ENT_SUBSTITUTE));
		return str_replace("\u{FFFD}", $char, $string);
	}

	static function escapeInvalidForLikeStatement($string) {
		return self::replaceInvalidChars(str_replace(['_', '%'], ['\_', '\%'], $string), '_');
	}

}