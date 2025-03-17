<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ShowSerialPasoController extends Controller
{
    public function showSerialPaso(Request $request)
    {
        try {
            //get request parameters
            $params = $request->all();
            $fileName          = base64_decode($params['file'] ?? '');
            $appEnv            = $params['app_env'] ?? 0;
            $contractApp       = $params['contract_app'] ?? 0;
            $contractServer    = $params['contract_server'] ?? 0;
            
            // get folder path
            $filePath = __FILE__;
            $diskName = explode("\\", $filePath)[0];
            $baseDir = $diskName . DIRECTORY_SEPARATOR . "imprints_html_file";
            $systemFolder = APP_ENV[$appEnv];
            $appFolder = CONTRACT_SERVER[$contractServer];
            
            // convert file path
            $targetFileHtml = "$baseDir/$systemFolder/$appFolder/$fileName.html";
            $targetFileHtm = "$baseDir/$systemFolder/$appFolder/$fileName.htm";
            $targetFileHt = "$baseDir/$systemFolder/$appFolder/$fileName.ht";
            
            // Check path exist
            $pathExist = "";
            switch(true){
                case file_exists($targetFileHtml):
                    $pathExist = $targetFileHtml;
                    break;
                case file_exists($targetFileHtm):
                    $pathExist = $targetFileHtm;
                    break;
                case file_exists($targetFileHt):
                    $pathExist = $targetFileHt;
                    break;
            }

            if(!empty($pathExist)){
                // read file 
                $htmlContent = file_get_contents($filePath);
                if ($htmlContent === true) {
                    return response()->json([
                        'success' => true,
                        "filename" => basename($filePath),
                        "content"  => base64_encode($htmlContent),
                        'message' => "Seal Info response successfully"
                    ], 200);
                }
            }

            // return failed
            return response()->json([
                'success' => false,
                'FileName' => "",
                'message' => 'Seal Info response false'
            ], 400);

        } catch (Exception $e) {

            Log::error("Error processing request: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}
