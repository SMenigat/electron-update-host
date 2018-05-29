<?php

namespace App\Http\Controllers;

use App\VersionScanner;
use Illuminate\Http\Request;
use splitbrain\PHPArchive\Tar;

class UpdateController
{
    public function uploadUpdate($version, Request $request)
    {
        $updateDir = __DIR__ . '/../../../app-updates';
        $tempDir = sys_get_temp_dir();
        $uploadFileName = "${version}.tar.gz";
        $fullFilePath = "${tempDir}/${uploadFileName}";

        if ($request->hasFile('app-update') && $request->file('app-update')->isValid()) {

            // delete contents of update dir
            array_map('unlink', glob("${updateDir}/*"));

            // move tar into update folder
            $request->file('app-update')->move(
                $tempDir,
                $uploadFileName
            );

            // unpack tar into update dir
            $tar = new Tar();
            $tar->open($fullFilePath);
            $tar->extract($updateDir);

            // remove temp file
            if(file_exists($fullFilePath)) {
                unlink($fullFilePath);
            }
            
            return response('Ok', 200);
        }
        return response('Could not find app-update', 400);
    }
    public function checkUpdate($platform, $version)
    {

        $versionScanner = new VersionScanner();

        //
        $response = [
            'scannedVersion' => $versionScanner->parseToken($version),
            'updateAvailable' => true,
            'currentVersion' => '0.8.3',
            'mac' => '..../mac',
            'windows' => '..../windows',
            'linux' => '..../linux',
        ];

        return response()->json($response);
    }
}
