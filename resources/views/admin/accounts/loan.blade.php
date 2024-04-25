@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Loans Records" subSection1="Loans captures" subSection2=""></x-topnavbar>
    <livewire:accounts.loan-capture />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
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
        });
    
        
    </script>
@endsection