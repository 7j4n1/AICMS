
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Upload Members Details in Bulk (CSV Only)</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success auto-close">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger auto-close">
                                {{ session('error') }}
                            </div>
                        @endif
                        <h2>Import Member Data Bulk (CSV file)</h2>
                        

                        <div x-data="csvUploader()">
                            <input type="file" name="file" class="form-control" @change="handleFileUpload" accept=".csv">

                            <div x-show="isProcessing" class="mt-2">
                                <div>Progress: <span x-text="progress"></span>%</div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :style="'width: ' + progress + '%'" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div>Processed Chunks: <span x-text="processedChunks"></span> / <span x-text="totalChunks"></span></div>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

