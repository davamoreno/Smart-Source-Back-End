<?php

namespace App\Http\Controllers\Post;

use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function authorizeRole(array $roles)
    {
        if (!Auth::user() || !Auth::user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }
    }

    public function getReport($id){
        $post = Post::with('reports')->findOrFail($id);
        return response()->json([
            'post' => $post
        ], 200);
    }

    public function userReportPost(ReportRequest $request, $postId)
    {
        $post = Post::find($postId);

        if ($post->status === 'allow') 
        {
            if (!$post) {
                return response()->json([
                    'message' => 'Post not found'
                ], 404);
            }

            $existingReport = Report::where('post_id', $postId)->where('user_id', auth()->id())->first();

            if ($existingReport) {
                return response()->json([
                    'message' => 'You have already reported this post'
                ], 409);
            }

            $post->report_status = 'pending';
            $post->save();

            $report = $post->reports()->create([
                'reason' => $request->reason,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Report submitted successfully',
                'report' => $report
            ], 201);

        }
        else if($post->status === 'deny' && $post->report_status === 'accept')
        {
            return response()->json([
                'message' => 'This Post Is Taken Down'
            ], 404);
        } 
        else if($post->status === 'deny')
        {
            return response()->json([
                'message' => 'This Post Is Denied'
            ], 404);

        } 
        else 
        {
            return response()->json([
                'message' => 'This Post Is Not Allowed'
            ], 404);
        }
    }

    public function validatePostReport(Request $request, $id)
    {
        $this->authorizeRole(['admin', 'super_admin']);
        $post = Post::with('reports')->find($id);

        if(!$post){
            return response()->json([
                'message' => 'Post Not Found'
            ], 404);
        }

        $reports = Report::where('post_id', $id)->get();

        if ($reports->isEmpty()) {
            return response()->json([
                'message' => 'No pending reports for this post',
            ], 404);
        }

        $request->validate([
            'report_status' => 'required|in:accept,reject'
        ]);

        $post->update([
            $post->report_status = $request->input('report_status'),
        ]);

        if ($post->report_status === 'accept') {
            $post->update([
                $post->status = 'deny',
            ]);
        }

        foreach($reports as $report){
            $report->update([
                $report->handled_at = now()
            ]);
        }

        $post->refresh();

        return response()->json([
            'message' => 'success', 
            'post' => $post
        ], 201);       
    }
}
