<div class="container">
    <!-- start: page -->
    <section class="body-sign">
        <div class="center-sign">
            <a href="{{route('login')}}" class="logo float-start">
                <img src="{{asset('media/logo/4a7as2ssw24j8hG6slbirlogo.png')}}" height="70" alt="Login Admin" />
            </a>

            <div class="panel card-sign">
                <div class="card-title-sign mt-3 text-end">
                    <h2 class="title text-uppercase font-weight-bold m-0"><i class="bx bx-user-circle me-1 text-6 position-relative top-5"></i> Sign In</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if (session()->has('message'))
                                <div class="auto-close alert alert-success d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                    <div>
                                        {{ session('message') }}
                                    </div>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="auto-close alert alert-danger d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                    <div>
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <form wire:submit="login">
                        <div class="form-group mb-3">
                            <label>Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input name="username" type="text" class="form-control form-control-lg" wire:model.blur="loginForm.username" />
                                <span class="input-group-text">
                                    <i class="bx bx-user text-4"></i>
                                </span>
                            </div>
                            <div class="clearfix">
                                <label class="float-start">@error('loginForm.username') <span class="text-danger">{{ $message }}</span> @enderror</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="clearfix">
                                <label class="float-start">Password <span class="text-danger">*</span></label>
                            </div>
                            <div class="input-group">
                                <input name="pwd" type="password" class="form-control form-control-lg" wire:model.defer="loginForm.password" />
                                <span class="input-group-text">
                                    <i class="bx bx-lock text-4"></i>
                                </span>
                            </div>
                            <div class="clearfix">
                                <label class="float-start">@error('loginForm.password') <span class="text-danger">{{ $message }}</span> @enderror</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="checkbox-custom checkbox-default">
                                    <input id="RememberMe" name="rememberme" type="checkbox"/>
                                    <label for="RememberMe">Remember Me</label>
                                </div>
                            </div>
                            <div class="col-sm-4 text-end">
                                <div wire:loading wire:target="login">
                                    <button class="btn btn-primary mt-2 transition duration-300" type="button" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="24" height="24" style="shape-rendering: auto; display: block; background: transparent;" xmlns:xlink="http://www.w3.org/1999/xlink"><g><circle stroke-dasharray="164.93361431346415 56.97787143782138" r="35" stroke-width="10" stroke="#fcfdff" fill="none" cy="50" cx="50">
  <animateTransform keyTimes="0;1" values="0 50 50;360 50 50" dur="1s" repeatCount="indefinite" type="rotate" attributeName="transform"></animateTransform>
</circle><g></g></g><!-- [ldio] generated by https://loading.io --></svg>
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2 transition duration-300">Sign In</button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-3 mb-3">&copy; Copyright 2024. All Rights Reserved.</p>
        </div>
    </section>
    <!-- end: page -->
</div>
