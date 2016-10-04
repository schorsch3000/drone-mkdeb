#!/usr/bin/env php
<?php
$lopts = [
    'name:',
    'version:',
    'root:',
    'appendbuilddate',
    'section::',
    'priority::',
    'architecture::',
    'depends::',
    'maintainer::',
    'description:'
];
$options = getopt('', $lopts);
if (!is_dir($options['root'])) {
    echo "root ", $options['root'], "is not a directory\n";
    die(1);
}
$options['root'] = realpath($options['root']);

chdir(__DIR__);


include 'vendor/autoload.php';

$defaults = [
    'appendbuilddate' => false,
    'section' => 'base',
    'priority' => 'optional',
    'architecture' => 'amd64',
    'depends' => '',
    'maintainer' => "Dirk Heilig <dirk@hheilig-online.com>"
];
foreach ($defaults as $k => $v) {
    if (isset($options[$k]) and substr($k, -1) !== ':') {
        $options[$k] = true;
    }
    if (!isset($options[$k])) {
        $options[$k] = $v;
    }
}
if (count($lopts) > count($options)) {
    echo <<<EOH

Invalid options.
Availible options are:
--name              required
--version           required
--root              required, path to the root-fs
--appendbuilddate   flag, defaults to false
--section           defaults to base
--priority          defaults to optional
--architecture      defaults to amd64
--depends           optional
--maintainer        defaults to me
--description       required, may be multilines

EOH;
    die(1);
}
if ($options['appendbuilddate']) {
    $options['versionSuffix'] = "-" . date("YmdHi");
}else{
    $options['versionSuffix']='';
}
chdir($options['root']);
@mkdir('DEBIAN');
$m = new Mustache_Engine;
$control = $m->render(file_get_contents(__DIR__ . '/control.mustache'), $options); // "Hello World!
file_put_contents("DEBIAN/control", $control);
chdir('..');
system("dpkg-deb --build " . escapeshellarg($options['root'])." ".escapeshellarg($options['name'].'-'.$options['version'].$options['versionSuffix'].'.deb'));