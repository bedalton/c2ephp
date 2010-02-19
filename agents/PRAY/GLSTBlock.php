<?php

require_once(dirname(__FILE__).'/../../support/StringReader.php');
require_once(dirname(__FILE__).'/../../support/Archiver.php');
require_once(dirname(__FILE__).'/PrayBlock.php');

define('GLST_FORMAT_UNKNOWN',0);
define('GLST_FORMAT_C3',1);
define('GLST_FORMAT_DS',2);
class GLSTBlock extends PrayBlock {
	private $history;
	private $format = GLST_FORMAT_UNKNOWN;
	public function GLSTBlock(&$prayfile,$name,$content,$flags) {
		parent::PrayBlock($prayfile,$name,$content,$flags);
		$this->Decode();
//		print_r($this->history);
	}
	private function Decompress() {
			$content = $this->GetData();
			if($content{0} == 'C') {
				$content = DeArchive($content);
				if($content !== false) {
					$this->SetData($content);
					return true;
				}
				echo 'GLST information is invalid!';
				return false;
			}
			print('Not decompressing.');
	}
	public function Decode() {
		$reader = new StringReader($this->GetData());
		$firstchar = $reader->Read(1);
		if($firstchar == 'C') { //Still compressed
			if($this->Decompress()) {
				return $this->Decode();
			}
			return false;
		} else {
			if($firstchar == chr(0x27)) {
				//ds
				$this->format = GLST_FORMAT_DS;
			} else if($firstchar == chr(0x0C)) {
				//c3
				$this->format = GLST_FORMAT_C3;
			}
			//Good. Let's begin.
			//bunch of bytes I don't get. (always seemed to be null, I think)
			$reader->Read(3); // 3 nulls.
			if($reader->ReadInt(4)!=1) { //always 1.
				return false;
			}
			$this->history['information'] = array(
			'moniker'			=> $reader->Read($reader->ReadInt(4)),
			'moniker2'			=> $reader->Read($reader->ReadInt(4)),
			'name'				=> $reader->Read($reader->ReadInt(4)),
			'gender'			=> $reader->ReadInt(4),
			'genus'				=> $reader->ReadInt(4), //0 for norn, 1 for grendel, 2 for ettin
			'species'			=> $reader->ReadInt(4),
			'eventslength'		=> $reader->ReadInt(4)
			);
			if(!isset($this->history['information']['eventslength'])) {
				return false;
			}
			for($i=0;$i<$this->history['information']['eventslength'];$i++) {
				$this->DecodeEvent($reader);
			}
			
			//reading the footer
			for($i=0;$i<4;$i++) {
				$this->history['information'] += array(
					'unknown1' => $reader->ReadInt(4),
					'unknown2' => $reader->ReadInt(4),
					'unknown3' => $reader->ReadInt(4),
					'warpveteran' => (($reader->ReadInt(4)==1)?1:0),
					'unknown4' => $reader->Read($reader->ReadInt(4))
				);
			}
			return $this->history;
		} /*else {
			print('Unexpected character: '.$firstchar.' ('.ord($firstchar).')');
			return false;
		}*/
	}
	private function DecodeEvent(&$reader) {
		$eventNumber = $reader->ReadInt(4);
		//echo 'Event '.$eventNumber."\n";
		if($eventNumber < 18) {
				$eventInfo = array(
			'eventnumber'		=> $eventNumber,
			'eventname'			=> $this->GetEventNameByNumber($eventNumber),
			'worldtime'			=> $reader->ReadInt(4),
			'creatureage'		=> $reader->ReadInt(4),
			'timestamp'			=> $reader->ReadInt(4),
			'lifestage'			=> $reader->ReadInt(4),
			'monikers'			=> array($reader->Read($reader->ReadInt(4)),$reader->Read($reader->ReadInt(4))),
			'usertext'			=> $reader->Read($reader->ReadInt(4)),
			'photograph'		=> array('name' => $reader->Read($reader->ReadInt(4))),
			'worldname'			=> $reader->Read($reader->ReadInt(4)),
			'worldUID'			=> $reader->Read($reader->ReadInt(4)),
			);
			if($this->format == GLST_FORMAT_DS) {
					$eventInfo += array(
						'DSUser'			=> $reader->Read($reader->ReadInt(4)),
						'unknown1'			=> $reader->ReadInt(4),
						'unknown2'			=> $reader->ReadInt(4)
					);
			}
			/*if($this->prayFile != null && $eventInfo['photograph']['name'] != '') {
				$eventInfo['photograph']['data'] = $this->prayFile->GetBlockByName($eventInfo['photograph']['name'].'.DSEX.photo');
				if($eventInfo['photograph']['data'] == '') {
					$eventInfo['photograph']['data'] = $this->prayFile->GetBlockByName($eventInfo['photograph']['name'].'.EXPC.photo');
				}
				$eventInfo['photograph']['data'] = $eventInfo['photograph']['data']['Content'];
				
			}*/
			$this->history['events'][] = $eventInfo;
			return true;
		}
		return false;
	}
	private function DecodeRegularEvent($eventNumber) {
		
	}
	public static function GetEventNameByNumber($eventnumber) {
		switch($eventnumber) {
			case 0:
				return 'conceived';
			case 1:
				return 'spliced';
			case 2:
				return 'engineered';
			case 3:
				return 'hatched';
			case 4:
				return 'aged';
			case 5:
				return 'exported';
			case 6:
				return 'imported';
			case 7:
				return 'died';
			case 8:
				return 'got pregnant';
			case 9:
				return 'made other creature pregnant';
			case 10:
				return 'child hatched';
			case 11:
				return 'egg laid';
			case 12:
				return 'laid egg';
			case 13:
				return 'photo taken';
			case 14:
				return 'cloned from';
			case 15:
				return 'was cloned';
			case 16:
				return 'warped out';
			case 17:
				return 'warped in';
			default:
				return 'unknown';
				
		}
	}
	public function GetGenderFromInteger($gender) {
		return ($gender==0)?'F':'M';
	}
}
?>
