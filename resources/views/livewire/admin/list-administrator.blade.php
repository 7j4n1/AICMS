
<div x-data="{ isOpen: @entangle('isModalOpen') }" class="container">
    
    <!-- Button to open the modal for adding a new member details -->
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">List of all members</h2>
                </header>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if (session()->has('success'))
                                <div class="auto-close alert alert-success d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                    <div>
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif
                            @if (session()->has('message'))
                                <div class="auto-close alert alert-success d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                    <div>
                                        {{ session('message') }}
                                    </div>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="auto-close alert alert-danger d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                    <div>
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <button class="btn btn-primary" @click="isOpen = true; @this.set('isModalOpen', true);">Add New <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        
                    </div>

                    <div  x-cloak x-show="isOpen" x-transition:opacity.duration.500ms class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"  tabindex="-1">
                        <div class="bg-white rounded-lg w-1/2">
                            <div class="">
                                <div class="bg-gray-200 p-3 flex justify-between items-center rounded-t-lg">
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingAdminId ? 'Edit Admin details' : 'Add new details' }}</h5>
                                    <button type="button" class="btn btn-danger transition duration-300" @click="isOpen = false; @this.set('isModalOpen', false);$wire.toggleModalClose()" aria-label="Close"><i class="fas fa-close"></i> Cancel</button>
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingAdminId ? 'updateAdmin' : 'saveAdmin' }}" class="row g-3">

                                        {{-- Name --}}
                                        <div class="col-md-6">
                                            <label for="title">Full Name <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Full Name" wire:model.live="adminForm.name" />
                                            @error('adminForm.name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                        
                                            {{-- username --}}
                                        <div class="col-md-6">
                                            <label for="username">Username <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="username" wire:model.live="adminForm.username" />
                                            @error('adminForm.username') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        @if(!$editingAdminId)
                                        {{-- Password --}}
                                        <div class="col-md-6">
                                            <label for="password">Password </label>
                                            <input type="password"  class="form-control" placeholder="Password" wire:model.live="adminForm.password" />
                                            @error('adminForm.password') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Comfirm Password --}}
                                        <div class="col-md-6">
                                            <label for="password">Confirm Password </label>
                                            <input type="password"  class="form-control" placeholder="Confirm Password" wire:model.live="adminForm.password_confirmation" />
                                            @error('adminForm.password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        @endif
                                        <div class="text-end">

                                            <button type="submit" class="btn btn-success transition duration-300">{{ $editingAdminId ? 'Update' : 'Add' }} Admin</button>
                                        </div>

                                    </form>
                                    {{-- Form ends --}}
                                    
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Members Table --}}
                    <!-- <div class="table-responsive"> -->
                    <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">

                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                @canAny(['can edit', 'can delete'], 'admin')
                                <th>Actions</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $admin)
                                <tr wire:key="item-profile-{{ $admin->id }}">

                                    <td>{{ $admin->id }}</td>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->username }}</td>
                                    <td>{{ $admin->email }}</td>
                                    @canAny(['can edit', 'can delete'], 'admin')
                                    <td class="">
                                        @can('can edit', 'admin')
                                            <button onclick="sendMemEvent('{{$admin->id}}')"  class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Edit</button>
                                        @endcan
                                        @can('can delete', 'admin')
                                            <button onclick="sendDeleteEvent('{{ $admin->id }}')" 
                                            class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>
                                        @endcan
                                    </td>
                                    @endcanany
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    
                    <!-- </div> -->
                    
                </div>
            </section>
        </div>
    </div>
    
    <x-scriptvendor></x-scriptvendor>

    
    <!-- Specific Page Vendor -->
    <script src="{{ asset('vendor/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/media/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js') }}"></script>
    
    <script>
        function sendMemEvent(value) {
            Livewire.dispatch('edit-admins', { id: value });
        }

        
        function sendDeleteEvent(value) {
            var result = confirm("Are you sure you want to delete this admin?");
            if (result) {
                // User clicked 'OK', dispatch delete event
                Livewire.dispatch('delete-admins', { id: value });
            }
        }
       
    </script>

    
</div>
{{-- </div> --}}