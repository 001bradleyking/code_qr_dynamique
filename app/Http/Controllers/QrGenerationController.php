<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QrGenerationController extends Controller
{
    /**
     * Dashboard listant toutes les ressources (= QR codes) avec leurs stats.
     */
    public function index()
    {
        $resources = Resource::withCount('statistiques')
            ->latest()
            ->paginate(12);

        return view('qrcodes.index', compact('resources'));
    }

    /**
     * Page de présentation des fonctionnalités.
     */
    public function features()
    {
        return view('qrcodes.features');
    }

    /**
     * Formulaire de création. Accepte ?type=IMAGE pour pré-sélectionner
     * le type quand on arrive depuis la page features.
     */
    public function create(Request $request)
    {
        $preselectedType = $request->query('type');

        return view('qrcodes.create', compact('preselectedType'));
    }

    /**
     * Crée une Resource (= le QR dynamique + sa donnée cible).
     *
     * La validation dépend du type choisi : c'est ce qui empêche de créer
     * un QR "cassé" (ex: IMAGE sans fichier), la cause du bug
     * "Undefined array key file_url" rencontré au scan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'type'  => 'required|in:WEBSITE,IMAGE,VIDEO,PDF,VCARD,WIFI,MAPS',
            'file'  => 'nullable|file|max:20480',
        ]);

        $validator->after(function ($validator) use ($request) {
            $type = $request->input('type');
            $data = $request->input('data', []);

            match ($type) {
                'WEBSITE' => $this->requireField($validator, $data, 'url', 'URL du site'),
                'VCARD'   => $this->requireField($validator, $data, 'name', 'Nom complet'),
                'WIFI'    => $this->requireField($validator, $data, 'ssid', 'Nom du réseau (SSID)'),
                'MAPS'    => $this->requireField($validator, $data, 'address', 'Adresse'),
                'IMAGE', 'VIDEO', 'PDF' => $request->hasFile('file')
                    ? null
                    : $validator->errors()->add('file', 'Un fichier est requis pour ce type de ressource.'),
                default => null,
            };
        });

        $validated = $validator->validate();

        $resourceData = $validated['data'] ?? $request->input('data', []);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('resources', 'public');
            $resourceData['file_url'] = Storage::disk('public')->url($path);
        }

        $resource = Resource::create([
            'title' => $validated['title'] ?? null,
            'type'  => $validated['type'],
            'data'  => $resourceData,
        ]);

        return response()->json([
            'resource'   => $resource,
            'qr_content' => $resource->qrContent(),
            'short_url'  => url("/r/{$resource->short_code}"),
        ]);
    }

    /**
     * Ajoute une erreur de validation si un champ attendu dans `data` est vide.
     */
    private function requireField($validator, array $data, string $key, string $label): void
    {
        if (empty($data[$key])) {
            $validator->errors()->add("data.$key", "$label est requis pour ce type de ressource.");
        }
    }

    /**
     * Page de statistiques d'un QR code : total, répartition par jour
     * (14 derniers jours) et historique des derniers scans.
     */
    public function statistiques(Resource $resource)
    {
        $resource->loadCount('statistiques');

        // Scans des 14 derniers jours, groupés par date, pour le graphique en barres
        $scansParJour = $resource->statistiques()
            ->selectRaw('DATE(scanned_at) as jour, COUNT(*) as total')
            ->where('scanned_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('jour')
            ->pluck('total', 'jour');

        // On complète les jours sans scan avec 0, pour un graphique continu
        $historiqueJours = collect(range(0, 13))->map(function ($i) use ($scansParJour) {
            $date = now()->subDays(13 - $i)->format('Y-m-d');

            return [
                'date'  => $date,
                'label' => now()->subDays(13 - $i)->format('d/m'),
                'total' => $scansParJour[$date] ?? 0,
            ];
        });

        $maxScansJour = max(1, $historiqueJours->max('total')); // évite une division par 0

        // 20 derniers scans, les plus récents en premier
        $derniersScans = $resource->statistiques()
            ->latest('scanned_at')
            ->limit(20)
            ->get();

        return view('qrcodes.statistiques', compact('resource', 'historiqueJours', 'maxScansJour', 'derniersScans'));
    }

    public function toggleActive(Resource $resource)
    {
        $resource->update(['is_active' => ! $resource->is_active]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();

        return back()->with('success', 'QR code supprimé.');
    }
}
