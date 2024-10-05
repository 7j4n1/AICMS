<?php $title="Import Csv Data"; ?>
@extends('components.layouts.app')

@section('content')
<x-navbars.business_topnavbar homeUrl="/" sectionName="Business" subSection1="Import" subSection2="Import CSV"></x-business_topnavbar>
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



<h2>Import Previous Business ledger CSV file</h2>
<form method="POST" action="{{ route('prev_repay_upload') }}" enctype="multipart/form-data">
  @csrf

  <div class="form-group">
    <label for="csvfile">Prev Ledger CSV File:</label>
    <input type="file" name="csvfile" id="csvfile" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Import Ledger</button>
</form>


<x-scriptvendor></x-scriptvendor>
@endsection
