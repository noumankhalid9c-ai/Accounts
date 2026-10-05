<?php

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;

class ClientRepository implements ClientRepositoryInterface
{
    public function getAll()
    {
        return Client::orderBy('id', 'desc')->paginate(15);
    }

    public function getById($id)
    {
        return Client::findOrFail($id);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function update($id, array $data)
    {
        $client = $this->getById($id);
        $client->update($data);
        return $client;
    }

    public function delete($id)
    {
        $client = $this->getById($id);
        return $client->delete();
    }
}
