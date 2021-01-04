<?php


namespace App\Traits;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait ModelLog {

    public static $_CREATED = 1;
    public static $_UPDATED = 2;
    public static $_DELETED = 3;

    /**
     * Gets the action type label
     * @param int $action
     * @return string|null
     */
    public static function getActionTypeLabel(int $action) {
        $actions = Array(
            static::$_CREATED => 'CREATED',
            static::$_UPDATED => 'UPDATED',
            static::$_DELETED => 'DELETED'
        );

        return $actions[$action] ?? null;
    }

    /**
     * Logs the model action
     */
    public static function bootModelLog() {
        static::saved(function(Model $model) {
            if ($model->wasRecentlyCreated) {
                static::storeLog($model, static::class, self::$_CREATED);
            } else {
                if (!$model->getChanges()) {
                    return;
                }
                static::storeLog($model, static::class, (self::$_UPDATED));
            }
        });

        static::deleted(function(Model $model) {
            static::storeLog($model, static::class, self::$_DELETED);
        });
    }

    /**
     * Generates model tag name
     * @param Model $model
     * @return mixed|string
     */
    public static function getTagName(Model $model) {
        return !empty($model->tagName) ? $model->tagName : Str::title(Str::snake(class_basename($model), ' '));
    }

    /**
     * Gets the active user id
     *
     * @return int|string|null
     */
    public static function getActiveUserId() {
            return Auth::guard(static::getActiveUserGuard())->id();
    }

    /**
     * Gets the active user guard type
     *
     * @return null
     */
    public static function getActiveUserGuard() {
        foreach (array_keys(config('auth.guards')) as $quard) {
            if (auth()->guard($quard)->check()) {
                return $quard;
            }
        }

        return null;
    }

    /**
     * Store model actions to system logs table
     *
     * @param Model $model
     * @param string $modelPath
     * @param int $action
     */
    public static function storeLog(Model $model, string $modelPath, int $action) {
        $newValues = null;
        $oldValues = null;

        if ($action == static::$_CREATED) {
            $newValues = json_encode($model->getAttributes())    ?? null;
        } elseif ($action == static::$_UPDATED) {
            $newValues = json_encode($model->getChanges()) ?? null;
            $oldValues = json_encode($model->getOriginal()) ?? null;
        }

        $systemLog = new SystemLog();
        $systemLog->system_logable_id = $model->id;
        $systemLog->system_logable_type = $modelPath;
        $systemLog->user_id = static::getActiveUserId();
        $systemLog->guard_name = static::getActiveUserGuard();
        $systemLog->module_name = static::getTagName($model);
        $systemLog->action = static::getActionTypeLabel($action);
        $systemLog->old_value = $oldValues;
        $systemLog->new_value = $newValues;
        $systemLog->ip_address = request()->ip();
        $systemLog->save();
    }
}