<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class ModelAuditObserver
{
    public function created(Model $model): void
    {
        $this->write($model, 'created', null, $this->cleanValues($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $changes = $this->cleanValues($model->getChanges());
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = [];

        foreach (array_keys($changes) as $key) {
            $original[$key] = $model->getOriginal($key);
        }

        $this->write($model, 'updated', $this->cleanValues($original), $changes);
    }

    public function deleted(Model $model): void
    {
        $this->write($model, 'deleted', $this->cleanValues($model->getOriginal()), null);
    }

    private function write(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
        ]);
    }

    private function cleanValues(array $values): array
    {
        $clean = Arr::except($values, ['updated_at']);

        foreach ($clean as $key => $value) {
            if ($value instanceof \DateTimeInterface) {
                $clean[$key] = $value->format(DATE_ATOM);
            }
        }

        return $clean;
    }
}