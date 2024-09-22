<?php $title="Backup-Restore DB"; ?>
@extends('components.layouts.app')

@section('content')
<x-navbars.topnavbar homeUrl="{{route('dashboard')}}" sectionName="Backup" subSection1="Backup/Restore" subSection2=""></x-topnavbar>
<h3>Backup/Restore database</h3>
<p>Backup your database and download the backup file. You can also restore your database using the backup file.</p>
<p>Last backup was created on {{ $lastBackupTime }}</p>

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

<a href="{{ route('backup.new') }}" class="btn btn-primary">Create Backup</a>

<h2>List of Backup files</h2>
<table class="table table-bordered table-striped mb-0" id="datatable-tabletools">
  <thead>
    <tr>
      <th>File Name</th>
      <th>File Size</th>
      <th>Created At</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($backups as $backup)
    <tr>
      <td>{{ $backup['file_name'] }}</td>
      <td>{{ $backup['file_size'] }}</td>
      <td>{{ $backup['created_at'] }}</td>
      <td>
        <a href="{{ route('backup.download', $backup['file_name']) }}" class="btn btn-success">Download</a>
        <a href="{{ route('backup.delete', $backup['file_name']) }}" class="btn btn-danger">Delete</a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<x-scriptvendor></x-scriptvendor>
@endsection
