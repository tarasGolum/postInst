<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{

  public function uploadZip(Request $request)
  {
    $a = $request->all();
    $c = $request->allFiles();

    return response()->json(['Archive uploaded to the storage']);
  }

}
