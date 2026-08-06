<?php

namespace App\Livewire;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class MemberReservationModal extends Component
{
    public bool $show = false;
    public ?int $productId = null;
    public string $productName = '';
    public ?string $saleDate = null;
    public string $visitTime = '';
    public bool $success = false;
    public string $errorMessage = '';

    #[On('open-member-reservation')]
    public function open(int $productId, string $productName, ?string $saleDate = null): void
    {
        $this->productId    = $productId;
        $this->productName  = $productName;
        $this->saleDate     = $saleDate;
        $this->visitTime    = '';
        $this->success      = false;
        $this->errorMessage = '';
        $this->show         = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function reserve(): void
    {
        abort_unless(auth()->check(), 403);

        $this->validate([
            'visitTime' => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
        ], [
            'visitTime.required' => '来店予定時間を入力してください。',
            'visitTime.regex'    => '時間は HH:MM 形式で入力してください。',
        ]);

        $product  = Product::findOrFail($this->productId);
        $user     = auth()->user();
        $isFuture = $this->saleDate && $this->saleDate > now()->toDateString();

        $result = DB::transaction(function () use ($product, $user, $isFuture) {
            if ($isFuture) {
                Reservation::create([
                    'user_id'          => $user->id,
                    'product_id'       => $product->id,
                    'customer_name'    => $user->name,
                    'email'            => $user->email,
                    'reservation_date' => $this->saleDate,
                    'guest_count'      => 1,
                    'status'           => 'pending',
                    'note'             => "来店予定: {$this->visitTime}",
                ]);

                return ['ok' => true];
            }

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
                'note'             => "来店予定: {$this->visitTime}",
            ]);

            return ['ok' => true];
        });

        if (isset($result['error'])) {
            $this->errorMessage = 'この商品は在庫がなく、予約できません。';
            return;
        }

        $this->success = true;
    }

    public function render()
    {
        return view('livewire.member-reservation-modal');
    }
}
