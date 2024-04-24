{{-- New Register Modal Form --}}
<div wire:ignore.self id="MemberModal" class="modal fade"  tabindex="-1"
        aria-labelledby="MemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="MemberModalLabel">New Member</h5>
                    <button type="button" wire:click="closeModal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Form starts --}}
                    <form wire:submit.prevent="saveMember" class="row g-3">

                        {{-- CoopId --}}
                        <div class="col-md-12">
                            <label for="title">Assign Coop ID <span class="text-danger">*</span></label>
                            <input type="text"  class="form-control" placeholder="Coop ID" wire:model.live="coopId" />
                            @error('coopId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
        
                            {{-- Surname --}}
                        <div class="col-md-6">
                            <label for="surname">Surname <span class="text-danger">*</span></label>
                            <input type="text"  class="form-control" placeholder="Surname" wire:model.live="surname" />
                            @error('surname') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Other Names --}}
                        <div class="col-md-6">
                            <label for="otherNames">Other Names </label>
                            <input type="text"  class="form-control" placeholder="Other Names" wire:model.live="otherNames" />
                        </div>

                        {{-- Occupation --}}
                        <div class="col-md-6">
                            <label for="occupation" class="form-label">Occupation </label>
                            <input type="text"  class="form-control" placeholder="Occupation" wire:model.live="occupation" />
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender </label>
                            <select class="form-select" wire:model.live="gender">
                                <option value="">--Select--</option>
                                <option value="Male">Male</option>
                                <option value="Male">Female</option>
                            </select>
                        </div>

                        {{-- Religion --}}
                        <div class="col-md-6">
                            <label for="religion" class="form-label">Religion </label>
                            <select class="form-select" wire:model.live="religion">
                                <option value="">--Select--</option>
                                <option value="Male">Islam</option>
                                <option value="Male">Christianity</option>
                            </select>
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-md-6">
                            <label for="phoneNumber" class="form-label">Phone Number </label>
                            <input type="text"  class="form-control" placeholder="Phone Number" wire:model.live="phoneNumber" />
                        </div>

                        {{-- Account Number --}}
                        <div class="col-md-6">
                            <label for="accountNumber" class="form-label">Account Number </label>
                            <input type="text"  class="form-control" placeholder="Account Number" wire:model.live="accountNumber" />
                        </div>

                        {{-- Bank Name --}}
                        <div class="col-md-6">
                            <label for="bankName" class="form-label">Bank Name </label>
                            <input type="text"  class="form-control" placeholder="Bank Name" wire:model.live="bankName" />
                        </div>

                        {{-- Next of Kin Name --}}
                        <div class="col-md-6">
                            <label for="nextOfKinName" class="form-label">Next of Kin Name </label>
                            <input type="text"  class="form-control" placeholder="Next of Kin Name" wire:model.live="nextOfKinName" />
                        </div>

                        {{-- Next of Kin Phone Number --}}
                        <div class="col-md-6">
                            <label for="nextOfKinPhoneNumber" class="form-label">Next of Kin Phone Number </label>
                            <input type="text"  class="form-control" placeholder="Next of Kin Phone Number" wire:model.live="nextOfKinPhoneNumber" />
                        </div>

                        {{-- Year Joined --}}
                        <div class="col-md-6">
                            <label for="yearJoined" class="form-label">Year Joined </label>
                            <select class="form-select" wire:model.live="yearJoined">
                                <option value="">--Select--</option>
                                @foreach(range(2013, date('Y')) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="text-end">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-secondary">Close</button>

                            <button type="submit" class="btn btn-success">{{ $isEdit ? 'Update' : 'Save' }}</button>
                        </div>

                    </form>
                    {{-- Form ends --}}
                    
                </div>
            </div>
        </div>
    </div>

    {{-- Update Modal Form --}}
    <div wire:ignore.self id="MemberUpdateModal" class="modal fade"  tabindex="-1"
        aria-labelledby="MemberUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="MemberUpdateModalLabel">Update Member</h5>
                    <button type="button" wire:click="closeModal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Form starts --}}
                    <form wire:submit.prevent="saveMember" class="row g-3">
                        {{-- Id --}}
                        <input type="hidden" wire:model="id" name="id">
                        {{-- CoopId --}}
                        <div class="col-md-12">
                            <label for="title">Assign Coop ID <span class="text-danger">*</span></label>
                            <input type="text" name="coopId" class="form-control" placeholder="Coop ID" wire:model="coopId" />
                            @error('coopId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
        
                            {{-- Surname --}}
                        <div class="col-md-6">
                            <label for="surname">Surname <span class="text-danger">*</span></label>
                            <input type="text" name="surname" class="form-control" placeholder="Surname" wire:model="surname" />
                            @error('surname') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Other Names --}}
                        <div class="col-md-6">
                            <label for="otherNames">Other Names </label>
                            <input type="text" name="otherNames" class="form-control" placeholder="Other Names" wire:model="otherNames" />
                        </div>

                        {{-- Occupation --}}
                        <div class="col-md-6">
                            <label for="occupation" class="form-label">Occupation </label>
                            <input type="text" name="occupation" class="form-control" placeholder="Occupation" wire:model="occupation" />
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender </label>
                            <select class="form-select" wire:model="gender" id="gender">
                                <option value="">--Select--</option>
                                <option value="Male">Male</option>
                                <option value="Male">Female</option>
                            </select>
                        </div>

                        {{-- Religion --}}
                        <div class="col-md-6">
                            <label for="religion" class="form-label">Religion </label>
                            <select class="form-select" wire:model="religion" id="religion">
                                <option value="">--Select--</option>
                                <option value="Male">Islam</option>
                                <option value="Male">Christianity</option>
                            </select>
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-md-6">
                            <label for="phoneNumber" class="form-label">Phone Number </label>
                            <input type="text"  class="form-control" placeholder="Phone Number" name="phoneNumber" wire:model="phoneNumber" />
                        </div>

                        {{-- Account Number --}}
                        <div class="col-md-6">
                            <label for="accountNumber" class="form-label">Account Number </label>
                            <input type="text"  class="form-control" placeholder="Account Number" name="accountNumber" wire:model="accountNumber" />
                        </div>

                        {{-- Bank Name --}}
                        <div class="col-md-6">
                            <label for="bankName" class="form-label">Bank Name </label>
                            <input type="text"  class="form-control" placeholder="Bank Name" name="bankName" wire:model="bankName" />
                        </div>

                        {{-- Next of Kin Name --}}
                        <div class="col-md-6">
                            <label for="nextOfKinName" class="form-label">Next of Kin Name </label>
                            <input type="text"  class="form-control" placeholder="Next of Kin Name" name="nextOfKinName" wire:model="nextOfKinName" />
                        </div>

                        {{-- Next of Kin Phone Number --}}
                        <div class="col-md-6">
                            <label for="nextOfKinPhoneNumber" class="form-label">Next of Kin Phone Number </label>
                            <input type="text"  class="form-control" placeholder="Next of Kin Phone Number" name="nextOfKinPhoneNumber" wire:model="nextOfKinPhoneNumber" />
                        </div>

                        {{-- Year Joined --}}
                        <div class="col-md-6">
                            <label for="yearJoined" class="form-label">Year Joined </label>
                            <select class="form-select" wire:model="yearJoined" id="yearJoined">
                                <option value="">--Select--</option>
                                @foreach(range(2013, date('Y')) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="text-end">
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-secondary">Close</button>

                            <button type="submit" class="btn btn-success">{{ $isEdit ? 'Update' : 'Save' }}</button>
                        </div>

                    </form>
                    {{-- Form ends --}}
                    
                </div>
            </div>
        </div>
        <script>
            Livewire.on('close-modal', () => {
                $('#MemberModal').modal('hide');
                $('#MemberUpdateModal').modal('hide');
                (function($) {

                    'use strict';

                    var datatableInit = function() {
                        var $table = $('#datatable-tabletools');

                        var $table = $table.dataTable({
                            sDom: '<"text-right mb-md"T><"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p',
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: 'Excel',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    }
                                },
                                {
                                    extend: 'pdf',
                                    text: 'PDF',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    },
                                    customize : function(doc){
                                        var colCount = new Array();
                                        $('#datatable-tabletools').find('tbody tr:first-child td').each(function(){
                                            if($(this).attr('colspan')){
                                                for(var i=1;i<=$(this).attr('colspan');$i++){
                                                    colCount.push('*');
                                                }
                                            }else{ colCount.push('*'); }
                                        });
                                        doc.content[1].table.widths = colCount;
                                    }
                                        
                                },
                            ]
                        });

                        $('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-tabletools_wrapper');

                        $table.DataTable().buttons().container().prependTo( '#datatable-tabletools_wrapper .dt-buttons' );

                        $('#datatable-tabletools_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
                    };

                    $(function() {
                        datatableInit();
                    });

                }).apply(this, [jQuery]);

                (function($) {

                    'use strict';

                    var datatableInit2 = function() {
                        var table = $('#datatable-tabletools2');

                        var table2 = table.dataTable({
                            sDom: '<"text-right mb-md"T><"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p',
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print Others'
                                },
                                {
                                    extend: 'excel',
                                    text: 'Excel-Others'
                                },
                                {
                                    extend: 'pdf',
                                    text: 'PDF-Others',
                                    customize : function(doc){
                                        var colCount = new Array();
                                        $('#datatable-tabletools2').find('tbody tr:first-child td').each(function(){
                                            if($(this).attr('colspan')){
                                                for(var i=1;i<=$(this).attr('colspan');$i++){
                                                    colCount.push('*');
                                                }
                                            }else{ colCount.push('*'); }
                                        });
                                        doc.content[1].table.widths = colCount;
                                    }
                                }
                            ]
                        });

                        $('<div />').addClass('dt-buttons mb-2 pb-1 text-end').prependTo('#datatable-tabletools2_wrapper');

                        table2.DataTable().buttons().container().prependTo( '#datatable-tabletools2_wrapper .dt-buttons' );

                        $('#datatable-tabletools2_wrapper').find('.btn-secondary').removeClass('btn-secondary').addClass('btn-default');
                    };

                    $(function() {
                        datatableInit2();
                    });

                }).apply(this, [jQuery]);

            });
        </script>
    </div>













































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
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <button class="btn btn-primary" @click="isOpen = true; @this.set('isModalOpen', true)">Add New <i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div x-cloak x-show="isOpen" x-transition:opacity.duration.500ms class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"  tabindex="-1">
                        <div class="bg-white rounded-lg w-1/2">
                            <div class="">
                                <div class="bg-gray-200 p-3 flex justify-between items-center rounded-t-lg">
                                    <h5 class="modal-title fw-bold" id="MemberModalLabel">{{ $editingMemberId ? 'Edit Member details' : 'Add new details' }}</h5>
                                    <button type="button" class="btn-close transition duration-300" @click="isOpen = false; @this.set('isModalOpen', false)" aria-label="Close"></button>
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
                                            <input type="text"  class="form-control" placeholder="Surname" wire:model="memberForm.surname" />
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
                                                <option value="Male">Female</option>
                                            </select>
                                        </div>

                                        {{-- Religion --}}
                                        <div class="col-md-6">
                                            <label for="religion" class="form-label">Religion </label>
                                            <select class="form-select" wire:model="memberForm.religion">
                                                <option value="">--Select--</option>
                                                <option value="Male">Islam</option>
                                                <option value="Male">Christianity</option>
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
                                            <!-- <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-secondary">Close</button> -->

                                            <button type="submit" class="btn btn-success transition duration-300">{{ $editingMemberId ? 'Update' : 'Add' }} Member</button>
                                        </div>

                                    </form>
                                    {{-- Form ends --}}
                                    
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Members Table --}}
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
                                <th>Actions</th>
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
                                    <td class="">
                                    
                                        <button wire:click="editOldMember('{{ $mem->id }}')" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></button>
                                        <button wire:click="deleteOldMember({{ $mem->id }})" 
                                            class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
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
                </div>
            </section>
        </div>
    </div>

    <x-scriptvendor></x-scriptvendor>

    
    <!-- Specific Page Vendor -->
    <script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/media/js/dataTables.bootstrap5.min.js"></script>
    <script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js"></script>
    <script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js"></script>
    <script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js"></script>
    <script src="vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js"></script>
    <script src="vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js"></script>
    <script src="vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js"></script>

    <!-- Theme Custom -->
	<script src="{{ asset('js/custom.js') }}"></script>

</div>
{{-- </div> --}}


