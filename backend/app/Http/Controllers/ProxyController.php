<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProxyStoreRequest;
use App\Http\Requests\ProxyUpdateRequest;
use App\Models\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    public function store (ProxyStoreRequest $request) {
        $data = $request->validated();

        try {
            utils::checkProxy($data['ip'], $data['port'], $data["type"], $data['username'] ?? null, $data['password'] ?? null);
            $proxy = Proxy::create($data);
            return response()->json($proxy);
        }
        catch (\Exception $e) {
            // Обработка ошибок, например, если прокси не работает
            abort(409, 'Ошибка: ' . $e->getMessage());
        }
    }

    public function destroy (Proxy $proxy) {
        $proxy->delete();
        return response()->json(["message" => "successful"]);
    }

    public function update (Proxy $proxy, ProxyUpdateRequest $request) {
        $data = $request->validated();

        try {
            utils::checkProxy($data['ip'] ?? $proxy->ip, $data['port'] ?? $proxy->port, $data["type"] ?? $proxy->type,
                $data['username'] ?? $proxy->username, $data['password'] ?? $proxy->password);
            $proxy->update($data);
            response()->json($proxy);
        }
        catch (\Exception $e) {
            // Обработка ошибок, например, если прокси не работает
            abort(409, 'Ошибка: ' . $e->getMessage());
        }

        return response()->json($proxy);
    }

    public function index () {
        return response()->json(Proxy::all());
    }
}
