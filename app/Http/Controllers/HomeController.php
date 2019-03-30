<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InstagramAPI\Instagram;
use Intervention\Image\ImageManagerStatic;
use Psy\Exception\ErrorException;

class HomeController extends Controller
{

  const LOGIN    = 'noiackerman';
  const PASSWORD = 'qweasd123';

  const IMG_WIDTH  = 1080;
  const IMG_HEIGHT = 1080;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function postImage()
    {
      if (empty(Storage::disk('local')->allFiles(self::STORAGE_FOLDER)))
        exit;

      $images    = Storage::disk('local')->allFiles(self::STORAGE_FOLDER);
      $imagePath = storage_path() . '/app/' . $images[0];
      $this->scaleImage($imagePath);

      Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

      $ig = new Instagram();
      try {

        $ig->login(self::LOGIN, self::PASSWORD);
        $ig->timeline->uploadPhoto($imagePath);
        sleep(5);
        unlink($imagePath);

        Log::info(date('m/d/Y H:i:s', time()) . ' - ' . 'Img uploaded!');
      } catch (\InvalidArgumentException | \ErrorException $errorException) {

        Log::error(date('m/d/Y H:i:s', time()) . ' - ' . $errorException->getMessage());
      } finally {

        $ig->logout();
      }

      return view('home');
    }

  /**
   * @param string $path
   *
   * @return void
   */
    private function scaleImage(string $path): void
    {
      $imageResize = ImageManagerStatic::make($path);

      $imageResize->resize(self::IMG_WIDTH, self::IMG_HEIGHT);
      $imageResize->save($path);
    }

}
