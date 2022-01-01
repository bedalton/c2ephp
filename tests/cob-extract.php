<?php

use C2ePhp\Agents\COB\COB;
use C2ePhp\Support\FileReader;

$cob = new COB(new FileReader($argv[1]));

$blocks = $cob->GetBlocks(COB_BLOCK_FILE);
foreach($blocks as $block) {
  print $block->GetName()."\n";
  $fh = fopen($block->GetName(),'wb');
  fwrite($fh,$block->GetContents());
  fclose($fh);
}
