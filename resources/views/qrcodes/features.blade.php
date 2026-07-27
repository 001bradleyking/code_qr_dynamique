<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>QR Dynamique — Fonctionnalités</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-blur-shape { position: absolute; border-radius: 9999px; filter: blur(80px); opacity: 0.5; z-index: 0; }
    </style>
</head>
<body class="relative min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50 to-slate-200 overflow-x-hidden">

    <div class="bg-blur-shape w-96 h-96 bg-indigo-300 -top-20 -left-20"></div>
    <div class="bg-blur-shape w-96 h-96 bg-sky-300 top-40 -right-20"></div>

    <div class="relative z-10">

    <!-- Barre de connexion -->
    <div class="relative z-10 max-w-6xl mx-auto px-6 pt-6 flex justify-end">
        @auth
            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-600">Bonjour, {{ auth()->user()->name }}</span>
                <a href="{{ route('qrcodes.index') }}" class="text-indigo-600 hover:underline">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-800">Déconnexion</button>
                </form>
            </div>
        @else
            <a href="{{ route('login') }}"
                class="text-sm bg-white/70 backdrop-blur-xl border border-white/60 px-4 py-2 rounded-xl text-gray-700 hover:bg-white transition">
                Se connecter
            </a>
        @endauth
    </div>

    <!-- Header -->
    <header class="max-w-6xl mx-auto px-6 pt-10 pb-12 text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">QR Codes Dynamiques</h1>
        <p class="text-gray-500 max-w-xl mx-auto">
            Un seul QR imprimé, un contenu que tu peux changer à tout moment.
            Choisis ce que tu veux encoder et génère ton QR en quelques secondes.
        </p>
        <div class="mt-8 flex justify-center gap-3">
            <a href="{{ route('qrcodes.create') }}"
                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-indigo-700 transition shadow-sm">
                + Créer un QR Code
            </a>
            <a href="{{ route('qrcodes.index') }}"
                class="bg-white text-gray-700 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition border border-gray-200">
                Voir le dashboard
            </a>
        </div>
    </header>

    <!-- Grille des fonctionnalités -->
    <div class="max-w-6xl mx-auto px-6 pb-20">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <a href="{{ route('qrcodes.create', ['type' => 'WEBSITE']) }}"
                class="group bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center mb-4 group-hover:bg-indigo-100 transition">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Site web</h3>
                <p class="text-sm text-gray-500">Redirige vers n'importe quelle URL, modifiable à tout moment.</p>
            </a>

            <a href="{{ route('qrcodes.create', ['type' => 'IMAGE']) }}"
                class="group bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center mb-4 group-hover:bg-pink-100 transition">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Image</h3>
                <p class="text-sm text-gray-500">Partage une photo ou une affiche depuis un simple scan.</p>
            </a>

            <a href="{{ route('qrcodes.create', ['type' => 'VIDEO']) }}"
                class="group bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center mb-4 group-hover:bg-red-100 transition">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Vidéo</h3>
                <p class="text-sm text-gray-500">Diffuse une vidéo de présentation ou un tutoriel.</p>
            </a>

            <a href="{{ route('qrcodes.create', ['type' => 'PDF']) }}"
                class="group bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center mb-4 group-hover:bg-orange-100 transition">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">PDF</h3>
                <p class="text-sm text-gray-500">Documents, menus, catalogues, brochures téléchargeables.</p>
            </a>

            <a href="{{ route('qrcodes.create', ['type' => 'VCARD']) }}"
                class="group bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Fiche de contact</h3>
                <p class="text-sm text-gray-500">Ajout automatique au répertoire du téléphone (vCard).</p>
            </a>

            <a href="{{ route('qrcodes.create', ['type' => 'WIFI']) }}"
                class="group bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center mb-4 group-hover:bg-sky-100 transition">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01M4.929 12.929a10 10 0 0114.142 0M1.5 9.5a14 14 0 0121 0" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Wi-Fi</h3>
                <p class="text-sm text-gray-500">Connexion automatique au réseau en un seul scan.</p>
            </a>

            <a href="{{ route('qrcodes.create', ['type' => 'MAPS']) }}"
                class="group bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all sm:col-span-2 lg:col-span-1">
                <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center mb-4 group-hover:bg-violet-100 transition">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 mb-1">Localisation</h3>
                <p class="text-sm text-gray-500">Ouvre directement l'itinéraire dans Google Maps.</p>
            </a>

        </div>
    </div>
    </div>
</body>
</html>
