<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $orderRequests = OrderRequest::query()
            ->where('user_id', $request->user()->id)
            ->with('items.product')
            ->orderByDesc('created_at')
            ->get();

        return view('account.index', [
            'orderRequests' => $orderRequests,
            'user' => $request->user(),
        ]);
    }

    public function show(Request $request, string $locale, OrderRequest $orderRequest): View
    {
        if ($orderRequest->user_id !== $request->user()->id) {
            abort(404);
        }

        return view('account.show', [
            'orderRequest' => $orderRequest->load('items.design', 'items.product'),
        ]);
    }
}
