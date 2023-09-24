<?php

final class Lic {
    public $first_name = null;
    public $last_name = null;
    public $email = null;
    public $company_name = null;
    public $product_ref = null;
    public $txn_id = 0;
    public $date_created = null;
    public $date_expiry = null;
    public $dominios = null;
    public $licencia = null;
    public $checksum = null;
    
    public function __construct(string $first_name = null, string $last_name = null, string $email = null, string $company_name = null, string $product_ref = null, int $txn_id = 0, $date_created = null, $date_expiry = null, string $dominios = null, string $licencia = null, string $checksum = null) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->company_name = $company_name;
        $this->product_ref = $product_ref;
        $this->txn_id = $txn_id;
        $this->date_created = $date_created;
        $this->date_expiry = $date_expiry;
        $this->dominios = $dominios;
        $this->licencia = $licencia;
        $this->checksum = $checksum;
    }

    public static function fromArray(array $data): Lic {
        return new Lic($data['first_name'], $data['last_name'], $data['email'], $data['company_name'], $data['product_ref'], $data['txn_id'], $data['date_created'], $data['date_expiry'], $data['dominios'], $data['licencia'], $data['checksum']);
    }

    public static function generar(Lic $l): Lic {
        $interval = date_diff(new DateTime($l->date_created), new DateTime($l->date_expiry))->format('%a');
        $timeStep = 86400*$interval;
        $codeLength = 9;
        $secret = $l->checksum."_".$l->company_name.$l->first_name." ".$l->last_name.":".$l->date_created.":".$l->email.":".$l->dominios.":".$l->date_expiry.":".$l->txn_id.":".$l->product_ref;
        $timestamp = floor(time() / $timeStep);
        $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $timestamp), $secret);
        $code = substr($hash, -$codeLength);
        $numericCode = 0;
        for ($i = 0; $i < $codeLength; $i++) {
            $numericCode = $numericCode * 10 + hexdec($code[$i]);
        }
        $l->licencia = $numericCode;
        return $l;
    }
}