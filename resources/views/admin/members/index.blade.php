@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Members" subSection1="List Members" subSection2=""></x-topnavbar>
    <livewire:members.list-members />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        // window.addEventListener('livewire:navigated', (event) => {
            
        //     initTable([0, 1, 2, 3, 4, 5, 6]);
        // });
        window.addEventListener('DOMContentLoaded', (event) => {
            
            initTable([0, 1, 2, 3, 4, 5, 6]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                initTable([0, 1, 2, 3, 4, 5, 6]);

                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
            });
        });
    
        
        
    </script>
@endsection