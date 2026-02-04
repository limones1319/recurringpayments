<?php
require '/var/www/html/config/config.inc.php';

if ($argc < 3) {
    fwrite(STDERR, "Usage: php ps_module_deploy.php <module_name> <zip_path>\n");
    exit(2);
}

$moduleName = $argv[1];
$zipPath = $argv[2];

if (!file_exists($zipPath)) {
    fwrite(STDERR, "Zip not found: {$zipPath}\n");
    exit(2);
}

function rrmdir($dir)
{
    if (!is_dir($dir)) {
        return;
    }
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }
    rmdir($dir);
}

function rcopy($src, $dst)
{
    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($items as $item) {
        $target = $dst . DIRECTORY_SEPARATOR . $items->getSubPathName();
        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
        } else {
            copy($item->getPathname(), $target);
        }
    }
}

$tmpDir = sys_get_temp_dir() . '/ps_module_' . uniqid();
if (!mkdir($tmpDir, 0755, true)) {
    fwrite(STDERR, "Failed to create temp dir: {$tmpDir}\n");
    exit(2);
}

$zip = new ZipArchive();
if ($zip->open($zipPath) !== true) {
    rrmdir($tmpDir);
    fwrite(STDERR, "Failed to open zip: {$zipPath}\n");
    exit(2);
}
if (!$zip->extractTo($tmpDir)) {
    $zip->close();
    rrmdir($tmpDir);
    fwrite(STDERR, "Failed to extract zip: {$zipPath}\n");
    exit(2);
}
$zip->close();

$moduleRoot = $tmpDir . DIRECTORY_SEPARATOR . $moduleName;
if (!is_dir($moduleRoot)) {
    $dirs = glob($tmpDir . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
    if (count($dirs) === 1) {
        $moduleRoot = $dirs[0];
    }
}
if (!is_dir($moduleRoot)) {
    rrmdir($tmpDir);
    fwrite(STDERR, "Module folder not found in zip for {$moduleName}\n");
    exit(2);
}

$destPath = _PS_MODULE_DIR_ . $moduleName;
rcopy($moduleRoot, $destPath);
@exec('chown -R www-data:www-data ' . escapeshellarg($destPath));

$module = Module::getInstanceByName($moduleName);
if (!$module) {
    rrmdir($tmpDir);
    fwrite(STDERR, "Module not found after deploy: {$moduleName}\n");
    exit(2);
}

$db = Db::getInstance();
$installedVersion = $db->getValue(
    'SELECT `version` FROM `' . _DB_PREFIX_ . 'module` WHERE `name` = \'' . pSQL($moduleName) . '\''
);
$codeVersion = $module->version;
$upgrade = [
    'installed_version' => $installedVersion,
    'applied' => [],
];

if ($installedVersion && version_compare($codeVersion, $installedVersion, '>')) {
    $upgradeDir = _PS_MODULE_DIR_ . $moduleName . '/upgrade';
    if (is_dir($upgradeDir)) {
        $files = glob($upgradeDir . '/install-*.php');
        $versions = [];
        foreach ($files as $file) {
            if (preg_match('/install-([0-9.]+)\\.php$/', $file, $m)) {
                $versions[$m[1]] = $file;
            }
        }
        uksort($versions, 'version_compare');
        foreach ($versions as $ver => $file) {
            if (version_compare($ver, $installedVersion, '>') && version_compare($ver, $codeVersion, '<=')) {
                include_once $file;
                $fn = 'upgrade_module_' . str_replace('.', '_', $ver);
                if (function_exists($fn)) {
                    $ok = (bool)$fn($module);
                    if (!$ok) {
                        rrmdir($tmpDir);
                        fwrite(STDERR, "Upgrade failed at {$ver} for {$moduleName}\n");
                        exit(1);
                    }
                    $upgrade['applied'][] = $ver;
                }
            }
        }
    }
    Module::upgradeModuleVersion($moduleName, $codeVersion);
}

$module = Module::getInstanceByName($moduleName);
$dbVersion = $db->getValue(
    'SELECT `version` FROM `' . _DB_PREFIX_ . 'module` WHERE `name` = \'' . pSQL($moduleName) . '\''
);
rrmdir($tmpDir);

echo json_encode([
    'ok' => true,
    'module' => $moduleName,
    'code_version' => $module->version,
    'db_version' => $dbVersion,
    'upgrade' => $upgrade,
], JSON_PRETTY_PRINT);
