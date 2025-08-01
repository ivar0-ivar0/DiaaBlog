<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\Comments\CommentsStoreRequest;
use App\Http\Requests\Comments\ReplyCommentsStoreRequest;
use Illuminate\Support\Facades\Auth;
use App\Mail\CommentAdded;
use Resend\Laravel\Facades\Resend;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::paginate(10);
        return view('comments.index', compact('comments'));  
    }

    public function store(CommentsStoreRequest $request)
    {
       $commentData = $request->validated();

       $comment = new Comment();
       $comment->fill($commentData);

       $comment->save();

       /*Resend::emails()->send([
        'from' => 'Diaa Blog <onboarding@resend.dev>',
        'to' => ['WAITING TO VERIFY DOMAIN TO ADD DIAA ALHAK EL FALLOUS email'],
        'subject' => 'New Comment Added',
        'html' => (new CommentAdded($comment))->render(),
       ]);*/

       return redirect()->back()->with('success', 'Your comment added successfully, I will review it and approve it very soon :)');
    }

    public function reply(ReplyCommentsStoreRequest $request)
    {
        $commentData = $request->validated();

        $comment = new Comment();
        $comment->fill($commentData);

        $comment->parent_id = $request->parent_id;

        $comment->save();

        /*Resend::emails()->send([
            'from' => 'Diaa Blog <onboarding@resend.dev>',
            'to' => ['WAITING TO VERIFY DOMAIN TO ADD DIAA ALHAK EL FALLOUS email'],
            'subject' => 'New Comment Added',
            'html' => (new CommentAdded($comment))->render(),
        ]);*/

        return redirect()->back()->with('success', 'Your reply added successfully, I will review it and approve it very soon :)');
    }

    public function approveComment($id)
    {
        if(!Auth::check()) {
            abort(403, 'Unauthorized action');
        }

        $comment = Comment::findOrFail($id);
        $comment->status = 'approved';
        $comment->save();

        return redirect()->route('comments.index')->with('success', 'Comment approved successfully');
    }

    public function rejectComment($id)
    {
        if(!Auth::check()) {
            abort(403, 'Unauthorized action');
        }

        $comment = Comment::findOrFail($id);
        $comment->status = 'rejected';
        $comment->save();

        return redirect()->route('comments.index')->with('success', 'Comment rejected successfully');
    }

    public function destroy($id)
    {
        if(!Auth::check()) {
            abort(403, 'Unauthorized action');
        }
        
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->route('comments.index')->with('success', 'Comment deleted successfully');
    }
}
