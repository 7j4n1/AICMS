<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\AdminForm;
use App\Models\Member;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ListAdministrator extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    public AdminForm $adminForm;
    public $isModalOpen = false;
    public $editingAdminId = null;
    public $isMember = false;

    public $paginate = 10;
    public $search = '';

    public function render()
    {
        if($this->adminForm->role == 'member')
        {
            $this->isMember = true;

            // get member Username
            $this->adminForm->name = $this->getMemberName($this->adminForm->coopId);
        }else
        {
            $this->isMember = false;
        }
        
        return view('livewire.admin.list-administrator')
            ->with(['session' => session(),
            'isMember' => $this->isMember,
        ]);
    }

    #[Computed]
    public function admins()
    {
        return Admin::query()
            ->orWhere('coopId', 'like', '%'.$this->search.'%')
            ->orWhere('name', 'like', '%'.$this->search.'%')
            ->orderBy('id', 'asc')
            ->paginate($this->paginate);

    }

    public function getMemberName($id)
    {
        $member = Member::where('coopId', $id)->first();

        return $member ? $member->surname.' '.$member->otherNames : 'User not found';
    }

    public function mount()
    {
        $this->adminForm = new AdminForm($this, 'adminForm');
        $this->sendDispatchEvent();
    }

    public function resetForm()
    {
        $this->adminForm = new AdminForm($this, 'adminForm');
    }

    public function toggleModalOpen()
    {
        $this->isModalOpen = true;
        $this->editingAdminId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function toggleModalClose()
    {
        $this->isModalOpen = false;
        $this->editingAdminId = null;
        $this->adminForm->resetForm();
        $this->sendDispatchEvent();
    }

    public function saveAdmin()
    {
        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $this->adminForm->save();

        unset($this->admins);

        session()->flash('success','New Login details with '.$this->adminForm->role.' priviledges added successfully');
        $this->adminForm->resetForm();
        $this->isModalOpen = false;
    
        
        $this->sendDispatchEvent();
    }

    #[On('edit-admins')]
    public function editOldAdmin($id, $role)
    {
        
        // $this->resetForm();
        
        $admin = Admin::find($id);

        if(!$admin){

            session()->flash('error','Admin not found.');
            $this->toggleModalClose();

            return;
        }
            

        $this->adminForm->fill([
            'name' => $admin->name,
            'username' => $admin->username,
            'email' => $admin->email,
            'coopId' => $admin->coopId,
            'role' => $role,
        ]);

        $this->editingAdminId = $id;

        $this->isModalOpen = true;
        $this->sendDispatchEvent();
    }

    public function updateAdmin()
    {

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $admin = Admin::find($this->editingAdminId);

        $admin->update([
            'name' => $this->adminForm->name,
            'username' => $this->adminForm->username,
            'email' => $this->adminForm->email,
            'role' => $this->adminForm->role,
        ]);

        $this->editingAdminId = null;

        session()->flash('message','Admin details updated successfully');

        $this->adminForm->resetForm();

        $this->isModalOpen = false;

        $this->sendDispatchEvent();
    }

    #[On('delete-admins')]
    public function deleteOldAdmin($id, $role) {

        try {
            $admin = Admin::find($id);
            $admin->delete();

        } catch (\Exception $e) {
            session()->flash('error','Admin login details cannot be deleted.');

            $this->sendDispatchEvent();
            return;
        }
        

        session()->flash('message','Admin login details deleted successfully.');

        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

}
