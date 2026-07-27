<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Wi-Fi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-blur-shape { position: absolute; border-radius: 9999px; filter: blur(80px); opacity: 0.5; z-index: 0; }
    </style>
</head>
<body class="relative min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50 to-slate-200 flex items-center justify-center px-4 overflow-x-hidden">

    <div class="bg-blur-shape w-96 h-96 bg-indigo-300 -top-20 -left-20"></div>
    <div class="bg-blur-shape w-96 h-96 bg-sky-300 top-40 -right-20"></div>

    <div class="relative z-10 bg-white/70 backdrop-blur-2xl border border-white/60 rounded-3xl shadow-xl p-8 max-w-sm w-full text-center">
        <h1 class="text-xl font-bold text-gray-800 mb-2">Réseau : {{ $ssid }}</h1>
        <p class="text-sm text-gray-500 mb-6">
            Si la connexion automatique n'a pas fonctionné, connecte-toi manuellement
            avec ces informations :
        </p>

        <div class="bg-white/80 rounded-2xl p-4 text-left text-sm text-gray-700 space-y-1 border border-white/60">
            <div><span class="font-medium">Réseau :</span> {{ $ssid }}</div>
            <div class="break-all"><span class="font-medium">Chaîne Wi-Fi :</span> {{ $wifiUri }}</div>
        </div>
    </div>
</body>
</html>
