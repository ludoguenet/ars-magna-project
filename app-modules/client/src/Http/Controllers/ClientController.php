<?php

declare(strict_types=1);

namespace AppModules\Client\src\Http\Controllers;

use AppModules\Client\src\Contracts\ClientRepositoryContract;
use AppModules\Client\src\DataTransferObjects\ClientDTO;
use AppModules\Client\src\Http\Requests\StoreClientRequest;
use AppModules\Client\src\Http\Requests\UpdateClientRequest;
use AppModules\Client\src\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController
{
    public function __construct(
        private ClientService $clientService,
        private ClientRepositoryContract $repository
    ) {}

    /**
     * Display a listing of clients.
     */
    public function index(Request $request): View
    {
        $clients = $this->repository->allModels();

        return view('client::index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        return view('client::create');
    }

    /**
     * Store a newly created client.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $clientData = ClientDTO::fromArray($request->validated());
        $client = $this->clientService->create($clientData);

        return redirect()
            ->route('client::show', $client)
            ->with('success', 'Client created successfully');
    }

    /**
     * Display the specified client.
     */
    public function show(int $id): View
    {
        $client = $this->repository->findModel($id);

        if (! $client) {
            abort(404);
        }

        return view('client::show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(int $id): View
    {
        // Internal use - can use models directly
        $client = $this->repository->findModel($id);

        if (! $client) {
            abort(404);
        }

        return view('client::edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(UpdateClientRequest $request, int $id): RedirectResponse
    {
        // Internal use - can use models directly
        $client = $this->repository->findModel($id);

        if (! $client) {
            abort(404);
        }

        $clientData = ClientDTO::fromArray($request->validated());
        $this->clientService->update($client, $clientData);

        return redirect()
            ->route('client::show', $client)
            ->with('success', 'Client updated successfully');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(int $id): RedirectResponse
    {
        // Internal use - can use models directly
        $client = $this->repository->findModel($id);

        if ($client) {
            $this->clientService->delete($client);
        }

        return redirect()
            ->route('client::index')
            ->with('success', 'Client deleted successfully');
    }
}
