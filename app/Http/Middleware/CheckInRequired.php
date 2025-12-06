<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CaLamViec;

class CheckInRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has an active shift with check-in completed
        $shift = CaLamViec::where('nguoi_dung_id', auth()->id())
            ->where('trang_thai', 'dang_lam')
            ->first();

        if (!$shift || !$shift->trang_thai_checkin) {
            return redirect()
                ->route('admin.shift.check-in')
                ->with('error', 'Vui lòng check-in trước khi sử dụng POS!');
        }

        return $next($request);
    }
}
