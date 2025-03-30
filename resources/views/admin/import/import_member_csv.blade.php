<?php
    $title = "Import Member CSV Data to Database";
?>
@extends('components.layouts.app')

@section('content')
    <x-navbars.topnavbar homeUrl="/" sectionName="Upload CSV(Bulk)" subSection1="Account" subSection2="Members Deatils"></x-topnavbar>
    
    <livewire:utils.import-member-csv />

    <x-scriptvendor></x-scriptvendor>

    
{{-- Handle Browser dispatched Events --}}
<script>
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

                Papa.parse(file, {
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
                        // Process any remaining data in the current chunk
                        if(self.currentChunkData.length > 0) {
                            self.processCurrentChunk();
                        }
                        console.log('Parsing complete');
                        console.log(`Final stats: ${self.processedChunks} chunks / ${self.rowsProcessed} rows`);
                        // Update progress to 100% after all chunks are processed
                        self.progress = 100;
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

                    // Upload chunk via AXIOS
                    this.uploadChunk(formData, this.chunkCounter);
                    
                } catch (error) {
                    console.error('Error processing chunk:', error, this.currentChunkData);
                } finally {
                    // Reset current chunk data
                    this.currentChunkData = [];
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
                // Use AXios query to upload the chunk
                axios.post('/admin/upload-chunk', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    this.processedChunks++;

                    // Only update progress if not using row-based counting
                    if (!this.useRowCounting) {
                        this.progress = Math.min(Math.round((this.processedChunks / this.totalChunks) * 100), 99);
                    }
                    // Log the response for debugging
                    console.log(`Chunk ${chunkNumber} uploaded (${this.processedChunks}/${this.totalChunks})`);

                    // Update Livewire component state
                    Livewire.dispatch('chunk-uploaded', {
                        chunkNumber: chunkNumber,
                        totalChunks: this.totalChunks
                    });
                })
                .catch(error => {
                    console.error('Error uploading chunk:', error);
                })
            },

        };
    }

    
</script>
@endsection