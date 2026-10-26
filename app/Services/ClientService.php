<?php

namespace App\Services;

use App\Interfaces\ClientRepositoryInterface;

class ClientService
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAllClients()
    {
        return $this->clientRepository->getAll();
    }

    public function getClientById($id)
    {
        return $this->clientRepository->getById($id);
    }

    public function createClient(array $data)
    {
        if (empty($data['company_name'])) {
            $data['company_name'] = !empty($data['name']) ? $data['name'] : (!empty($data['contact_person']) ? $data['contact_person'] : 'Individual');
        }
        if (empty($data['name'])) {
            $data['name'] = !empty($data['contact_person']) ? $data['contact_person'] : $data['company_name'];
        }
        if (isset($data['reference_type']) && $data['reference_type'] === 'Direct') {
            $data['vendor_name'] = null;
        }
        return $this->clientRepository->create($data);
    }

    public function updateClient($id, array $data)
    {
        if (isset($data['company_name']) && empty($data['company_name'])) {
            $data['company_name'] = !empty($data['name']) ? $data['name'] : (!empty($data['contact_person']) ? $data['contact_person'] : 'Individual');
        }
        if (isset($data['reference_type']) && $data['reference_type'] === 'Direct') {
            $data['vendor_name'] = null;
        }
        return $this->clientRepository->update($id, $data);
    }

    public function deleteClient($id)
    {
        return $this->clientRepository->delete($id);
    }
}
