@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Annual Fee Records" subSection1="Annual Fee captures" subSection2="Per year"></x-topnavbar>
    <livewire:accounts.annual-fee-capture />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        // window.addEventListener('DOMContentLoaded', (event) => {
        //     // initTable([0, 1, 2, 3, 4]);
        // });
        document.addEventListener('livewire:init', () => {
            window.setTimeout(function() {
                $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 5000);
            Livewire.on('on-openModal', () => {
                initTable([0, 1, 2, 3, 4]);
            });
        });
    
        
    </script>
@endsection