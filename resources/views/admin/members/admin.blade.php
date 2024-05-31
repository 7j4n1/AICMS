<?php
    $title = "Administrators List";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Admins" subSection1="List Administrators" subSection2=""></x-topnavbar>
    <livewire:admin.list-administrator />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        // window.addEventListener('livewire:navigated', (event) => {
        //     initTable([0, 1, 2, 3]);
        // });
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                initTable([0, 1, 2, 3]);

                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
            });
        });
    
        
        
    </script>
@endsection