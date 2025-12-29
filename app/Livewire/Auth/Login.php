<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    public $login = ''; // Can be email or phone
    public $password = '';
    public $remember = false;

    public function loginUser()
    {
        $this->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        // Determine if login is email or phone
        $fieldType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'so_dien_thoai';
        
        // Attempt login with the determined field - Session lifetime handles persistence (30 days)
        if (Auth::attempt([$fieldType => $this->login, 'password' => $this->password], false)) {
            session()->regenerate();

            if (Auth::user()->vai_tro === 'nhan_vien') {
                return redirect()->route('employee.dashboard');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        $this->addError('login', 'Thông tin đăng nhập không chính xác.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
