<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guru = Guru::all()->map(function($item) {
            return [
                'id' => $item->id,
                'nip' => $item->nip,
                'nama' => $item->nama,
                'email' => $item->email,
                'links' => [
                    'self' => url('/api/guru/' . $item->id)
                ]
            ];
        });
        return $this->success([
            'guru' => $guru,
            'links' => [
                'self' => url('/api/guru')
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'nip' => 'nullable|unique:guru,nip',
            'nama' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'required|in:laki-laki,perempuan',
            'phone_number' => 'nullable|string|max:15',
            'email' => 'required|email|unique:guru,email',
            'alamat' => 'nullable|string',
            'pendidikan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $guru = new Guru();
        $guru->user_id = $request->user_id;
        $guru->nip = $request->nip;
        $guru->nama = $request->nama;
        $guru->tempat_lahir = $request->tempat_lahir;
        $guru->tgl_lahir = $request->tgl_lahir;
        $guru->gender = $request->gender;
        $guru->phone_number = $request->phone_number;
        $guru->email = $request->email;
        $guru->alamat = $request->alamat;
        $guru->pendidikan = $request->pendidikan;

        $saveGuru = $guru->save();
        if ($saveGuru) {
            $data = [
                'id' => $guru->id,
                'nama' => $guru->nama,
                'links' => [
                    'self' => url('/api/guru/' . $guru->id),
                    'collection' => url('/api/guru')
                ]
            ];
            return $this->success($data,201,'Guru berhasil ditambahkan');  
        } else {
            return $this->failedResponse('Guru gagal ditambahkan',500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Guru $guru)
    {
        $guru->load('user', 'jadwals');

        $data = [
            'id' => $guru->id,
            'nip' => $guru->nip,
            'nama' => $guru->nama,
            'tempat_lahir' => $guru->tempat_lahir,
            'tgl_lahir' => $guru->tgl_lahir,
            'gender' => $guru->gender,
            'phone_number' => $guru->phone_number,
            'email' => $guru->email,
            'alamat' => $guru->alamat,
            'pendidikan' => $guru->pendidikan,
            'user' => [
                'id' => $guru->user->id,
                'name' => $guru->user->name,
                'email' => $guru->user->email,
                'links' => [
                    'self' => url('/api/users/' . $guru->user->id),
                ]
            ],
            'jadwals' => $guru->jadwals->map(function($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'hari' => $jadwal->hari,
                    'jam_pelajaran' => $jadwal->jam_pelajaran,
                    'links' => [
                        'self' => url('/api/jadwal/' . $jadwal->id)
                    ]
                ];
            }),
            'links' => [
                'self' => url('/api/guru/' . $guru->id),
                'collection' => url('/api/guru'),
                'user' => url('/api/users/' . $guru->user_id),
                'jadwals' => url('/api/guru/' . $guru->id . '/jadwal')
            ]
        ];
        return $this->success($data,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'nullable|unique:guru,nip,'.$guru->id,
            'nama' => 'required|string',
            'tempat_lahir' => 'nullable|string',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'required|in:laki-laki,perempuan',
            'phone_number' => 'nullable|string|max:15',
            'email' => 'required|email|unique:guru,email,'.$guru->id,
            'alamat' => 'nullable|string',
            'pendidikan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg,422);
        }

        $guru->nip = $request->nip;
        $guru->nama = $request->nama;
        $guru->tempat_lahir = $request->tempat_lahir;
        $guru->tgl_lahir = $request->tgl_lahir;
        $guru->gender = $request->gender;
        $guru->phone_number = $request->phone_number;
        $guru->email = $request->email;
        $guru->alamat = $request->alamat;
        $guru->pendidikan = $request->pendidikan;

        $saved = $guru->save();
        if ($saved) {
            return $this->success([
                'id' => $guru->id,
                'nama' => $guru->nama,
                'links' => [
                    'self' => url('/api/guru/' . $guru->id),
                    'collection' => url('/api/guru')
                ]
            ], 200, 'Guru berhasil diupdate');
        } else {
            return $this->failedResponse('Guru gagal diupdate',500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        $deleteData = $guru->delete();

        if ($deleteData) {
            return $this->success([
                'links' => [
                    'collection' => url('/api/guru')
                ]
            ], 200, 'Guru berhasil dihapus');
        } else {
            return $this->failedResponse('Guru gagal dihapus!',500);
        }
    }

    public function jadwal(Guru $guru)
    {
        $guru->load('jadwals.kelas', 'jadwals.mapel');

        $jadwals = $guru->jadwals->map(function($jadwal) {
            return [
                'id' => $jadwal->id,
                'hari' => $jadwal->hari,
                'jam_pelajaran' => $jadwal->jam_pelajaran,
                'kelas' => [
                    'id' => $jadwal->kelas->id,
                    'nama_kelas' => $jadwal->kelas->nama_kelas,
                    'links' => [
                        'self' => url('/api/kelas/' . $jadwal->kelas->id)
                    ]
                ],
                'mapel' => [
                    'id' => $jadwal->mapel->id,
                    'nama_mapel' => $jadwal->mapel->nama_mapel,
                    'links' => [
                        'self' => url('/api/mapel/' . $jadwal->mapel->id)
                    ]
                ],
                'links' => [
                    'self' => url('/api/jadwal/' . $jadwal->id)
                ]
            ];
        });
        return $this->success([
            'guru' => [
                'id' => $guru->id,
                'nama' => $guru->nama,
                'links' => [
                    'self' => url('/api/guru/' . $guru->id),
                ]
            ],
            'jadwals' => $jadwals,
            'links' => [
                'self' => url('/api/guru/' . $guru->id . '/jadwal'),
                'guru' => url('/api/guru/' . $guru->id),
                'collection' => url('/api/guru/jadwal'),
            ]
        ], 200);
    }
}