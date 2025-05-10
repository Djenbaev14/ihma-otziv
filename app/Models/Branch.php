<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Branch extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($branch) {
            $baseSlug = Str::slug($branch->name);
            $slug = $baseSlug;
            $count = 1;

            // Agar slug takrorlangan bo‘lsa, yangisini yaratamiz
            while (static::where('slug', $slug)->where('id', '!=', $branch->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            
            $url = "https://ihma.webclub.uz/feedback/{$slug}";
            $qrCode = QrCode::format('png')->size(500)->generate($url);

            $filePath = auth()->user()->name.'/' . $slug . '.png';

            Storage::disk('public')->put($filePath, $qrCode);
            
            $branch->slug = $slug;
            $branch->qr_code_path = $filePath;
        });
    }
}
