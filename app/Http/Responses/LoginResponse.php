<?php

namespace App\Http\Responses;

use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('filament.auth.login'); // fallback em caso extremo
        }

        if ($user->hasRole('Super Admin')) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }

        if ($user->hasRole('Professor')) {
            return redirect()->to(Dashboard::getUrl(panel: 'teacher'));
        }

        // fallback para quem não for Super Admin nem Professor
        return redirect()->to(Dashboard::getUrl(panel: 'parents'));
    }
}
