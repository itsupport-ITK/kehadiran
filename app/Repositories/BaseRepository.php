<?php

namespace App\Repositories;

use App\Services\ApiService;

class BaseRepository{

    protected $client;

    /**
     * client API getter
     * @param  string  $type
     */
    public function client()
    {
        $this->client = new ApiService();

        return $this->client;
    }

    /**
     * return success data with true status, dipanggil ketika return dari api bukan json dan perlu ada pengecekan nantinya di controller
     */
    public function successData(string $message = '', mixed $data = null): array{
        
        return [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * return error data with false status, dipanggil ketika return dari api bukan json  ketika perlu ada pengecekan nantinya di controller
     */
    public function errorData(string $message = '', mixed $data = null): array{
        
        return [
            'status' => false,
            'message' => $message,
            'data' => $data
        ];
    }

}