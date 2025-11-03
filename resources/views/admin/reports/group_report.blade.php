<?php
    $title = 'Group-Based Members Report';
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="{{route('dashboard')}}" sectionName="Group Report" subSection1="Reports" subSection2="Group report"></x-topnavbar>
    <livewire:admin.reports.group-report />

    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                initTable([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);

                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
            });
        });
    
    </script>
@endsection
