<?php

namespace App\Http\Controllers;

use App\Models\Poruka;
use App\Models\Soba;
use App\Models\ClanSobe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PorukaController extends Controller
{
    /**
     * Prikaz poruka za određenu sobu
     */
    public function index(Request $request, string $sobaId): JsonResponse
    {
        $soba = Soba::findOrFail($sobaId);

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

        $poruke = Poruka::where('soba_id', $sobaId)
                        ->with('korisnik')
                        ->orderBy('created_at', 'desc')
                        ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $poruke
        ]);
    }

    /**
     * Slanje nove poruke
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'soba_id' => 'required|exists:sobas,id',
            'sadrzaj' => 'required|string|max:1000',
            'tip_poruke' => 'in:tekst,slika,fajl'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška u validaciji',
                'errors' => $validator->errors()
            ], 422);
        }

        $soba = Soba::findOrFail($request->soba_id);

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

        $poruka = Poruka::create([
            'sadrzaj' => $request->sadrzaj,
            'korisnik_id' => auth()->id(),
            'soba_id' => $request->soba_id,
            'tip_poruke' => $request->tip_poruke ?? 'tekst'
        ]);

        // Učitavanje korisnika za odgovor
        $poruka->load('korisnik');

        return response()->json([
            'success' => true,
            'message' => 'Poruka uspešno poslata',
            'data' => $poruka
        ], 201);
    }

    /**
     * Prikaz određene poruke
     */
    public function show(string $id): JsonResponse
    {
        $poruka = Poruka::with(['korisnik', 'soba'])->findOrFail($id);

        // Provera da li je korisnik član sobe
        $jeClan = $poruka->soba->clanovi()
                               ->where('korisnik_id', auth()->id())
                               ->exists();

        if (!$poruka->soba->je_javna && !$jeClan) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate pristup ovoj poruci'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $poruka
        ]);
    }

    /**
     * Ažuriranje poruke
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $poruka = Poruka::findOrFail($id);

        // Provera da li je korisnik autor poruke
        if ($poruka->korisnik_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Možete menjati samo svoje poruke'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'sadrzaj' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška u validaciji',
                'errors' => $validator->errors()
            ], 422);
        }

        $poruka->update([
            'sadrzaj' => $request->sadrzaj
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Poruka uspešno ažurirana',
            'data' => $poruka
        ]);
    }

    /**
     * Brisanje poruke
     */
    public function destroy(string $id): JsonResponse
    {
        $poruka = Poruka::with('soba')->findOrFail($id);

        // Provera da li je korisnik autor poruke ili admin sobe
        $jeAutor = $poruka->korisnik_id === auth()->id();
        $jeAdmin = $poruka->soba->clanovi()
                                ->where('korisnik_id', auth()->id())
                                ->where('uloga', 'admin')
                                ->exists();

        if (!$jeAutor && !$jeAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate dozvolu za brisanje ove poruke'
            ], 403);
        }

        $poruka->delete();

        return response()->json([
            'success' => true,
            'message' => 'Poruka uspešno obrisana'
        ]);
    }

    /**
     * Označavanje poruke kao pročitane
     */
    public function oznaciKaoProcitanu(string $id): JsonResponse
    {
        $poruka = Poruka::findOrFail($id);

        // Provera da li je korisnik član sobe
        $jeClan = $poruka->soba->clanovi()
                               ->where('korisnik_id', auth()->id())
                               ->exists();

        if (!$poruka->soba->je_javna && !$jeClan) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate pristup ovoj poruci'
            ], 403);
        }

        $poruka->update(['je_procitana' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Poruka označena kao pročitana'
        ]);
    }

    /**
     * Pretraga poruka u sobi
     */
    public function pretrazi(Request $request, string $sobaId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška u validaciji',
                'errors' => $validator->errors()
            ], 422);
        }

        $soba = Soba::findOrFail($sobaId);

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

        $poruke = Poruka::where('soba_id', $sobaId)
                        ->where('sadrzaj', 'like', '%' . $request->query . '%')
                        ->with('korisnik')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $poruke
        ]);
    }
}
