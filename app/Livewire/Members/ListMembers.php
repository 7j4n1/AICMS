<?php

namespace App\Livewire\Members;

use App\Models\Member;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\MemberForm;

class ListMembers extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    protected $members;
    public MemberForm $memberForm;
    public $isModalOpen = false;
    public $editingMemberId = null;

    private $paginate = 10;
    public $search = '';


    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
  
    public function render()
    {
        $this->members = Member::query()
            ->orderBy('coopId', 'asc')
            ->get();

        if($this->editingMemberId == null)
            $this->memberForm->coopId = Member::max('coopId') + 1;

        // $this->sendDispatchEvent();

        return view('livewire.members.list-members', [
            'members' => $this->members,
        ])->with(['session' => session()]);
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
    
        
        $this->sendDispatchEvent();
    }

    #[On('edit-members')]
    public function editOldMember($id)
    {
        // $this->resetForm();
        $member = Member::where('coopId', '=', $id)
            ->orWhere('id', '=', $id)
            ->first();

        if(!$member){

            session()->flash('error','Member not found.');
            $this->toggleModalClose();

            return;
        }
            

        $this->memberForm->fill($member->toArray());

        $this->editingMemberId = $id;

        $this->isModalOpen = true;
        $this->sendDispatchEvent();
    }

    public function updateMember()
    {

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $member = Member::find($this->editingMemberId);

        if($this->memberForm->yearJoined == '')
            $this->memberForm->yearJoined = null;

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
