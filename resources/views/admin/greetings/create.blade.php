@extends('admin.layout')

@section('title', 'Buat Kartu Ucapan — ' . $templateInfo['label'])

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-slate-400 text-sm">💌 Template: <strong class="text-violet-300">{{ $templateInfo['label'] }}</strong></p>
            <h1 class="text-2xl font-semibold text-slate-100">
                Buat Kartu Ucapan Ulang Tahun
            </h1>
        </div>
        <a href="{{ route('admin.weddings.create') }}" class="text-violet-400 hover:text-violet-300 hover:underline text-sm">← Ganti template</a>
    </div>

    @if(session('error'))
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-red-500">
            <div class="text-red-300 text-sm">{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="adm-card px-6 py-4 mb-4 border-l-4 border-red-500">
            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.greetings.store') }}" method="post">
        @csrf
        @if(!empty($orderId))
            <input type="hidden" name="order_id" value="{{ $orderId }}">
        @endif
        @include('admin.greetings._form', ['w' => null, 'template' => $template, 'templateInfo' => $templateInfo, 'package' => $package])

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold shadow-lg transition-all">
                💌 Buat Kartu Ucapan
            </button>
            <a href="{{ route('admin.weddings.index') }}"
               class="px-5 py-2.5 rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 transition">
               Batal
            </a>
        </div>
    </form>

@endsection
