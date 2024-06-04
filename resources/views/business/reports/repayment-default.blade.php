<?php
    $title = "All Repayment Defaulters";
?>

@extends('components.layouts.app')

@section('content')
    <x-navbars.business_topnavbar homeUrl="{{route('dashboard')}}" sectionName="Defaulters Report" subSection1="Reports" subSection2="Defaulters report"></x-navbars.business_topnavbar>
    <livewire:business.reports.repayment-defaulters />

    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2, 3, 4]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('table-show', () => {
                initTable([0, 1, 2, 3, 4]);

                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
            });
        });
    
    </script>
@endsection