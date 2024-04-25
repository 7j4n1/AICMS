<?php

namespace App\Livewire\Authentication;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Livewire\Forms\LoginForm;

#[Title('Login')]
class Login extends Component
{
    public LoginForm $loginForm;

    public function render()
    {
        return view('livewire.authentication.login')->with(['session' => session()]);
    }

    // public function mount()
    // {
    //     $this->loginForm = new LoginForm($this, 'loginForm');
    // }

    public function resetForm()
    {
        $this->loginForm = new LoginForm($this, 'loginForm');
    }

    public function login()
    {
        $login = $this->loginForm->authenticate();

        if(!$this->getErrorBag()->isEmpty())
        {
            session()->flash('error','The provided credentials are incorrect.');
            return;
        }

    
        session()->flash('success','You have successfully logged in.');

        return redirect()->route('dashboard');

    }

}
