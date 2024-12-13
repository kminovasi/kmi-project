<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Get all comments by paper_id.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommentsByPaper(Request $request)
    {
        $paperId = $request->input('paper_id');

        if (!$paperId) {
            return response()->json(['error' => 'Paper ID is required'], 400);
        }

        $comments = Comment::where('paper_id', $paperId)->get();

        return response()->json($comments);
    }
}
