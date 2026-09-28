<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function postReactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function postComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function postCommentReactions(): HasMany
    {
        return $this->hasMany(PostCommentReaction::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // ===== Friend Request Relationships =====

    /**
     * Friend requests sent by this user.
     */
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    /**
     * Friend requests received by this user.
     */
    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    /**
     * Get all accepted friends (users who have accepted this user's request).
     */
    public function friendsOfMine(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id')->where('status', 'accepted');
    }

    /**
     * Get all accepted friends (users whose requests this user has accepted).
     */
    public function friendsOf(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id')->where('status', 'accepted');
    }

    /**
     * Get all friends of this user.
     */
    public function friends()
    {
        return $this->friendsOfMine()->with('receiver')
            ->get()
            ->map(function ($request) {
                return $request->receiver;
            })
            ->concat(
                $this->friendsOf()->with('sender')
                    ->get()
                    ->map(function ($request) {
                        return $request->sender;
                    })
            );
    }
}
