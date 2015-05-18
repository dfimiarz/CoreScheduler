<?php

require_once __DIR__ . '/../ext/Symfony/Component/ClassLoader/Psr4ClassLoader.php';

use Symfony\Component\ClassLoader\Psr4ClassLoader;

$loader = new Psr4ClassLoader();
$loader->addPrefix('Symfony\\Component\\HttpFoundation', __DIR__ . '/../ext/Symfony/Component/HttpFoundation');
$loader->addPrefix('Symfony\\Component\\Yaml', __DIR__ . '/../ext/Symfony/Component/Yaml');
$loader->addPrefix('ccny\\scidiv\\cores\\components', __DIR__ . '/../ccny/scidiv/cores/components');
$loader->addPrefix('ccny\\scidiv\\cores\\model', __DIR__ . '/../ccny/scidiv/cores//model');
$loader->addPrefix('ccny\\scidiv\\cores\\view', __DIR__ . '/../ccny/scidiv/cores//view/');
$loader->addPrefix('ccny\\scidiv\\cores\\ctrl', __DIR__ . '/../ccny/scidiv/cores//ctrl/');
$loader->register();