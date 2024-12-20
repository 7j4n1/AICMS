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
    <label for="csvfile">Members CSV File:</label>
    <input type="file" name="csvfile" id="csvfile" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Import Data</button>
</form>

<h2>Import Previous Loan CSV file</h2>
<form method="POST" action="{{ route('newImportPrevLoan') }}" enctype="multipart/form-data">
  @csrf

  <div class="form-group">
    <label for="csvfile">Prev Loan CSV File:</label>
    <input type="file" name="csvfile" id="csvfile" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Import Loans</button>
</form>

 <h2>Import Ledger CSV file</h2>
 <form method="POST" action="{{ route('lastledger2023') }}" enctype="multipart/form-data">
  @csrf

  <div class="form-group">
    <label for="csvfile">Ledger CSV File:</label>
    <input type="file" name="csvfile" id="csvfile" class="form-control">
  </div>

  <button type="submit" class="btn btn-primary">Import Ledger Data</button>
</form>


{{-- <h2>Load Previous Loans</h2>

<div class="row mt-4">
  <div class="col-sm-6">
    <a class="btn btn-success" href="{{route('getPrevLoans')}}" target="_blank">Click here to load previous Loans</a>
  </div>
</div> --}}

<x-scriptvendor></x-scriptvendor>
@endsection
