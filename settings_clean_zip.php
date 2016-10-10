<?php

shell_exec('rm -fr '.__DIR__.'/api/pack*.zip');
header('Location: settings.php');