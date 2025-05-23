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
                    
                    <div class="row mb-3">
                        <div class="col-xl-6">
                            <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col widget-summary-col-icon">
                                            <div class="summary-icon bg-secondary">
                                                <i class="fas fa-naira-sign"></i>
                                            </div>
                                        </div>
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h3 class="">Loan Balance</h3>
                                                <div class="info">
                                                    <strong class="amount">&#8358; {{ ($activeLoan ? number_format($activeLoan->loanBalance, 2) : number_format(0, 2)) }} </strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-xl-6">
                            <section class="card card-featured-left card-featured-secondary">
                                <div class="card-body">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col widget-summary-col-icon">
                                            <div class="summary-icon bg-secondary">
                                                <!-- <i class="fas fa-naira-sign"></i> -->
                                            </div>
                                        </div>
                                        <div class="widget-summary-col">
                                            <div class="summary">
                                                <h3 class="">Loan Status</h3>
                                                <div class="info">
                                                    <strong class="amount"> {{ ($activeLoan ? 'On Loan' : '-')}}</strong>
                                                </div>
                                            </div>
                                            <div class="summary-footer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        
                    </div>

                    {{-- Modal for capturing new payment details --}}
                    <div x-cloak x-show="isOpen" x-transition:opacity.duration.500ms class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"  tabindex="-1">
                        <div class="bg-white rounded-lg w-1/2">
                            <div class="">
                                <div class="bg-gray-200 p-3 flex justify-between items-center rounded-t-lg">
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingPaymentId ? 'Edit Payment details' : 'Capture New Payment details' }}</h5>
                                    <button type="button" class="btn btn-danger transition duration-300" @click="isOpen = false; @this.set('isModalOpen', false);$wire.toggleModalClose()" aria-label="Close"><i class="fas fa-close"></i> Cancel</button>
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingPaymentId ? 'updatePayment' : 'savePayment' }}" class="row g-3">

                                        {{-- CoopId --}}
                                        <div class="col-md-6">
                                            <label for="title">Coop ID <span class="text-danger">*</span></label>
                                            <input type="text" id="coopId" class="form-control" placeholder="Coop ID" wire:model.live="paymentForm.coopId" {{ $editingPaymentId ? 'disabled' : '' }}/>
                                            @error('paymentForm.coopId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="title">Full Name:</label>
                                            <input type="text" id="fullname" class="form-control" placeholder="Full Name" value="{{$fullname}}" readonly />
                                        </div>
                                        @if($activeLoan)
                                        <!-- Loan Status & Balance -->
                                        <div class="col-md-6">
                                            <label for="title">Loan Balance: </label>
                                            <input type="text" id="loanBalance" class="form-control" placeholder="Full Name" value="&#8358; {{ ($activeLoan ? number_format($activeLoan->loanBalance, 2) : number_format(0, 2)) }}" readonly />
                                        </div>

                                        <div class="col-md-6">
                                            <label for="title">Loan Status:</label>
                                            <input type="text" id="loanStatus" class="form-control" placeholder="Full Name" value="{{ ($activeLoan ? 'On Loan' : '-')}}" readonly />
                                        </div>
                                        @endif

                                        <div class="col-md-6">
                                            <label for="splitOption" class="form-label">Split Option</label>
                                            <select class="form-select" wire:model.live="paymentForm.splitOption" id="splitOption">
                                                <option value="0">0</option>
                                                @foreach(range(1, 10) as $perc)
                                                    <option value="{{ $perc*10 }}">{{ $perc*10 }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <input type="hidden" value="{{ $prev_amount}}" id="prevAmount" />
                                        
                                            {{-- Amount --}}
                                        <div class="col-md-6">
                                            <label for="totalAmount">Total Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="totalAmount" class="form-control" placeholder="Total Amount" wire:model.blur="paymentForm.totalAmount" />
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
                                            <!-- Other Savings Type Dropdown -->
                                            <div class="form-group">
                                                <label for="otherSavingsType">Special Savings Type</label>
                                                <select id="otherSavingsType" class="form-control" wire:model="paymentForm.otherSavingsType">
                                                    <option value="">Select Savings Type</option>
                                                    <option value="special">Special savings</option>
                                                    <option value="hajj">Hajj</option>
                                                    <option value="ileya">Ileya</option>
                                                    <!-- Add more options as needed -->
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="others">Special Saving Amount <span class="text-danger">*</span></label>
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

                                            <button type="button" class="btn btn-success transition duration-300" onclick="{{ $editingPaymentId ? 'UpdateDataAfterValidation()' : 'sendDataAfterValidation()' }}">{{ $editingPaymentId ? 'Update' : 'Add' }} Payment</button>
                                        </div>


                                    </form>
                                    {{-- Form ends --}}
                                    
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Payment Records Table --}}
                    <div class="container-fluid " style="margin-bottom: 10px;">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group d-flex">
                                    <label for="datatable-tabletools_length" class="me-2">Records per page:</label>
                                    <select name="datatable-tabletools_length" class="form-select form-select-sm w-auto" data-select2-id="1" wire:model.live="paginate">
                                        <option value="10" data-select2-id="3">10</option>
                                        <option value="25" data-select2-id="18">25</option>
                                        <option value="50" data-select2-id="19">50</option>
                                        <option value="100" data-select2-id="20">100</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group d-flex">
                                    <label for="datatable-search" class="form-label me-2">Search:</label>
                                    <input type="search" id="datatable-search" wire:model.live.debounce.300ms="search" class="form-control w-auto" placeholder="Search By CoopId/Date" aria-controls="datatable-tabletools">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0 dataTable no-footer" id="datatable-tabletools22">

                            <thead>
                                <tr>
                                    <!-- <th>S/N</th> -->
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
                                <?php //$counter = 1; ?>

                                @foreach($payments as $payment)
                                    <tr wire:key="item-profile-{{ $payment->id }}">
                                        <!-- <td>{{-- $counter++ --}}</td> -->
                                        <td>{{ $payment->coopId }}</td>
                                        <td>{{ number_format($payment->totalAmount, 2) }}</td>
                                        <td>{{ number_format($payment->savingAmount, 2) }}</td>
                                        <td>{{ number_format($payment->shareAmount, 2) }}</td>
                                        <td>{{ number_format($payment->loanAmount, 2) }}</td>
                                        <td>{{ number_format($payment->others, 2) }}</td>
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
                        
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 dataTables_paginate paging_simple_numbers">
                            {{ $payments->links() }}
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

        function sendDataAfterValidation() {
            let coopId = document.getElementById('coopId').value;
            let totalAmount = document.getElementById('totalAmount').value.replace(/,/g, '');
            let loanAmount = document.getElementById('loanAmount').value.replace(/,/g, '');
            let splitOption = document.getElementById('splitOption').value;
            let savingAmount = document.getElementById('savingAmount').value.replace(/,/g, '');
            let shareAmount = document.getElementById('shareAmount').value.replace(/,/g, '');
            let others = document.getElementById('otherAmount').value.replace(/,/g, '');
            let adminCharge = document.getElementById('adminCharge').value;

            if(loanAmount === '' || savingAmount === '' || shareAmount === '' || others === '' || adminCharge === '') {
                alert("All fields are required..");
                return;
            }else if(isNaN(Number(totalAmount)) || isNaN(Number(loanAmount)) || isNaN(Number(savingAmount)) || isNaN(Number(shareAmount)) || isNaN(Number(others)) || isNaN(adminCharge)) {
                alert("All fields must be numbers..");
                return;
            } 
            
            if(totalAmount < 0 || loanAmount < 0 || savingAmount < 0 || shareAmount < 0 || others < 0 || adminCharge < 0) {
                alert("All fields must be greater than zero..");
                return;
            }

            if ((Number(loanAmount) + Number(savingAmount) + Number(shareAmount) + Number(others) + Number(adminCharge)) !== Number(totalAmount)) {
                alert("Your computation cannot be greater than the TOTAL.");
            }else{
                Livewire.dispatch('save-payments', {id: coopId,totalAmount: totalAmount, loanAmount: loanAmount, splitOption: splitOption, savingAmount: savingAmount, shareAmount: shareAmount, others: others, adminCharge: adminCharge});
            }

            
        }

        function calculatePercent() {
            let totalAmount = parseFloat(document.getElementById('totalAmount').value.replace(/,/g, ''));
            let loanAmount = parseFloat(document.getElementById('loanAmount').value.replace(/,/g, ''));
            let splitOption = parseFloat(document.getElementById('splitOption').value);
            let others = parseFloat(document.getElementById('otherAmount').value.replace(/,/g, ''));
            let adminCharge = 0;

            // convert total amount to number
            if (isNaN(totalAmount)) {
                alert("Total Amount cannot be empty..");
                return;
            }

            // Admin charge computation logic
            if (totalAmount >= 10000) {
                adminCharge = 50;
            }

            // calculate the remaining total after charges
            let remainingTotal = totalAmount - others;

            // calculate the savings and shares based on the split option
            let savingAmount = ((remainingTotal - loanAmount) * (100 - splitOption) / 100) - adminCharge;
            let shareAmount = (remainingTotal - loanAmount) * splitOption / 100;

            // Ensure savingAmount and shareAmount are not negative
            if (savingAmount < 0) {
                adminCharge += savingAmount;
                savingAmount = 0;
            }

            // Update input fields with the computed values
            document.getElementById('adminCharge').value = adminCharge;
            document.getElementById('savingAmount').value = savingAmount.toLocaleString('en-US');
            document.getElementById('shareAmount').value = shareAmount.toLocaleString('en-US');
        
        }

        // When saving amount is changed, calculate the share and savings amount
        function recalculateFromSavings()
        {
            let totalAmount = parseFloat(document.getElementById('totalAmount').value.replace(/,/g, ''));
            let loanAmount = parseFloat(document.getElementById('loanAmount').value.replace(/,/g, ''));
            let savingAmount = parseFloat(document.getElementById('savingAmount').value.replace(/,/g, ''));
            let shareAmount = parseFloat(document.getElementById('shareAmount').value.replace(/,/g, ''));
            let others = parseFloat(document.getElementById('otherAmount').value.replace(/,/g, ''));
            let adminCharge = parseFloat(document.getElementById('adminCharge').value);

            // convert total amount to number
            if (isNaN(totalAmount)) {
                alert("Total Amount cannot be empty..");
                return;
            }

            // calculate the remaining total after charges
            let remainingTotal = totalAmount - adminCharge - loanAmount - others;

            // calculate the savings and shares based on the split option
            let newShareAmount = remainingTotal - savingAmount;

            // Update input fields with the computed values
            document.getElementById('shareAmount').value = newShareAmount.toLocaleString('en-US');
            document.getElementById('otherAmount').value = others.toLocaleString('en-US');
        }

        // When share amount is changed, calculate the savings and loan amount
        function recalculateFromShares()
        {
            let totalAmount = parseFloat(document.getElementById('totalAmount').value.replace(/,/g, ''));
            let loanAmount = parseFloat(document.getElementById('loanAmount').value.replace(/,/g, ''));
            let shareAmount = parseFloat(document.getElementById('shareAmount').value.replace(/,/g, ''));
            let others = parseFloat(document.getElementById('otherAmount').value.replace(/,/g, ''));
            let adminCharge = parseFloat(document.getElementById('adminCharge').value);

            // convert total amount to number
            if (isNaN(totalAmount)) {
                alert("Total Amount cannot be empty..");
                return;
            }

            // calculate the remaining total after charges
            let remainingTotal = totalAmount - adminCharge - loanAmount - others;

            // calculate the savings and shares
            let newSavingAmount = remainingTotal - shareAmount;

            // Update input fields with the computed values
            document.getElementById('savingAmount').value = newSavingAmount.toLocaleString('en-US');
            document.getElementById('otherAmount').value = others.toLocaleString('en-US');
        }

        function UpdateDataAfterValidation() {
            let coopId = document.getElementById('coopId').value;
            let totalAmount = document.getElementById('totalAmount').value.replace(/,/g, '');
            let loanAmount = document.getElementById('loanAmount').value.replace(/,/g, '');
            let splitOption = document.getElementById('splitOption').value;
            let savingAmount = document.getElementById('savingAmount').value.replace(/,/g, '');
            let shareAmount = document.getElementById('shareAmount').value.replace(/,/g, '');
            let others = document.getElementById('otherAmount').value.replace(/,/g, '');
            let adminCharge = document.getElementById('adminCharge').value;
            let prevAmount = document.getElementById('prevAmount').value;

            if(loanAmount === '' || savingAmount === '' || shareAmount === '' || others === '' || adminCharge === '') {
                alert("All fields are required..");
                return;
            }else if(isNaN(Number(totalAmount)) || isNaN(Number(loanAmount)) || isNaN(Number(savingAmount)) || isNaN(Number(shareAmount)) || isNaN(Number(others)) || isNaN(adminCharge)) {
                alert("All fields must be numbers..");
                return;
            }  
            
            if(totalAmount < 0 || loanAmount < 0 || savingAmount < 0 || shareAmount < 0 || others < 0 || adminCharge < 0) {
                alert("All fields must be >= zero..");
                return;
            }

            if ((Number(loanAmount) + Number(savingAmount) + Number(shareAmount) + Number(others) + Number(adminCharge)) !== Number(totalAmount)) {
                alert("Your computation cannot be greater than the TOTAL.");
            }else{
                Livewire.dispatch('update-payments', {id: coopId,totalAmount: totalAmount, loanAmount: loanAmount, splitOption: splitOption, savingAmount: savingAmount, shareAmount: shareAmount, others: others, adminCharge: adminCharge, prevAmount: prevAmount});
            }

            
        }


        // Event listener for the split option
        document.getElementById('splitOption').addEventListener('change', calculatePercent);

        // Add an input event on totalAmount id
        document.getElementById('totalAmount').addEventListener('input', calculatePercent);
        // Add an input event on loanAmount id
        document.getElementById('loanAmount').addEventListener('input', calculatePercent);
        // Add an input event on savingAmount id
        document.getElementById('otherAmount').addEventListener('input', calculatePercent);

        
        // Event listener for user manipulations of the savings and shares
        // Add an input event on savingAmount id
        document.getElementById('savingAmount').addEventListener('input', recalculateFromSavings);
        // Add an input event on shareAmount id
        document.getElementById('shareAmount').addEventListener('input', recalculateFromShares);
        

    </script>
    

</div>