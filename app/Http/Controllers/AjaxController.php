<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Psy\Exception\ErrorException;

class AjaxController extends Controller
{
  const STORAGE_FOLDER = 'files';

  public function uploadZip(Request $request)
  {
    try {

    $zipFile = $request->file('zip');
    $aa = $request->file('zip')->getFilename();
    $a = $request->allFiles();

    if (!Storage::disk()->makeDirectory(self::STORAGE_FOLDER))
      throw new ErrorException('Cant create folder');

      $path = storage_path('files');

      $a = Storage::disk('local')->put(self::STORAGE_FOLDER, $zipFile, 'private');

    return response()->json(['Archive uploaded to the storage']);
    } catch (ErrorException $e) {
      return response()->json([$e->getMessage()]);
    }
  }

}
