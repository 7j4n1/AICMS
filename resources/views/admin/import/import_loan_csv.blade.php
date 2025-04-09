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
    function csvUploader() {
        return {
            isProcessing: false,
            progress: 0,
            processedChunks: 0,
            totalChunks: 0,
            chunkSize: 500,
            currentChunkData: [],
            chunkCounter: 0,
            totalRows: 0,
            rowsProcessed: 0,
            useRowCount: true, // Use row count for chunking
            parsingComplete: false,
            parser: null, // Store the Papa.parse instance
            activeChunkPromises: {}, // Store active chunk promises
            importComplete: false, // Track if import is complete

            // Add a method to clear all pending promises when complete
            clearPendingPromises() {
                Object.values(this.activeChunkPromises).forEach(promiseData => {
                    if (promiseData.listener) {
                        window.removeEventListener('chunk-processed', promiseData.listener);
                    }
                    if (promiseData.timeout) {
                        clearTimeout(promiseData.timeout);
                    }
                });
                this.activeChunkPromises = {};
                console.log('Cleared all pending chunk promises');
            },

            // Add initialization function
            initUploader() {
                console.log('CSV Uploader initialized');

                // Register this instance with Alpine
                if(Alpine && Alpine.store) {
                    Alpine.store('csvState').setInstance(this);
                } 

                window.addEventListener('import-complete', () => {
                    this.progress = 100; // Set progress to 100%
                    this.isProcessing = false;
                    this.resetFileInput();
                });
            },

            resetFileInput() {
                const fileInput = document.getElementById('csv-file-input');
                if(fileInput) {
                    fileInput.value = ''; // Reset the file input value
                }
                // Reset all other properties
                this.isProcessing = false;
                this.progress = 0;
                this.processedChunks = 0;
                this.totalChunks = 0;
                this.chunkCounter = 0;
                this.currentChunkData = [];
                this.rowsProcessed = 0;
                this.totalRows = 0;
                this.parsingComplete = false;
                this.importComplete = false; // Reset import complete flag
                
                if(this.parser)
                {
                    try {
                        this.parser.abort(); // Abort the parser if it's still running
                    } catch (error) {
                        console.error('Error aborting parser:', error);
                    }
                    this.parser = null; // Reset the parser instance
                }
            },

            signalCompletion() {
                if(this.parsingComplete && this.processedChunks === this.chunkCounter && !this.importComplete) {
                    console.log('Signaling import completion...');
                    this.importComplete = true; // Set import complete flag
                    this.progress = 100;

                    // Clear any pending promises first
                    this.clearPendingPromises();
                    
                    // Notify Livewire
                    Livewire.dispatch('import-complete', {
                        totalChunks: this.chunkCounter,
                        totalRows: this.rowsProcessed,
                    });
                    
                    // Set state
                    this.isProcessing = false;
                }
            },

            handleFileUpload(event) {
                const file = event.target.files[0];

                if(!file) return;

                this.isProcessing = true;
                this.progress = 0;
                this.processedChunks = 0;
                this.chunkCounter = 0;
                this.currentChunkData = [];
                this.rowsProcessed = 0;

                // Capture 'this' context
                const self = this;

                // Estimate total chunks based on file size
                const estimatedTotalChunks = this.estimateTotalChunks(file);
                this.totalChunks = estimatedTotalChunks;
                console.log('Estimated total chunks:', estimatedTotalChunks);

                if(this.useRowCount) {
                    // Use Papa.parse to get the total number of rows in the file
                    Papa.parse(file, {
                        header: true,
                        skipEmptyLines: true,
                        preview: 1000,
                        step: function(results, parser) {
                            // just count the rows
                            self.totalRows++;
                        },
                        complete: (results) => {
                            const sampleRows = self.totalRows;
                            const avgRowSize = file.size / sampleRows;
                            const estimatedRows = Math.ceil(file.size / avgRowSize);
                            self.totalRows = estimatedRows;
                            self.totalChunks = Math.ceil(estimatedRows / self.chunkSize);
                            console.log('Total rows:', self.totalRows);
                            console.log('total chunks:', self.totalChunks);

                            // do the actual parsing
                            self.parseAndProcess(file);
                        },
                        error: (error) => {
                            console.error('Error counting rows:', error);
                            self.isProcessing = false;
                        }
                    });
                } else {
                    // Estimate based on file size directly
                    const estimatedTotalChunks = this.estimateTotalChunks(file);
                    this.totalChunks = estimatedTotalChunks;
                    console.log('Estimated total chunks:', estimatedTotalChunks);
                    this.parseAndProcess(file);
                }
            },

            parseAndProcess(file) {
                const self = this;
                this.parsingComplete = false;

                this.parser = Papa.parse(file, {
                    header: true,
                    skipEmptyLines: true,
                    step: function(results) {
                        // Add data to current chunk
                        if(results.data && Object.keys(results.data).length > 0)
                        {
                            self.currentChunkData.push(results.data);
                            self.rowsProcessed++;
                        }
                        // If chunk size is reached, process the chunk
                        if(self.currentChunkData.length >= self.chunkSize)
                        {
                            self.processCurrentChunk();

                            // update progress based on rows
                            if(self.useRowCount)
                            {
                                self.progress = Math.min(Math.round((self.rowsProcessed / self.totalRows) * 100), 99);
                            }
                        }
                    },
                    complete: () => {
                        this.parsingComplete = true;

                        // Process any remaining data in the current chunk
                        if(self.currentChunkData.length > 0) {
                            self.processCurrentChunk();
                        }else {
                            self.signalCompletion();
                        }
                        console.log('Parsing complete');
                        console.log(`Final stats: ${self.processedChunks} chunks / ${self.rowsProcessed} rows`);
                        // Update progress to 100% after all chunks are processed
                        // if(self.useRowCount) {
                        //     self.progress = Math.min(Math.round((self.rowsProcessed / self.totalRows) * 100), 100);
                        // } else {
                        //     self.progress = 100;
                        // }

                        // Set the processing status to false
                        // self.isProcessing = false;
                        // reset the file input
                        // document.getElementById('csv-file-input').value = '';

                        // 
                        
                        // self.isProcessing = false;
                        // self.currentChunkData = []; // Clear current chunk data
                    },
                    error: (error) => {
                        console.error('Error parsing CSV:', error);
                        self.isProcessing = false;
                    }
                });
            },

            processCurrentChunk() {
                if (this.currentChunkData.length === 0) return;

                // Pause parsing to process the current chunk
                if(this.parser) {
                    this.parser.pause();
                }

                this.chunkCounter++;
                console.log(`Processing chunk ${this.chunkCounter} with ${this.currentChunkData.length} rows`);


                try {
                    // Create CSV content for this chunk
                    const headers = Object.keys(this.currentChunkData[0]).join(',');
                    const csvContent = headers + '\n' +
                        this.currentChunkData.map(row =>
                            Object.values(row).map(val =>
                                typeof val === 'string' ? `"${val.replace(/"/g, '""')}"` : `"${val}"`
                            ).join(',')
                        ).join('\n');

                    // Convert to Blob
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                    // Create FormData for upload
                    const formData = new FormData();
                    formData.append('chunk', blob, `chunk_${this.chunkCounter}.csv`);
                    formData.append('chunkNumber', this.chunkCounter);

                    // Clear the current chunk data
                    this.currentChunkData = [];

                    // Upload chunk via AXIOS
                    this.uploadChunk(formData, this.chunkCounter)
                        .then(() => {
                            // Chunk uploaded successfully
                            console.log(`Chunk ${this.chunkCounter} processed successfully`);

                            // Update progress based on chunks
                            if (this.useRowCount) {
                                this.progress = Math.min(Math.round((this.processedChunks / this.totalRows) * 100), 99);
                            }

                            // Resume parsing if paused for the next chunk
                            if (this.parser && !this.parsingComplete) {
                                this.parser.resume();
                            }
                        })
                        .catch(error => {
                            console.error('Error uploading chunk:', error);
                            
                            // Handle error (e.g., retry, notify user, etc.)
                            this.isProcessing = false;

                            // try to resume parsing if paused
                            if (this.parser && !this.parsingComplete) {
                                this.parser.resume();
                            }
                        });
                    
                } catch (error) {
                    console.error('Error processing chunk:', error, this.currentChunkData);
                    this.isProcessing = false;

                    // try to resume parsing if paused
                    if (this.parser && !this.parsingComplete) {
                        this.parser.resume();
                    }
                }

            },

            estimateTotalChunks(file) {
                // Rough estimate of total chunks based on file size
                const avgRowSizeBytes = 80;
                const estimatedRows = Math.ceil(file.size / avgRowSizeBytes);
                // Add a buffer to account for parsing overhead
                const bufferMultiplier = 1.2;
                return Math.ceil((estimatedRows / this.chunkSize) * bufferMultiplier);
            },

            uploadChunk(formData, chunkNumber) {
                // Skip upload if import is already complete
                if(this.importComplete) {
                    console.log('Upload skipped, import already complete');
                    return Promise.resolve(); // Resolve immediately if import is complete
                }

                // Use AXios query to upload the chunk
                // Return the promise chain
                return axios.post('/admin/upload-chunk', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    this.processedChunks++;

                    // Only update progress if not using row-based counting
                    if (!this.useRowCount) {
                        this.progress = Math.min(Math.round((this.processedChunks / this.totalChunks) * 100), 100);
                    }
                    // Log the response for debugging
                    console.log(`Chunk ${chunkNumber} uploaded (${this.processedChunks}/${this.totalChunks})`);

                    // Update Livewire component state
                    return new Promise((resolve, reject) => {
                        Livewire.dispatch('process-chunk', {
                            chunkNumber: chunkNumber,
                            totalChunks: this.totalChunks,
                            path: response.data.path,
                        });

                        // setup a listener for when the chunk is uploaded
                        const processListener = (e) => {
                            if (e.detail.chunkNumber === chunkNumber) {
                                // Remove the listener after processing
                                window.removeEventListener('chunk-processed', processListener);

                                // Clear the timeout if it exists
                                if (this.activeChunkPromises[chunkNumber]?.timeout) {
                                    clearTimeout(this.activeChunkPromises[chunkNumber].timeout);
                                }
                                
                                // Delete this promise from tracking
                                delete this.activeChunkPromises[chunkNumber];
                                
                                if(e.detail.success) {
                                    resolve();
                                } else {
                                    reject(new Error(e.detail.message || 'Error processing upload'));
                                }
                            }
                        };


                        // Add the listener for the chunk upload
                        window.addEventListener('chunk-processed', processListener);

                        // Set a timeout for this chunk
                        const timeoutId = setTimeout(() => {
                            window.removeEventListener('chunk-processed', processListener);
                            delete this.activeChunkPromises[chunkNumber];
                            
                            // Check if processing is already complete
                            if (!this.isProcessing) {
                                // If we're already done, don't show an error
                                console.log(`Chunk ${chunkNumber} timed out, but processing is already complete - ignoring`);
                                resolve(); // Resolve anyway to prevent cascading errors
                            } else {
                                reject(new Error(`Timeout waiting for chunk ${chunkNumber} processing`));
                            }
                        }, 100000);
                        
                        // Store the promise details for tracking
                        this.activeChunkPromises[chunkNumber] = {
                            listener: processListener,
                            timeout: timeoutId
                        };
                        
                        // Dispatch the Livewire event to start processing
                        Livewire.dispatch('process-chunk', {
                            chunkNumber: chunkNumber,
                            totalChunks: this.totalChunks,
                            path: response.data.path,
                        });


                    });
                })
                .catch(error => {
                    console.error('Error uploading chunk:', error);
                    throw error; // Rethrow the error to be caught in the calling block
                })
            },

        };
    }

    
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Wait for Alpine.js to be initialized
        setTimeout(() => {
            if(!Alpine) {
                console.error('Alpine.js is not loaded yet. Please check your script inclusion.');
                return;
            }
            console.log('Alpine.js is loaded and ready to use.');
        }, 100);

        window.addEventListener('chunk-processed', function(event) {
            console.log('Chunk processed:', event.detail);
            
            
            const csvUploader = Alpine.store('csvState').getInstance();
            
            if(!csvUploader) {
                console.log('CSV Uploader not initialized yet');

                setTimeout(() => {
                const retryUploader = Alpine.store('csvState').getInstance();
                    if (retryUploader && retryUploader.processedChunks === retryUploader.chunkCounter) {
                        handleProcessingComplete(retryUploader);
                    }
                }, 500);

                return;
            }

            console.log('CSV Uploader:', csvUploader);

            if(csvUploader && csvUploader.processedChunks === csvUploader.chunkCounter && csvUploader.parsingComplete) {
                handleProcessingComplete(csvUploader);
                
            }
        });

        // Extract the completion handler as a separate function
        function handleProcessingComplete(csvUploader) {
            // All chunks processed, reset the uploader
            console.log('All chunks processed, resetting uploader...');

            if(typeof csvUploader.clearPendingPromises === 'function') {
                csvUploader.clearPendingPromises(); // Clear pending promises
            }

            // Notify Livewire component
            Livewire.dispatch('import-complete', {
                totalChunks: csvUploader.chunkCounter,
                totalRows: csvUploader.rowsProcessed,
            });

            csvUploader.progress = 100; // Set progress to 100%
            csvUploader.isProcessing = false;

            // Reset the file input
            csvUploader.resetFileInput();
        }

        window.addEventListener('show-notification', function(event) {
            console.log('Notification:', event.detail);

            const detail = event.detail[0] || {};
            const type = detail.type || 'info';
            const message = detail.message || 'Notification received';
            
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            } else {
                console.log(type + ': ' + message);

                // Create div with a class of 'auto-close'
                const notificationDiv = document.createElement('div');
                notificationDiv.className = 'alert alert-' + type;
                notificationDiv.innerHTML = message;
                // append it to class of 'card-body' 
                const cardBody = document.querySelector('.card-body');
                if (cardBody) {
                    cardBody.appendChild(notificationDiv);
                }
                // set a timeout to remove the div after 10 seconds
                setTimeout(() => {
                    notificationDiv.classList.add('fade-out');
                    notificationDiv.addEventListener('transitionend', () => {
                        notificationDiv.remove();
                    });
                }, 1000);

            }
        });
        // Listen for Livewire events
        // window.addEventListener('notify', event => {
        //     // You can integrate with your preferred notification library here
        //     // For example with toastr:
        //     if (typeof toastr !== 'undefined') {
        //         toastr[event.detail.type || 'info'](event.detail.message);
        //     } else {
        //         console.log(event.detail.type + ': ' + event.detail.message);
        //     }
        // });
        
        // // Listen for Alpine.js events
        // window.addEventListener('processing-status-changed', event => {
        //     console.log('Processing status changed:', event.detail);
        // });

        
        // document.addEventListener('livewire:init', () => {
        //     Livewire.on('processing-status-changed', () => {

        //         // window.setTimeout(function() {
        //         //     $(".auto-close").fadeTo(5000, 0).slideUp(5000, function(){
        //         //         $(this).remove(); 
        //         //     });
        //         // }, 500);
        //     });
        // });
    
        
    });
</script>
@endsection