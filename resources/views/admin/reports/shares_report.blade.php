<?php
    $title = 'Monthly Shares Report';
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="{{route('dashboard')}}" sectionName="Monthly Shares" subSection1="Reports" subSection2="Monthly Shares Report"></x-topnavbar>
    <livewire:admin.reports.monthly-shares />

    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                initTable([0, 1, 2]);

                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
            });
        });
    
    </script>
@endsection