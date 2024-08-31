<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function index()
    {
        return response()->json([
            'tags' => Tag::all(),
        ], 200);
    }

    public function show(Tag $tag)
    {
        return response()->json([
            'tag' => $tag,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $tag = Tag::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'tag' => $tag,
        ], 201);
    }

    public function update(Request $request, Tag $tag)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags,id,' . $tag->id,
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $tag->update([
            'name' => $request->name,
        ]);
        return response()->json([
            'tag' => $tag,
        ], 200);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json([
            'message' => 'Tag deleted successfully',
        ], 200);
    }
}
