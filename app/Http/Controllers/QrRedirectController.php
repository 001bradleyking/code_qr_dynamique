<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class QrRedirectController extends Controller
{
    /**
     * Redirige un scan de QR code vers sa ressource cible.
     */
    public function redirect(string $short_code, Request $request): RedirectResponse|Response
    {
        // 1. Recherche de la ressource (404 si absente ou inactive)
        $resource = Resource::where('short_code', $short_code)
            ->where('is_active', true)
            ->firstOrFail();

        // 2. Enregistrement du scan
        $this->logScan($resource, $request);

        // 3. Traitement selon le type — accès SÉCURISÉ au tableau `data`
        //    (?? null au lieu d'un accès direct) pour ne jamais planter
        //    si une donnée attendue manque, et donner un message clair.
        $data = $resource->data ?? [];

        return match ($resource->type) {
            'WEBSITE' => $this->redirectToUrl($data['url'] ?? null),
            'IMAGE', 'VIDEO', 'PDF' => $this->redirectToUrl($data['file_url'] ?? null),
            'VCARD' => $this->renderVCard($data),
            'WIFI'  => $this->renderWifi($data),
            'MAPS'  => $this->redirectToUrl($this->buildMapsUrl($data)),
            default => abort(422, 'Type de ressource non supporté.'),
        };
    }

    /**
     * Redirige vers une URL, ou renvoie une 404 explicite si elle manque
     * (au lieu de faire planter l'application avec "Undefined array key").
     */
    private function redirectToUrl(?string $url): RedirectResponse
    {
        if (empty($url)) {
            abort(404, 'Cette ressource est incomplète : aucun contenu n\'est associé à ce QR code.');
        }

        return redirect()->away($url);
    }

    private function logScan(Resource $resource, Request $request): void
    {
        $geo = $this->resolveGeoFromIp($request->ip());

        $resource->statistiques()->create([
            'scanned_at' => now(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'country'    => $geo['country'] ?? null,
            'city'       => $geo['city'] ?? null,
        ]);
    }

    /**
     * Simulation de résolution géographique à partir de l'IP.
     * À remplacer par un vrai service (ipapi.co, MaxMind GeoLite2...).
     */
    private function resolveGeoFromIp(?string $ip): array
    {
        return ['country' => null, 'city' => null];
    }

    private function renderVCard(array $data): Response
    {
        $vcard  = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "FN:" . ($data['name'] ?? '') . "\r\n";
        $vcard .= "TEL:" . ($data['phone'] ?? '') . "\r\n";
        $vcard .= "EMAIL:" . ($data['email'] ?? '') . "\r\n";
        $vcard .= "END:VCARD\r\n";

        return response($vcard, 200, [
            'Content-Type'        => 'text/vcard',
            'Content-Disposition' => 'attachment; filename="contact.vcf"',
        ]);
    }

    private function renderWifi(array $data): \Illuminate\Contracts\View\View
    {
        $wifiUri = sprintf(
            'WIFI:S:%s;T:%s;P:%s;;',
            $data['ssid'] ?? '',
            $data['encryption'] ?? 'WPA',
            $data['password'] ?? ''
        );

        return view('qr.wifi', ['wifiUri' => $wifiUri, 'ssid' => $data['ssid'] ?? '']);
    }

    private function buildMapsUrl(array $data): ?string
    {
        if (isset($data['lat'], $data['lng'])) {
            return "https://www.google.com/maps/search/?api=1&query={$data['lat']},{$data['lng']}";
        }

        if (! empty($data['address'])) {
            return "https://www.google.com/maps/search/?api=1&query=" . urlencode($data['address']);
        }

        return null;
    }
}
