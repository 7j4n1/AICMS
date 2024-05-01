<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\AdminForm;

class ListAdministrator extends Component
{
    protected $admins;
    public AdminForm $adminForm;
    public $isModalOpen = false;
    public $editingAdminId = null;

    public function render()
    {
        $this->admins = Admin::query()
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.admin.list-administrator')
            ->with(['admins' => $this->admins, 'session' => session()]);
    }

    public function mount()
    {
        $this->adminForm = new AdminForm($this, 'adminForm');
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

        $saved = $this->adminForm->save();

        session()->flash('success','New Admin details with Manager priviledges added successfully');
        $this->adminForm->resetForm();
        $this->isModalOpen = false;
    
        
        $this->sendDispatchEvent();
    }

    #[On('edit-admins')]
    public function editOldAdmin($id)
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
        ]);

        $this->editingAdminId = null;

        session()->flash('message','Admin details updated successfully');

        $this->adminForm->resetForm();

        $this->isModalOpen = false;

        $this->sendDispatchEvent();
    }

    #[On('delete-admins')]
    public function deleteOldAdmin($id) {
        Admin::find($id)->delete();

        session()->flash('message','Admin details deleted successfully.');

        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

}
