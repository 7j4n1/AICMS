<div class="container">
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">{{ $editingItemId ? 'Edit Item purchase' : 'New Purchase' }}</h2>
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
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingItemId ? 'Edit Purchase details' : 'Add new Purchase' }}</h5>
                                    
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingItemId ? 'updateItem' : 'saveItem' }}" class="row g-3">

                                        {{-- CoopId --}}
                                        <div class="col-md-6">
                                            <label for="title">Coop ID <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Coop ID" wire:model.blur="itemForm.coopId" />
                                            @error('itemForm.coopId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="title">Full Name</label>
                                            <input type="text"  class="form-control"  value="{{$fullname}}" disabled/>
                                        </div>
                                        {{-- Categories --}}
                                        <div class="col-md-6">
                                            <label for="title">Select Category <span class="text-danger">*</span></label>
                                            <select class="form-select" wire:model.live="itemForm.category_id">
                                                <option value=""></option>
                                                @foreach($this->itemcategories as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('itemForm.category_id') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        {{-- Price --}}
                                        <div class="col-md-6">
                                            <label for="title">Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="price"  class="form-control" wire:model.blur="itemForm.price"  />
                                            @error('itemForm.price') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Description --}}

                                        <div class="col-md-6">
                                            <label for="title">Item(s) List <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Description" wire:model.blur="itemForm.description" />
                                            @error('itemForm.description') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                        
                                        {{-- Buying Date --}}
                                        <div class="col-md-6">
                                            <label for="username">Buying Date <span class="text-danger">*</span></label>
                                            <input type="date"  class="form-control" placeholder="Buying Date (yyyy-m-d)" wire:model.live="itemForm.buyingDate" />
                                            @error('itemForm.buyingDate') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Time Frame --}}
                                        <div class="col-md-6">
                                            <label for="title">Payment Duration <span class="text-danger">*</span></label>
                                            <select class="form-select" wire:model.blur="itemForm.payment_timeframe">
                                                <option value="">Select Duration</option>
                                                <option value="1">Immediately-Cash</option>
                                                <option value="6">6 Months</option>
                                                <option value="9">9 Months</option>
                                                <option value="18">18 Months</option>
                                                <option value="24">24 Months</option>
                                            </select>
                                            @error('itemForm.payment_timeframe') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="title">Status <span class="text-danger">*</span></label>
                                            <select class="form-select" wire:model.blur="itemForm.payment_status">
                                                <option value="1">Active</option>
                                                <option value="0">In Active</option>
                                            </select>
                                            @error('itemForm.payment_status') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="text-end">

                                            <button type="submit" class="btn btn-success transition duration-300">{{ $editingItemId ? 'Update' : 'Add' }} Item</button>
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
