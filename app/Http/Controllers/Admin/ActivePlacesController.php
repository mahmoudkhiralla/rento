<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivePlacesController extends Controller
{
    /**
     * Stop publishing an active place (admin action)
     */
    public function stop(Request $request, ActivePlace $place)
    {
        // Toggle publish off (keep the record)
        $place->is_published = false;
        $place->save();

        return back()->with('status', 'تم إيقاف نشر المكان بنجاح');
    }

    /**
     * Re-publish an active place (admin action)
     */
    public function publish(Request $request, ActivePlace $place)
    {
        $place->is_published = true;
        $place->save();

        return back()->with('status', 'تم إعادة نشر المكان بنجاح');
    }

    /**
     * Permanently delete an active place (admin action)
     */
    public function destroy(Request $request, ActivePlace $place)
    {
        // Attempt to delete stored image if it's a local storage path
        if ($place->image && ! preg_match('/^https?:\/\//', $place->image)) {
            try {
                Storage::disk('public')->delete($place->image);
            } catch (\Throwable $e) {
                // Silently ignore image deletion errors
            }
        }

        $place->delete();

        return back()->with('status', 'تم حذف المكان بنجاح');
    }
}
