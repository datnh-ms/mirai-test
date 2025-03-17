<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
   // Get all with paginate
   public function index(Request $request) {
        $accounts = Account::paginate($request->get('per_page', 20));
        return response()->json($accounts);
    }

    // Get record by id
    public function show($id) {
        try {

            $account = Account::findOrFail($id);
            return response()->json($account);
        } catch (Exception $e) {

            Log::error("Account not found: " . $e->getMessage());
            return response()->json(['error' => 'Account not found'], 404);
        }
    }

    // Create new record
    public function store(AccountRequest $request) {
        try {

            $data = $request->validated();
            $data['password'] = $data['password'];
            // $data['password'] = Hash::make($data['password']); // Mã hóa mật khẩu
            $account = Account::create($data);

            return response()->json(['message' => 'Account created successfully', 'data' => $account]);
        } catch (Exception $e) {

            Log::error("Error creating account: " . $e->getMessage());
            return response()->json(['error' => 'Failed to create account'], 500);
        }
    }

    // Update record
    public function update(AccountRequest $request, $id) {
        try {
            $account = Account::findOrFail($id);
            $account->update($request->only(['login', 'password', 'phone']));

            return response()->json(['message' => 'Account updated successfully', 'data' => $account]);
        } catch (Exception $e) {
            Log::error("Error updating account: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update account'], 500);
        }
    }

    // Delete
    public function destroy($id) {
        try {
            $account = Account::findOrFail($id);
            $account->delete();

            return response()->json(['message' => 'Account deleted successfully']);
        } catch (Exception $e) {
            
            Log::error("Error deleting account: " . $e->getMessage());
            return response()->json(['error' => 'Failed to delete account'], 500);
        }
    }
}
