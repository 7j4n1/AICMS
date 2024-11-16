<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public $username;

    public function validateUsername()
    {
        $this->validate([
            'username' => 'required|string',
        ]);

        if (!\App\Models\Admin::where('username', $this->username)->exists() ) {
            $this->addError('username', 'The provided username does not exist.');
        }
    }
    #[Validate('required|string')]
    public $password;

    protected $messages = [
        'username.required' => 'The username field is required.',
        'username.exists' => 'The provided username does not exist.',
        'password.required' => 'The password field is required.',
        'password.string' => 'The password field must be a string.',
    ];

    public function authenticate()
    {
        $this->validate();
        
        $credentials = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        // Authenticate user against the admin guard
        if (!auth()->guard('admin')->attempt($credentials)) {
            // Authenticate user against the user guard
            // if (!auth()->guard('user')->attempt($credentials)) {
                $this->addError('password', 'The provided credentials are incorrect.');
                return false;
            // }
        }

        // start session for the logged in user
        session()->regenerate();

        return true;
    }

    public function resetForm()
    {
        $this->username = '';
        $this->password = '';
    }
}
