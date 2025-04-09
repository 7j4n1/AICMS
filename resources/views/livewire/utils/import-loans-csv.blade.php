
<div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Upload Previous Loan Details (CSV Only)</h4>
                    </div>
                    <div class="card-body">
                        @if(session('info'))
                            <div class="alert alert-info">
                                {{ session('info') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <h2>Import Loan Data (CSV file)</h2>
                        

                        <div x-data="csvUploader()" x-init="initUploader()" class="mt-4">
                            <input type="file" name="file" @change="handleFileUpload($event)" accept=".csv"
                            class="form-control" id="csv-file-input" :disabled="isProcessing" />


                            <div x-show="isProcessing" class="mt-2">
                                <div>Uploading Progress: <span x-text="progress"></span>%</div>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :style="'width: ' + progress + '%'" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                               
                            </div>
                        </div>
                        

                        
                    </div>


                </div>
            </div>
        </div>
    </div>

