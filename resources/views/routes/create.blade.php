@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    @include('routes.partials.form', [
        'action' => route('routes.store'),
        'isEdit' => false,
        'currentStep' => request()->get('step', 1),
        'route' => null
    ])
@endsection 