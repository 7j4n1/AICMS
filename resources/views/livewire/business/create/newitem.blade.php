<div class="container">
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">{{ $editingAdminId ? 'Edit Admin' : 'New Admin' }}</h2>
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
                                <button type="button" x-show="isOpen" @click="$wire.toggleModalClose" class="btn btn-danger transition duration-300" aria-label="Close"><i class="fas fa-close"></i> Cancel </button>
                            </div>
                        </div>
                        
                    </div>

                    <div x-transition:opacity.duration.500ms class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"  tabindex="-1">
                        <div class="bg-white rounded-lg w-1/2">
                            <div class="">
                                <div class="bg-gray-200 p-3 flex justify-between items-center rounded-t-lg">
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingAdminId ? 'Edit Admin details' : 'Add new details' }}</h5>
                                    
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingAdminId ? 'updateAdmin' : 'saveAdmin' }}" class="row g-3">

                                        {{-- Name --}}
                                        <div class="col-md-6">
                                            <label for="title">Full Name <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Full Name" wire:model.blur="adminForm.name" />
                                            @error('adminForm.name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                        
                                            {{-- username --}}
                                        <div class="col-md-6">
                                            <label for="username">Username <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="username" wire:model.blur="adminForm.username" />
                                            @error('adminForm.username') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        @if(!$editingAdminId)
                                        {{-- Password --}}
                                        <div class="col-md-6">
                                            <label for="password">Password </label>
                                            <input type="password"  class="form-control" placeholder="Password" wire:model.blur="adminForm.password" />
                                            @error('adminForm.password') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Comfirm Password --}}
                                        <div class="col-md-6">
                                            <label for="password">Confirm Password </label>
                                            <input type="password"  class="form-control" placeholder="Confirm Password" wire:model.blur="adminForm.password_confirmation" />
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
                    </div>
            </section>
        </div>
    </div>
    
    <x-scriptvendor></x-scriptvendor>
</div>
