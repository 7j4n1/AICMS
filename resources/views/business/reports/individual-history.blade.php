<?php
    $title = "Individual History";
?>

@extends('components.layouts.app')

@section('content')
    <x-navbars.business_topnavbar homeUrl="{{route('dashboard')}}" sectionName="Individual Report" subSection1="Reports" subSection2="Individual report"></x-navbars.business_topnavbar>
    <livewire:business.reports.individual-history />

    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2, 3, 4, 5, 6, 7, 8]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('table-show', () => {
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