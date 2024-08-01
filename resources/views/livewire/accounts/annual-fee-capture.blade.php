
<div x-data="{ isOpen: $wire.entangle('isModalOpen').live }" class="container">
    
    <!-- Button to open the modal for adding a new loan items details -->
    <div x-show="! isOpen" class="row mb-3">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">List of all Annual Fee Captures</h2>
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
                                <button type="button" x-show="! isOpen" x-on:click="isOpen = ! isOpen" class="btn btn-primary" >New Annual fee <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        
                    </div>
                    
                    {{-- Annual fees Table --}}
                    <!-- <div class="table-responsive"> -->
                    <div >
                    <div class="col-md-2 mb-2">
                        <div class="form-group">
                            <label for="coopId">By Year</label>
                            <select class="form-select" wire:model.live="year">
                                @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped mb-0" id="datatable-tabletools">

                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Coop Id</th>
                                <th>Savings</th>
                                <th>Fee</th>
                                <th>Balance</th>
                                {{-- @canAny(['can edit', 'can delete'], 'admin')
                                <th>Actions</th>
                                @endcanany --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->annualFees as $fee)
                                <tr wire:key="item-profile-{{ $fee->id }}">
                                    <td>{{ $fee->annual_year }}</td>
                                    <td>{{ $fee->coopId }}</td>
                                    <td>{{ number_format($fee->annual_savings, 2) }}</td>
                                    <td>{{ number_format($fee->annual_fee, 2) }}</td>
                                    <td>{{ number_format($fee->total_savings, 2) }}</td>
                                    {{-- @canAny(['can edit', 'can delete'], 'admin')
                                    <td class="">
                                        @can('can delete', 'admin')
                                            <button onclick="sendDeleteEvent('{{ $itemcapture->id }}')" 
                                            class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</button>
                                        @endcan
                                    </td>
                                    @endcanany --}}
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    
                    </div>
                    
                </div>
            </section>
        </div>
    </div>

    <div x-show="isOpen">
        @include('livewire.accounts.annualpay')
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

        document.getElementById('amount_fee').addEventListener('input', function() {
            let amount_fee = document.getElementById('amount_fee');
            // remove formatting before a new one is applied
            amount_fee.value = amount_fee.value.replace(/,/g, '');
            amount_fee.value = new Intl.NumberFormat().format(Number(amount_fee.value));
            
        });
       
    </script>

    
</div>
{{-- </div> --}}