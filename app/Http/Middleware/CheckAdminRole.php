<?php

namespace App\Http\Middleware;

use App\Models\Soba;
use App\Models\ClanSobe;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sobaId = $request->route('id');
        
        if (!$sobaId) {
            return response()->json([
                'success' => false,
                'message' => 'ID sobe nije pronađen'
            ], 400);
        }

        $soba = Soba::find($sobaId);
        
        if (!$soba) {
            return response()->json([
                'success' => false,
                'message' => 'Soba nije pronađena'
            ], 404);
        }

        // Provera da li je korisnik admin sobe
        $jeAdmin = ClanSobe::where('korisnik_id', auth()->id())
                           ->where('soba_id', $sobaId)
                           ->where('uloga', 'admin')
                           ->exists();

        if (!$jeAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate admin dozvolu za ovu sobu'
            ], 403);
        }

        return $next($request);
    }
}
