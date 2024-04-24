<?php

namespace App\Livewire\Members;

use App\Livewire\Forms\MemberForm;
use App\Models\Member;
use Livewire\Component;
use Livewire\WithPagination;

class ListMembers extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    protected $members;
    public MemberForm $memberForm;
    public $isModalOpen = false;
    public $editingMemberId = null;

    private $paginate = 10;


    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
  
    public function render()
    {
        $this->members = Member::query()
            ->orderBy('coopId', 'asc')
            ->paginate($this->paginate);

        $this->memberForm->coopId = Member::max('coopId') + 1;

        return view('livewire.members.list-members', [
            'members' => $this->members,
        ]);
    }

    public function mount()
    {
        $this->memberForm = new MemberForm($this, 'memberForm');
    }

    public function resetForm()
    {
        $this->memberForm = new MemberForm($this, 'memberForm');
    }

    public function toggleModalOpen()
    {
        $this->isModalOpen = true;
        $this->editingMemberId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function toggleModalClose()
    {
        $this->isModalOpen = false;
        $this->editingMemberId = null;
        $this->sendDispatchEvent();
    }

    public function saveMember()
    {
        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $saved = $this->memberForm->save();

        
        session()->flash('success','Member details added successfully');

        $this->memberForm->resetForm();
        $this->isModalOpen = false;
        

    }

    public function editOldMember($id)
    {
        $member = Member::find($id);

        $this->memberForm->fill($member->toArray());

        $this->editingMemberId = $id;

        $this->isModalOpen = true;
    }

    public function updateMember()
    {

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $member = Member::find($this->editingMemberId);

        $member->update($this->memberForm->toArray());

        $this->editingMemberId = null;

        session()->flash('message','Member details updated successfully');

        $this->memberForm->resetForm();

        $this->isModalOpen = false;

        $this->sendDispatchEvent();
    }

    public function deleteOldMember($id) {
        Member::find($id)->delete();

        session()->flash('message','Member details deleted successfully.');
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

}
