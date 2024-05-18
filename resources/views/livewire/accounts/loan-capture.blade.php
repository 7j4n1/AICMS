<div x-data="{ isOpen: @entangle('isModalOpen') }" class="container">
    <!-- Button to open the modal for capturing new loan details -->
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">All Loan records</h2>
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
                                <button class="btn btn-primary" @click="isOpen = true; @this.set('isModalOpen', true);">Capture New Loan <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div x-cloak x-show="isOpen" x-transition:opacity.duration.500ms class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"  tabindex="-1">
                        <div class="bg-white rounded-lg w-1/2">
                            <div class="">
                                <div class="bg-gray-200 p-3 flex justify-between items-center rounded-t-lg">
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingLoanId ? 'Edit Loan details' : 'Capture New Loan details' }}</h5>
                                    <button type="button" class="btn btn-danger transition duration-300" @click="isOpen = false; @this.set('isModalOpen', false);$wire.toggleModalClose()" aria-label="Close"><i class="fas fa-close"></i> Cancel</button>
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingLoanId ? 'updateLoan' : 'saveLoan' }}" class="row g-3">

                                        {{-- CoopId --}}
                                        <div class="col-md-12">
                                            <label for="title">Coop ID <span class="text-danger">*</span></label>
                                            <!-- <input type="text"  class="form-control" placeholder="Coop ID" wire:model.live="loanForm.coopId" {{ $editingLoanId ? 'disabled' : '' }}/> -->
                                            <select class="form-select" wire:model.live="loanForm.coopId" {{ $editingLoanId ? 'disabled' : '' }}>
                                                <option value=""></option>
                                                @foreach($memberIds as $memberId)
                                                    <option value="{{ $memberId->coopId }}">{{ $memberId->coopId }}</option>
                                                @endforeach
                                            </select>
                                            @error('loanForm.coopId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                        
                                            {{-- Amount --}}
                                        <div class="col-md-6">
                                            <label for="loanAmount">Loan Amount <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Loan Amount" wire:model.live="loanForm.loanAmount" />
                                            @error('loanForm.loanAmount') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Loan Date --}}
                                        <div class="col-md-6">
                                            <label for="otherNames">Loan Date </label>
                                            <input type="date"  class="form-control" placeholder="Loan Date (mm-dd-yyyy)" wire:model.live="loanForm.loanDate" />
                                            @error('loanForm.loanDate') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Guarantor 1 --}}
                                        <div class="col-md-6">
                                            <label for="guarantor1" class="form-label">Guarantor 1 </label>
                                            
                                            <select class="form-select" wire:model.live="loanForm.guarantor1">
                                                <option value=""></option>
                                                @foreach($memberIds as $memberId)
                                                    <option value="{{ $memberId->coopId }}">{{ $memberId->coopId }}</option>
                                                @endforeach
                                            </select>
                                            @error('loanForm.guarantor1') <span class="text-danger">{{ $message }}</span> @enderror
                                            
                                        </div>
                                        {{-- Guarantor 2 --}}
                                        <div class="col-md-6">
                                            <label for="guarantor2" class="form-label">Guarantor 2 </label>
                                            <select class="form-select" wire:model="loanForm.guarantor2">
                                                <option value=""></option>
                                                @foreach($memberIds as $memberId)
                                                    <option value="{{ $memberId->coopId }}">{{ $memberId->coopId }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Guarantor 3 --}}
                                        <div class="col-md-6">
                                            <label for="guarantor3" class="form-label">Guarantor 3 </label>
                                            <select class="form-select" wire:model="loanForm.guarantor3">
                                                <option value=""></option>
                                                @foreach($memberIds as $memberId)
                                                    <option value="{{ $memberId->coopId }}">{{ $memberId->coopId }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Guarantor 4 --}}
                                        <div class="col-md-6">
                                            <label for="guarantor4" class="form-label">Guarantor 4 </label>
                                            <select class="form-select" wire:model="loanForm.guarantor4">
                                                <option value=""></option>
                                                @foreach($memberIds as $memberId)
                                                    <option value="{{ $memberId->coopId }}">{{ $memberId->coopId }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="status" class="form-label">Status </label>
                                            <select class="form-select" wire:model="loanForm.status">
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                            </select>
                                        </div>

                                        <div class="text-end">

                                            <button type="submit" class="btn btn-success transition duration-300">{{ $editingLoanId ? 'Update' : 'Add' }} Member</button>
                                        </div>


                                    </form>
                                    {{-- Form ends --}}
                                    
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Loan Records Table --}}
                    <!-- <divclass="table-responsive"> -->
                        <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">

                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Coop Id</th>
                                    <th>Loan Amount</th>
                                    <th>Loan Date</th>
                                    <th>Guarantor1</th>
                                    <th>Guarantor2</th>
                                    <th>Guarantor3</th>
                                    <th>Guarantor4</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>

                                @foreach($loans as $loan)
                                    <tr wire:key="item-profile-{{ $loan->id }}">
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $loan->coopId }}</td>
                                        <td>{{ $loan->loanAmount }}</td>
                                        <td>{{ $loan->loanDate }}</td>
                                        <td>{{ $loan->guarantor1 }}</td>
                                        <td>{{ $loan->guarantor2 }}</td>
                                        <td>{{ $loan->guarantor3 }}</td>
                                        <td>{{ $loan->guarantor4 }}</td>
                                        <td>{{ $loan->status }}</td>
                                        <td class="">
                                            @if($loan->status == 1)
                                                <button onclick="sendCompleteEvent('{{$loan->id}}')"  class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Complete</button>
                                            @endif
                                            
                                            @canAny(['can edit', 'can delete'], 'admin')
                                            
                                                @can('can edit', 'admin')
                                                    <button onclick="sendLoanEvent('{{$loan->id}}')"  class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Edit</button>
                                                @endcan
                                                @can('can delete', 'admin')
                                                    <button onclick="sendDeleteEvent('{{ $loan->id }}')" 
                                                    class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>
                                                @endcan
                                            
                                            @endcanany
                                        </td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                        
                    <!-- </div> -->
                    <div class="row mt-4">
                        <div class="col-sm-6 offset-5">
                            {{ $loans->links() }}
                        </div>
                    </div>
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

    <!-- Theme Custom -->
	<!-- <script src="{{ asset('js/custom.js') }}"></script> -->

    <script>
        function sendLoanEvent(value) {
            Livewire.dispatch('edit-loans', { id: value });
        }

        function sendCompleteEvent(value) {
            Livewire.dispatch('complete-loans', { id: value });
        }

        function sendDeleteEvent(value) {
            var result = confirm("Are you sure you want to delete this loan details?");
            if (result) {
                // User clicked 'OK', dispatch delete event
                Livewire.dispatch('delete-loans', { id: value });
            }
        }
    </script>
    
</div>