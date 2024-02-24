<?php

namespace App\Http\Controllers\Ajax\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ajax\UploadImageRequest;
use App\Models\Product;

class ImagesController extends Controller
{
    public function store(UploadImageRequest $request, Product $product)
    {

        try {
            $data = $request->validated();
            $image = $product->images()->create(
                [
                    'path' => [
                        'image' => $data['image'],
                        'directory' => $product->directory,
                    ],
                ]
            );

            return response()->json([
                'url' => $image->url,
                'imageUrl' => route('ajax.images.show', $image),
            ]);
        } catch (\Exception $exception) {
            logs()->error($exception);

            return response(status: 422)->json([
                'message' => $exception,
            ]);
        }
    }
}
