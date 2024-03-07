<?php

namespace App\Http\Controllers;

use App\Enums\User\SubscriptionType;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WishlistController extends Controller
{
    public function add(Product $product, Request $request)
    {
        $data = $this->validateRequest($request);
        auth()->user()->addToWish($product, SubscriptionType::tryFrom($data['type']));

        notify()->success('Product was added to your wish list', position: 'topRight');

        return redirect()->back();
    }

    public function remove(Product $product, Request $request)
    {
        $data = $this->validateRequest($request);
        auth()->user()->removeFromWish($product, SubscriptionType::tryFrom($data['type']));

        notify()->success('Product was removed from your wish list', position: 'topRight');

        return redirect()->back();
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'type' => ['required', Rule::in(SubscriptionType::getValues())],
        ]);
    }
}
