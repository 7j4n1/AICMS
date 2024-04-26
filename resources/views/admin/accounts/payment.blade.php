@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Payment Records" subSection1="Payments captures" subSection2=""></x-topnavbar>
    <livewire:accounts.payment-capture />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            initTable([0, 1, 2, 3, 4, 5, 6, 7, 8]);
        });
        document.addEventListener('livewire:init', () => {
            window.setTimeout(function() {
                $(".auto-close").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 10000);
            Livewire.on('on-openModal', () => {
                initTable([0, 1, 2, 3, 4, 5, 6, 7, 8]);
            });
        });
    
        
    </script>
@endsection