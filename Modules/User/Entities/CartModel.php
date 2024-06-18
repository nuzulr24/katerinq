<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Seller\Entities\AccountModel as User;

class CartModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_carts';
    protected $fillable = [
        'id', 'user_id', 'name',
        'price', 'quantity'
    ];

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    public $sessionUnique = null;

    public function user()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    public static function session($sessionId)
    {
        $instance = new static(); // Create an instance of the class
        $instance->sessionUnique = $sessionId; // Set the session ID

        return $instance; // Return the instance for chaining
    }

    public function getCart()
    {
        return self::where('user_id', user()->id)->limit(5)->get();
    }

    public function store($data)
    {
        // Add cart item to the database with the session ID from $this
        return self::insert(array_merge(['user_id' => user()->id], $data));
    }

    public static function updateCart($uniqueId, $data)
    {
        // Update the cart item based on the unique ID and the session ID from $this
        return self::where('id', $uniqueId)->where('user_id', user()->id)->update($data);
    }

    public function remove($uniqueId)
    {
        // Remove the cart item based on the unique ID and the session ID from $this
        return self::where('id', $uniqueId)->where('user_id', $this->id)->delete();
    }

    public function getTotal()
    {
        // Get the total cart amount based on the session ID from $this
        return self::where('user_id', $this->id)->sum('price');
    }

    public function getSubTotal()
    {
        // Get the subtotal cart amount based on the session ID from $this
        return self::where('user_id', $this->id)
        ->selectRaw('SUM(price * quantity) as subtotal')
        ->value('subtotal');
    }
}
