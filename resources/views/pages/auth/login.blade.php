@extends('layout.master2')

@section('content')
<div class="page-content d-flex align-items-center justify-content-center">

  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-4 col-xl-4 mx-auto">
      <div class="card">
        <div class="row">
          <div class="col-md-12">
            <div class="auth-form-wrapper px-3 py-4">
              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                  <label for="email" class="form-label">E-mail</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="concessionaria@gmail.com">
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Senha</label>
                  <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="••••••••••">
                </div>
                <div class="text-center mb-3">
                  <a href="{{ route('password.request') }}" class="text-muted">Esqueceu sua senha?</a>
                </div>
                <div>
                  <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
