<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Customer extends BaseModel
{
    use HasFactory;

    protected $table = 'mscustomer';

    public $timestamps = false;

    public function getAllData($params)
    {
        $result = DB::select(
            "SELECT
                customer_id,
                customer_name,
                isnull(customer_contact_person, '') as customer_contact_person,
                isnull(customer_city, '') as customer_city,
                isnull(customer_phone, '') as customer_phone,
                isnull(customer_email, '') as customer_email,
                isnull(customer_npwp, '') as customer_npwp,
                isnull(customer_account_manager, '') as customer_account_manager,
                isnull(customer_limit_piutang, 0) as customer_limit_piutang,
                isnull(customer_address, '') as customer_address,
                isnull(customer_address_npwp, '') as customer_address_npwp,
                isnull(customer_note, '') as customer_note,
                isnull(customer_term, 0) as customer_term,
                upddate,
                upduser
            from mscustomer
            where customer_name like :search_keyword
            order by customer_name",
            [
                'search_keyword' => '%' . $params['search_keyword'] . '%'
            ]
        );

        return $result;
    }

    public function getDataById($id)
    {
        $result = DB::selectOne(
            "SELECT
                customer_id,
                customer_name,
                isnull(customer_contact_person, '') as customer_contact_person,
                isnull(customer_city, '') as customer_city,
                isnull(customer_phone, '') as customer_phone,
                isnull(customer_email, '') as customer_email,
                isnull(customer_npwp, '') as customer_npwp,
                isnull(customer_account_manager, '') as customer_account_manager,
                isnull(customer_limit_piutang, 0) as customer_limit_piutang,
                isnull(customer_address, '') as customer_address,
                isnull(customer_address_npwp, '') as customer_address_npwp,
                isnull(customer_note, '') as customer_note,
                isnull(customer_term, 0) as customer_term,
                upddate,
                upduser
            from mscustomer
            where customer_id = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function cekData($id)
    {
        $result = DB::selectOne(
            'SELECT * from mscustomer WHERE customer_id = :id',
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function insertData($params)
    {
        $result = DB::insert(
            "INSERT INTO mscustomer
            (customer_id, customer_name, customer_contact_person, customer_city, customer_phone, customer_email, customer_npwp, customer_account_manager, customer_limit_piutang, customer_address, customer_address_npwp, customer_note, customer_term, upddate, upduser)
            VALUES
            (:customer_id, :customer_name, :customer_contact_person, :customer_city, :customer_phone, :customer_email, :customer_npwp, :customer_account_manager, :customer_limit_piutang, :customer_address, :customer_address_npwp, :customer_note, :customer_term, getdate(), :upduser)",
            [
                'customer_id' => $params['customer_id'],
                'customer_name' => $params['customer_name'],
                'customer_contact_person' => $params['customer_contact_person'],
                'customer_city' => $params['customer_city'],
                'customer_phone' => $params['customer_phone'],
                'customer_email' => $params['customer_email'],
                'customer_npwp' => $params['customer_npwp'],
                'customer_account_manager' => $params['customer_account_manager'],
                'customer_limit_piutang' => $params['customer_limit_piutang'],
                'customer_address' => $params['customer_address'],
                'customer_address_npwp' => $params['customer_address_npwp'],
                'customer_note' => $params['customer_note'],
                'customer_term' => $params['customer_term'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function updateData($params)
    {
        $result = DB::update(
            "UPDATE mscustomer SET
                customer_name = :customer_name,
                customer_contact_person = :customer_contact_person,
                customer_city = :customer_city,
                customer_phone = :customer_phone,
                customer_email = :customer_email,
                customer_npwp = :customer_npwp,
                customer_account_manager = :customer_account_manager,
                customer_limit_piutang = :customer_limit_piutang,
                customer_address = :customer_address,
                customer_address_npwp = :customer_address_npwp,
                customer_note = :customer_note,
                customer_term = :customer_term,
                upddate = getdate(),
                upduser = :upduser
            WHERE customer_id = :customer_id",
            [
                'customer_id' => $params['customer_id'],
                'customer_name' => $params['customer_name'],
                'customer_contact_person' => $params['customer_contact_person'],
                'customer_city' => $params['customer_city'],
                'customer_phone' => $params['customer_phone'],
                'customer_email' => $params['customer_email'],
                'customer_npwp' => $params['customer_npwp'],
                'customer_account_manager' => $params['customer_account_manager'],
                'customer_limit_piutang' => $params['customer_limit_piutang'],
                'customer_address' => $params['customer_address'],
                'customer_address_npwp' => $params['customer_address_npwp'],
                'customer_note' => $params['customer_note'],
                'customer_term' => $params['customer_term'],
                'upduser' => $params['upduser']
            ]
        );

        return $result;
    }

    public function deleteData($id)
    {
        $result = DB::delete(
            "DELETE FROM mscustomer WHERE customer_id = :id",
            [
                'id' => $id
            ]
        );

        return $result;
    }

    public function beforeAutoNumber()
    {
        $autoNumber = $this->autoNumber($this->table, 'customer_id', 'CUS', '0000');

        return $autoNumber;
    }
}
