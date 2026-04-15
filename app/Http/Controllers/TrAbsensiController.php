<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TrAbsensiHd;
use App\Models\TrAbsensiDt;
use Illuminate\Support\Facades\DB;
use App\Traits\ArrayPaginator;
use App\Traits\HttpResponse;
use App\Http\Requests\TrAbsensi\InsertRequest;
use App\Http\Requests\TrAbsensi\UpdateRequest;
use App\Http\Requests\TrAbsensi\DeleteRequest;
use App\Http\Requests\TrAbsensi\GetRequest;
use App\Http\Requests\TrAbsensi\GetRequestById;
use Illuminate\Support\Facades\Auth;

class TrAbsensiController extends Controller
{
    use ArrayPaginator, HttpResponse;

    private function toBool($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private function resolveUpduser(): string
    {
        $user = Auth::user();
        $token = $user ? $user->currentAccessToken() : null;

        if ($token && isset($token['namauser']) && trim((string) $token['namauser']) !== '') {
            return (string) $token['namauser'];
        }

        foreach (['namauser', 'name', 'username', 'email'] as $field) {
            if ($user && isset($user->{$field}) && trim((string) $user->{$field}) !== '') {
                return (string) $user->{$field};
            }
        }

        return 'system';
    }

    public function getListData(GetRequest $request)
    {
        $model = new TrAbsensiHd();
        $params = [
            'search_keyword' => $request->search_keyword ?? '',
        ];
        $result = $model->getAllData($params);
        $resultPaginated = $this->arrayPaginator($request, $result);
        return $this->responsePagination($resultPaginated);
    }

    public function getDataById(GetRequestById $request)
    {
        $modelHd = new TrAbsensiHd();
        $modelDt = new TrAbsensiDt();
        $id = $request->tr_absensi_header_code;

        $header = $modelHd->getDataById($id);
        if (!$header) {
            return $this->responseError('Data header absensi tidak ditemukan', 404);
        }

        $detail = $modelDt->getAllData([
            'header_code' => $id,
        ]);

        return $this->responseData([
            'header' => $header,
            'detail' => $detail,
        ]);
    }

    public function insertData(InsertRequest $request)
    {
        $modelHd = new TrAbsensiHd();
        $modelDt = new TrAbsensiDt();
        $upduser = $this->resolveUpduser();
        $inputHeaderCode = trim((string) ($request->tr_absensi_header_code ?? ''));
        $isReplaceDetail = $this->toBool($request->input('replace_detail', false));

        DB::beginTransaction();
        try {
            $headerCode = $inputHeaderCode;

            if ($headerCode === '') {
                $headerCode = $modelHd->beforeAutoNumber('ABS/' . date('Y') . '/');

                $insertHeader = $modelHd->insertData([
                    'tr_absensi_header_code' => $headerCode,
                    'tr_absensi_header_date' => $request->tr_absensi_header_date,
                    'tr_absensi_header_name' => $request->tr_absensi_header_name,
                    'tr_absensi_header_branch' => $request->tr_absensi_header_branch,
                    'upduser' => $upduser,
                ]);

                if ($insertHeader == false) {
                    return $this->responseError('Gagal menyimpan data header absensi', 500);
                }
            } else {
                $header = $modelHd->getDataById($headerCode);
                if (!$header) {
                    return $this->responseError('Data header absensi tidak ditemukan', 404);
                }

                $updateHeader = $modelHd->updateData([
                    'tr_absensi_header_code' => $headerCode,
                    'tr_absensi_header_date' => $request->tr_absensi_header_date,
                    'tr_absensi_header_name' => $request->tr_absensi_header_name,
                    'tr_absensi_header_branch' => $request->tr_absensi_header_branch,
                    'upduser' => $upduser,
                ]);

                if ($updateHeader === false) {
                    return $this->responseError('Gagal memperbarui data header absensi', 500);
                }
            }

            if ($isReplaceDetail) {
                DB::delete(
                    "DELETE FROM trabsensidt WHERE tr_absensi_header_code = :code",
                    [
                        'code' => $headerCode,
                    ]
                );
            }

            $arrDetail = $request->input('detail', []);
            $expectedDetailCount = (int) $request->input('expected_detail_count', 0);
            $receivedDetailCount = is_array($arrDetail) ? count($arrDetail) : 0;

            if ($expectedDetailCount > 0 && $receivedDetailCount !== $expectedDetailCount) {
                DB::rollBack();
                return $this->responseError('Jumlah detail diterima tidak sesuai. Diterima ' . $receivedDetailCount . ' dari ' . $expectedDetailCount . '. Kemungkinan payload terpotong oleh max_input_vars/post_max_size. Gunakan raw JSON atau kirim per batch lebih kecil.', 422);
            }

            for ($i = 0; $i < $receivedDetailCount; $i++) {
                $detailId = $arrDetail[$i]['tr_absensi_dt_id'] ?? '';
                if ($detailId == '') {
                    $detailId = $modelDt->autoNumber('trabsensidt', 'tr_absensi_dt_id', $headerCode . '/', '0000');
                }

                $detailPayload = [
                    'tr_absensi_dt_id' => $detailId,
                    'tr_absensi_header_code' => $headerCode,
                    'tr_absensi_dt_name' => $arrDetail[$i]['tr_absensi_dt_name'],
                    'tr_absensi_dt_date' => $arrDetail[$i]['tr_absensi_dt_date'] ?? null,
                    'tr_absensi_dt_clock_in' => $arrDetail[$i]['tr_absensi_dt_clock_in'] ?? null,
                    'tr_absensi_dt_clock_out' => $arrDetail[$i]['tr_absensi_dt_clock_out'] ?? null,
                    'tr_absensi_dt_nominal' => $arrDetail[$i]['tr_absensi_dt_nominal'] ?? null,
                    'upduser' => $upduser,
                ];

                $insertDetail = $isReplaceDetail
                    ? $modelDt->insertData($detailPayload)
                    : $modelDt->upsertData($detailPayload);

                if ($insertDetail == false) {
                    DB::rollBack();
                    return $this->responseError('Gagal menyimpan data detail absensi', 500);
                }
            }

            DB::commit();
            return $this->responseSuccess('Data absensi berhasil disimpan', 200, [
                'tr_absensi_header_code' => $headerCode,
                'received_detail_count' => $receivedDetailCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function updateData(UpdateRequest $request)
    {
        $modelHd = new TrAbsensiHd();
        $modelDt = new TrAbsensiDt();
        $id = $request->tr_absensi_header_code;
        $upduser = $this->resolveUpduser();
        $isReplaceDetail = $this->toBool($request->input('replace_detail', false));

        $header = $modelHd->getDataById($id);
        if (!$header) {
            return $this->responseError('Data header absensi tidak ditemukan', 404);
        }

        DB::beginTransaction();
        try {
            $updateHeader = $modelHd->updateData([
                'tr_absensi_header_code' => $id,
                'tr_absensi_header_date' => $request->tr_absensi_header_date,
                'tr_absensi_header_name' => $request->tr_absensi_header_name,
                'tr_absensi_header_branch' => $request->tr_absensi_header_branch,
                'upduser' => $upduser,
            ]);
            if ($updateHeader == false) {
                return $this->responseError('Gagal memperbarui data header absensi', 500);
            }

            if ($isReplaceDetail) {
                DB::delete(
                    "DELETE FROM trabsensidt WHERE tr_absensi_header_code = :code",
                    [
                        'code' => $id,
                    ]
                );
            }

            $arrDetail = $request->input('detail', []);
            $expectedDetailCount = (int) $request->input('expected_detail_count', 0);
            $receivedDetailCount = is_array($arrDetail) ? count($arrDetail) : 0;

            if ($expectedDetailCount > 0 && $receivedDetailCount !== $expectedDetailCount) {
                DB::rollBack();
                return $this->responseError('Jumlah detail diterima tidak sesuai. Diterima ' . $receivedDetailCount . ' dari ' . $expectedDetailCount . '. Kemungkinan payload terpotong oleh max_input_vars/post_max_size. Gunakan raw JSON atau kirim per batch lebih kecil.', 422);
            }

            for ($i = 0; $i < $receivedDetailCount; $i++) {
                $detailId = $arrDetail[$i]['tr_absensi_dt_id'] ?? '';
                if ($detailId == '') {
                    $detailId = $modelDt->autoNumber('trabsensidt', 'tr_absensi_dt_id', $id . '/', '0000');
                }

                $detailPayload = [
                    'tr_absensi_dt_id' => $detailId,
                    'tr_absensi_header_code' => $id,
                    'tr_absensi_dt_name' => $arrDetail[$i]['tr_absensi_dt_name'],
                    'tr_absensi_dt_date' => $arrDetail[$i]['tr_absensi_dt_date'] ?? null,
                    'tr_absensi_dt_clock_in' => $arrDetail[$i]['tr_absensi_dt_clock_in'] ?? null,
                    'tr_absensi_dt_clock_out' => $arrDetail[$i]['tr_absensi_dt_clock_out'] ?? null,
                    'tr_absensi_dt_nominal' => $arrDetail[$i]['tr_absensi_dt_nominal'] ?? null,
                    'upduser' => $upduser,
                ];

                $insertDetail = $isReplaceDetail
                    ? $modelDt->insertData($detailPayload)
                    : $modelDt->upsertData($detailPayload);

                if ($insertDetail == false) {
                    DB::rollBack();
                    return $this->responseError('Gagal memperbarui data detail absensi', 500);
                }
            }

            DB::commit();
            return $this->responseSuccess('Data absensi berhasil diperbarui', 200, [
                'tr_absensi_header_code' => $id,
                'received_detail_count' => $receivedDetailCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function deleteData(DeleteRequest $request)
    {
        $model = new TrAbsensiHd();
        $id = $request->tr_absensi_header_code;

        $header = $model->getDataById($id);
        if (!$header) {
            return $this->responseError('Data header absensi tidak ditemukan', 404);
        }

        DB::beginTransaction();
        try {
            DB::delete(
                "DELETE FROM trabsensidt WHERE tr_absensi_header_code = :code",
                [
                    'code' => $id,
                ]
            );

            $deleteResult = $model->deleteData($id);
            if ($deleteResult == false) {
                return $this->responseError('Gagal menghapus data header absensi', 500);
            }

            DB::commit();
            return $this->responseSuccess('Data header absensi berhasil dihapus', 200, ['tr_absensi_header_code' => $id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
