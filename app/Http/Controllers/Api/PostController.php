<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Requests\UploadImageRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except('index', 'show');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $filters = [];
        if (request()->has('tags')) {
            $filters['tags'] = request()->tags;
        }
        if (request()->has('category')) {
            $filters['category'] = request()->category;
        }
        $posts = Post::with('user', 'category')->filters($filters)->latest()->get();
        $data = [
            'posts' => $posts
        ];
        return $this->success($data, 201);
    }


    /**
     * @param PostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $request)
    {
        $post = Post::create($request->all());
        $data = [
            'post' => $post
        ];
        return $this->success($data, 201);
    }


    /**
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        $data = [
            'post' => $post->load('user', 'comments')
        ];
        return $this->success($data, 201);
    }


    /**
     * @param PostRequest $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostRequest $request, Post $post)
    {
        $data = $request->all();
        if (!isset($data['tags'])) {
            $data['tags'] = [];
        }
        $post->update($data);
        $data = [
            'post' => $post
        ];
        return $this->success($data, 201);
    }


    /**
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $post->delete();
        $data = [
            'message' => 'Post deleted'
        ];
        return $this->success($data, 201);
    }

    /**
     * @param UploadImageRequest $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(UploadImageRequest $request, Post $post)
    {
        if ($request->has('file')) {
            $old_image = $post->image;
            $post->image = $request->file('file')->store('posts', 'public');
            \File::delete(public_path('storage/' . $old_image));
            $post->save();

            $data = [
                'message' => 'Image uploaded'
            ];
            return $this->success($data, 201);
        } else {
            return response()->json(['message' => 'error'], 500);
        }
    }

    /**
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeImage(Post $post)
    {
        if ($post->image) {
            \File::delete(public_path('storage/' . $post->image));
            $post->update(['image' => null]);
            $data = [
                'message' => 'Image deleted'
            ];
            return $this->success($data, 201);
        }
        return response()->json(['message' => 'error'], 500);
    }
}
