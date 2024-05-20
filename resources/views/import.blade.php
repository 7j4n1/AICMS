<?php
    $title = "Import Data from Excel to Database";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Check Guarantor Status" subSection1="Account" subSection2="Check Guarantor Status"></x-topnavbar>
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Import Data from Excel to Database</h4>
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
                        <h2>Import Member Data (Excel file)</h2>
                        <form action="{{ route('member_import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" class="form-control">
                            <br>
                            <button class="btn btn-success">Import Member Data</button>
                        </form>

                        <h2>Import The Ledger (Excel file)</h2>
                        <h4>It will replace all the ledger in the database, so ensure you are importing all.</h4>

                        <form action="{{ route('payment_import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" class="form-control">
                            <br>
                            <button class="btn btn-success">Import Payments Data</button>
                        </form>


                        <h2>Import The Active Loans Table(Excel file)</h2>
                        <h4>It will replace all the ledger in the database, so ensure you are importing all.</h4>

                        <form action="{{ route('loan_import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" class="form-control">
                            <br>
                            <button class="btn btn-success">Import Active Loans Data</button>
                        </form>
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