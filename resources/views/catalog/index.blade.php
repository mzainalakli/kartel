{{-- resources/views/catalog/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Katalog Produk</title>

  {{-- Tailwind CDN untuk prototipe cepat --}}
  <script src="https://cdn.tailwindcss.com"></script>
  {{-- Alpine untuk interaksi ringan (toast, dll.) --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    /* Rasio gambar kartu */
    .card-img { aspect-ratio: 4 / 3; object-fit: cover; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800" x-data="{ toast:false, toastText:'' }">

  {{-- Header --}}
  <header class="bg-gradient-to-r from-amber-500 to-orange-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-6">
      <h1 class="text-2xl md:text-3xl font-bold">Katalog Produk</h1>
      <p class="opacity-90">Keripik • Pastel • Tela-Tela — kumpulan snack favorit pelanggan</p>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6">
    {{-- Toolbar: Search + Filter kategori + Sort --}}
    <form method="GET" class="grid gap-3 md:grid-cols-12 items-center mb-6">
      {{-- Search --}}
      <div class="md:col-span-6">
        <label class="sr-only" for="q">Cari</label>
        <div class="relative">
          <input id="q" name="q" value="{{ $q ?? '' }}" type="search" placeholder="Cari produk, contoh: keripik, pastel..."
                 class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-amber-500">
          <button class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-500" aria-label="Cari">
            🔎
          </button>
        </div>
      </div>

      {{-- Kategori --}}
      <div class="md:col-span-4">
        <div class="flex gap-2 overflow-x-auto scrollbar-hide">
          @php
            $cats = [
              ['key'=>null,          'label'=>'Semua'],
              ['key'=>'keripik',     'label'=>'Keripik'],
              ['key'=>'pastel',      'label'=>'Pastel'],
              ['key'=>'tela-tela',   'label'=>'Tela-Tela'],
            ];
          @endphp
          @foreach ($cats as $c)
            @php
              $active = ($category ?? null) === $c['key'] || ($c['key'] === null && empty($category));
              $query  = array_filter([
                'q' => $q ?? null,
                'category' => $c['key'],
                'sort' => $sort ?? null,
              ], fn($v)=>$v!==null && $v!=='');
              $url = request()->url() . (empty($query) ? '' : '?' . http_build_query($query));
            @endphp
            <a href="{{ $url }}"
               class="whitespace-nowrap rounded-full px-4 py-2 text-sm border
                      {{ $active ? 'bg-amber-500 border-amber-500 text-white' : 'bg-white border-slate-300 hover:border-amber-400' }}">
              {{ $c['label'] }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- Sort --}}
      <div class="md:col-span-2">
        <label for="sort" class="sr-only">Urutkan</label>
        <select id="sort" name="sort" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 focus:ring-2 focus:ring-amber-500"
                onchange="this.form.submit()">
          <option value="pop"        {{ ($sort ?? '') === 'pop' ? 'selected' : '' }}>Paling relevan</option>
          <option value="price_asc"  {{ ($sort ?? '') === 'price_asc' ? 'selected' : '' }}>Harga: termurah</option>
          <option value="price_desc" {{ ($sort ?? '') === 'price_desc' ? 'selected' : '' }}>Harga: termahal</option>
          <option value="points_desc"{{ ($sort ?? '') === 'points_desc' ? 'selected' : '' }}>Poin tertinggi</option>
        </select>
      </div>
    </form>

    {{-- Badge filter aktif --}}
    @if(($q ?? '') || ($category ?? ''))
      <div class="mb-4 text-sm text-slate-600">
        <span class="mr-2">Filter:</span>
        @if($q ?? '')
          <span class="inline-flex items-center gap-2 bg-slate-200 rounded-full px-3 py-1">Cari: “{{ $q }}”</span>
        @endif
        @if($category ?? '')
          <span class="inline-flex items-center gap-2 bg-slate-200 rounded-full px-3 py-1">Kategori: {{ ucfirst($category) }}</span>
        @endif
      </div>
    @endif

    {{-- Grid produk --}}
    @if (count($products))
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach ($products as $p)
          <div class="group rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition overflow-hidden">
            {{-- Gambar --}}
            @php
              // Taruh gambar di: public/images/products/<image>
              $img = asset('images/products/' . $p['image']);
            @endphp
            <img class="w-full card-img" src="{{ $img }}" alt="{{ $p['name'] }}" onerror="this.src='https://placehold.co/600x450?text={{ urlencode($p['name']) }}'">

            <div class="p-4">
              {{-- Tags --}}
              @if (!empty($p['tags']))
                <div class="mb-2 flex flex-wrap gap-2">
                  @foreach ($p['tags'] as $t)
                    <span class="text-[11px] uppercase tracking-wide rounded-full bg-amber-100 text-amber-700 px-2 py-1">{{ $t }}</span>
                  @endforeach
                  <span class="text-[11px] uppercase tracking-wide rounded-full bg-slate-100 text-slate-700 px-2 py-1">{{ ucfirst($p['category']) }}</span>
                </div>
              @endif

              {{-- Nama --}}
              <h3 class="font-semibold text-slate-800 group-hover:text-amber-600 transition">
                {{ $p['name'] }}
              </h3>

              {{-- Harga & Poin --}}
              <div class="mt-2 flex items-center justify-between">
                <div class="text-lg font-bold text-slate-900">
                  Rp {{ number_format($p['price'], 0, ',', '.') }}
                </div>
                <div class="text-xs bg-emerald-50 text-emerald-700 px-2 py-1 rounded-full">
                  +{{ $p['points'] }} poin
                </div>
              </div>

              {{-- Aksi --}}
              <div class="mt-4 flex gap-2">
                {{-- <button
                  type="button"
                  class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 text-white px-4 py-2.5 font-medium
                         hover:bg-amber-600 active:bg-amber-700 transition"
                  @click="toastText = 'Ditambahkan: {{ $p['name'] }}'; toast=true; setTimeout(()=>toast=false,1400);">
                  Tambah
                </button> --}}

                {{-- <a href="{{ route('catalog.index', ['q' => $p['name']]) }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 hover:border-amber-400 transition"
                   aria-label="Lihat serupa">
                  ⋯
                </a> --}}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
        Tidak ada produk yang cocok. Coba ubah kata kunci atau kategori.
      </div>
    @endif
  </main>

  {{-- Toast sederhana --}}
  <div x-show="toast" x-transition
       class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-4 py-2 rounded-full shadow-lg">
    <span x-text="toastText"></span>
  </div>
</body>
</html>
