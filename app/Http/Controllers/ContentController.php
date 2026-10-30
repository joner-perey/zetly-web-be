<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Get All Published Tips (Live on For You feed)
     */
    public function index(): JsonResponse
    {
        $contents = Content::where('status', 'published')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tag' => $item->tag,
                    'title' => $item->title,
                    'excerpt' => $item->excerpt,
                    'published_at' => $item->published_at ? $item->published_at->diffForHumans() : 'Recently',
                ];
            });

        return response()->json([
            'data' => $contents,
        ]);
    }

    /**
     * Publish New Health Tip to Patient Feed
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tag' => 'required|string|max:100',
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
        ]);

        $content = Content::create([
            'title' => $validated['title'],
            'tag' => $validated['tag'],
            'excerpt' => $validated['excerpt'] ?? $validated['title'],
            'body' => $validated['body'] ?? null,
            'author_id' => $request->user()?->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Health tip published to patient feed.',
            'data' => [
                'id' => $content->id,
                'tag' => $content->tag,
                'title' => $content->title,
                'excerpt' => $content->excerpt,
            ],
        ], 201);
    }
}

