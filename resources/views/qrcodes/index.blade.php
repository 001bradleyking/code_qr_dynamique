<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard QR Codes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.js"></script>
    <style>
        .bg-blur-shape { position: absolute; border-radius: 9999px; filter: blur(80px); opacity: 0.5; z-index: 0; }
    </style>
</head>
<body class="relative min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50 to-slate-200 overflow-x-hidden">

    <div class="bg-blur-shape w-96 h-96 bg-indigo-300 -top-20 -left-20"></div>
    <div class="bg-blur-shape w-96 h-96 bg-sky-300 top-40 -right-20"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-6 py-14">

        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('qrcodes.features') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">← Fonctionnalités</a>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">Mes QR Codes</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-800">Déconnexion</button>
                    </form>
                </div>
                <a href="{{ route('qrcodes.create') }}"
                    class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-gray-800 transition shadow-sm">
                    + Nouveau QR Code
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-white/70 backdrop-blur-xl border border-white/60 text-green-700 text-sm px-4 py-3 rounded-2xl">
                {{ session('success') }}
            </div>
        @endif

        @if ($resources->isEmpty())
            <div class="text-center text-gray-500 py-20 bg-white/50 backdrop-blur-xl border border-white/60 rounded-2xl">
                Aucun QR code pour le moment. <a href="{{ route('qrcodes.create') }}" class="text-indigo-600 underline">Créer le premier</a>.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($resources as $resource)
                    <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl shadow-sm p-5 flex flex-col gap-3">

                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="font-semibold text-gray-800">
                                    {{ $resource->title ?? 'Sans titre' }}
                                </h2>
                                <span class="text-xs text-gray-400">/{{ $resource->short_code }}</span>
                            </div>

                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                {{ $resource->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                {{ $resource->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>

                        <!-- QR dessiné en JS, jamais stocké -->
                        <div class="flex justify-center bg-white/60 rounded-xl py-4">
                            <div class="qr-render" data-content="{{ $resource->qrContent() }}"></div>
                        </div>

                        <div class="text-sm text-gray-500">
                            Type :
                            <span class="font-medium text-gray-700">{{ $resource->type }}</span>
                        </div>

                        <div class="flex items-center gap-1 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ $resource->statistiques_count }} scan(s)
                        </div>

                        <div class="flex gap-2 mt-2 pt-3 border-t border-white/70">
                            <a href="{{ route('qrcodes.statistiques', $resource) }}"
                                class="flex-1 text-center text-sm bg-white/70 text-gray-700 rounded-lg py-1.5 hover:bg-white">
                                📊 Stats
                            </a>

                            <a class="qr-download-link flex-1 text-center text-sm bg-indigo-50 text-indigo-600 rounded-lg py-1.5 hover:bg-indigo-100 cursor-pointer"
                                data-short-code="{{ $resource->short_code }}">
                                Télécharger
                            </a>

                            <a href="{{ url('/r/' . $resource->short_code) }}" target="_blank"
                                class="flex-1 text-center text-sm bg-white/70 text-gray-700 rounded-lg py-1.5 hover:bg-white">
                                Ouvrir
                            </a>
                        </div>

                        <div class="flex gap-2">
                            <form action="{{ route('qrcodes.toggle', $resource) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full text-sm bg-white/70 text-gray-700 rounded-lg py-1.5 hover:bg-white">
                                    {{ $resource->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>

                            <form action="{{ route('qrcodes.destroy', $resource) }}" method="POST"
                                onsubmit="return confirm('Supprimer ce QR code ?');" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full text-sm bg-red-50 text-red-600 rounded-lg py-1.5 hover:bg-red-100">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $resources->links() }}
            </div>
        @endif
    </div>

    <script>
        document.querySelectorAll('.qr-render').forEach((el) => {
            const qr = qrcode(0, 'M');
            qr.addData(el.dataset.content);
            qr.make();
            el.innerHTML = qr.createImgTag(4, 4);
            el.querySelector('img').classList.add('w-28', 'h-28');
        });

        document.querySelectorAll('.qr-download-link').forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const shortCode = link.dataset.shortCode;
                const content = link.closest('.bg-white\\/70').querySelector('.qr-render').dataset.content;

                const qr = qrcode(0, 'M');
                qr.addData(content);
                qr.make();

                const a = document.createElement('a');
                a.href = qr.createDataURL(8, 8);
                a.download = `qr-${shortCode}.png`;
                a.click();
            });
        });
    </script>
</body>
</html>
