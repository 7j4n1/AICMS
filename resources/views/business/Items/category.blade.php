<?php
    $title = "Categories List";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.business_topnavbar homeUrl="/" sectionName="Business" subSection1="Items" subSection2="Categories"></x-business_topnavbar>
    <livewire:business.category />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        // window.addEventListener('livewire:navigated', (event) => {
        //     initTable([0, 1, 2, ]);
        // });
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