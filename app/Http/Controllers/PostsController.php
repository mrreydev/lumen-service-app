<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\CategoryPost;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
                $posts = Post::with('categories')->with('comments')->OrderBy("id", "DESC")->paginate()->toArray();
            } else {
                $posts = Post::Where(['user_id' => Auth::user()->id])->with('categories')->with('comments')->OrderBy("id", "DESC")->paginate()->toArray();
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
        // dd($input);

        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'categories' => 'required|exists:categories,id'
        ];

        $input['user_id'] = Auth::user()->id;

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($acceptHeader == 'application/json' || $acceptHeader == 'application/xml') {
            $post = new Post(); 
            // Post::create($input);
            $post->title = $input['title'];
            $post->content = $input['content'];
            $post->status = $input['status'];
            $post->user_id = $input['user_id'];

            if ($request->hasFile('image')) {
                $strRandom = Str::random(10);
                $postTitle = str_replace(' ', '_', $input['title']);
                
                $imgName = Auth::user()->id.'_'.$postTitle.'_'.$strRandom;
                $request->file('image')->move(storage_path('uploads/image_post'), $imgName);
                
                $post->image = $imgName;
            }

            if ($request->hasFile('video')) {
                $strRandom = Str::random(10);
                $postTitle = str_replace(' ', '_', $input['title']);
                
                $videoName = Auth::user()->id.'_'.$postTitle.'_'.$strRandom;
                $request->file('video')->move(storage_path('uploads/video_post'), $videoName);

                $post->video = $videoName;
            }

            if ($post->save()) {
                $dataPivot = [];
                foreach ($input['categories'] as $key => $value) {
                    $row = [
                        'post_id' => $post->id,
                        'category_id' => $value
                    ];
    
                    array_push($dataPivot, $row);
                }
    
                $post->categories()->attach($dataPivot);
    
                if ($acceptHeader == 'application/json') {
                    foreach ($post->categories as $category) {
                        # code...
                        $category->pivot;
                    }
                    return response()->json($post, Response::HTTP_CREATED);
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
                $response = [
                    'message' => 'Create Post Failed',
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                ];

                return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return response('Not Acceptable', Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function show(Request $request, $postId) {
        $acceptHeader = $request->header('Accept');

        $post = Post::with('comments')->find($postId);

        if (!$post) {
            abort(404);
        }
        
        foreach ($post->categories as $category) {
            # code...
            $category->pivot;
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
        $input['user_id'] = Auth::user()->id;

        $post = Post::with('categories')->find($postId);

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
            'categories' => 'required|exists:categories,id',
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

            $postCat = [];
            foreach ($post->categories as $category) {
                $postCat = array_merge($postCat, [$category->id]);
            }

            $removedCat = array_filter($postCat, function($cat) use ($input) {
                return !in_array($cat, $input['categories']);
            });
            
            if ($removedCat && count($removedCat)) {
                $post->categories()->detach($removedCat);
            } else {
                $addedCat = array_filter($input['categories'], function($cat) use ($postCat) {
                    return !in_array(intval($cat), $postCat);
                });

                $post->categories()->attach($addedCat);
            }

            if ($request->hasFile('image')) {
                $strRandom = Str::random(10);
                $postTitle = str_replace(' ', '_', $input['title']);
                
                $imgName = Auth::user()->id.'_'.$postTitle.'_'.$strRandom;
                $request->file('image')->move(storage_path('uploads/image_post'), $imgName);
                
                $current_image_path = storage_path('uploads/image_post').'/'.$post->image;
                if (file_exists($current_image_path)) {
                    unlink($current_image_path);
                }

                $post->image = $imgName;
            }

            if ($request->hasFile('video')) {
                $strRandom = Str::random(10);
                $postTitle = str_replace(' ', '_', $input['title']);
                
                $videoName = Auth::user()->id.'_'.$postTitle.'_'.$strRandom;
                $request->file('video')->move(storage_path('uploads/video_post'), $videoName);

                $current_video_path = storage_path('uploads/video_post').'/'.$post->video;
                if (file_exists($current_video_path)) {
                    unlink($current_video_path);
                }

                $post->video = $videoName;
            }

            $post->save();

            $post->refresh();

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

            $post->categories()->detach();
            $post->comments()->detach();

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

    public function getMedia(Request $request, $type, $name)
    {
        $path = $type == 'image' ? 'uploads/image_post' : 'uploads/video_post';
        $contentType = $type == 'image' ? 'image/jpeg' : 'video/mp4';
        $file = getFile($name, $path);

        if (!$file) {
            $response = [
                'message' => $type.' Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        return response($file, Response::HTTP_OK)->header('Content-Type', $contentType);
    }
}
