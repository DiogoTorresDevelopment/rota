@extends('layout.master2')

@section('content')
<div class="page-content d-flex align-items-center justify-content-center">
  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-6 col-xl-4 mx-auto">
      <div class="card">
        <div class="auth-form-wrapper px-4 py-5">
          <h4 class="text-center mb-3">ROTA</h4>
          <p class="text-center text-muted mb-4">Crie sua conta gratuitamente!</p>
          
          <form method="POST" action="{{ route('register.submit') }}" class="forms-sample">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Nome</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Seu nome">
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">E-mail</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="seu@email.com">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Senha</label>
              <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirmar Senha</label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
            </div>
            <div>
              <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
            </div>
            <div class="text-center mt-3">
              <a href="{{ route('login') }}" class="text-muted">Já tem uma conta? Entre aqui</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection