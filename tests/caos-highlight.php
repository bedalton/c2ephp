<?php
include(dirname(__FILE__).'/../src/CAOS/highlight/highlight.php');
$cos = file_get_contents('tests/ant.cos');
$highLighter = new \C2ePhp\CAOS\Highlight\CAOSHighlighter('C3');
$highlighted = $highLighter->highlightScript($cos);
echo <<<HTML
<html lang="en">
    <head>
        <title>Caos Test</title>
        <link rel="stylesheet" type="text/css" href="highlight.css" />
    </head>
    <body>
HTML;


echo <<<HTML
    </body>
</html>
HTML;
?>