<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model  {

    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'code',
        'name',
        'model',
        'description',
        'price',
        'stock_quantity',
        'photo',
    ];

    /**
     * Check if the product is in stock
     */
    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Safely update stock quantity (prevent negative values)
     *
     * @param int $quantity The quantity to reduce (negative to increase)
     * @return boolean Whether the update was successful
     */
    public function updateStock($quantity)
    {
        // If reducing stock (positive quantity)
        if ($quantity > 0) {
            // Prevent reducing below zero
            if ($this->stock_quantity < $quantity) {
                return false;
            }
        }

        $this->stock_quantity -= $quantity;
        $this->save();

        return true;
    }

    /**
     * Get the orders that include this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the comments for this product.
     */
    public function comments()
    {
        return $this->hasMany(ProductComment::class);
    }
    
    /**
     * Get the likes for this product.
     */
    public function likes()
    {
        return $this->hasMany(ProductLike::class);
    }

    /**
     * Check if a product is liked by a specific user
     */
    public function isLikedByUser($user = null)
    {
        if (!$user) {
            return false;
        }
        
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
