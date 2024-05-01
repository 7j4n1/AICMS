<div x-data="{ isOpen: @entangle('isModalOpen') }" class="container">
    <!-- Button to open the modal for capturing new payment details -->
    <div class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">All Payment Records</h2>
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
                                <button class="btn btn-primary" @click="isOpen = true; @this.set('isModalOpen', true);">Capture New Payment <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div x-cloak x-show="isOpen" x-transition:opacity.duration.500ms class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"  tabindex="-1">
                        <div class="bg-white rounded-lg w-1/2">
                            <div class="">
                                <div class="bg-gray-200 p-3 flex justify-between items-center rounded-t-lg">
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingPaymentId ? 'Edit Payment details' : 'Capture New Payment details' }}</h5>
                                    <button type="button" class="btn-close transition duration-300" @click="isOpen = false; @this.set('isModalOpen', false);$wire.toggleModalClose()" aria-label="Close"></button>
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingPaymentId ? 'updatePayment' : 'savePayment' }}" class="row g-3">

                                        {{-- CoopId --}}
                                        <div class="col-md-12">
                                            <label for="title">Coop ID <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Coop ID" wire:model.live="paymentForm.coopId" {{ $editingPaymentId ? 'disabled' : '' }}/>
                                            @error('paymentForm.coopId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="splitOption" class="form-label">Split Option</label>
                                            <select class="form-select" wire:model.live="paymentForm.splitOption" id="splitOption">
                                                <option value="0">0</option>
                                                @foreach(range(1, 10) as $perc)
                                                    <option value="{{ $perc*10 }}">{{ $perc*10 }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                            {{-- Amount --}}
                                        <div class="col-md-6">
                                            <label for="totalAmount">Total Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="totalAmount" class="form-control" placeholder="Total Amount" wire:model.live="paymentForm.totalAmount" />
                                            @error('paymentForm.totalAmount') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                        
                                            {{-- Loan Amount --}}
                                        <div class="col-md-6">
                                            <label for="loanAmount">Loan Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="loanAmount" class="form-control" placeholder="Loan Amount" wire:model.live="paymentForm.loanAmount"  />
                                            @error('paymentForm.loanAmount') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>


                                            {{-- Savings Amount --}}
                                        <div class="col-md-6">
                                            <label for="savingAmount">Savings Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="savingAmount" class="form-control"  placeholder="Savings Amount" wire:model.live="paymentForm.savingAmount" />
                                            @error('paymentForm.savingAmount') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                            {{-- Shares Amount --}}
                                        <div class="col-md-6">
                                            <label for="shareAmount">Shares Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="shareAmount" class="form-control" placeholder="Shares Amount" wire:model.live="paymentForm.shareAmount"  />
                                            @error('paymentForm.shareAmount') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="others">Others <span class="text-danger">*</span></label>
                                            <input type="text" id="otherAmount" class="form-control" placeholder="Others Amount" wire:model.live="paymentForm.others"  />
                                            @error('paymentForm.others') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="adminCharge">Admin Charge <span class="text-danger">*</span></label>
                                            <input type="text" id="adminCharge" class="form-control" placeholder="admin Charge" wire:model.live="paymentForm.adminCharge"  />
                                            @error('paymentForm.adminCharge') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Payment Date --}}
                                        <div class="col-md-6">
                                            <label for="otherNames">Payment Date </label>
                                            <input type="date"  class="form-control" placeholder="Payment Date (mm-dd-yyyy)" wire:model="paymentForm.paymentDate" />
                                            @error('paymentForm.paymentDate') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        

                                        <div class="text-end">

                                            <button type="submit" class="btn btn-success transition duration-300">{{ $editingPaymentId ? 'Update' : 'Add' }} Payment</button>
                                        </div>


                                    </form>
                                    {{-- Form ends --}}
                                    
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Payment Records Table --}}
                    <!-- class="table-responsive"> -->
                        <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">

                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Coop Id</th>
                                    <th>Total Amount</th>
                                    <th>Savings</th>
                                    <th>Shares</th>
                                    <th>Loans</th>
                                    <th>Others</th>
                                    <th>Split</th>
                                    <th>Capture Date</th>
                                    @canAny(['can edit', 'can delete'], 'admin')
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>

                                @foreach($payments as $payment)
                                    <tr wire:key="item-profile-{{ $payment->id }}">
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $payment->coopId }}</td>
                                        <td>{{ $payment->totalAmount }}</td>
                                        <td>{{ $payment->savingAmount }}</td>
                                        <td>{{ $payment->shareAmount }}</td>
                                        <td>{{ $payment->loanAmount }}</td>
                                        <td>{{ $payment->others }}</td>
                                        <td>{{ $payment->splitOption }}</td>
                                        <td>{{ $payment->paymentDate }}</td>
                                        @canany(['can edit', 'can delete'], 'admin')
                                            <td class="">
                                                @can('can edit', 'admin')
                                                    <button onclick="sendMsg('{{ $payment->id }}')" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Edit</button>
                                                @endcan
                                                @can('can delete', 'admin')
                                                    <button onclick="sendDeleteEvent('{{ $payment->id }}')" 
                                                        class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                        
                    <!-- </div> -->
                    <div class="row mt-4">
                        <div class="col-sm-6 offset-5">
                            {{-- $payments->links() --}}
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

    <script>
        function sendMsg(value) {
            Livewire.dispatch('edit-payments', { id: value });
        }

        function sendDeleteEvent(value) {
            var result = confirm("Are you sure you want to delete this Payment details?");
            if (result) {
                // User clicked 'OK', dispatch delete event
                Livewire.dispatch('delete-payments', { id: value });
            }
        }

        function calculatePercent() {
            let totalAmount = document.getElementById('totalAmount');
            let loanAmount = document.getElementById('loanAmount');
            let splitOption = document.getElementById('splitOption').value;
            let savingAmount = document.getElementById('savingAmount');
            let shareAmount = document.getElementById('shareAmount');
            let others = document.getElementById('otherAmount');
            let adminCharge = document.getElementById('adminCharge');

            let total = parseFloat(totalAmount.value);
            let loan = parseFloat(loanAmount.value);
            let split = parseFloat(splitOption);
            let charge = parseFloat(adminCharge.value);
            
            let saving = total - loan;
            let share = total - loan;
            if (split > 0) {
                let remainBalance = total - loan;
                if(charge > 0)
                    remainBalance = remainBalance - charge;
                if(others.value > 0)
                    remainBalance = remainBalance - parseFloat(others.value);
                share = (split / 100) * remainBalance;
                saving = remainBalance - share;
                
            }
            savingAmount.value = saving;
            shareAmount.value = share;
        }

        // subtract the loan amount from the total amount to get the saving amount and shares amount based on the split option changes
        // and each value changes events
        // document.getElementById('splitOption').addEventListener('change', function() {
        //     calculatePercent();
        // });


    </script>
    

</div>