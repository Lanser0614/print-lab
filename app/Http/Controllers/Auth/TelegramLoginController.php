<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TelegramLoginToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Auth\StartTelegramLoginUseCase;

class TelegramLoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function start(Request $request, StartTelegramLoginUseCase $useCase): JsonResponse
    {
        $request->validate(['redirect_to' => ['nullable', 'string', 'max:2048']]);

        $result = $useCase->execute(
            redirectTo: $request->input('redirect_to'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json($result);
    }

    public function poll(string $locale, string $token): JsonResponse
    {
        $loginToken = TelegramLoginToken::query()->where('token', $token)->first();

        if (! $loginToken) {
            return response()->json(['status' => 'expired'], 410);
        }

        if ($loginToken->status === 'expired' || $loginToken->isExpired()) {
            $loginToken->markExpired();

            return response()->json(['status' => 'expired'], 410);
        }

        if ($loginToken->isConfirmed()) {
            Auth::login($loginToken->user, true);
            request()->session()->regenerate();

            $loginToken->markExpired();

            return response()->json([
                'status' => 'confirmed',
                'redirect_to' => $loginToken->redirect_to ?? route('account.index'),
            ]);
        }

        return response()->json(['status' => 'pending']);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
