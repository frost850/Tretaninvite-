@extends('invitation.layout')

@section('title', 'Masukkan Password — ' . $wedding->bride_name)

@section('content')
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#1e1b4b,#4c1d95,#1e1b4b);padding:20px">
    <div style="width:100%;max-width:380px;background:rgba(255,255,255,.07);backdrop-filter:blur(16px);border-radius:20px;border:1px solid rgba(255,255,255,.12);padding:40px 32px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.4)">
        <div style="font-size:3rem;margin-bottom:12px">🔒</div>
        <h1 style="color:#fff;font-size:1.3rem;margin:0 0 6px;font-family:'Segoe UI',sans-serif">Undangan Dilindungi</h1>
        <p style="color:rgba(255,255,255,.6);font-size:.9rem;margin:0 0 28px">Masukkan password untuk membuka undangan dari <strong style="color:rgba(255,255,255,.85)">{{ $wedding->bride_name }}</strong>.</p>

        @if(session('error'))
        <div style="background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.4);color:#fca5a5;padding:10px 14px;border-radius:10px;margin-bottom:16px;font-size:.85rem">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ url('/' . $wedding->slug . '/unlock') }}">
            @csrf
            @if(request()->has('to'))
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
            @endif
            <input type="password" name="password" placeholder="Masukkan password..."
                   autofocus required autocomplete="off"
                   style="width:100%;box-sizing:border-box;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:12px;padding:14px 18px;color:#fff;font-size:1rem;margin-bottom:14px;outline:none;transition:border .2s"
                   onfocus="this.style.borderColor='rgba(167,139,250,.7)'"
                   onblur="this.style.borderColor='rgba(255,255,255,.15)'">
            <button type="submit"
                    style="width:100%;padding:14px;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;border:none;border-radius:12px;font-size:1rem;font-weight:700;cursor:pointer;transition:opacity .2s"
                    onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                Buka Undangan →
            </button>
        </form>
    </div>
</div>
@endsection
