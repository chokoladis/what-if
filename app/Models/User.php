<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'photo_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function photo()
    {
        return $this->hasOne(File::class, 'id', 'photo_id');
    }

    public static function boot()
    {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::updated(function ($user) {

            if ($user->getOriginal('photo_id') !== $user->photo_id) {
                File::find($user->getOriginal('photo_id'))->delete();
            }

            // $item->path_thumbnail = FileService::createThumbWebp($item->path);

        });

    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function getShortName()
    {
        return mb_strlen($this->name) > 8 ? mb_substr($this->name,0, 8).'...' : $this->name;
    }

    //    paginate
    public function questions() : HasMany
    {
        return $this->hasMany(Question::class); //->latest()
    }

    public function getQuestionsWithPages(Request $request)
    {
        $data = $request->validate([
            'perPage' => 'integer|min:1|max:30',
        ]);

        return $this->hasMany(Question::class)->latest()->paginate($data['perPage'] ?? 5);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'user_tags');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function lastNoViewedNotifications()
    {
        return Cache::remember('notify_'.auth()->id(), 86400, function () {
            return $this->notifications()->where('viewed', false)->limit(5)->latest()->get();
        });
    }

    public function getPhotoIdAttribute()
    {
        return $this->photo_id ?? null;
    }
}
