<?php
include('../src/Agents/COB/COB.php');
require_once('../src/Support/FileReader.php');

$cob = new COB(new FileReader($argv[1]));

$blocks = $cob->GetBlocks(COB_BLOCK_AGENT);
print $blocks[0]->GetThumbnail()->ToPNG();