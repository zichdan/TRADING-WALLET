<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileManagerController extends Controller
{
    //return index of file manager
    public function index(Request $request)
    {
        $page_title = 'File Manager';

        $base_path = root_path() . 'public/assets/imgs/';
        $path_to_root = $base_path;
        if ($request->has('folder')) {
            $requested = realpath(urldecode($request->folder));
            if ($requested && strpos($requested, realpath($base_path)) === 0) {
                $path_to_root = $requested;
            }
        }

        $root_folders = File::directories($path_to_root);
        $root_files = File::files($path_to_root);

        return view('admin.file-manager.index', compact(
            'page_title',
            'root_folders',
            'root_files'
        ));

        
        
    }

    //editor
    public function editor(Request $request) 
    {
        $base_path = root_path() . 'public/assets/imgs/';
        $path_to_file = realpath(urldecode($request->file));
        if (!$request->has('file') || !$path_to_file || strpos($path_to_file, realpath($base_path)) !== 0) {
            abort(404);
        }
        //check if file exist
        if (!file_exists($path_to_file)) {
            abort('404');
        }

        $file_info = pathinfo($path_to_file);
        $file_name = $file_info['basename'];
        $page_title = 'Live Editor | ' . $file_name;       
        $extension = $file_info['extension'] ?? 'NULL';       
        
        
        $file = \file_get_contents($path_to_file);    


        return view('admin.file-manager.editor', compact(
            'page_title',
            'file',
            'extension',
            'path_to_file'
        ));
    }

    //validate editor
    public function editorValidate(Request $request) 
    {
        $request->validate([
            'code' => 'required',
            'path_to_file' => 'required',
            
        ]);

        $base_path = root_path() . 'public/assets/imgs/';
        $path_to_file = realpath(urldecode($request->path_to_file));

        if (!$path_to_file || strpos($path_to_file, realpath($base_path)) !== 0 || !file_exists($path_to_file)) {
            abort(404);
        }

        $update = file_put_contents($path_to_file, $request->code);

        if ($update) {
            return response()->json(['message' => 'Updated'], 200);
        } else {
            abort(500);
        }
    }

    //delete images
    public function delete(Request $request){
        $request->validate([
            'path_to_file' => 'required',
            
        ]);

        $base_path = root_path() . 'public/assets/imgs/';
        $path_to_file = realpath(urldecode($request->path_to_file));

        if (!$path_to_file || strpos($path_to_file, realpath($base_path)) !== 0 || !file_exists($path_to_file)) {
            abort(404);
        }

        //make sure the user is deleting only images
        $allowed_to_delete = ['png', 'jpeg', 'jpg', 'gif', 'svg'];
        $file_info  = pathinfo($path_to_file);
        $extension = $file_info['extension'];

        if (!in_array($extension, $allowed_to_delete)) {
            abort(403);
        }

        //delete the file
        $is_deleted = unlink($path_to_file);
        if ($is_deleted) {
            return response()->json(['message', 'File Deleted'], 200);
        } else {
            abort(500);
        }
    }

    //download file
    public function downloadFile(Request $request) 
    {
        $base_path = root_path() . 'public/assets/imgs/';
        $file = realpath(urldecode($request->file));
        if (!$file || strpos($file, realpath($base_path)) !== 0 || !file_exists($file)) {
            abort(404);
        }
        return response()->download($file);
    }

    //upload
    public function uploadFile(Request $request) {
        $request->validate([
            'folder' => 'required',
            'file' => 'required|image|mimes:png,jpeg,jpg,gif,svg|max:5120',
        ]);

        $base_path = root_path() . 'public/assets/imgs/';
        $folder = realpath(urldecode($request->folder));
        if (!$folder || strpos($folder, realpath($base_path)) !== 0) {
            abort(403);
        }

        $file = $request->file('file');
        $file_name = $file->getClientOriginalName();
        
        $is_uploaded = $file->move($folder, $file_name);
        
        if ($is_uploaded) {
            return back()->with('success', 'file uploaded');
        } else {
            return back()->with('fail', 'something went wrong');
        }
    }
}
