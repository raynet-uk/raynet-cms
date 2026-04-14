<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\CommitteeNetwork;
use App\Models\User;
use Illuminate\Http\Request;

class NetworksController extends Controller
{
    public function index()
    {
        $networks = CommitteeNetwork::with('owner')->orderBy('type')->orderBy('name')->get();
        $statusCounts = $networks->groupBy('status')->map->count();
        return view('committee.networks.index', compact('networks', 'statusCounts'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('committee.networks.form', ['network' => new CommitteeNetwork(), 'users' => $users]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        CommitteeNetwork::create(array_merge($data, ['created_by' => auth()->id()]));
        return redirect()->route('committee.networks.index')->with('success', 'Network added.');
    }

    public function edit(CommitteeNetwork $network)
    {
        $users = User::orderBy('name')->get();
        return view('committee.networks.form', compact('network', 'users'));
    }

    public function update(Request $request, CommitteeNetwork $network)
    {
        $network->update($this->validated($request));
        return redirect()->route('committee.networks.index')->with('success', 'Network updated.');
    }

    public function destroy(CommitteeNetwork $network)
    {
        $network->delete();
        return redirect()->route('committee.networks.index')->with('success', 'Network removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'                 => 'required|string|max:120',
            'type'                 => 'required|in:VHF/UHF,DMR,YSF,VoIP,LoRa,APRS,HF,Other',
            'description'          => 'nullable|string|max:500',
            'status'               => 'required|in:operational,degraded,offline,unknown',
            'last_tested'          => 'nullable|date',
            'test_result'          => 'nullable|string|max:500',
            'frequency_channel'    => 'nullable|string|max:80',
            'talkgroup_network_id' => 'nullable|string|max:80',
            'notes'                => 'nullable|string|max:1000',
            'owner_id'             => 'nullable|exists:users,id',
        ]);
    }
}
