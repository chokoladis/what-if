<?php

namespace App\Models;

use App\Services\FileService;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
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
        'photo_id',
        'active'
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

    public static function boot()
    {
        parent::boot();

        static::updated(function ($user) {

            if ($user->getOriginal('photo_id') !== $user->photo_id) {
                File::find($user->getOriginal('photo_id'))?->delete();
            }

            // $item->path_thumbnail = FileService::createThumbWebp($item->path);

        });

    }

    public static function getNameById(int $id): ?User
    {
        return Cache::remember('user_get_name_by_id_' . $id, 86400, function () use ($id) {
            return self::query()->find($id, 'name');
        });
    }

    public function photo(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'photo_id');
    }

    public function getAvatarPath(): string
    {
//        todo drop by update
        return Cache::remember('avatar_' . $this->id, 86400, function () {
            return FileService::getPhoto($this->photo);
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function getShortName(): string
    {
        return mb_strlen($this->name) > 8 ? mb_substr($this->name, 0, 8) . '...' : $this->name;
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class); //->latest()
    }

    public function getQuestionsWithPages(Request $request): LengthAwarePaginator
    {
        $data = $request->validate([
            'perPage' => 'integer|min:5|max:30',
        ]);

        return $this->hasMany(Question::class)->latest()->paginate($data['perPage'] ?? 5);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'user_tags');
    }

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
//    todo check active for what?
}
