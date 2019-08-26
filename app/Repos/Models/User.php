<?php

namespace App\Repos\Models;

use App\Repos\Interfaces\HasEmail;
use App\Repos\Traits\Filterable;
use App\Repos\Traits\HasMedia;
use App\Services\Mailable\User\PasswordResetEmail;
use App\Services\Media\HasMediaInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable implements MustVerifyEmail, HasMediaInterface, HasEmail
{
    use Notifiable, Filterable, HasMedia;

    const ADMIN = 1;
    const AGENT = 2;
    const CLIENT = 3;

    public static $roles = [
        self::ADMIN => 'Admin',
        self::AGENT => 'Agent',
        self::CLIENT => 'Client',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'username',
        'phone',
        'address',
    ];

    protected $guarded = [
        'api_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected $dates = [
        'suspended_at',
        'email_verified_at',
    ];

    protected $appends = [
        'role_label',
    ];
    private $shouldSendVerificationEmail = false;

    /*
     * Mutators
     */
    public function setRoleAttribute($value)
    {
        $role = $value;
        if (!is_numeric($value)) {
            $value = array_search(ucfirst($value), self::$roles);
            if (false === $value) {
                throw ValidationException::withMessages([
                    'role' => ["$role is not a valid role."],
                ]);
            }
        }
        $this->attributes['role'] = $value;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
    }

    public function setApiTokenAttribute($token)
    {
        if ($token && Hash::needsRehash($token)) {
            $token = hash('sha256', $token);
        }
        $this->attributes['api_token'] = $token;
    }

    public function setUsername($value)
    {
    }

    public function getRoleLabelAttribute()
    {
        return self::$roles[$this->role];
    }

    public function getAvatarAttribute($value)
    {
        if (!$value) {
            return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email)));
        }

        return $value;
    }

    public function getRoleLabel()
    {
        return self::$roles[$this->role];
    }

    /*
     * Function overriding
     */
    public function sendPasswordResetNotification($token)
    {
        $this->token = $token;

        return (new PasswordResetEmail($this))->sendEmail();
    }

    public function generateResetPasswordUrl()
    {
        return getNotificationRoute($this) . '/reset-password?token=' . $this->token;
    }

    /**
     * @override laravel's default sendEmailVerificationNotification
     *  and sends custom notification
     */
    public function sendVerificationEmail()
    {
        $this->shouldSendVerificationEmail = true;
    }

    public function getVerificationUrl()
    {
        $role = $this->role;
        switch ($role) {
            case self::ADMIN:
                $expireKey = 'auth.verification.admins.expire';
                break;
            default:
                $expireKey = 'auth.verification.' . config('auth.verification.default', 'users') . '.expire';
                break;
        }
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config($expireKey, 60)),
            ['id' => $this->getKey()]
        );
        $path = getNotificationRoute($this);
        $path = "$path/verify";

        return str_replace(route('verification.verify', $this->getKey()), $path, $url);
    }

    public function shouldSendVerificationEmail(): bool
    {
        return $this->shouldSendVerificationEmail;
    }

    /*
     * Scopes
     */

    public function scopeExcludeSelf($query)
    {
        return $query->where('id', '!=', auth()->user()->id);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnVerified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeSuspended(Builder $query)
    {
        return $query->whereNotNull('suspended_at');
    }

    public function scopeUnSuspended(Builder $query)
    {
        return $query->whereNull('suspended_at');
    }

    /*
     * Custom Functions
     */

    public function resetEmailVerification()
    {
        $this->email_verified_at = null;

        return $this->save();
    }

    public function suspend()
    {
        $this->suspended_at = now();

        return $this;
    }

    /**
     * @return $this
     */
    public function unsuspend()
    {
        $this->suspended_at = null;

        return $this;
    }

    public function isAdmin()
    {
        return self::ADMIN === $this->role;
    }

    public function isAgent()
    {
        return self::AGENT === $this->role;
    }

    public function isClient()
    {
        return self::CLIENT === $this->role;
    }

    /**
     * @param string $slug
     *
     * @return int
     */
    public static function getRoleBySlug(string $slug): int
    {
        return array_search(ucfirst(Str::singular($slug)), self::$roles);
    }

    /**
     * @param string $slug
     *
     * @return int
     */
    public static function getRoleBySlugOrFail(string $slug): int
    {
        $value = self::getRoleBySlug($slug);
        if (!$value) {
            throw new \UnexpectedValueException("invalid role $slug");
        }

        return $value;
    }

    public function getSizes(): array
    {
        return [
            'extra_small' => [400, 300],
        ];
    }

    public function getSingleImageCategories()
    {
        return [
            'avatar',
        ];
    }
}
