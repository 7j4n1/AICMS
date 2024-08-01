<div class="container">
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Annual fee Deduction</h2>
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
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">Annual fee Deduction</h5>
                                    
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="saveAnnualFee" class="row g-3">

                                        {{-- Annual Year --}}
                                        <div class="col-md-6">
                                            <label for="username">Select Year: </label>
                                            <select class="form-select" wire:model="annualForm.year" aria-label="Default select example">
                                                <option selected>Choose...</option>
                                                @foreach($years as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                            @error('annualForm.year') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        {{-- Amount --}}
                                        <div class="col-md-6">
                                            <label for="password">Annual Fee </label>
                                            <input type="text" id="amount_fee" wire:model="annualForm.amount" class="form-control" placeholder="Amount" />
                                            @error('annualForm.amount') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        
                                        <div class="text-end">

                                            <button type="submit" class="btn btn-success transition duration-300">Deduct Fee</button>
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
    
</div>
