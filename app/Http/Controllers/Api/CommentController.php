<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    /**
     * @param CommentRequest $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentRequest $request, Post $post)
    {
        $post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body
        ]);

        $data = [
            'post' => $post->load('user', 'comments')
        ];
        return $this->success($data, 201);
    }

    /**
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        if (auth()->id() == $comment->user_id) {
            $comment->delete();
            $data = [
                'message' => 'Successfuly deleted comment!'
            ];
            return $this->success($data, 201);
        }
        return response()->json(['message' => 'error'], 500);
    }
}
