@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Members" subSection1="List Members" subSection2=""></x-topnavbar>
    <livewire:members.list-members />

    
    
    {{-- Handle Browser dispatched Events --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('on-openModal', () => {
                // setTableData();

            });
        });
    
        
    </script>
@endsection