<?php
    $title = "Export Data to Excel";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.business_topnavbar homeUrl="/" sectionName="Export Business Data (Excel file)" subSection1="Account" subSection2="Export Data (Excel file)"></x-navbars.business_topnavbar>
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Export Data to Excel from Database</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success auto-close">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger auto-close">
                                {{ session('error') }}
                            </div>
                        @endif
                        <h2>Export Item Categories Data (Excel file)</h2>
                        <a href="{{ route('cat_export') }}" target="_blank" class="btn btn-success">Export Item Categories Data</a>

                        <h2>Export The ItemCaptures Data (Excel file)</h2>
                        <a href="{{ route('itemcap_export') }}" target="_blank" class="btn btn-success">Export Item Capture Data</a>


                        <h2>Export The Repayments Capture Table(Excel file)</h2>
                        
                        <a href="{{ route('repaycap_export') }}" target="_blank" class="btn btn-success">Export Repays Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-scriptvendor></x-scriptvendor>

    
    {{-- Handle Browser dispatched Events --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
            });
        });
    
        
    </script>
@endsection