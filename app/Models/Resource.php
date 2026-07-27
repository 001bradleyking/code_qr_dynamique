<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Resource extends Model
{
    use HasUuids;

    protected $fillable = ['short_code', 'title', 'type', 'data', 'is_active'];

    protected $casts = [
        'data'      => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Génère automatiquement un short_code unique à la création.
     */
    protected static function booted(): void
    {
        static::creating(function (Resource $resource) {
            if (empty($resource->short_code)) {
                $resource->short_code = self::generateUniqueShortCode();
            }
        });
    }

    private static function generateUniqueShortCode(): string
    {
        do {
            $code = Str::lower(Str::random(7));
        } while (self::where('short_code', $code)->exists());

        return $code;
    }

    public function statistiques(): HasMany
    {
        return $this->hasMany(Statistique::class);
    }

    /**
     * Contenu réel à encoder dans le QR (dessiné en JS côté vue).
     *
     * WIFI encode directement le protocole WIFI:S:...;P:...;; pour une
     * connexion automatique au scan. Tous les autres types encodent
     * l'URL de redirection courte /r/{short_code} : c'est ce niveau
     * d'indirection qui permet de changer la cible sans réimprimer le QR.
     */
    public function qrContent(): string
    {
        if ($this->type === 'WIFI') {
            $data = $this->data ?? [];

            return sprintf(
                'WIFI:S:%s;T:%s;P:%s;;',
                $data['ssid'] ?? '',
                $data['encryption'] ?? 'WPA',
                $data['password'] ?? ''
            );
        }

        return url("/r/{$this->short_code}");
    }
}
