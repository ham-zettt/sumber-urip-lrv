@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-ink">Tambah Produk</h1>
        <p class="text-sm text-ink-subtle mt-1">Isi data produk baru.</p>
    </div>

    <div class="max-w-2xl bg-surface-1 border border-hairline rounded-lg p-5 md:p-6">
        <form method="POST" action="{{ route('produk.store') }}">
            @csrf
            @include('products._form')
        </form>
    </div>
@endsection