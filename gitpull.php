<?php

$result = shell_exec('git pull https://github.com/ebukaodini/globalsportsfans.git master');

$result = str_replace("\n", "<br>", $result);

exit("$result<br>Done!");