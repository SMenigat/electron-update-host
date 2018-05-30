<?php

namespace App\Http\Controllers;

use App\VersionManager;
use App\VersionScanner;
use App\PlatformDetector;
use Illuminate\Http\Request;
use splitbrain\PHPArchive\Tar;

class UpdateController
{
    public function uploadUpdate($version, Request $request)
    {
        $versionManager = new VersionManager();
        $versionScanner = new VersionScanner();

        // check if this upload is password protected
        $passwordHeader = env('UPLOAD_PASSWORD_HEADER', false);
        $uploadPassword = env('UPLOAD_PASSWORD', false);
        if ($passwordHeader && $uploadPassword) {

            // check if password header is set correctly
            $sentPw = trim($request->header($passwordHeader));
            $uploadPassword = trim($uploadPassword);
            if ($sentPw !== $uploadPassword) {
                return response("Upload password wrong.", 401);
            }
        }

        $updateDir = $versionManager->getUpdateDir();
        $tempDir = sys_get_temp_dir();
        $uploadFileName = "${version}.tar.gz";
        $fullFilePath = "${tempDir}/${uploadFileName}";

        if ($request->hasFile('app-update') && $request->file('app-update')->isValid()) {

            // delete contents of update dir
            $versionManager->clearUpdateDirectory();

            // move tar into update folder
            $request->file('app-update')->move(
                $tempDir,
                $uploadFileName
            );

            // unpack tar into update dir
            $tar = new Tar();
            $tar->open($fullFilePath);
            $tar->extract($updateDir);

            // write current version file
            $versionManager->setCurrentVersion($versionScanner->parseToken($version));

            // remove temp file
            if (file_exists($fullFilePath)) {
                unlink($fullFilePath);
            }

            return response('Ok', 200);
        }
        return response('Could not find app-update', 400);
    }

    public function checkUpdate($platform, $version)
    {
        $versionManager = new VersionManager();
        $versionScanner = new VersionScanner();
        $requestedPlattform = PlatformDetector::detect($platform);

        if ($versionManager->updateAvailable(
            $versionScanner->parseToken($version),
            $requestedPlattform
        )) {
            $currentVersion = $versionManager->getCurrentVersion();
            $response = [
                "latestVersion" => $currentVersion->currentVersion,
                "updatePath"  => url("/update/${requestedPlattform}/latest"),
            ];
            return response()->json($response);
        }
        return response('No update available.', 204);
    }

    public function downloadLatest($platform) {
        $versionManager = new VersionManager();
        $currentVersion = $versionManager->getCurrentVersion();
        $updateFilePath = $versionManager->getUpdateFilePathByPlatform($platform);

        if ($updateFilePath) {
            return response()->download($updateFilePath, $currentVersion->$platform);
        }
        return response('No update available.', 204);
    }
}
