<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Member;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;

class MemberForm extends Form
{
    #[Locked]
    public $id;
    #[Rule('required|numeric|min:1|unique:members,coopId')]
    public $coopId;
    #[Rule('required|string')]
    public $surname;
    public $otherNames;
    public $gender, $occupation;
    public $phoneNumber, $religion;
    public $bankName, $accountNumber;
    public $nextOfKinName, $nextOfKinPhoneNumber;
    public $yearJoined;

    /**
     * FILEPATH: app/Livewire/Members/ListMembers.php
     *
     * This class represents the ListMembers component in the AICMS application.
     * It contains an array of validation error messages for the form fields.
     *
     * @var array $messages
     */
    protected $messages = [
        'coopId.required' => 'The Coop ID field is required.',
        'coopId.numeric' => 'The Coop ID field must be a number.',
        'coopId.min' => 'The Coop ID field must be at least 1.',
        'coopId.unique' => 'The Coop ID field must be unique.',
        'surname.required' => 'The Surname field is required.',
        'surname.string' => 'The Surname field must be a string.',
    ];

    public function save()
    {
        $this->validate();
        $member = Member::create([
            'coopId' => $this->coopId,
            'surname' => $this->surname,
            'otherNames' => $this->otherNames,
            'occupation' => $this->occupation,
            'gender' => $this->gender,
            'phoneNumber' => $this->phoneNumber,
            'religion'=> $this->religion,
            'bankName'=> $this->bankName,
            'accountNumber'=> $this->accountNumber,
            'nextOfKinName'=> $this->nextOfKinName,
            'nextOfKinPhoneNumber'=> $this->nextOfKinPhoneNumber,
            'yearJoined'=> $this->yearJoined,
            'userId' => auth('admin')->user()->id,
        ]);

        if($member)
            return false;

        return true;
    }

    public function resetForm()
    {
        $this->coopId = '';
        $this->surname = '';
        $this->otherNames = '';
        $this->occupation = '';
        $this->phoneNumber = '';
        $this->gender = '';
        $this->religion = '';
        $this->bankName = '';
        $this->accountNumber = '';
        $this->nextOfKinName = '';
        $this->nextOfKinPhoneNumber = '';
        $this->yearJoined = '';
    }
}
