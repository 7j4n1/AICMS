<?php
    $title = "Export Data to Excel";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Export Member Data (Excel file)" subSection1="Account" subSection2="Export Data (Excel file)"></x-topnavbar>
    
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
                        <h2>Export Member Data (Excel file)</h2>
                        <a href="{{ route('exportMembers') }}" target="_blank" class="btn btn-success">Export Member Data</a>

                        <h2>Export The Ledger (Excel file)</h2>
                        <a href="{{ route('exportLedgers') }}" target="_blank" class="btn btn-success">Export Payments Data</a>


                        <h2>Export The Active Loans Table(Excel file)</h2>
                        
                        <a href="{{ route('exportLoans') }}" target="_blank" class="btn btn-success">Export Active Loans Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-scriptvendor></x-scriptvendor>

    
    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2, 3, 4, 5]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
                initTable([0, 1, 2, 3, 4, 5]);
            });
        });
    
        
    </script>
@endsection