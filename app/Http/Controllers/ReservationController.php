<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function create(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('reservations.create', compact('product'));
    }

    public function store(StoreReservationRequest $request, Product $product, DatabaseManager $db): RedirectResponse|View
    {
        abort_unless($product->is_active, 404);

        $result = $db->transaction(function () use ($request, $product) {
            $inventory = Inventory::where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if (! $inventory || $inventory->status === 'sold_out' || $inventory->quantity <= 0) {
                return ['error' => 'sold_out'];
            }

            $newQuantity = $inventory->quantity - 1;
            $newStatus = $newQuantity === 0 ? 'sold_out' : ($newQuantity <= 3 ? 'limited' : $inventory->status);

            $inventory->update([
                'quantity' => $newQuantity,
                'status' => $newStatus,
            ]);

            $reservation = Reservation::create([
                'customer_name' => $request->customer_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'reservation_date' => now()->toDateString(),
                'guest_count' => 1,
                'status' => 'pending',
                'note' => "商品: {$product->name} / 来店予定: {$request->visit_time}",
            ]);

            return ['reservation' => $reservation, 'product' => $product];
        });

        if (isset($result['error'])) {
            return back()->withErrors(['product' => 'ご指定の商品は在庫がなく、予約できません。'])->withInput();
        }

        return redirect()->route('reservations.complete', ['reservation' => $result['reservation']->id]);
    }

    public function complete(Reservation $reservation): View
    {
        return view('reservations.complete', compact('reservation'));
    }

    public function memberStore(Request $request, Product $product, DatabaseManager $db): JsonResponse
    {
        abort_unless($product->is_active, 404);

        $request->validate([
            'visit_time' => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
        ], [
            'visit_time.required' => '来店予定時間を入力してください。',
            'visit_time.regex'    => '時間は HH:MM 形式で入力してください。',
        ]);

        $user = $request->user();

        $result = $db->transaction(function () use ($request, $product, $user) {
            $inventory = Inventory::where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if (! $inventory || $inventory->status === 'sold_out' || $inventory->quantity <= 0) {
                return ['error' => 'sold_out'];
            }

            $newQuantity = $inventory->quantity - 1;
            $inventory->update([
                'quantity' => $newQuantity,
                'status'   => $newQuantity === 0 ? 'sold_out' : ($newQuantity <= 3 ? 'limited' : $inventory->status),
            ]);

            Reservation::create([
                'user_id'          => $user->id,
                'product_id'       => $product->id,
                'customer_name'    => $user->name,
                'email'            => $user->email,
                'reservation_date' => now()->toDateString(),
                'guest_count'      => 1,
                'status'           => 'pending',
                'note'             => "来店予定: {$request->visit_time}",
            ]);

            return ['ok' => true];
        });

        if (isset($result['error'])) {
            return response()->json(['message' => 'この商品は在庫がなく、予約できません。'], 422);
        }

        return response()->json(['message' => '予約が完了しました。']);
    }
}
