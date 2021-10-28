<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $posts = Post::latest()->paginate(config("test.pagination"));
        return PostResource::collection($posts);
    }

    /**
     * @param Post $post
     * @return PostResource
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * @param PostRequest $request
     * @return PostResource
     */
    public function store(PostRequest $request): PostResource
    {
        $data = $request->validated();
        $user = auth()->user();
        $image = $data['image']->store('public/images');
        unset($data['image']);

        $post = $user->posts()->create($data);
        $post->image()->create(['url' => $image]);

        return new PostResource($post);
    }

    /**
     * @param PostRequest $request
     * @param Post $post
     * @return PostResource|Application|ResponseFactory|Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $data = $request->validated();
        $user = auth()->user();

        if ($user->id != $post->user_id) {
            return response([
                'success' => false,
                'message' => 'You don\'t have a permission to update this post'
            ], 403);
        }

        if(!empty($data['image'])) {
            Storage::delete($post->image->url);
            $post->image->delete();
            $image = $data['image']->store('public/images');
            $post->image()->create(['url' => $image]);
            unset($data['image']);
        }

        $post->update($data);

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $user = auth()->user();
        if ($user->id != $post->user_id) {
            return response([
                'success' => false,
                'message' => 'You don\'t have a permission to delete this post'
            ], 403);
        }

        $post->delete();

        return [
            'success' => true,
            'message' => 'Post has been deleted'
        ];
    }
}
