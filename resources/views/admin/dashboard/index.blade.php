<?php $title="Dashboard"; ?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="{{route('dashboard')}}" sectionName="Dashboard" subSection1="Dashboard" subSection2=""></x-topnavbar>
    <livewire:admin.dashboard />

@endsection