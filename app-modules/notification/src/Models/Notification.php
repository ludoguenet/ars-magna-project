<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Models;

use App\Models\User;
use AppModules\Notification\database\factories\NotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return NotificationFactory::new();
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    public function unread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    #[Scope]
    public function read(Builder $query): void
    {
        $query->whereNotNull('read_at');
    }

    #[Scope]
    public function byType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function markAsRead(): void
    {
        $this->read_at = now();
        $this->save();
    }
}
