<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            if (Auth::user()->vai_tro === 'nhan_vien') {
                return redirect()->route('admin.shift.closing');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        $this->addError('email', 'Thông tin đăng nhập không chính xác.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
