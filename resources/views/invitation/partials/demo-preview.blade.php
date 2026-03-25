{{-- Bagian demo lengkap saat preview template: ucapan, detail acara, galeri foto --}}
<section class="demo-preview bg-stone-100 text-stone-800 py-12 px-4">
    <div class="max-w-2xl mx-auto space-y-12">
        {{-- Ucapan / Bismillah --}}
        @if(isset($demo['quote']) || isset($demo['invitation_text']))
            <div class="text-center">
                @if(!empty($demo['quote']))
                    <p class="text-2xl md:text-3xl text-amber-700/90 font-serif mb-4">{{ $demo['quote'] }}</p>
                @endif
                @if(!empty($demo['invitation_text']))
                    <p class="text-stone-600 leading-relaxed">{{ $demo['invitation_text'] }}</p>
                @endif
            </div>
        @endif

        {{-- Detail Acara (jadwal) --}}
        @if(!empty($demo['events']))
            <div>
                <h2 class="text-center text-lg font-semibold text-stone-800 uppercase tracking-wider mb-6">Detail Acara</h2>
                <div class="space-y-4">
                    @foreach($demo['events'] as $event)
                        <div class="flex flex-wrap gap-3 items-start rounded-xl bg-white/80 border border-stone-200 p-4 shadow-sm">
                            <div class="w-20 shrink-0 text-amber-700 font-medium">{{ $event['time'] }}</div>
                            <div>
                                <p class="font-semibold text-stone-800">{{ $event['name'] }}</p>
                                <p class="text-stone-600 text-sm">{{ $event['place'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Galeri Foto --}}
        @if(!empty($demoPhotos))
            <div>
                <h2 class="text-center text-lg font-semibold text-stone-800 uppercase tracking-wider mb-6">Galeri</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($demoPhotos as $photo)
                        <div class="aspect-[4/3] rounded-xl overflow-hidden shadow-md bg-stone-200">
                            <img src="{{ $photo }}" alt="Preview" class="w-full h-full object-cover" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Lokasi & Maps (dari $wedding) --}}
        @if(isset($wedding) && $wedding->location)
            <div class="text-center pt-4">
                <h2 class="text-lg font-semibold text-stone-800 uppercase tracking-wider mb-2">Lokasi</h2>
                <p class="text-stone-600">{{ $wedding->location }}</p>
                @if($wedding->map_link)
                    <a href="{{ $wedding->map_link }}" target="_blank" rel="noopener" class="inline-block mt-3 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 transition">Lihat di Google Maps</a>
                @endif
            </div>
        @endif
    </div>
</section>
