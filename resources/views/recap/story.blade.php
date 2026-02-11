<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recap KHI Story</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;800&family=Playfair+Display:ital,wght@1,600&display=swap');
        .font-sans { font-family: 'Outfit', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-black font-sans antialiased h-screen w-screen overflow-hidden flex justify-center items-center">

    <div x-data="{
            step: 1,
            total: 6,
            next() { if(this.step < this.total) this.step++ },
            prev() { if(this.step > 1) this.step-- },
            restart() { this.step = 1 }
         }"
         class="relative w-full max-w-[420px] h-full max-h-[850px] bg-gray-900 shadow-2xl overflow-hidden sm:rounded-3xl border border-white/10">

        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>
            <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            <div class="absolute inset-0 opacity-[0.05]" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"></div>
        </div>

        <div class="relative z-10 h-full flex flex-col">

            <div class="pt-4 px-3 flex gap-1.5 z-50">
                <template x-for="i in total">
                    <div class="flex-1 h-1 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-white transition-all duration-500 ease-out"
                             :style="step >= i ? 'width: 100%' : 'width: 0%'"></div>
                    </div>
                </template>
            </div>

            <div class="px-4 py-4 flex justify-between items-center text-white/60 z-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-yellow-400 to-orange-500 p-[1px]">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->fullname ?? 'User') }}&background=0D8ABC&color=fff" class="rounded-full border-2 border-black" alt="Profile">
                    </div>
                    <span class="text-xs font-bold tracking-wide text-white">KHI Wrapped {{ date('Y') - 1 }}</span>
                </div>
                <button onclick="parent.$('#recapModal').modal('hide')" class="hover:text-white transition-colors z-50 relative p-2">✕</button>
            </div>

            <div class="flex-1 flex flex-col justify-center px-6 pb-20 text-white text-center">

                <div x-show="step === 1" x-transition:enter="transition ease-out duration-700 transform" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="space-y-8 flex flex-col items-center">
                    <div class="animate-pulse mb-4">
                        <span class="text-6xl text-yellow-400">✨</span>
                    </div>
                    <h2 class="text-2xl font-serif italic opacity-90 leading-relaxed px-4">
                        Waktunya refleksi kegiatanmu di
                    </h2>
                    <div class="relative">
                        <div class="absolute inset-0 bg-yellow-500 blur-3xl opacity-20"></div>
                        <span class="relative text-8xl font-black text-transparent bg-clip-text bg-gradient-to-b from-yellow-200 to-yellow-600 drop-shadow-2xl">
                            2025
                        </span>
                    </div>
                    <h3 class="text-xl font-light tracking-widest uppercase opacity-80 mt-2">
                        dari KHI
                    </h3>

                    <div class="absolute bottom-32 left-0 right-0 flex justify-center animate-bounce opacity-60">
                        <div class="flex flex-col items-center">
                            <span class="text-xs uppercase tracking-widest">Ketuk layar untuk mulai</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div x-show="step === 2" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    <div class="inline-block p-4 rounded-full bg-white/10 backdrop-blur-md mb-4 border border-white/20 shadow-[0_0_30px_rgba(255,255,255,0.2)]">
                        <span class="text-5xl">🚀</span>
                    </div>
                    <h2 class="text-lg font-light tracking-widest uppercase opacity-80">Performa Kamu</h2>
                    <div>
                        <span class="text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-b from-white to-white/50">
                            {{ $totalKegiatan }}
                        </span>
                        <p class="text-xl mt-2 font-medium">Total Kegiatan</p>
                    </div>
                    <p class="text-sm opacity-60 px-4">Setiap klik, setiap input, adalah jejak kontribusi nyata Anda di BPS Padang Panjang.</p>
                </div>

                <div x-show="step === 3" x-transition:enter="transition ease-out duration-500" class="space-y-8">
                    <h2 class="text-2xl font-serif italic leading-relaxed">"Bukan cuma angka, ini bukti dedikasimu."</h2>

                    <div class="space-y-3 text-left">
                        @forelse($summarySatuan as $index => $row)
                            @php
                                $icons = ['📄', '💻', '📊', '🤝', '✅'];
                                $currentIcon = $icons[$index % count($icons)];
                            @endphp
                            <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/10 flex justify-between items-center hover:bg-white/20 transition-colors transform {{ $index % 2 == 0 ? 'rotate-1' : '-rotate-1' }}">
                                <div>
                                    <p class="text-xs uppercase tracking-wider opacity-60">{{ $row->satuan }}</p>
                                    <p class="text-xl font-bold">{{ $row->total }} <span class="text-sm font-normal">Selesai</span></p>
                                </div>
                                <span class="text-2xl">{{ $currentIcon }}</span>
                            </div>
                        @empty
                            <div class="text-center opacity-50">Belum ada data detail satuan.</div>
                        @endforelse
                    </div>
                </div>

                <div x-show="step === 4" x-transition:enter="transition ease-out duration-500" class="space-y-2">
                    <p class="text-sm uppercase tracking-widest opacity-60">Mode "Work Hard"</p>
                    <h2 class="text-4xl font-bold text-yellow-400 mb-8">{{ $busiestMonth }} 🔥</h2>

                    <div class="relative bg-gradient-to-br from-orange-500 to-rose-600 p-8 rounded-3xl shadow-2xl rotate-2 transform hover:rotate-0 transition-transform duration-300">
                        <div class="absolute top-2 left-1/2 -translate-x-1/2 w-8 h-1 bg-black/20 rounded-full"></div>
                        <p class="text-sm text-black/60 font-bold uppercase mb-2">Hari Paling Sibuk</p>
                        <p class="text-4xl font-black text-white leading-tight">{{ $busiestDate }}</p>
                        <div class="mt-4 inline-block bg-black/20 px-3 py-1 rounded-full text-xs font-bold text-white">
                            {{ $busiestCount }} Aktivitas Tercatat
                        </div>
                    </div>
                </div>

                <div x-show="step === 5" x-transition:enter="transition ease-out duration-500" class="space-y-8">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-500 blur-3xl opacity-20"></div>
                        <span class="relative text-8xl">☕</span>
                    </div>

                    <div>
                        <h2 class="text-5xl font-bold mb-2">{{ $favHour }} <span class="text-xl font-normal opacity-50">WIB</span></h2>
                        <div class="h-1 w-20 bg-blue-500 mx-auto rounded-full mb-6"></div>
                    </div>

                    <p class="text-xl font-light leading-relaxed px-4">
                        Kamu paling sering update di jam ini. Waktu favoritmu untuk <span class="text-blue-300 font-bold">refleksi pekerjaan</span>.
                    </p>
                </div>

                <div x-show="step === 6" x-transition:enter="transition ease-out duration-700" class="space-y-10">
                    <div class="animate-bounce text-7xl">💙</div>

                    <div class="font-serif text-2xl leading-relaxed space-y-4">
                        <p>"Terima kasih sudah membersamai KHI."</p>
                        <p class="text-lg opacity-80 font-sans font-light">
                            Keberadaanmu lebih dari sekadar rekan kerja, tapi sebagai sebuah <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400 font-bold">makna</span>.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-white/10 mt-6">
                        <p class="text-xs font-bold tracking-[0.2em] text-white/50 uppercase font-sans">
                            - Tim Dilan BPS Kota Padang Panjang
                        </p>
                    </div>

                    <div class="pt-2 relative z-50">
                        <button onclick="parent.$('#recapModal').modal('hide')" class="w-full py-4 bg-white text-gray-900 rounded-xl font-bold text-lg shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:scale-105 active:scale-95 transition-transform">
                            Tutup Recap
                        </button>
                        <button @click="restart()" class="mt-4 text-sm text-white/50 hover:text-white underline block w-full text-center">
                            Putar Lagi
                        </button>
                    </div>
                </div>

            </div>

            <div class="absolute inset-0 z-40 flex mt-16 mb-24">
                <div @click="prev()" class="w-1/3 h-full cursor-pointer"></div>
                <div @click="next()" class="w-2/3 h-full cursor-pointer"></div>
            </div>

        </div>
    </div>

</body>
</html>
