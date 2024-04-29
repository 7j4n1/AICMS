<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Admin;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;

class AdminForm extends Form
{
    public $id;
    #[Validate('required|string|max:255')]
    public $name;
    #[Validate('required|string|max:255|unique:admins,username')]
    public $username;
    public $email;
    #[Validate('required|string|min:6|max:255|confirmed')]
    public $password;
    public $password_confirmation;

    
    /**
     * FILEPATH: app/Livewire/Members/ListAdministrators.php
     *
     * This class represents the ListAdministrators component in the AICMS application.
     * It contains an array of validation error messages for the form fields.
     *
     * @var array $messages
     */
    protected $messages = [
        'name.required' => 'The Name field is required.',
        'name.string' => 'The Name field must be a string.',
        'name.max' => 'The Name field must not exceed 255 characters.',
        'username.required' => 'The Username field is required.',
        'username.string' => 'The Username field must be a string.',
        'username.max' => 'The Username field must not exceed 255 characters.',
        'username.unique' => 'The Username field must be unique.',
        'password.required' => 'The Password field is required.',
        'password.string' => 'The Password field must be a string.',
        'password.min' => 'The Password field must be at least 6 characters.',
        'password.max' => 'The Password field must not exceed 255 characters.',
        'password.confirmed' => 'The Password field must be confirmed.',
    ];
    public function save()
    {
        $this->validate();
        $admin = Admin::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        if($admin)
            return false;

        $admin->assignRole('manager');
        
        return true;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        
    }

}
