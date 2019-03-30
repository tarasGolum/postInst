<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Psy\Exception\ErrorException;

class AjaxController extends Controller
{
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
    $filePath      = storage_path('app/' . self::STORAGE_FOLDER) . '/' . $zipFile->hashName();
    $directoryPath = storage_path('app/' . self::STORAGE_FOLDER);

    $zip = new \ZipArchive();

    if (!$zip->open($filePath))
      throw new ErrorException('Cant open zip file');

    $zip->extractTo($directoryPath);
    $zip->close();

    unlink($filePath);
    chmod($directoryPath, 0755);
  }

}
