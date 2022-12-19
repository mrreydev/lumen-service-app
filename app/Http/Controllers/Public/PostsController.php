<?php

namespace App\Http\Controllers\Public;

use App\Models\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader == 'application/json' || $acceptHeader == 'application/xml') {
            $posts = Post::with('user')->OrderBy("id", "DESC")->paginate()->toArray();

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

    public function show(Request $request, $postId) {
        $acceptHeader = $request->header('Accept');

        $post = Post::with(['user' => function ($query) {
            $query->select('id', 'name');
        }]);

        if (!$post) {
            abort(404);
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
}
