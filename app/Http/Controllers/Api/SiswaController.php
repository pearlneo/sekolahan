<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswa = Siswa::all()->map(function($item) {
            return [
                'id' => $item->id,
                'nis' => $item->nis,
                'nama' => $item->nama,
                'email' => $item->email,
                'links' => [
                    'self' => url('/api/siswa/' . $item->id)
                ]
            ];
        });
        return $this->success([
            'siswa' => $siswa,
            'links' => [
                'self' => url('/api/siswa')
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'nullable|unique:siswa,nis',
            'nama' => 'required|string',
            'gender' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'nullable|string',
            'tgl_lahir' => 'nullable|date',
            'email' => 'required|email|unique:siswa,email',
            'nama_ortu' => 'nullable|string',
            'alamat' => 'nullable|string',
            'phone_number' => 'nullable|string|max:15',
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $siswa = new Siswa();
        $siswa->nis = $request->nis;
        $siswa->nama = $request->nama;
        $siswa->gender = $request->gender;
        $siswa->tempat_lahir = $request->tempat_lahir;
        $siswa->tgl_lahir = $request->tgl_lahir;
        $siswa->email = $request->email;
        $siswa->nama_ortu = $request->nama_ortu;
        $siswa->alamat = $request->alamat;
        $siswa->phone_number = $request->phone_number;
        $siswa->kelas_id = $request->kelas_id;

        $saveSiswa = $siswa->save();
        if ($saveSiswa) {
            $data = [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'links' => [
                    'self' => url('/api/siswa/' . $siswa->id),
                    'collection' => url('/api/siswa')
                ]
            ];
            return $this->success($data,201,'Siswa berhasil ditambahkan');
        } else {
            return $this->failedResponse('Siswa gagal ditambahkan',500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        $siswa->load('kelas'); 

        $data = [
            'id' => $siswa->id,
            'nis' => $siswa->nis,
            'nama' => $siswa->nama,
            'gender' => $siswa->gender,
            'tempat_lahir' => $siswa->tempat_lahir,
            'tgl_lahir' => $siswa->tgl_lahir,
            'email' => $siswa->email,
            'nama_ortu' => $siswa->nama_ortu,
            'alamat' => $siswa->alamat,
            'phone_number' => $siswa->phone_number,
            'kelas' => $siswa->kelas ? [
                'id' => $siswa->kelas->id,
                'nama_kelas' => $siswa->kelas->nama_kelas,
                'links' => [
                    'self' => url('/api/kelas/' . $siswa->kelas->id)
                ]
            ] : null,
            'links' => [
                'self' => url('/api/siswa/' . $siswa->id),
                'collection' => url('/api/siswa'),  
                'kelas' => $siswa->kelas_id ? url('/api/kelas/' . $siswa->kelas->id) : null,
                'kelas_siswa' => $siswa->kelas_id ? url('/api/kelas/' . $siswa->kelas->id . '/siswa') : null
            ]
        ];
        return $this->success($data,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'nullable|unique:siswa,nis,' .$siswa->id,
            'nama' => 'required|string',
            'gender' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'nullable|string',
            'tgl_lahir' => 'nullable|date',
            'email' => 'required|email|unique:siswa,email,' .$siswa->id,
            'nama_ortu' => 'nullable|string',
            'alamat' => 'nullable|string',
            'phone_number' => 'nullable|string|max:15',
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $siswa->nis = $request->nis;
        $siswa->nama = $request->nama;
        $siswa->gender = $request->gender;
        $siswa->tempat_lahir = $request->tempat_lahir;
        $siswa->tgl_lahir = $request->tgl_lahir;
        $siswa->email = $request->email;
        $siswa->nama_ortu = $request->nama_ortu;
        $siswa->alamat = $request->alamat;
        $siswa->phone_number = $request->phone_number;
        $siswa->kelas_id = $request->kelas_id;

        $saved = $siswa->save();
        if ($saved) {
            return $this->success([
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'links' => [
                    'self' => url('/api/siswa/' . $siswa->id),
                    'collection' => url('/api/siswa')
                ]
            ], 200, 'Siswa berhasil diupdate');
        } else {
            return $this->failedResponse('Siswa gagal diupdate',500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        $deleteData = $siswa->delete();

        if ($deleteData) {
            return $this->success([
                'links' => [
                    'collection' => url('/api/siswa')
                ]
            ], 200, 'Siswa berhasil dihapus');
        } else {
            return $this->failedResponse('Siswa gagal dihapus!',500);
        }
    }
}
