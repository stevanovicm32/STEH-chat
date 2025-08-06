<?php

namespace App\Http\Middleware;

use App\Models\Soba;
use App\Models\ClanSobe;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSobaAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sobaId = $request->route('id') ?? $request->route('sobaId');
        
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

        // Ako je soba javna, dozvoli pristup
        if ($soba->je_javna) {
            return $next($request);
        }

        // Provera da li je korisnik član privatne sobe
        $jeClan = ClanSobe::where('korisnik_id', auth()->id())
                          ->where('soba_id', $sobaId)
                          ->exists();

        if (!$jeClan) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate pristup ovoj sobi'
            ], 403);
        }

        return $next($request);
    }
}
