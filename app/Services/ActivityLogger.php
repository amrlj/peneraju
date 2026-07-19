<?php
namespace App\Services;
use App\Models\ActivityLog; use Illuminate\Database\Eloquent\Model; use Illuminate\Http\Request;
class ActivityLogger { public static function record(string $action, ?Model $entity=null, ?string $description=null, ?Request $request=null): void { ActivityLog::create(['user_id'=>auth()->id(),'action'=>$action,'entity_type'=>$entity?->getMorphClass(),'entity_id'=>$entity?->getKey(),'description'=>$description,'ip_address'=>$request?->ip() ?? request()->ip(),'user_agent'=>$request?->userAgent() ?? request()->userAgent()]); } }
