<?php $title="Import Csv Data"; ?>
@extends('components.layouts.app')

@section('content')
<x-navbars.topnavbar homeUrl="{{route('dashboard')}}" sectionName="Import" subSection1="Import CSV" subSection2=""></x-topnavbar>
<h1>Import CSV Data</h1>

@if (session()->has('success'))
<div class="alert alert-success">
  {{ session()->get('success') }}
</div>
@endif

@if (session()->has('error'))
<div class="alert alert-danger">
  {{ session()->get('error') }}
</div>
@endif

<form method="POST" action="{{ route('newImport') }}" enctype="multipart/form-data">
  @csrf

  <div class="form-group">
    <label for="csvfile">CSV File:</label>
    <input type="file" name="csvfile" id="csvfile" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Import Data</button>
</form>

<x-scriptvendor></x-scriptvendor>
@endsection
