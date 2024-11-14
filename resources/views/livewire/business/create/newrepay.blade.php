<div class="container">
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">{{ $editingRepayId ? 'Edit Repayment' : 'New Repayment' }}</h2>
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
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingRepayId ? 'Edit Repayment details' : 'Add new details' }}</h5>
                                    
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingRepayId ? 'updateRepay' : 'saveRepay' }}" class="row g-3">

                                        {{-- Coop Id --}}
                                        <div class="col-md-6">
                                            <label for="title">Coop Id <span class="text-danger">*</span></label>
                                            <select class="form-select" wire:model.live="repayForm.coopId">
                                                <option value=""></option>
                                                @foreach($this->getActiveItemCapturesMembers as $item)
                                                    <option value="{{ $item->coopId }}">{{ $item->coopId }}</option>
                                                @endforeach
                                            </select>
                                            @error('repayForm.coopId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Loan Details --}}
                                        <div class="col-md-6">
                                            <label for="username">Name </label>
                                            <input type="text"  class="form-control" placeholder="name" value="{{ $this->getMemberInfo() }}" disabled />
                                        </div>

                                        <div class="col-md-6">
                                            <label for="title">Loan List <span class="text-danger">*</span></label>
                                            <select class="form-select" wire:model.live="repayForm.item_capture_id">
                                                <option value="">Select a Loan Item</option>
                                                @foreach($this->getLoanDetails as $loan)
                                                    <option value="{{ $loan->id }}">{{ $loan->description }}</option>
                                                @endforeach
                                            </select>
                                            @error('repayForm.item_capture_id') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                        
                                        
                                        {{-- List all the loan  --}}
                                        
                                        @if($this->getLoanDetails->count() > 0)

                                        {{-- Loan Balance --}}
                                        <div class="col-md-6">
                                            <label for="username">Balance</label>
                                            <input type="text"  class="form-control" placeholder="Loan Balance" value="{{$loanBalance}}" disabled />
                                            <input type="hidden"  class="form-control"  wire:model="repayForm.loanBalance" />
                                        </div>

                                       @endif
                                        {{-- Amount --}}
                                        <div class="col-md-6">
                                            <label for="password">Repay Amount </label>
                                            <input type="text" id="repayAmount"  class="form-control" placeholder="Amount" wire:model.blur="repayForm.amountToRepay" {{$this->getLoanDetails->count() > 0 ? '' : 'disabled'}}/>
                                            @error('repayForm.amountToRepay') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Repay Date --}}
                                        <div class="col-md-6">
                                            <label for="password">Repay Date </label>
                                            <input type="date"  class="form-control" placeholder="Repay Date" wire:model.blur="repayForm.repaymentDate" />
                                            @error('repayForm.repaymentDate') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Service Charge --}}
                                        <div class="col-md-6">
                                            <label for="password">Service Charge </label>
                                            <input type="text"  class="form-control" placeholder="Service Charge" wire:model.blur="repayForm.serviceCharge" />
                                            @error('repayForm.serviceCharge') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="text-end">

                                            <button type="submit" class="btn btn-success transition duration-300">{{ $editingRepayId ? 'Update' : 'Add' }} Repayment</button>
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
