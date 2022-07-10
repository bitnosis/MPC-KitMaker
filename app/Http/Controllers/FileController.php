<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Excel;
use App\Imports\TradeImport;
use App\Imports\RithmicImport;
use App\Models\File;

class FileController extends Controller
{
    public function store(Request $request)
    {
       $request->validate([
        'title' => 'required:max:255',
        'filename' => 'required',
      ]);

      auth()->user()->files()->create([
        'title' => $request->get('title'),
        'overview' => $request->get('overview'),
      ]);

      return back()->with('message', 'Your file is submitted Successfully');
    }

    public function uploadfile(Request $request)
    {
      $uploadedFile = $request->file('file');
      $filename = time().$uploadedFile->getClientOriginalName();

      $theFile = Storage::disk('local')->putFileAs(
        'files/'.$filename,
        $uploadedFile,
        $filename
      );

      $upload = new File;
      $upload->filename = $filename;
      $upload->title = $filename;
      $upload->user()->associate(auth()->user());

      Excel::import(new TradeImport, $theFile);

      $upload->save();

      return response()->json([
        'id' => $upload->id
      ]);
    }

    public function processfile(Request $request, $id)
    {
      $uploadedFile = $request->file('file');
      $filename = time().$uploadedFile->getClientOriginalName();

      $theFile = Storage::disk('local')->putFileAs(
        'files/'.$filename,
        $uploadedFile,
        $filename
      );

      $upload = new File;
      $upload->filename = $filename;
      $upload->title = $filename;
      $upload->user()->associate(auth()->user());

      Excel::import(new TradeImport, $theFile);

      $upload->save();

      return response()->json([
        'id' => $upload->id
      ]);
    }
}
