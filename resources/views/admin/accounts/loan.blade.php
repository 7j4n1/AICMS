@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Loans Records" subSection1="Loans captures" subSection2=""></x-topnavbar>
    <livewire:accounts.loan-capture />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2, 3, 4, 5, 6, 7, 8]);
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                window.setTimeout(function() {
                    $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                        $(this).remove(); 
                    });
                }, 10000);
                initTable([0, 1, 2, 3, 4, 5, 6, 7, 8]);
            });
        });
    
        
    </script>
@endsection