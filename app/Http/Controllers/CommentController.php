<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function index()
    {
        $comments = Comment::with('user')->OrderBy('id', 'ASC')->paginate();

        $response = [
            'message' => 'Get Comments Success',
            'status_code' => Response::HTTP_OK,
            'data' => $comments
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id)
    {
        $comment = Comment::with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->find($id);

        if (!$comment) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $response = [
            'message' => 'Get Comment Success',
            'status_code' => Response::HTTP_OK,
            'data' => $comment
        ];

        return response()->json($response, Response::HTTP_OK);
    }
    
    public function getPostComments(Request $request, $id)
    {
        $post = Post::with(['comments' => function ($qComment) {
            $qComment->select('id', 'body', 'post_id', 'user_id')
                        ->with('user');
        }])->find($id);

        $response = [
            'message' => 'Get Posts Success',
            'status_code' => Response::HTTP_OK,
            'data' => $post
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $input = $request->all();

        $input['user_id'] = Auth::user()->id;
        $input['post_id'] = $id;

        $comment = new Comment();

        $comment->body = $input['body'];
        $comment->user_id = $input['user_id'];
        $comment->post_id = $input['post_id'];

        if ($comment->save()) {
            $comment->user;

            $response = [
                'message' => 'Create Comment Success',
                'status_code' => Response::HTTP_CREATED,
                'data' => $comment
            ];

            return response()->json($response, Response::HTTP_CREATED);
        }

        $response = [
            'message' => 'Create Comment Failed',
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];

        return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
