<?php
/**
 * @throws Exception
 */
function deArchive($data) {
	if(is_string($data)) {
		if(substr($data,0,55) == 'Creatures Evolution Engine - Archived information file.') {
			$data = substr($data,strpos($data,chr(0x1A).chr(0x04))+2);
			return gzuncompress($data);
		}
		throw new Exception('Couldn\'t de-archive -- Probably invalid file');
	} else if (is_resource($data)) {
		//coming soon
		return FALSE;
	}
	return FALSE;
}

/**
 * @param string $data
 * @param resource|null $fileHandle
 * @return false|string
 */
function archive(string $data, $fileHandle=null) {
	if(is_resource($fileHandle)) {
		return false;
	}
	$data = gzcompress($data);
	return 'Creatures Evolution Engine - Archived information file. zLib 1.13 compressed.' .chr(0x1A).chr(0x04).$data;

}
