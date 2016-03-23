<?php

$old_sizes = json_decode(file_get_contents("sizes.json"), true);

$sizes = [];

$dir = '/var/log/apache2/domlogs';

$files = scandir($dir);

$result = [];

foreach ($files as $file)
{
    if($file != '.' and $file != '..')
    {
        $sizes[$file] = filesize($dir . '/' . $file);
    }

    $diff = calculateDiff($file, $sizes[$file], $old_sizes);

    $result[$file] = $diff;
}

asort($result);

print_r($result);

//file_put_contents("sizes.json", json_encode($sizes));

function calculateDiff($file, $size, &$old_sizes)
{
    if(isset($old_sizes[$file]))
    {
        if($old_sizes[$file] != 0)
        {
            return ($size - $old_sizes[$file])/1024/1024;
        }
    }

    return 0;
}
?>