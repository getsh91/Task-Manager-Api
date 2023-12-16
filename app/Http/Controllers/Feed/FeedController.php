<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Models\Feed;
use App\Models\Like;
use App\Models\Comment;

class FeedController extends Controller
{
    public function index(){
        $feeds=Feed::with('user')->latest()->get();
        return response([
            'feeds'=>$feeds,
            'message'=>'Feeds retrieved successfully'
        ],200);
    }
    public function store(PostRequest $request)
    {
        $validatedData = $request->validated();

        $feed = auth()->user()->feed()->create([
            'content' => $validatedData['content']
        ]);

        return response([
            'message' => 'Feed created successfully',
            'feed' => $feed
        ], 201);
    }

    public function likePost($feed_id)
    {
        $feed = Feed::find($feed_id);

        if (!$feed) {
            return response([
                'message' => 'Feed not found'
            ], 404);
        }

        $unlike_post = Like::where('user_id', auth()->id())->where('feed_id', $feed_id)->delete();
        
        if ($unlike_post) {
            return response([
                'message' => 'unliked'
            ], 200);
        }

        $like_post = Like::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id
        ]);

        return response([
            'message' => 'liked'
        ], 201);
    }

    public function commentPost(Request $request, $feed_id)
    {
        Request()->validate([
            'body'=>'required'
        ]);
        $feed = Feed::find($feed_id);

        if (!$feed) {
            return response([
                'message' => 'Feed not found'
            ], 404);
        }
       
        $comment_post = Comment::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id,
            'body' => $request->body
        ]);
        return response([
            'message' => 'commented'
        ], 201);
        
    }
    public function getComment($feed_id)
    {
       $comments=Comment::with('feed')->with('user')->whereFeedId($feed_id)->latest()->get();
       return response([
           'comments'=>$comments
       ],200);
    }
   
}

// public function store (PostRequest $request){
//     $request->validated();
//     $feedData=[
//         'user_id'=>auth()->user()->id,
//         'content'=>$request->content
//     ];
//     $feed=Feed::create($feedData);
//     return response([
//         'feed'=>$feed,
//         'message'=>'Feed created successfully'
//     ],201);
// }

// public function likePost($feed_id){
//     $feed=Feed::find($feed_id);
//     $feed->likes()->create([
//         'user_id'=>auth()->user()->id
//     ]);
//     return response([
//         'message'=>'Post liked successfully'
//     ],201);
// }