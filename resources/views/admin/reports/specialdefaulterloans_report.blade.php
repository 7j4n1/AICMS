<?php
    $title = 'Special Loan Repayment Defaulters Report';
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="{{route('dashboard')}}" sectionName="Special Loan Repayment" subSection1="Reports" subSection2="Special Loan Defaulters"></x-topnavbar>
    <livewire:admin.reports.special-loan-defaulters />

    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2, 3, 4, 5, 6, 7, 8]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                initTable([0, 1, 2, 3, 4, 5, 6, 7, 8]);

                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
            });
        });
    
    </script>
@endsection