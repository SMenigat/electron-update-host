<?php

namespace App;

class VersionManager
{
    private $updateDir = __DIR__ . '/../app-updates';
    private $versionFile = 'currentVersion.json';

    private $versionDefault = [
        "currentVersion" => '',
        "windows" => null,
        "mac" => null,
        "linux" => null,
    ];

    /**
     * Returns path to update directory.
     *
     * @return string
     */
    public function getUpdateDir()
    {
        return $this->updateDir;
    }

    /**
     * Returns path to version file.
     *
     * @return string
     */
    public function getVersionFileFullPath()
    {
        $updateDir = $this->updateDir;
        $versionFile = $this->versionFile;
        return "${updateDir}/${versionFile}";
    }

    private function getFirstFileOfDir($dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            return @$files[2];
        }
        return null;
    }

    private function constructVersionFile($version)
    {
        $updateDir = $this->updateDir;
        $versionFile = array_merge($this->versionDefault, [
            "currentVersion" => $version,
            "windows" => $this->getFirstFileOfDir("${updateDir}/windows"),
            "mac" => $this->getFirstFileOfDir("${updateDir}/mac"),
            "linux" => $this->getFirstFileOfDir("${updateDir}/linux"),
        ]);

        return json_encode($versionFile);
    }

    private function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function clearUpdateDirectory()
    {
        $this->delTree($this->updateDir);
    }

    public function setCurrentVersion($version)
    {
        $versionFile = $this->getVersionFileFullPath();
        file_put_contents($versionFile, $this->constructVersionFile($version));
    }

    public function getCurrentVersion()
    {
        $versionFile = $this->getVersionFileFullPath();
        if (file_exists($versionFile)) {
            return json_decode(file_get_contents($versionFile));
        } else {
            return (object) $this->versionDefault;
        }
    }

    public function getUpdateFilePathByPlatform($platform)
    {
        $currentVersion = $this->getCurrentVersion();
        $updateFile = $currentVersion->$platform;
        if ($updateFile) {
            $updateDir = $this->updateDir;
            return "${updateDir}/${platform}/${updateFile}";
        }
        return null;
    }

    public function updateAvailable($requestedVersion, $platform)
    {
        $currentVersion = $this->getCurrentVersion();
        if (version_compare($requestedVersion, $currentVersion->currentVersion) === -1) {
            return ($currentVersion->$platform !== null);
        }
        return false;
    }
}
