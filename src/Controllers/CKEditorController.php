<?php

namespace HessamCMS\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CKEditorController extends Controller
{
	public function upload(Request $request)
	{
		if($request->hasFile('upload')) {
			$originName = $request->file('upload')->getClientOriginalName();
			$fileName = pathinfo($originName, PATHINFO_FILENAME);
			$extension = $request->file('upload')->getClientOriginalExtension();
			$fileName = $fileName.'_'.time().'.'.$extension;
			$request->file('upload')->move(public_path('blog_images'), $fileName);
			$CKEditorFuncNum = $request->input('CKEditorFuncNum');
			$url = asset('blog_images/'.$fileName);
			$msg = 'Image successfully uploaded';
			$response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

			@header('Content-type: text/html; charset=utf-8');
			echo $response;
		}
	}
}
