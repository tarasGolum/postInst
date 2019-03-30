<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Psy\Exception\ErrorException;
use Whoops\Util\TemplateHelper;

class AjaxController extends Controller
{
  const STORAGE_FOLDER      = 'files';
  const ALLOWED_FILE_FORMAT = 'zip';

  public function uploadZip(Request $request)
  {
    try {
      $zipFile = $request->file('zip');

      $this->manageDirectory($zipFile);
      Storage::disk('local')->put(self::STORAGE_FOLDER, $zipFile, 'private');
      $this->extractFiles($zipFile);

      return response()->json(['Archive uploaded to the storage']);
    } catch (ErrorException $e) {
      return response()->json([$e->getMessage()]);
    }
  }

  /**
   * @param UploadedFile $zipFile
   *
   * @return void
   * @throws ErrorException
   */
  private function manageDirectory(UploadedFile $zipFile): void
  {
    if ($zipFile->getClientOriginalExtension() !== self::ALLOWED_FILE_FORMAT)
      throw new ErrorException('File extension must be zip');

    if (!Storage::disk()->makeDirectory(self::STORAGE_FOLDER))
      throw new ErrorException('Cant create folder');

    if (!empty(Storage::disk('local')->files(self::STORAGE_FOLDER)))
      throw new ErrorException('Storage folder not empty');
  }

  /**
   * @param UploadedFile $zipFile
   *
   * @return void
   * @throws ErrorException
   */
  private function extractFiles(UploadedFile $zipFile): void
  {
    $zip      = new \ZipArchive();
    $filePath = storage_path('app/' . self::STORAGE_FOLDER) . '/' . $zipFile->hashName();

    $zipOpened = $zip->open($filePath);

    if (!$zipOpened)
      throw new ErrorException('Cant open zip file');

    $zip->extractTo(storage_path('app/' . self::STORAGE_FOLDER));
    $zip->close();
    unlink($filePath);
  }

}
