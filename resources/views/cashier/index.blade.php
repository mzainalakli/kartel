{{-- resources/views/cashier/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasir — Catat Pembelian</title>

  {{-- Tailwind CDN untuk prototipe cepat --}}
  <script src="https://cdn.tailwindcss.com"></script>
  {{-- Alpine untuk interaksi ringan --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    .btn { @apply rounded-xl px-4 py-2.5 font-medium transition; }
    .btn-primary { @apply bg-amber-500 text-white hover:bg-amber-600 active:bg-amber-700; }
    .btn-ghost { @apply border border-slate-300 bg-white hover:border-amber-400; }
    .input { @apply w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 focus:ring-2 focus:ring-amber-500; }
    .label { @apply block text-sm font-medium mb-1; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800" x-data="{ toast:false, toastText:'' }">

  {{-- Header --}}
  <header class="bg-gradient-to-r from-amber-500 to-orange-600 text-white">
    <div class="max-w-5xl mx-auto px-4 py-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold">Kasir — Catat Pembelian</h1>
        {{-- <p class="opacity-90">Dummy UI • Input nomor telepon, pilih produk & qty • Poin dihitung otomatis</p> --}}
      </div>
      <a href="{{ url('/katalog') }}" class="btn btn-ghost hidden sm:inline-flex">← Katalog</a>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-4 py-6">

    {{-- Kartu Form Kasir (Dummy) --}}
    <div
      x-data="{
        // ===== Dummy data produk =====
        products: [
          {id: 1, name: 'Keripik Balado', ppu: 1, price: 15000},
          {id: 2, name: 'Pastel Isi Abon', ppu: 1, price: 12000},
          {id: 3, name: 'Tela-Tela Keju', ppu: 1, price: 10000},
        ],
        phone: '',
        isNew: false,
        name: '',
        productId: 1,
        qty: 1,
        // ===== Computed =====
        get selected(){ return this.products.find(p => Number(p.id) === Number(this.productId)) || {ppu:0,price:0}; },
        get points(){ return Math.max(1, Number(this.qty || 1)) * Number(this.selected.ppu || 0); },
        get subtotal(){ return Math.max(1, Number(this.qty || 1)) * Number(this.selected.price || 0); },
        // ===== Actions =====
        inc(){ this.qty = Math.max(1, Number(this.qty||1) + 1); },
        dec(){ this.qty = Math.max(1, Number(this.qty||1) - 1); },
        resetForm(){
          this.phone=''; this.isNew=false; this.name='';
          this.productId=this.products[0]?.id||1; this.qty=1;
        },
        submit(){
          const payload = {
            phone: this.phone,
            name: this.isNew ? this.name : undefined,
            product_id: this.productId,
            qty: Number(this.qty),
            points: this.points,
            subtotal: this.subtotal
          };
          console.log('DUMMY SUBMIT:', payload);

          // Toast sukses (global)
          const bodyX = document.querySelector('body').__x?.$data;
          if (bodyX) {
            bodyX.toastText = `Dummy: +${this.points} poin untuk ${this.phone || 'pelanggan'} • Subtotal Rp ${this.subtotal.toLocaleString('id-ID')}`;
            bodyX.toast = true; setTimeout(()=> bodyX.toast = false, 1800);
          }
          this.resetForm();
        }
      }"
      class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 md:p-6"
    >
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Input Transaksi</h2>
        {{-- <span class="text-sm text-slate-500">*Dummy — belum menyimpan ke server</span> --}}
      </div>

      <form @submit.prevent="submit" class="grid gap-4 md:grid-cols-12">

        {{-- No. Telepon --}}
        <div class="md:col-span-5">
          <label class="label">No. Telepon</label>
          <input type="tel" inputmode="numeric" pattern="[0-9+ ]*" x-model.trim="phone" placeholder="08xx / +62..."
                 class="input" required>
          <p class="text-xs text-slate-500 mt-1">Untuk identifikasi pelanggan & akumulasi poin.</p>
        </div>

        {{-- Pelanggan Baru (dummy toggle) --}}
        <div class="md:col-span-3 flex items-end">
          <label class="inline-flex items-center gap-2 select-none">
            <input type="checkbox" x-model="isNew" class="h-4 w-4 rounded border-slate-300">
            <span class="text-sm">Pelanggan baru?</span>
          </label>
        </div>

        {{-- Nama Pelanggan (muncul jika pelanggan baru) --}}
        <div class="md:col-span-4" x-show="isNew" x-transition>
          <label class="label">Nama Pelanggan</label>
          <input type="text" x-model.trim="name" placeholder="Nama pelanggan" class="input" :required="isNew">
        </div>

        {{-- Produk --}}
        <div class="md:col-span-6">
          <label class="label">Produk</label>
          <select x-model="productId" class="input">
            <template x-for="p in products" :key="p.id">
              <option :value="p.id" x-text="p.name"></option>
            </template>
          </select>
          <div class="mt-1 text-xs text-slate-600">
            Poin per item: <span class="font-semibold" x-text="selected.ppu"></span> •
            Harga: Rp <span class="font-semibold" x-text="selected.price.toLocaleString('id-ID')"></span>
          </div>
        </div>

        {{-- Qty + stepper --}}
        <div class="md:col-span-3">
          <label class="label">Qty</label>
          <div class="flex items-center gap-2">
            <button type="button" class="btn btn-ghost" @click="dec()">−</button>
            <input type="number" min="1" step="1" x-model.number="qty" class="input text-center" />
            <button type="button" class="btn btn-ghost" @click="inc()">＋</button>
          </div>
        </div>

        {{-- Poin total --}}
        <div class="md:col-span-3">
          <label class="label">Poin</label>
          <div class="h-[42px] flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 font-semibold">
            +<span x-text="points"></span> poin
          </div>
        </div>

        {{-- Subtotal --}}
        <div class="md:col-span-4">
          <label class="label">Subtotal</label>
          <div class="h-[42px] flex items-center justify-center rounded-xl bg-slate-100 text-slate-700 font-semibold">
            Rp <span x-text="subtotal.toLocaleString('id-ID')"></span>
          </div>
        </div>

        {{-- Aksi --}}
        <div class="md:col-span-12 flex gap-3">
          <button type="submit" class="btn btn-primary">
            Simpan
          </button>
          <button type="button" class="btn btn-ghost" @click="resetForm()">
            Reset
          </button>
          <a href="{{ route('cashier.index') }}" class="btn btn-ghost hidden md:inline-flex">Refresh</a>
        </div>
      </form>
    </div>

    {{-- Tip kecil --}}
    {{-- <p class="mt-4 text-sm text-slate-500">
      Catatan: Ini tampilan dummy. Saat siap tersambung backend, ganti fungsi <code>submit()</code> untuk
      memanggil endpoint server (mis. <code>/admin/cashier/record</code>) dan hapus toggle “Pelanggan baru?”
      agar server yang menentukan kapan butuh nama.
    </p> --}}
  </main>

  {{-- Toast sederhana (global) --}}
  <div x-show="toast" x-transition
       class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-4 py-2 rounded-full shadow-lg">
    <span x-text="toastText"></span>
  </div>
</body>
</html>
