{{-- <div> --}}
{{-- @include('livewire.members.member-modals') --}}
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
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingMemberId ? 'Edit Member details' : 'Add new details' }}</h5>
                                    <button type="button" class="btn btn-danger transition duration-300" @click="isOpen = false; @this.set('isModalOpen', false);$wire.toggleModalClose()" aria-label="Close"><i class="fas fa-close"></i> Cancel</button>
                                </div>
                                <div class="p-5">

                                    {{-- Form starts --}}
                                    <form wire:submit="{{ $editingMemberId ? 'updateMember' : 'saveMember' }}" class="row g-3">

                                        {{-- CoopId --}}
                                        <div class="col-md-12">
                                            <label for="title">Assign Coop ID <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Coop ID" wire:model="memberForm.coopId" {{ $editingMemberId ? 'disabled' : '' }}/>
                                            @error('memberForm.coopId') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                        
                                            {{-- Surname --}}
                                        <div class="col-md-6">
                                            <label for="surname">Surname <span class="text-danger">*</span></label>
                                            <input type="text"  class="form-control" placeholder="Surname" wire:model.live="memberForm.surname" />
                                            @error('memberForm.surname') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Other Names --}}
                                        <div class="col-md-6">
                                            <label for="otherNames">Other Names </label>
                                            <input type="text"  class="form-control" placeholder="Other Names" wire:model="memberForm.otherNames" />
                                        </div>

                                        {{-- Occupation --}}
                                        <div class="col-md-6">
                                            <label for="occupation" class="form-label">Occupation </label>
                                            <input type="text"  class="form-control" placeholder="Occupation" wire:model="memberForm.occupation" />
                                        </div>

                                        {{-- Gender --}}
                                        <div class="col-md-6">
                                            <label for="gender" class="form-label">Gender </label>
                                            <select class="form-select" wire:model="memberForm.gender">
                                                <option value="">--Select--</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>

                                        {{-- Religion --}}
                                        <div class="col-md-6">
                                            <label for="religion" class="form-label">Religion </label>
                                            <select class="form-select" wire:model="memberForm.religion">
                                                <option value="">--Select--</option>
                                                <option value="Islam">Islam</option>
                                                <option value="Christianity">Christianity</option>
                                            </select>
                                        </div>

                                        {{-- Phone Number --}}
                                        <div class="col-md-6">
                                            <label for="phoneNumber" class="form-label">Phone Number </label>
                                            <input type="text"  class="form-control" placeholder="Phone Number" wire:model="memberForm.phoneNumber" />
                                        </div>

                                        {{-- Account Number --}}
                                        <div class="col-md-6">
                                            <label for="accountNumber" class="form-label">Account Number </label>
                                            <input type="text"  class="form-control" placeholder="Account Number" wire:model="memberForm.accountNumber" />
                                        </div>

                                        {{-- Bank Name --}}
                                        <div class="col-md-6">
                                            <label for="bankName" class="form-label">Bank Name </label>
                                            <input type="text"  class="form-control" placeholder="Bank Name" wire:model="memberForm.bankName" />
                                        </div>

                                        {{-- Next of Kin Name --}}
                                        <div class="col-md-6">
                                            <label for="nextOfKinName" class="form-label">Next of Kin Name </label>
                                            <input type="text"  class="form-control" placeholder="Next of Kin Name" wire:model="memberForm.nextOfKinName" />
                                        </div>

                                        {{-- Next of Kin Phone Number --}}
                                        <div class="col-md-6">
                                            <label for="nextOfKinPhoneNumber" class="form-label">Next of Kin Phone Number </label>
                                            <input type="text"  class="form-control" placeholder="Next of Kin Phone Number" wire:model="memberForm.nextOfKinPhoneNumber" />
                                        </div>

                                        {{-- Year Joined --}}
                                        <div class="col-md-6">
                                            <label for="yearJoined" class="form-label">Year Joined </label>
                                            <select class="form-select" wire:model="memberForm.yearJoined">
                                                <option value="">--Select--</option>
                                                @foreach(range(2013, date('Y')) as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="text-end">
                                            
                                            <button type="submit" class="btn btn-success transition duration-300">{{ $editingMemberId ? 'Update' : 'Add' }} Member</button>
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
                                    <th>Coop Id</th>
                                    <th>Surname</th>
                                    <th>Other Names</th>
                                    <th>Occupation</th>
                                    <th>Gender</th>
                                    <th>Religion</th>
                                    <th>Phone</th>
                                    @canAny(['can edit', 'can delete'], 'admin')
                                    <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $mem)
                                    <tr wire:key="item-profile-{{ $mem->id }}">

                                        <td>{{ $mem->coopId }}</td>
                                        <td>{{ $mem->surname }}</td>
                                        <td>{{ $mem->otherNames }}</td>
                                        <td>{{ $mem->occupation }}</td>
                                        <td>{{ $mem->gender }}</td>
                                        <td>{{ $mem->religion }}</td>
                                        <td>{{ $mem->phoneNumber }}</td>
                                        @canAny(['can edit', 'can delete'], 'admin')
                                        <td class="">
                                            @can('can edit', 'admin')
                                                <button onclick="sendMemEvent('{{$mem->id}}')"  class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i> Edit</button>
                                            @endcan
                                            @can('can delete', 'admin')
                                                <button onclick="sendDeleteEvent('{{ $mem->id }}')" 
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
                            {{-- $members->links() --}}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <section class="card">
                <header class="card-header">
                    <h2 class="card-title">Other Details</h2>
                </header>
                <div class="card-body">
                    <!-- <div  class="table-responsive"> -->
                        <table class="table table-bordered table-striped mb-0" id="datatable-tabletools2">

                            <thead>
                                <tr>
                                    <th>Coop Id</th>
                                    <th>Account</th>
                                    <th>Bank</th>
                                    <th>Next of kin Name</th>
                                    <th>Next of kin Phone</th>
                                    <th>Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $mem)
                                    <tr wire:key="item-other-{{ $mem->id }}">

                                        <td>{{ $mem->coopId }}</td>
                                        <td>{{ $mem->accountNumber }}</td>
                                        <td>{{ $mem->bankName }}</td>
                                        <td>{{ $mem->nextOfKinName }}</td>
                                        <td>{{ $mem->nextOfKinPhoneNumber }}</td>
                                        <td>{{ $mem->yearJoined }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    <!-- </div> -->
                    <div class="row mt-4">
                        <div class="col-sm-6 offset-5">
                            {{-- $members->links() --}}
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
        function sendMemEvent(value) {
            Livewire.dispatch('edit-members', { id: value });
        }

        
        function sendDeleteEvent(value) {
            var result = confirm("Are you sure you want to delete this member?");
            if (result) {
                // User clicked 'OK', dispatch delete event
                Livewire.dispatch('delete-members', { id: value });
            }
        }
       
    </script>

    
</div>
{{-- </div> --}}