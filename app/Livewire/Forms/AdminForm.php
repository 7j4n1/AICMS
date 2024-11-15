<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Admin;
use App\Models\Member;
use App\Models\User;
use Livewire\Attributes\Validate;

class AdminForm extends Form
{
    public $id;
    #[Validate('required|string|max:255')]
    public $name;
    #[Validate('required|string|max:255')]
    public $username;
    public $email;
    #[Validate('required|string|min:6|max:255')]
    public $password;
    #[Validate('required|string|min:6|max:255')]
    public $password_confirmation;
    public $role = 'manager';
    #[Validate('required|numeric')]
    public $coopId=0;

    
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
        'username.unique' => 'The Username field must be unique.',
        'password.required' => 'The Password field is required.',
        'password.string' => 'The Password field must be a string.',
        'password.confirmed' => 'The Password field must be confirmed.',
    ];

    public function boot()
    {
        $this->withValidator(function ($validator) {
            $validator->after(function ($validator) {
                if ($this->password !== $this->password_confirmation) {
                    $validator->errors()->add('password_confirmation', 'The password confirmation does not match.');
                }

                // check if the username already exist
                if($this->role != 'member'){
                    $admin = Admin::where('username', $this->username)->first();
                    if($admin)
                        $validator->errors()->add('username', 'The Username is already taken.');
                }

                if($this->role == 'member'){

                    // check if the username is valid in the User's table
                    $admin = User::where('username', $this->username)->first();
                    if($admin)
                        $validator->errors()->add('username', 'The Username is already taken.');

                    // check if the coopId is valid
                    if($this->coopId <= 0)
                        $validator->errors()->add('coopId', 'The Coop ID is invalid.');

                    

                    // check if the coopId is valid from member's table
                    $member = Member::where('coopId', $this->coopId)->first();
                    if(!$member)
                        $validator->errors()->add('coopId', 'The Coop ID is invalid.');
                }

                
            });
        });

    }

    public function save()
    {
        $this->validate();
        $admin = Admin::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        if(!$admin)
            return false;

        $admin->assignRole($this->role);
        
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
