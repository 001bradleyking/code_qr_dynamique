<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.js"></script>
    <style>
        /* Fond décoratif façon Apple : dégradé doux + formes floutées */
        .bg-blur-shape {
            position: absolute;
            border-radius: 9999px;
            filter: blur(80px);
            opacity: 0.5;
            z-index: 0;
        }
    </style>
</head>
<body class="relative min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50 to-slate-200 overflow-x-hidden">

    <!-- Formes décoratives en arrière-plan -->
    <div class="bg-blur-shape w-96 h-96 bg-indigo-300 -top-20 -left-20"></div>
    <div class="bg-blur-shape w-96 h-96 bg-sky-300 top-40 -right-20"></div>

    <div class="relative z-10 max-w-lg mx-auto py-16 px-4">

        <div class="flex items-center justify-between mb-6 px-2">
            <a href="{{ route('qrcodes.features') }}" class="text-sm text-gray-600 hover:text-gray-900 transition">← Fonctionnalités</a>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-600">{{ auth()->user()->name }}</span>
                <a href="{{ route('qrcodes.index') }}" class="text-gray-600 hover:text-gray-900 transition">Dashboard</a>
            </div>
        </div>

        <!-- Carte verre dépoli -->
        <div class="bg-white/60 backdrop-blur-2xl border border-white/70 rounded-3xl shadow-xl p-8">

            <h1 class="text-2xl font-bold text-gray-800 mb-6">Nouveau QR Code</h1>

            <form id="qrForm" class="space-y-5" enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Titre</label>
                    <input type="text" name="title"
                        class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Type de ressource</label>
                    <select id="typeSelect" name="type" required
                        class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                        <option value="">-- Choisir --</option>
                        <option value="WEBSITE">Site web</option>
                        <option value="IMAGE">Image</option>
                        <option value="VIDEO">Vidéo</option>
                        <option value="PDF">PDF</option>
                        <option value="VCARD">Fiche de contact (vCard)</option>
                        <option value="WIFI">Wi-Fi</option>
                        <option value="MAPS">Localisation (Maps)</option>
                    </select>
                </div>

                <!-- WEBSITE -->
                <div id="fields-WEBSITE" class="qr-fields hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">URL</label>
                    <input type="url" name="data[url]" placeholder="https://..."
                        class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                </div>

                <!-- IMAGE -->
                <div id="fields-IMAGE" class="qr-fields hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fichier image</label>
                    <input type="file" name="file" accept="image/*"
                        class="w-full rounded-xl border-2 border-dashed border-gray-300 bg-white/80 text-gray-900 px-4 py-3 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                </div>

                <!-- VIDEO -->
                <div id="fields-VIDEO" class="qr-fields hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fichier vidéo</label>
                    <input type="file" name="file" accept="video/*"
                        class="w-full rounded-xl border-2 border-dashed border-gray-300 bg-white/80 text-gray-900 px-4 py-3 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                </div>

                <!-- PDF -->
                <div id="fields-PDF" class="qr-fields hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fichier PDF</label>
                    <input type="file" name="file" accept="application/pdf"
                        class="w-full rounded-xl border-2 border-dashed border-gray-300 bg-white/80 text-gray-900 px-4 py-3 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                </div>

                <!-- VCARD -->
                <div id="fields-VCARD" class="qr-fields hidden space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet</label>
                        <input type="text" name="data[name]"
                            class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="text" name="data[phone]"
                            class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="data[email]"
                            class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                    </div>
                </div>

                <!-- WIFI -->
                <div id="fields-WIFI" class="qr-fields hidden space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom du réseau (SSID)</label>
                        <input type="text" name="data[ssid]"
                            class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                        <input type="text" name="data[password]"
                            class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type de sécurité</label>
                        <select name="data[encryption]"
                            class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                            <option value="WPA">WPA/WPA2</option>
                            <option value="WEP">WEP</option>
                            <option value="nopass">Aucun</option>
                        </select>
                    </div>
                </div>

                <!-- MAPS -->
                <div id="fields-MAPS" class="qr-fields hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
                    <input type="text" name="data[address]" placeholder="Ex: Cotonou, Bénin"
                        class="w-full rounded-xl border-2 border-gray-200 bg-white/80 text-gray-900 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition outline-none">
                </div>

                <button type="submit"
                    class="w-full bg-gray-900 text-white font-medium py-3 rounded-xl hover:bg-gray-800 active:scale-[0.98] transition shadow-lg">
                    Générer le QR Code
                </button>
            </form>

            <!-- Résultat : le QR est dessiné en JS, jamais généré côté serveur -->
            <div id="result" class="mt-8 hidden flex-col items-center gap-3 border-t border-white/70 pt-6">
                <div class="bg-white/90 rounded-2xl p-4 border-2 border-gray-100 shadow-sm">
                    <div id="qrContainer"></div>
                </div>
                <span id="shortUrl" class="text-sm text-gray-500"></span>
                <div class="flex gap-3">
                    <a id="downloadLink" download="qr-code.png"
                        class="text-sm bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                        Télécharger
                    </a>
                    <a href="{{ route('qrcodes.index') }}"
                        class="text-sm text-indigo-600 hover:underline self-center">Voir sur le dashboard →</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const typeSelect = document.getElementById('typeSelect');
        const allFieldGroups = document.querySelectorAll('.qr-fields');

        function showFieldsFor(type) {
            allFieldGroups.forEach(group => group.classList.add('hidden'));
            const target = document.getElementById(`fields-${type}`);
            if (target) target.classList.remove('hidden');
        }

        typeSelect.addEventListener('change', () => showFieldsFor(typeSelect.value));

        // Pré-sélection du type si on arrive depuis la page "Fonctionnalités" (?type=IMAGE)
        @if (!empty($preselectedType))
            typeSelect.value = "{{ $preselectedType }}";
            showFieldsFor("{{ $preselectedType }}");
        @endif

        /**
         * Dessine un QR code dans #qrContainer à partir d'un texte donné.
         * Librairie qrcode-generator : aucune dépendance serveur, aucun
         * appel réseau, tout se passe dans le navigateur.
         */
        function renderQrCode(content) {
            const qr = qrcode(0, 'M'); // typeNumber 0 = auto-ajusté, ECC niveau M
            qr.addData(content);
            qr.make();

            const container = document.getElementById('qrContainer');
            container.innerHTML = qr.createImgTag(6, 8); // taille de cellule 6px, marge 8

            // Prépare le bouton téléchargement à partir du data URL de l'image générée
            const img = container.querySelector('img');
            document.getElementById('downloadLink').href = img.src;
        }

        document.getElementById('qrForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);

            const response = await fetch("{{ route('qrcodes.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: formData,
            });

            if (!response.ok) {
                const err = await response.json().catch(() => null);
                alert('Erreur : ' + (err?.message ?? response.status));
                return;
            }

            const result = await response.json();

            renderQrCode(result.qr_content);
            document.getElementById('shortUrl').textContent = result.short_url;
            document.getElementById('result').classList.remove('hidden');
            document.getElementById('result').classList.add('flex');
        });
    </script>
</body>
</html>
