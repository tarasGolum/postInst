<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Instagram;
use Intervention\Image\Exception\NotWritableException;
use Intervention\Image\ImageManagerStatic;

class PostImage extends Command
{

  const STORAGE_FOLDER = 'files';

  const LOGIN    = '';
  const PASSWORD = 'qweasd123';

  const IMG_WIDTH  = 1080;
  const IMG_HEIGHT = 1080;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Posting image in instagram';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
        sleep(1);
        unlink($imagePath);

        Log::info(date('m/d/Y H:i:s', time()) . ' - ' . 'Img uploaded!');
      } catch (\InvalidArgumentException | InstagramException $errorException) {

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
    try{
      $imageResize->resize(self::IMG_WIDTH, self::IMG_HEIGHT);
      $imageResize->save($path);
    } catch (NotWritableException $e) {
      Log::error(date('m/d/Y H:i:s', time()) . ' - ' . $e->getMessage());
    }

  }
}
