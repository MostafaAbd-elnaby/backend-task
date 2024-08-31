<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('user_id', auth()->user()->id)->with('tags')->orderBy('pinned', 'desc')->get();
        return response()->json([
            'posts' => PostResource::collection($posts),
        ],200);
    }

    public function show(Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $post = $post->where('id', $post->id)->where('user_id', auth()->user()->id)->first();
        return new PostResource($post);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if ($request->hasFile('cover_image')) {
            $newImage = $request->file('cover_image');
            $newFileName = rand(0, 999) . auth()->user()->id. $newImage->getClientOriginalName();
            $newPath = $newImage->storeAs('/posts', $newFileName);
        }
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'cover_image' => $newPath,
            'pinned' => $request->pinned ?? false,
            'user_id' => auth()->user()->id,
        ]);
        $post->tags()->sync($request->tags);
        return response()->json([
            'message' => 'Post created successfully',
            'post' => new PostResource($post),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'tags' => 'array',
            'cover_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if ($request->hasFile('cover_image')) {
            $newImage = $request->file('cover_image');
            $newFileName = $post->id . rand(0, 999) . auth()->user()->id . $newImage->getClientOriginalName();
            $newPath = $newImage->storeAs('/posts', $newFileName);
            $post->cover_image = $newPath;
            $post->save();
        }
        $post->tags()->sync($request->tags);
        $post->update($request->all());
        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }

    public function getDeletedPosts()
    {
        $user = auth()->user();
        $deletedPosts = $user->posts()->onlyTrashed()->get();

        return response()->json([
            'deleted_posts' => PostResource::collection($deletedPosts ?? []),
        ]);
    }

    public function restoreDeletedPost($post_id)
    {
        $post = Post::withTrashed()->where('id', $post_id)->first();
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $post->restore();
        return response()->json([
            'message' => 'Post restored successfully',
            'post' => new PostResource($post),
        ]);
    }
}
