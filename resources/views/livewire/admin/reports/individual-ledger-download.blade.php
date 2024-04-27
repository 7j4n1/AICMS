<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-header text-uppercase">
                    <h3>Account Information</h3>
                </div>
                <div class="card-body">
                    <div class="row ">
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-info rounded">
                                <label class="p-2 text-uppercase">Account Name</label>
                                <label class="card-title p-2"> {{--$memberId->surname--}} {{--$memberId->otherNames--}}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-success rounded">
                                <label class="p-2 text-uppercase">Total Amounts(₦)</label>
                                <label class="card-title p-2"> #17,600,789.00</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-success rounded">
                                <label class="p-2 text-uppercase">Total Savings(₦)</label>
                                <label class="card-title p-2 "> #17,600,789.00</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-success rounded">
                                <label class="p-2 text-uppercase">Loan Status</label>
                                <label class="card-title p-2 "> {{--$isOnLoan ? 'Active' : 'Not Active'--}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-info rounded">
                                <label class="p-2 text-uppercase">Account Coop ID</label>
                                <label class="card-title p-2"> {{--$memberId->coop--}}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-success rounded">
                                <label class="p-2 text-uppercase">Total Shares(₦)</label>
                                <label class="card-title p-2 "> #17,600,789.00</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-danger rounded">
                                <label class="p-2 text-uppercase">Total Loans(₦)</label>
                                <label class="card-title p-2 "> #17,600,789.00</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column border border-success rounded">
                                <label class="p-2 text-uppercase">Admin & Others(₦)</label>
                                <label class="card-title p-2 "> #17,600,789.00</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                
                <div class="card-body">
                    <div class="row ">
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column">
                                <label class="p-2 text-uppercase">Date Printed</label>
                                <label class="card-title p-2"> {{--date('Y-M-d')--}}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column">
                                <label class="p-2 text-uppercase">From Date</label>
                                <label class="card-title p-2"> {{--$beginning_date--}}</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 mb-2">
                            <div class="d-flex flex-column">
                                <label class="p-2 text-uppercase">To Date</label>
                                <label class="card-title p-2 "> {{--$ending_date--}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 mb-2">
                            <table class="table table-responsive table-striped">
                                <thead>
                                    <tr class="table-secondary">
                                        <th scope="col">Trans. Date</th>
                                        <th scope="col">Amount(₦)</th>
                                        <th scope="col">Savings(₦)</th>
                                        <th scope="col">Shares(₦)</th>
                                        <th scope="col">Loans(₦)</th>
                                        <th scope="col">Admin(₦)</th>
                                        <th scope="col">Others(₦)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="row">27/04/2024</td>
                                        <td scope="row">Deposit</td>
                                        <td scope="row">#17,600,789.00</td>
                                        <td scope="row">#17,600,789.00</td>
                                    </tr>
                                    <tr>
                                        <td>27/04/2024</td>
                                        <td>Deposit</td>
                                        <td>#17,600,789.00</td>
                                        <td>#17,600,789.00</td>
                                    </tr>
                                    <tr>
                                        <td>27/04/2024</td>
                                        <td>Deposit</td>
                                        <td>#17,600,789.00</td>
                                        <td>#17,600,789.00</td>
                                    </tr>
                                    <tr>
                                        <td>27/04/2024</td>
                                        <td>Deposit</td>
                                        <td>#17,600,789.00</td>
                                        <td>#17,600,789.00</td>
                                    </tr>
                                    <tr>
                                        <td>27/04/2024</td>
                                        <td>Deposit</td>
                                        <td>#17,600,789.00</td>
                                        <td>#17,600,789.00</td>
                                    </tr>
                                    <tr>
                                        <td>27/04/2024</td>
                                        <td>Deposit</td>
                                        <td>#17,600,789.00</td>
                                        <td>#17,600,789.00</td>
                                    </tr>
                                    <tr>
                                        <td>27/04/2024</td>
                                        <td>Deposit</td>
                                        <td>#17,600,789.00</td>
                                        <td>#17,600,789.00</td>
                                    </tr>
                                    <tr>
                                        <td>27/04/2024</td>
                                        <td>Deposit</td>
                                        <td>#17,600,789.00</td>  
                                        <td>#17,600,789.00</td>  
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-scriptvendor></x-scriptvendor>
</div>