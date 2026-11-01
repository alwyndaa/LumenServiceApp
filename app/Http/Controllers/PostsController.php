<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    private function postToXml($post)
    {
        $xml = new \SimpleXMLElement('<post/>');
        $xml->addChild('id', $post->id);
        $xml->addChild('title', htmlspecialchars((string) $post->title));
        $xml->addChild('status', $post->status);
        $xml->addChild('content', htmlspecialchars((string) $post->content));
        $xml->addChild('user_id', $post->user_id);
        $xml->addChild('created_at', (string) $post->created_at);
        $xml->addChild('updated_at', (string) $post->updated_at);

        return $xml;
    }

    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $posts = Post::OrderBy("id", "DESC")->paginate(10);

            if ($acceptHeader === 'application/json') {
                return response()->json($posts->items('data'), 200);
            } else {
                $xml = new \SimpleXMLElement('<posts/>');

                foreach ($posts->items('data') as $item) {
                    $xmlItem = $xml->addChild('post');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('title', $item->title);
                    $xmlItem->addChild('status', $item->status);
                    $xmlItem->addChild('content', $item->content);
                    $xmlItem->addChild('user_id', $item->user_id);
                    $xmlItem->addChild('created_at', $item->created_at);
                    $xmlItem->addChild('updated_at', $item->updated_at);
                }

                return response($xml->asXML(), 200)
                    ->header('Content-Type', 'application/xml');
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function store(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $contentTypeHeader = $request->header('Content-Type');

            if ($contentTypeHeader === 'application/json') {
                $input = $request->all();
                $post = Post::create($input);

                if ($acceptHeader === 'application/json') {
                    return response()->json($post, 201);
                } else {
                    return response($this->postToXml($post)->asXML(), 201)
                        ->header('Content-Type', 'application/xml');
                }
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $post = Post::find($id);

            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            if ($acceptHeader === 'application/json') {
                return response()->json($post, 200);
            } else {
                return response($this->postToXml($post)->asXML(), 200)
                    ->header('Content-Type', 'application/xml');
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $contentTypeHeader = $request->header('Content-Type');

            if ($contentTypeHeader === 'application/json') {
                $post = Post::find($id);

                if (!$post) {
                    return response()->json(['message' => 'Post not found'], 404);
                }

                $post->update($request->all());

                if ($acceptHeader === 'application/json') {
                    return response()->json($post, 200);
                } else {
                    return response($this->postToXml($post)->asXML(), 200)
                        ->header('Content-Type', 'application/xml');
                }
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function destroy(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $post = Post::find($id);

            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            $post->delete();

            if ($acceptHeader === 'application/json') {
                return response()->json(['message' => 'Post deleted'], 200);
            } else {
                $xml = new \SimpleXMLElement('<result/>');
                $xml->addChild('message', 'Post deleted');

                return response($xml->asXML(), 200)
                    ->header('Content-Type', 'application/xml');
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
}