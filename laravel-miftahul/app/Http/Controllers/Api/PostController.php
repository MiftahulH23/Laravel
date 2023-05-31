<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * 
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * 
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * 
     */

    public function store(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * Display the specified resource.
     *
     *
     */
    public function show(Post $post)
    {
        return new PostResource(true, 'Data Post Ditemukan', $post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * 
     */
    public function edit($id)
    {
        //
    }
    public function update(Request $request,Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title' =>'required',
            'content' => 'required',
        ]);

        if($validator->fails()){
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            Storage::delete('public/posts/'.$post->image);

            $post->update([
                'image' =>$image->hashName(),
                'title' =>$request->title,
                'content' =>$request->content,
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }
    
    public function destroy(Post $post)
    {
        Storage::delete('public/posts/' .$post->image);
        $post->delete();
        return new PostResource(true, 'Data Post Berhasil dihapus!', null);
    }
}
