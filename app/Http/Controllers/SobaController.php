<?php

namespace App\Http\Controllers;

use App\Models\Soba;
use App\Models\ClanSobe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SobaController extends Controller
{
    /**
     * Prikaz svih javnih soba
     */
    public function index(): JsonResponse
    {
        $sobe = Soba::where('je_javna', true)
                    ->withCount('clanovi')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $sobe
        ]);
    }

    /**
     * Kreiranje nove sobe
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'naziv' => 'required|string|max:100|unique:sobas',
            'opis' => 'nullable|string',
            'je_javna' => 'boolean',
            'maksimalan_broj_clanova' => 'integer|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška u validaciji',
                'errors' => $validator->errors()
            ], 422);
        }

        $soba = Soba::create($request->all());

        // Automatski dodavanje kreatora kao admin
        ClanSobe::create([
            'korisnik_id' => $request->user()->id,
            'soba_id' => $soba->id,
            'uloga' => 'admin'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soba uspešno kreirana',
            'data' => $soba
        ], 201);
    }

    /**
     * Prikaz određene sobe sa porukama
     */
    public function show(string $id): JsonResponse
    {
        $soba = Soba::with(['poruke.korisnik', 'clanovi.korisnik'])
                    ->findOrFail($id);

        // Provera da li je korisnik član sobe
        $jeClan = $soba->clanovi()
                       ->where('korisnik_id', auth()->id())
                       ->exists();

        if (!$soba->je_javna && !$jeClan) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate pristup ovoj sobi'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $soba
        ]);
    }

    /**
     * Ažuriranje sobe
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $soba = Soba::findOrFail($id);

        // Provera da li je korisnik admin sobe
        $jeAdmin = $soba->clanovi()
                        ->where('korisnik_id', auth()->id())
                        ->where('uloga', 'admin')
                        ->exists();

        if (!$jeAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate dozvolu za izmenu sobe'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'naziv' => 'string|max:100|unique:sobas,naziv,' . $id,
            'opis' => 'nullable|string',
            'je_javna' => 'boolean',
            'maksimalan_broj_clanova' => 'integer|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška u validaciji',
                'errors' => $validator->errors()
            ], 422);
        }

        $soba->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Soba uspešno ažurirana',
            'data' => $soba
        ]);
    }

    /**
     * Brisanje sobe
     */
    public function destroy(string $id): JsonResponse
    {
        $soba = Soba::findOrFail($id);

        // Provera da li je korisnik admin sobe
        $jeAdmin = $soba->clanovi()
                        ->where('korisnik_id', auth()->id())
                        ->where('uloga', 'admin')
                        ->exists();

        if (!$jeAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate dozvolu za brisanje sobe'
            ], 403);
        }

        $soba->delete();

        return response()->json([
            'success' => true,
            'message' => 'Soba uspešno obrisana'
        ]);
    }

    /**
     * Pridruživanje sobi
     */
    public function pridruziSe(Request $request, string $id): JsonResponse
    {
        $soba = Soba::findOrFail($id);

        // Provera da li je korisnik već član
        $jeClan = $soba->clanovi()
                       ->where('korisnik_id', auth()->id())
                       ->exists();

        if ($jeClan) {
            return response()->json([
                'success' => false,
                'message' => 'Već ste član ove sobe'
            ], 400);
        }

        // Provera da li je soba puna
        $brojClanova = $soba->clanovi()->count();
        if ($brojClanova >= $soba->maksimalan_broj_clanova) {
            return response()->json([
                'success' => false,
                'message' => 'Soba je puna'
            ], 400);
        }

        ClanSobe::create([
            'korisnik_id' => auth()->id(),
            'soba_id' => $soba->id,
            'uloga' => 'clan'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Uspešno ste se pridružili sobi'
        ]);
    }

    /**
     * Napuštanje sobe
     */
    public function napusti(Request $request, string $id): JsonResponse
    {
        $clanSobe = ClanSobe::where('korisnik_id', auth()->id())
                            ->where('soba_id', $id)
                            ->first();

        if (!$clanSobe) {
            return response()->json([
                'success' => false,
                'message' => 'Niste član ove sobe'
            ], 400);
        }

        $clanSobe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Uspešno ste napustili sobu'
        ]);
    }
}
