<?php
    $title = "Import Loans CSV Data to Database";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Upload CSV(Bulk)" subSection1="Account" subSection2="Loan Details"></x-topnavbar>
    
    <livewire:utils.import-loans-csv />

    <x-scriptvendor></x-scriptvendor>

    
{{-- Handle Browser dispatched Events --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('csvState', {
            instance: null,
            setInstance(instance) {
                this.instance = instance;
            },
            getInstance() {
                return this.instance;
            },
        });
    });
    
</script>
@endsection