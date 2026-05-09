<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Masuk · Kelola Dapur')]
class Login extends Component
{
    public string $username = '';
    public string $password = '';

    protected function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ];
    }

    public function login(): void
    {
        $this->validate();

        if (! Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            $this->addError('username', 'Username atau password salah.');

            return;
        }

        session()->regenerate();

        $this->redirectRoute('recipes.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.login');
    }
}
