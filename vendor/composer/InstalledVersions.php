<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;








class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => 'dev-main',
    'version' => 'dev-main',
    'aliases' => 
    array (
    ),
    'reference' => '4ec8afa60c76a11a266f8abfaa7f9b21c79a7e4e',
    'name' => 'workerman/webman',
  ),
  'versions' => 
  array (
    'brick/varexporter' => 
    array (
      'pretty_version' => '0.6.0',
      'version' => '0.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'af98bfc2b702a312abbcaff37656dbe419cec5bc',
    ),
    'carbonphp/carbon-doctrine-types' => 
    array (
      'pretty_version' => '3.2.0',
      'version' => '3.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '18ba5ddfec8976260ead6e866180bd5d2f71aa1d',
    ),
    'doctrine/annotations' => 
    array (
      'pretty_version' => '2.0.2',
      'version' => '2.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '901c2ee5d26eb64ff43c47976e114bf00843acf7',
    ),
    'doctrine/inflector' => 
    array (
      'pretty_version' => '2.0.10',
      'version' => '2.0.10.0',
      'aliases' => 
      array (
      ),
      'reference' => '5817d0659c5b50c9b950feb9af7b9668e2c436bc',
    ),
    'doctrine/lexer' => 
    array (
      'pretty_version' => '3.0.1',
      'version' => '3.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd',
    ),
    'firebase/php-jwt' => 
    array (
      'pretty_version' => 'v6.11.1',
      'version' => '6.11.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd1e91ecf8c598d073d0995afa8cd5c75c6e19e66',
    ),
    'graham-campbell/result-type' => 
    array (
      'pretty_version' => 'v1.1.3',
      'version' => '1.1.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '3ba905c11371512af9d9bdd27d99b782216b6945',
    ),
    'guzzlehttp/guzzle' => 
    array (
      'pretty_version' => '7.9.3',
      'version' => '7.9.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '7b2f29fe81dc4da0ca0ea7d42107a0845946ea77',
    ),
    'guzzlehttp/promises' => 
    array (
      'pretty_version' => '2.2.0',
      'version' => '2.2.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '7c69f28996b0a6920945dd20b3857e499d9ca96c',
    ),
    'guzzlehttp/psr7' => 
    array (
      'pretty_version' => '2.7.1',
      'version' => '2.7.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c2270caaabe631b3b44c85f99e5a04bbb8060d16',
    ),
    'hg/apidoc' => 
    array (
      'pretty_version' => 'v5.3.0',
      'version' => '5.3.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'cda7b825bfcce7e3167437a37da42b6be63a9550',
    ),
    'illuminate/bus' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c66d57011eec385055e1426d026c270aeecb05aa',
    ),
    'illuminate/collections' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => '48de3d6bc6aa779112ddcb608a3a96fc975d89d8',
    ),
    'illuminate/conditionable' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => '3ee34ac306fafc2a6f19cd7cd68c9af389e432a5',
    ),
    'illuminate/container' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => 'ed6253f7dd3a67d468b2cc7a69a657e1f14c7ba3',
    ),
    'illuminate/contracts' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f90663a69f926105a70b78060a31f3c64e2d1c74',
    ),
    'illuminate/events' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => '3edcdad2f2fe6da6802afb0a256b0f7ee00d72e9',
    ),
    'illuminate/macroable' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => 'dff667a46ac37b634dcf68909d9d41e94dc97c27',
    ),
    'illuminate/pipeline' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => '3030a131e5e9cb18c9a826428fcffc076df9dcd7',
    ),
    'illuminate/support' => 
    array (
      'pretty_version' => 'v10.48.28',
      'version' => '10.48.28.0',
      'aliases' => 
      array (
      ),
      'reference' => '6d09b480d34846245d9288f4dcefb17a73ce6e6a',
    ),
    'monolog/monolog' => 
    array (
      'pretty_version' => '2.10.0',
      'version' => '2.10.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '5cf826f2991858b54d5c3809bee745560a1042a7',
    ),
    'nesbot/carbon' => 
    array (
      'pretty_version' => '2.73.0',
      'version' => '2.73.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '9228ce90e1035ff2f0db84b40ec2e023ed802075',
    ),
    'nikic/fast-route' => 
    array (
      'pretty_version' => 'v1.3.0',
      'version' => '1.3.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '181d480e08d9476e61381e04a71b34dc0432e812',
    ),
    'nikic/php-parser' => 
    array (
      'pretty_version' => 'v5.6.2',
      'version' => '5.6.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '3a454ca033b9e06b63282ce19562e892747449bb',
    ),
    'phpoption/phpoption' => 
    array (
      'pretty_version' => '1.9.3',
      'version' => '1.9.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e3fac8b24f56113f7cb96af14958c0dd16330f54',
    ),
    'psr/cache' => 
    array (
      'pretty_version' => '3.0.0',
      'version' => '3.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'aa5030cfa5405eccfdcb1083ce040c2cb8d253bf',
    ),
    'psr/cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '2.0|3.0',
      ),
    ),
    'psr/clock' => 
    array (
      'pretty_version' => '1.0.0',
      'version' => '1.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e41a24703d4560fd0acb709162f73b8adfc3aa0d',
    ),
    'psr/clock-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/container' => 
    array (
      'pretty_version' => '2.0.2',
      'version' => '2.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
    ),
    'psr/container-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.1|2.0',
      ),
    ),
    'psr/http-client' => 
    array (
      'pretty_version' => '1.0.3',
      'version' => '1.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
    ),
    'psr/http-client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-factory' => 
    array (
      'pretty_version' => '1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
    ),
    'psr/http-factory-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => '2.0',
      'version' => '2.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
    ),
    'psr/http-message-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/log' => 
    array (
      'pretty_version' => '3.0.2',
      'version' => '3.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
    ),
    'psr/log-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0.0 || 2.0.0 || 3.0.0',
        1 => '1.0|2.0|3.0',
      ),
    ),
    'psr/simple-cache' => 
    array (
      'pretty_version' => '3.0.0',
      'version' => '3.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
    ),
    'psr/simple-cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0|2.0|3.0',
      ),
    ),
    'ralouphie/getallheaders' => 
    array (
      'pretty_version' => '3.0.3',
      'version' => '3.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '120b605dfeb996808c31b6477290a714d356e822',
    ),
    'symfony/cache' => 
    array (
      'pretty_version' => 'v6.4.24',
      'version' => '6.4.24.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd038cd3054aeaf1c674022a77048b2ef6376a175',
    ),
    'symfony/cache-contracts' => 
    array (
      'pretty_version' => 'v3.6.0',
      'version' => '3.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '5d68a57d66910405e5c0b63d6f0af941e66fc868',
    ),
    'symfony/cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.1|2.0|3.0',
      ),
    ),
    'symfony/console' => 
    array (
      'pretty_version' => 'v6.4.24',
      'version' => '6.4.24.0',
      'aliases' => 
      array (
      ),
      'reference' => '59266a5bf6a596e3e0844fd95e6ad7ea3c1d3350',
    ),
    'symfony/deprecation-contracts' => 
    array (
      'pretty_version' => 'v3.6.0',
      'version' => '3.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '63afe740e99a13ba87ec199bb07bbdee937a5b62',
    ),
    'symfony/polyfill-ctype' => 
    array (
      'pretty_version' => 'v1.32.0',
      'version' => '1.32.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a3cc8b044a6ea513310cbd48ef7333b384945638',
    ),
    'symfony/polyfill-intl-grapheme' => 
    array (
      'pretty_version' => 'v1.32.0',
      'version' => '1.32.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b9123926e3b7bc2f98c02ad54f6a4b02b91a8abe',
    ),
    'symfony/polyfill-intl-normalizer' => 
    array (
      'pretty_version' => 'v1.32.0',
      'version' => '1.32.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '3833d7255cc303546435cb650316bff708a1c75c',
    ),
    'symfony/polyfill-mbstring' => 
    array (
      'pretty_version' => 'v1.32.0',
      'version' => '1.32.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '6d857f4d76bd4b343eac26d6b539585d2bc56493',
    ),
    'symfony/polyfill-php80' => 
    array (
      'pretty_version' => 'v1.32.0',
      'version' => '1.32.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '0cc9dd0f17f61d8131e7df6b84bd344899fe2608',
    ),
    'symfony/service-contracts' => 
    array (
      'pretty_version' => 'v3.6.0',
      'version' => '3.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f021b05a130d35510bd6b25fe9053c2a8a15d5d4',
    ),
    'symfony/string' => 
    array (
      'pretty_version' => 'v6.4.24',
      'version' => '6.4.24.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f0ce0bd36a3accb4a225435be077b4b4875587f4',
    ),
    'symfony/translation' => 
    array (
      'pretty_version' => 'v6.4.24',
      'version' => '6.4.24.0',
      'aliases' => 
      array (
      ),
      'reference' => '300b72643e89de0734d99a9e3f8494a3ef6936e1',
    ),
    'symfony/translation-contracts' => 
    array (
      'pretty_version' => 'v3.6.0',
      'version' => '3.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'df210c7a2573f1913b2d17cc95f90f53a73d8f7d',
    ),
    'symfony/translation-implementation' => 
    array (
      'provided' => 
      array (
        0 => '2.3|3.0',
      ),
    ),
    'symfony/var-exporter' => 
    array (
      'pretty_version' => 'v6.4.24',
      'version' => '6.4.24.0',
      'aliases' => 
      array (
      ),
      'reference' => '1e742d559fe5b19d0cdc281b1bf0b1fcc243bd35',
    ),
    'taoser/webman-validate' => 
    array (
      'pretty_version' => 'v1.7.2',
      'version' => '1.7.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f6ceda3700cabf3e48966b845fd980faa292127e',
    ),
    'tinywan/jwt' => 
    array (
      'pretty_version' => 'v1.11.3',
      'version' => '1.11.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '1b067c998d970c252b8ad113a460922f8108b9ac',
    ),
    'topthink/think-container' => 
    array (
      'pretty_version' => 'v2.0.5',
      'version' => '2.0.5.0',
      'aliases' => 
      array (
      ),
      'reference' => '2189b39e42af2c14203ed4372b92e38989e9dabb',
    ),
    'topthink/think-helper' => 
    array (
      'pretty_version' => 'v3.1.11',
      'version' => '3.1.11.0',
      'aliases' => 
      array (
      ),
      'reference' => '1d6ada9b9f3130046bf6922fe1bd159c8d88a33c',
    ),
    'topthink/think-orm' => 
    array (
      'pretty_version' => 'v4.0.31',
      'version' => '4.0.31.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a8bebe2713e6715f002416beb4e58e6b4ed7aeb9',
    ),
    'topthink/think-template' => 
    array (
      'pretty_version' => 'v3.0.2',
      'version' => '3.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '0b88bd449f0f7626dd75b05f557c8bc208c08b0c',
    ),
    'vlucas/phpdotenv' => 
    array (
      'pretty_version' => 'v5.6.2',
      'version' => '5.6.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '24ac4c74f91ee2c193fa1aaa5c249cb0822809af',
    ),
    'voku/portable-ascii' => 
    array (
      'pretty_version' => '2.0.3',
      'version' => '2.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b1d923f88091c6bf09699efcd7c8a1b1bfd7351d',
    ),
    'webman/captcha' => 
    array (
      'pretty_version' => 'v1.0.5',
      'version' => '1.0.5.0',
      'aliases' => 
      array (
      ),
      'reference' => '0b2645b813466e4e70bff311511364080bad2ec5',
    ),
    'webman/channel' => 
    array (
      'pretty_version' => 'v2.1.0',
      'version' => '2.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '148eb5ed53bca18d7da030d709d2d831164a7c27',
    ),
    'webman/console' => 
    array (
      'pretty_version' => 'v2.1.8',
      'version' => '2.1.8.0',
      'aliases' => 
      array (
      ),
      'reference' => '2d4ce527810f91e1b4f583ec90eae24a0334ab10',
    ),
    'webman/event' => 
    array (
      'pretty_version' => 'v1.0.5',
      'version' => '1.0.5.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b1c3f6b70fd290e48288703d59bead0e28f9fb84',
    ),
    'webman/log' => 
    array (
      'pretty_version' => 'v2.1.3',
      'version' => '2.1.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '14e3f3e64e87783952417a68cf5205c1293aba69',
    ),
    'webman/multi-session' => 
    array (
      'pretty_version' => 'v2.1.0',
      'version' => '2.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a6529aad9ba004bf0c522a784465775212db30b4',
    ),
    'webman/think-cache' => 
    array (
      'pretty_version' => 'v2.1.2',
      'version' => '2.1.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'cf9dcfe9afe8d7395d2cadce51a456bcf8ee53bd',
    ),
    'webman/think-orm' => 
    array (
      'pretty_version' => 'v2.1.7',
      'version' => '2.1.7.0',
      'aliases' => 
      array (
      ),
      'reference' => '9380f0fa22b7d28926c5f7b5b8f35b068f27e846',
    ),
    'workerman/channel' => 
    array (
      'pretty_version' => 'v1.2.3',
      'version' => '1.2.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '5edb0008eae35bf2da7218d911042abd23aa4370',
    ),
    'workerman/coroutine' => 
    array (
      'pretty_version' => 'v1.1.3',
      'version' => '1.1.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'df8fc428967d512a74a8a7d80355c1d40228c9fa',
    ),
    'workerman/crontab' => 
    array (
      'pretty_version' => 'v1.0.7',
      'version' => '1.0.7.0',
      'aliases' => 
      array (
      ),
      'reference' => '74f51ca8204e8eb628e57bc0e640561d570da2cb',
    ),
    'workerman/webman' => 
    array (
      'pretty_version' => 'dev-main',
      'version' => 'dev-main',
      'aliases' => 
      array (
      ),
      'reference' => '4ec8afa60c76a11a266f8abfaa7f9b21c79a7e4e',
    ),
    'workerman/webman-framework' => 
    array (
      'pretty_version' => 'v2.1.2',
      'version' => '2.1.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f803bd867f07bb0929faef060b59a19a44186bfc',
    ),
    'workerman/workerman' => 
    array (
      'pretty_version' => 'v5.1.3',
      'version' => '5.1.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '371f3a5decb28f1bd3464ae26d47ea1a4cf0a3c5',
    ),
    'yzh52521/easyhttp' => 
    array (
      'pretty_version' => 'v1.1.3',
      'version' => '1.1.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '02bcf47eaf723520fa3905d0e6f1852168fe646c',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}

if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}








public static function getRawData()
{
@trigger_error('getRawData only returns the first dataset loaded, which may not be what you expect. Use getAllRawData() instead which returns all datasets for all autoloaders present in the process.', E_USER_DEPRECATED);

return self::$installed;
}







public static function getAllRawData()
{
return self::getInstalled();
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}





private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
