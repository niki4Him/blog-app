<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Str;

class PostObserver
{
    /**
     * @param Post $post
     */
    public function creating(Post $post)
    {
        $this->slugOperation($post);
        if (optional(auth()->user())->role) {
            $post->user_id = auth()->id();
        }
    }

    /**
     * @param Post $post
     */
    public function updating(Post $post)
    {
        if ($post->isDirty('title')) {
            $this->slugOperation($post);
        }
    }

    /**
     * @param Post $post
     */
    protected function slugOperation(Post $post): void
    {
        $slug = Str::of($post->title)->slug();
        $count = 2;
        $updatedSlug = $slug;
        while (Post::whereSlug($updatedSlug)->exists()) {
            $updatedSlug = "{$slug}-" . $count++;
        }
        $post->slug = $updatedSlug;
    }

    /**
     * @param Post $post
     */
    public function deleting(Post $post)
    {
        if ($post->image) {
            \File::delete(public_path('storage/' . $post->image));
        }
    }
}
