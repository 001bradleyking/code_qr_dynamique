<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques — {{ $resource->title ?? $resource->short_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.js"></script>
    <style>
        .bg-blur-shape { position: absolute; border-radius: 9999px; filter: blur(80px); opacity: 0.5; z-index: 0; }
    </style>
</head>
<body class="relative min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50 to-slate-200 overflow-x-hidden">

    <div class="bg-blur-shape w-96 h-96 bg-indigo-300 -top-20 -left-20"></div>
    <div class="bg-blur-shape w-96 h-96 bg-sky-300 top-40 -right-20"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-6 py-14">

        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('qrcodes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">← Dashboard</a>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">
                    {{ $resource->title ?? 'Sans titre' }}
                </h1>
                <span class="text-sm text-gray-400">/{{ $resource->short_code }} · {{ $resource->type }}</span>
            </div>

            <div class="qr-render bg-white/80 rounded-2xl p-3 border border-white/60"
                data-content="{{ $resource->qrContent() }}"></div>
        </div>

        <!-- Cartes résumé -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-5">
                <div class="text-sm text-gray-500 mb-1">Total des scans</div>
                <div class="text-3xl font-bold text-gray-800">{{ $resource->statistiques_count }}</div>
            </div>
            <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-5">
                <div class="text-sm text-gray-500 mb-1">Statut</div>
                <div class="text-lg font-semibold {{ $resource->is_active ? 'text-green-600' : 'text-gray-500' }}">
                    {{ $resource->is_active ? 'Actif' : 'Inactif' }}
                </div>
            </div>
            <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-5">
                <div class="text-sm text-gray-500 mb-1">Scans (14 derniers jours)</div>
                <div class="text-3xl font-bold text-gray-800">{{ $historiqueJours->sum('total') }}</div>
            </div>
        </div>

        <!-- Graphique en barres (CSS pur, pas de dépendance JS supplémentaire) -->
        <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl p-6 mb-8">
            <h2 class="text-sm font-medium text-gray-600 mb-4">Scans par jour</h2>

            @if ($historiqueJours->sum('total') === 0)
                <p class="text-sm text-gray-400 italic py-6 text-center">Aucun scan enregistré sur cette période.</p>
            @else
                <div class="flex items-end gap-2 h-40">
                    @foreach ($historiqueJours as $jour)
                        @php
                            $hauteurPct = $jour['total'] > 0 ? max(6, round(($jour['total'] / $maxScansJour) * 100)) : 2;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group">
                            <span class="text-[10px] text-gray-500 opacity-0 group-hover:opacity-100 transition">
                                {{ $jour['total'] }}
                            </span>
                            <div class="w-full bg-indigo-400/70 rounded-t-md hover:bg-indigo-500 transition"
                                style="height: {{ $hauteurPct }}%"></div>
                            <span class="text-[10px] text-gray-400">{{ $jour['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Historique des derniers scans -->
        <div class="bg-white/70 backdrop-blur-xl border border-white/60 rounded-2xl overflow-hidden">
            <h2 class="text-sm font-medium text-gray-600 px-6 pt-6 pb-3">Derniers scans</h2>

            @if ($derniersScans->isEmpty())
                <p class="text-sm text-gray-400 italic px-6 pb-6">Ce QR code n'a pas encore été scanné.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-t border-white/70">
                            <th class="px-6 py-2 font-medium">Date</th>
                            <th class="px-6 py-2 font-medium">Localisation</th>
                            <th class="px-6 py-2 font-medium">Appareil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($derniersScans as $scan)
                            <tr class="border-t border-white/70">
                                <td class="px-6 py-3 text-gray-700">
                                    {{ $scan->scanned_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    @if ($scan->city || $scan->country)
                                        {{ collect([$scan->city, $scan->country])->filter()->join(', ') }}
                                    @else
                                        <span class="italic text-gray-400">Non disponible</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500 truncate max-w-xs" title="{{ $scan->user_agent }}">
                                    {{ \Illuminate\Support\Str::limit($scan->user_agent, 50) ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <p class="text-xs text-gray-400 mt-4 text-center">
            La géolocalisation par IP n'est pas encore activée — les colonnes pays/ville
            resteront vides tant qu'un service de géolocalisation n'est pas branché.
        </p>
    </div>

    <script>
        document.querySelectorAll('.qr-render').forEach((el) => {
            const qr = qrcode(0, 'M');
            qr.addData(el.dataset.content);
            qr.make();
            el.innerHTML = qr.createImgTag(3, 0);
            el.querySelector('img').classList.add('w-20', 'h-20');
        });
    </script>
</body>
</html>
