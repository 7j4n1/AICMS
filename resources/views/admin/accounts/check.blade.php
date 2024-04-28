<?php
    $title = "Check Guarantor Status";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Check Guarantor Status" subSection1="Account" subSection2="Check Guarantor Status"></x-topnavbar>
    <livewire:admin.check-guarantor />

    
    
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