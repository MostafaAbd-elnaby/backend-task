<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('stats', 60, function () {
            $userCount = User::count();
            $postCount = Post::count();
            $usersWith0PostsCount = User::whereDoesntHave('posts')->count();

            return [
                'users_count' => $userCount,
                'posts_count' => $postCount,
                'users_with_0_posts' => $usersWith0PostsCount,
            ];
        });

        return response()->json($stats, 200);
    }
}
