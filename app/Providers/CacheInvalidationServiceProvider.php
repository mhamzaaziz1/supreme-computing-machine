<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Product;
use App\Transaction;
use App\Contact;
use App\Variation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheInvalidationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Clear product-related cache when a product is updated or created
        Product::updated(function ($product) {
            $this->clearProductCache($product);
        });

        Product::created(function ($product) {
            $this->clearProductCache($product);
        });

        Product::deleted(function ($product) {
            $this->clearProductCache($product);
        });

        // Clear variation-related cache when a variation is updated or created
        Variation::updated(function ($variation) {
            $this->clearVariationCache($variation);
        });

        Variation::created(function ($variation) {
            $this->clearVariationCache($variation);
        });

        Variation::deleted(function ($variation) {
            $this->clearVariationCache($variation);
        });

        // Clear transaction-related cache when a transaction is updated or created
        Transaction::updated(function ($transaction) {
            $this->clearTransactionCache($transaction);
        });

        Transaction::created(function ($transaction) {
            $this->clearTransactionCache($transaction);
        });

        Transaction::deleted(function ($transaction) {
            $this->clearTransactionCache($transaction);
        });

        // Clear contact-related cache when a contact is updated or created
        Contact::updated(function ($contact) {
            $this->clearContactCache($contact);
        });

        Contact::created(function ($contact) {
            $this->clearContactCache($contact);
        });

        Contact::deleted(function ($contact) {
            $this->clearContactCache($contact);
        });
    }

    /**
     * Clear product-related cache
     *
     * @param  \App\Product  $product
     * @return void
     */
    private function clearProductCache($product)
    {
        try {
            // Clear stock report cache
            $this->clearCacheByPattern("stock_report_{$product->business_id}_*");
            
            // Clear product suggestion cache
            $this->clearCacheByPattern("product_suggestion_{$product->business_id}_*");
            
            // Clear featured products cache
            $this->clearCacheByPattern("featured_products_{$product->business_id}_*");
            
            // Clear product analytics cache
            $this->clearCacheByPattern("product_analytics_{$product->business_id}_*");
            
            Log::info("Cleared cache for product ID: {$product->id}");
        } catch (\Exception $e) {
            Log::error("Error clearing product cache: " . $e->getMessage());
        }
    }

    /**
     * Clear variation-related cache
     *
     * @param  \App\Variation  $variation
     * @return void
     */
    private function clearVariationCache($variation)
    {
        try {
            // Get the product to access business_id
            $product = Product::find($variation->product_id);
            if ($product) {
                // Clear product row cache
                $this->clearCacheByPattern("product_row_{$variation->id}_*");
                
                // Clear stock report cache
                $this->clearCacheByPattern("stock_report_{$product->business_id}_*");
                
                // Clear product suggestion cache
                $this->clearCacheByPattern("product_suggestion_{$product->business_id}_*");
                
                Log::info("Cleared cache for variation ID: {$variation->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error clearing variation cache: " . $e->getMessage());
        }
    }

    /**
     * Clear transaction-related cache
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    private function clearTransactionCache($transaction)
    {
        try {
            // Clear profit/loss cache
            $this->clearCacheByPattern("profit_loss_{$transaction->business_id}_*");
            
            // Clear purchase/sell cache
            $this->clearCacheByPattern("purchase_sell_{$transaction->business_id}_*");
            
            // Clear recent transactions cache
            $this->clearCacheByPattern("recent_transactions_{$transaction->business_id}_*");
            
            // Clear business analytics cache
            $this->clearCacheByPattern("business_analytics_{$transaction->business_id}_*");
            
            // If it's a sell transaction, clear customer analytics
            if ($transaction->type == 'sell') {
                $this->clearCacheByPattern("customer_analytics_{$transaction->business_id}_*");
            }
            
            Log::info("Cleared cache for transaction ID: {$transaction->id}");
        } catch (\Exception $e) {
            Log::error("Error clearing transaction cache: " . $e->getMessage());
        }
    }

    /**
     * Clear contact-related cache
     *
     * @param  \App\Contact  $contact
     * @return void
     */
    private function clearContactCache($contact)
    {
        try {
            // Clear customer analytics cache
            $this->clearCacheByPattern("customer_analytics_{$contact->business_id}_*");
            
            // Clear customer-specific transaction cache
            $this->clearCacheByPattern("*_{$contact->business_id}_*_{$contact->id}_*");
            
            Log::info("Cleared cache for contact ID: {$contact->id}");
        } catch (\Exception $e) {
            Log::error("Error clearing contact cache: " . $e->getMessage());
        }
    }

    /**
     * Clear cache by pattern using Redis
     *
     * @param  string  $pattern
     * @return void
     */
    private function clearCacheByPattern($pattern)
    {
        try {
            // If using Redis, we can use the Redis facade to delete by pattern
            if (config('cache.default') == 'redis') {
                $redis = Cache::getRedis();
                $keys = $redis->keys(config('cache.prefix') . ':' . $pattern);
                
                foreach ($keys as $key) {
                    // Remove the cache prefix from the key
                    $key = str_replace(config('cache.prefix') . ':', '', $key);
                    Cache::forget($key);
                }
            } else {
                // For other cache drivers, we can't easily delete by pattern
                // Consider implementing a more specific cache clearing strategy
                // or using cache tags if available
                Log::warning("Pattern-based cache clearing is optimized for Redis. Current driver: " . config('cache.default'));
            }
        } catch (\Exception $e) {
            Log::error("Error clearing cache by pattern: " . $e->getMessage());
        }
    }
}