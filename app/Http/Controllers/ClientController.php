<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Services\ClientService;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        $clients = $this->clientService->getAllClients();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $vendors = \App\Models\B2bAgent::where('status', 'Active')->pluck('company_name', 'company_name')->toArray();
        return view('clients.create', compact('vendors'));
    }

    public function store(ClientRequest $request)
    {
        $this->clientService->createClient($request->validated());
        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show($id)
    {
        $client = $this->clientService->getClientById($id);
        return view('clients.show', compact('client'));
    }

    public function edit($id)
    {
        $client = $this->clientService->getClientById($id);
        $vendors = \App\Models\B2bAgent::where('status', 'Active')->pluck('company_name', 'company_name')->toArray();
        return view('clients.edit', compact('client', 'vendors'));
    }

    public function update(ClientRequest $request, $id)
    {
        $this->clientService->updateClient($id, $request->validated());
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy($id)
    {
        $this->clientService->deleteClient($id);
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
