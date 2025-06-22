
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mx-auto mt-5" style="max-width: 768px;">
    <div class="card-body p-4">
        <h2 class="card-title h4 mb-4 text-dark">CSV Prev Ledger File Upload & Batch Processing</h2>

        @if ($status === 'idle')
            <div class="mb-4">
                <div class="mb-3">
                    <label class="form-label text-sm text-muted mb-2">
                        Select CSV File
                    </label>
                    <input
                        type="file"
                        wire:model.live="csvFile"
                        accept=".csv,.txt"
                        class="form-control"
                    >
                    @error('csvFile')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                    

                    @if ($csvFile)
                        <div class="mt-2 text-sm text-muted">
                            <strong>Selected:</strong> {{ $csvFile->getClientOriginalName() }}
                            ({{ number_format($csvFile->getSize() / 1024, 2) }} KB)
                        </div>
                    @endif
                </div>

                <button
                    wire:click="startImport"
                    wire:loading.attr="disabled"
                    wire:target="startImport"
                    @disabled(!$csvFile)
                    class="btn btn-primary w-100 {{ !$csvFile ? 'disabled' : '' }}"
                >
                    <span wire:loading.remove wire:target="startImport">
                        @if ($csvFile)
                            Upload & Process CSV in Batches
                        @else
                            Select a CSV file first
                        @endif
                    </span>
                    <span wire:loading wire:target="startImport">Reading CSV...</span>
                </button>
            </div>

        @elseif ($status === 'uploading')
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">{{ $statusMessage }}</p>
            </div>

        @elseif ($status === 'processing')
            <div class="mb-4">
                <div class="text-center mb-4">
                    <h3 class="h5 mb-2 text-dark">Processing CSV in Batches</h3>
                    <p class="text-muted">{{ $statusMessage }}</p>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between text-sm text-muted">
                        <span>Batch Progress</span>
                        <span>{{ number_format($progress, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 0.75rem;">
                        <div
                            class="progress-bar bg-primary"
                            role="progressbar"
                            style="width: {{ $progress }}%"
                            aria-valuenow="{{ $progress }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        >{{ number_format($progress, 1) }}%</div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="h4 font-weight-bold text-primary">{{ $completedJobs }}</div>
                            <div class="text-sm text-primary">Jobs Completed</div>
                            <div class="text-xs text-muted">of {{ $totalJobs }} total</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="h4 font-weight-bold text-success">{{ number_format($validRows) }}</div>
                            <div class="text-sm text-success">Valid Rows</div>
                            <div class="text-xs text-muted">Successfully processed</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="h4 font-weight-bold text-danger">{{ number_format($invalidRows) }}</div>
                            <div class="text-sm text-danger">Invalid Rows</div>
                            <div class="text-xs text-muted">Errors encountered</div>
                        </div>
                    </div>
                </div>

                @if ($totalRows > 0)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-sm text-muted">
                            <span>Row Processing</span>
                            <span>{{ number_format($processedRows) }} / {{ number_format($totalRows) }}</span>
                        </div>
                        <div class="progress" style="height: 0.5rem;">
                            <div
                                class="progress-bar bg-success"
                                role="progressbar"
                                style="width: {{ $totalRows > 0 ? ($processedRows / $totalRows) * 100 : 0 }}%"
                                aria-valuenow="{{ $totalRows > 0 ? ($processedRows / $totalRows) * 100 : 0 }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                    </div>
                @endif

                @if (count($errors) > 0)
                    <div class="alert alert-danger border-danger-subtle bg-danger-subtle p-3 mb-4" role="alert">
                        <h4 class="alert-heading h6 text-danger mb-2">Recent Errors (showing latest 10):</h4>
                        <div style="max-height: 8rem; overflow-y: auto;">
                            <ul class="text-sm text-danger mb-0">
                                @foreach ($errors as $error)
                                    <li class="text-xs">• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="text-center">
                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

        @elseif ($status === 'completed')
            <div class="mb-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle rounded-circle mb-3" style="width: 3rem; height: 3rem;">
                        <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="h5 text-dark mt-2">Batch Processing Complete!</h3>
                    <p class="text-muted">{{ $statusMessage }}</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-3">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="h4 font-weight-bold text-primary">{{ $totalJobs }}</div>
                            <div class="text-sm text-primary">Total Jobs</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="h4 font-weight-bold text-success">{{ number_format($validRows) }}</div>
                            <div class="text-sm text-success">Valid Rows</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="h4 font-weight-bold text-danger">{{ number_format($invalidRows) }}</div>
                            <div class="text-sm text-danger">Invalid Rows</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="bg-light p-3 rounded text-center">
                            <div class="h4 font-weight-bold text-warning">{{ $failedJobs }}</div>
                            <div class="text-sm text-warning">Failed Jobs</div>
                        </div>
                    </div>
                </div>

                @if (count($downloadLinks) > 0)
                    <div class="card bg-light p-4 mb-4">
                        <h4 class="h6 text-dark mb-3">Download Reports</h4>
                        <div class="row g-3">
                            @if (isset($downloadLinks['valid']))
                                <div class="col-12 col-md-4">
                                    <a href="{{ $downloadLinks['valid'] }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-success d-flex align-items-center justify-content-center py-2">
                                        <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Valid Rows Excel
                                    </a>
                                </div>
                            @endif

                            @if (isset($downloadLinks['invalid']))
                                <div class="col-12 col-md-4">
                                    <a href="{{ $downloadLinks['invalid'] }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-danger d-flex align-items-center justify-content-center py-2">
                                        <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Invalid Rows Excel
                                    </a>
                                </div>
                            @endif

                            @if (isset($downloadLinks['errors']))
                                <div class="col-12 col-md-4">
                                    <a href="{{ $downloadLinks['errors'] }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-warning d-flex align-items-center justify-content-center py-2">
                                        <svg class="me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Error Logs Excel
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="text-center">
                    <button
                        wire:click="resetUpload"
                        class="btn btn-primary"
                    >
                        Process Another File
                    </button>
                </div>
            </div>

        @elseif ($status === 'failed')
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle rounded-circle mb-3" style="width: 3rem; height: 3rem;">
                    <svg class="h-6 w-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="h5 text-dark">Processing Failed</h3>
                <p class="text-danger">{{ $statusMessage }}</p>

                <button
                    wire:click="resetUpload"
                    class="btn btn-primary mt-3"
                >
                    Try Again
                </button>
            </div>
        @endif
    </div>
</div>


        </div>
    </div>
</div>

@script
<script>
    let ledgerPollingInterval;

    $wire.on('start-ledger-polling', () => {
        ledgerPollingInterval = setInterval(() => {
            $wire.dispatch('check-ledger-progress');
        }, 2000); // Check every 2 seconds for batch processing
    });

    $wire.on('stop-ledger-polling', () => {
        if (ledgerPollingInterval) {
            clearInterval(ledgerPollingInterval);
            ledgerPollingInterval = null;
        }
    });

    // Cleanup on component destruction
    document.addEventListener('livewire:navigated', () => {
        if (ledgerPollingInterval) {
            clearInterval(ledgerPollingInterval);
            ledgerPollingInterval = null;
        }
    });
</script>
@endscript