<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Models\Post;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function index()
    {
        return view('broadcast');
    }

    public function sendMessage(Request $request)
    {
        // $message = $request->input('message');
        $post=Post::first();
        event(new PostCreated($post));

        return response()->json(['status' => 'Message Sent!']);
    }
}
