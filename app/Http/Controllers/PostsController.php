<?php

namespace App\Http\Controllers;

use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
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

    public function index(Request $request)
    {
        // authorization
        if (Gate::denies('read-post')) {
            $response = [
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ];

            return response()->json($response, 403);
        }
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader == 'application/json' || $acceptHeader == 'application/xml') {
            if (Auth::user()->role === 'admin') {
                $posts = Post::OrderBy("id", "DESC")->paginate()->toArray();
            } else {
                $posts = Post::Where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->paginate()->toArray();
            }

            $response = [
                'total_count' => $posts['total'],
                "limit" => $posts['per_page'],
                "pagination" => [
                    'next_page' => $posts['next_page_url'],
                    'current_page' => $posts['current_page']
                ],
                'data' => $posts['data']
            ];

            if ($acceptHeader == 'application/json') {
                return response()->json($response, 200);
            } else {
                $xml = new \SimpleXMLElement('<posts />');

                foreach ($posts['data'] as $item) {
                    // create xml
                    $xmlItem = $xml->addChild('post');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('title', $item->title);
                    $xmlItem->addChild('status', $item->status);
                    $xmlItem->addChild('content', $item->content);
                    $xmlItem->addChild('user_id', $item->user_id);
                    $xmlItem->addChild('created_at', $item->created_at);
                    $xmlItem->addChild('updated_at', $item->updated_at);
                }

                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable', 406);
        }
    }

    public function store(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        // authorization
        if (Gate::denies('create-post')) {
            $response = [
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ];

            return response()->json($response, 403);
        }

        $input = $request->all();
        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published'
        ];

        $input['user_id'] = Auth::user()->id;

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($acceptHeader == 'application/json' || $acceptHeader == 'application/xml') {
            $post = Post::create($input);

            if ($acceptHeader == 'application/json') {
                return response()->json($post, 201);
            } else {
                $xml = new \SimpleXMLElement('<posts />');

                // create xml
                $xmlItem = $xml->addChild('post');

                $xmlItem->addChild('id', $post->id);
                $xmlItem->addChild('title', $post->title);
                $xmlItem->addChild('status', $post->status);
                $xmlItem->addChild('content', $post->content);
                $xmlItem->addChild('user_id', $post->user_id);
                $xmlItem->addChild('created_at', $post->created_at);
                $xmlItem->addChild('updated_at', $post->updated_at);

                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable', 406);
        }
    }

    public function show(Request $request, $postId) {
        $acceptHeader = $request->header('Accept');

        $post = Post::find($postId);

        if (!$post) {
            abort(404);
        }

        // Gate Show, Update, Delete Post
        if (Gate::denies('sud-post', $post)) {
            $response = [
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ];

            return response()->json($response, 403);
        }

        if ($acceptHeader == 'application/json' || $acceptHeader == 'application/xml') {

            if ($acceptHeader == 'application/json') {
                return response()->json($post, 200);
            } else {
                $xml = new \SimpleXMLElement('<posts />');

                // create xml
                $xmlItem = $xml->addChild('post');

                $xmlItem->addChild('id', $post->id);
                $xmlItem->addChild('title', $post->title);
                $xmlItem->addChild('status', $post->status);
                $xmlItem->addChild('content', $post->content);
                $xmlItem->addChild('user_id', $post->user_id);
                $xmlItem->addChild('created_at', $post->created_at);
                $xmlItem->addChild('updated_at', $post->updated_at);

                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable', 406);
        }
    }

    public function update(Request $request, $postId)
    {
        $acceptHeader = $request->header('Accept');

        $input = $request->all();
        $post = Post::find($postId);

        if (!$post) {
            abort(404);
        }

        // Gate Show, Update, Delete Post
        if (Gate::denies('sud-post', $post)) {
            $response = [
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ];

            return response()->json($response, 403);
        }

        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($acceptHeader == 'application/json' || $acceptHeader == 'application/xml') {

            $post->title = $input['title'];
            $post->content = $input['content'];
            $post->status = $input['status'];
            $post->user_id = $input['user_id'];

            $post->save();

            if ($acceptHeader == 'application/json') {
                return response()->json($post, 200);
            } else {
                $xml = new \SimpleXMLElement('<posts />');

                // create xml
                $xmlItem = $xml->addChild('post');

                $xmlItem->addChild('id', $post->id);
                $xmlItem->addChild('title', $post->title);
                $xmlItem->addChild('status', $post->status);
                $xmlItem->addChild('content', $post->content);
                $xmlItem->addChild('user_id', $post->user_id);
                $xmlItem->addChild('created_at', $post->created_at);
                $xmlItem->addChild('updated_at', $post->updated_at);

                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable', 406);
        }
    }

    public function delete(Request $request, $postId)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader == 'application/json' || $acceptHeader == 'application/xml') {
            $post = Post::find($postId);

            // Gate Show, Update, Delete Post
            if (Gate::denies('sud-post', $post)) {
                $response = [
                    'success' => false,
                    'status' => 403,
                    'message' => 'You are unauthorized'
                ];

                return response()->json($response, 403);
            }

            $post->delete();

            if ($acceptHeader == 'application/json') {
                return response()->json($post, 200);
            } else {
                $xml = new \SimpleXMLElement('<posts />');

                // create xml
                $xmlItem = $xml->addChild('post');

                $xmlItem->addChild('id', $post->id);
                $xmlItem->addChild('title', $post->title);
                $xmlItem->addChild('status', $post->status);
                $xmlItem->addChild('content', $post->content);
                $xmlItem->addChild('user_id', $post->user_id);
                $xmlItem->addChild('created_at', $post->created_at);
                $xmlItem->addChild('updated_at', $post->updated_at);
                $xmlItem->addChild('message', 'Data Deleted');

                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable', 406);
        }
    }
}
