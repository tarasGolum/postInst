<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Psy\Exception\ErrorException;

class AjaxController extends Controller
{
  const STORAGE_FOLDER = 'files';

  public function uploadZip(Request $request)
  {
    try {
     $this->isFileValid($request);


    $zipFile = $request->file('zip');
    $zipFileName = $request->file('zip')->getFilename();

    if (!Storage::disk()->makeDirectory(self::STORAGE_FOLDER))
      throw new ErrorException('Cant create folder');

      $path = storage_path('files');

      $a = Storage::disk('local')->put(self::STORAGE_FOLDER, $zipFile, 'private');

    return response()->json(['Archive uploaded to the storage']);
    } catch (ErrorException $e) {
      return response()->json([$e->getMessage()]);
    }
  }

  /**
   * @param Request $request
   *
   * @return bool
   */
  private function isFileValid(Request $request): bool
  {
    $rules    = ['zip' => 'required|file|mimes:zip'];
    $messages = [
      'file'  => 'The :file must be a file.', // wtf
      'mimes' => 'The file type must be zip.',
    ];

    $validator = Validator::make([$request->file('zip')], $rules, $messages);

    return $validator->fails();
  }

}
