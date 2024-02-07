<?php

namespace App\Http\Controllers;

use App\Models\Mata_kuliah;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use DataTables;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     */
    public function lists(Request  $request)
    {
        $mahasiswa = DB::select("SELECT mahasiswas.id, mahasiswas.nama, jenis_kelamin, alamat, COUNT(mata_kuliahs.mahasiswa_id) FROM mahasiswas
        LEFT JOIN mata_kuliahs ON mata_kuliahs.mahasiswa_id = mahasiswas.id
        WHERE LOWER(mahasiswas.nama) LIKE '%".strtolower($request->query('search'))."%'
        OR LOWER(jenis_kelamin) LIKE '%".strtolower($request->query('search'))."%'
        OR LOWER(alamat) LIKE '%".strtolower($request->query('search'))."%'
        GROUP BY mahasiswas.id, mahasiswas.nama, jenis_kelamin, alamat");
        return Response::json($mahasiswa);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $post = $request->all();

        $request->validate([
            'nama' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'sks' => 'required'
        ]);
        $mahasiswa = new Mahasiswa;
        $mahasiswa->nama = $post['nama'];
        $mahasiswa->jenis_kelamin = $post['jenis_kelamin'];
        $mahasiswa->alamat = $post['alamat'];
        $mahasiswa->sks = $post['sks'];
        $mahasiswa->save();

        foreach($post['mata_kuliah'] as $obj) {
            Mata_kuliah::create([
               "mahasiswa_id" => $mahasiswa->id,
               "nama" => $obj['nama'] 
            ]);
        }
        return Response::json('Mahasiswa created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        $mahasiswa = Mahasiswa::find($id);
        $mata_kuliah = DB::table('mata_kuliahs')->where('mahasiswa_id', $id)->get();

        return Response::json(['mahasiswa' => $mahasiswa, 'mata_kuliah' => $mata_kuliah]);
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $mahasiswa = Mahasiswa::all();

            return Datatables::of($mahasiswa)
            ->make(true);
        }

        return view('pages.mahasiswa.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $post = $request->all();

        $request->validate([
            'nama' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'sks' => 'required'
        ]);
        $mahasiswa = DB::table('mahasiswas');
        $mahasiswa->where('id', $id);
        $mahasiswa->update([
            'nama' => $post['nama'],
            'jenis_kelamin' => $post['jenis_kelamin'],
            'alamat' => $post['alamat'],
            'sks' => $post['sks']
        ]);

        DB::table('mata_kuliahs')->where('mahasiswa_id', $id)->delete();
        foreach($post['mata_kuliah'] as $obj) {
            Mata_kuliah::create([
               "mahasiswa_id" => $id,
               "nama" => $obj['nama'] 
            ]);
        }
        return Response::json('Mahasiswa updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $mata_kuliahs = DB::table('mata_kuliahs')->where('mahasiswa_id', $id);
        if ($mata_kuliahs) $mata_kuliahs->delete();
        $mahasiswa = Mahasiswa::find($id);
        $mahasiswa->delete();
        return Response::json('Mahasiswa deleted successfully');
    }
}
