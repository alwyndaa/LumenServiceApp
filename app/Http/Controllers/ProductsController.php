<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    private function productToXml($product)
    {
        $xml = new \SimpleXMLElement('<product/>');
        $xml->addChild('id', $product->id);
        $xml->addChild('name', htmlspecialchars((string) $product->name));
        $xml->addChild('sku', htmlspecialchars((string) $product->sku));
        $xml->addChild('category', htmlspecialchars((string) $product->category));
        $xml->addChild('description', htmlspecialchars((string) $product->description));
        $xml->addChild('price', $product->price);
        $xml->addChild('stock', $product->stock);
        $xml->addChild('status', $product->status);
        $xml->addChild('created_at', (string) $product->created_at);
        $xml->addChild('updated_at', (string) $product->updated_at);

        return $xml;
    }

    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $products = Product::OrderBy("id", "DESC")->paginate(10);

            if ($acceptHeader === 'application/json') {
                return response()->json($products->items('data'), 200);
            } else {
                $xml = new \SimpleXMLElement('<products/>');

                foreach ($products->items('data') as $item) {
                    $xmlItem = $xml->addChild('product');

                    $xmlItem->addChild('id', $item->id);
                    $xmlItem->addChild('name', htmlspecialchars((string) $item->name));
                    $xmlItem->addChild('sku', htmlspecialchars((string) $item->sku));
                    $xmlItem->addChild('category', htmlspecialchars((string) $item->category));
                    $xmlItem->addChild('description', htmlspecialchars((string) $item->description));
                    $xmlItem->addChild('price', $item->price);
                    $xmlItem->addChild('stock', $item->stock);
                    $xmlItem->addChild('status', $item->status);
                    $xmlItem->addChild('created_at', (string) $item->created_at);
                    $xmlItem->addChild('updated_at', (string) $item->updated_at);
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
                $this->validate($request, [
                    'name'        => 'required|string|max:100',
                    'sku'         => 'required|string|max:50|unique:products,sku',
                    'category'    => 'required|string|max:50',
                    'description' => 'nullable|string',
                    'price'       => 'required|numeric|min:0',
                    'stock'       => 'required|integer|min:0',
                    'status'      => 'required|in:active,inactive',
                ]);

                $input = $request->all();
                $product = Product::create($input);

                if ($acceptHeader === 'application/json') {
                    return response()->json($product, 201);
                } else {
                    return response($this->productToXml($product)->asXML(), 201)
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
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            if ($acceptHeader === 'application/json') {
                return response()->json($product, 200);
            } else {
                return response($this->productToXml($product)->asXML(), 200)
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
                $product = Product::find($id);

                if (!$product) {
                    return response()->json(['message' => 'Product not found'], 404);
                }

                $this->validate($request, [
                    'name'        => 'sometimes|required|string|max:100',
                    'sku'         => 'sometimes|required|string|max:50|unique:products,sku,' . $id,
                    'category'    => 'sometimes|required|string|max:50',
                    'description' => 'nullable|string',
                    'price'       => 'sometimes|required|numeric|min:0',
                    'stock'       => 'sometimes|required|integer|min:0',
                    'status'      => 'sometimes|required|in:active,inactive',
                ]);

                $product->update($request->all());

                if ($acceptHeader === 'application/json') {
                    return response()->json($product, 200);
                } else {
                    return response($this->productToXml($product)->asXML(), 200)
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
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $product->delete();

            if ($acceptHeader === 'application/json') {
                return response()->json(['message' => 'Product deleted'], 200);
            } else {
                $xml = new \SimpleXMLElement('<result/>');
                $xml->addChild('message', 'Product deleted');

                return response($xml->asXML(), 200)
                    ->header('Content-Type', 'application/xml');
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
}