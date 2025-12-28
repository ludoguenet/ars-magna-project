<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only migrate if address columns exist in clients table
        $hasAddressColumns = Schema::hasColumn('clients', 'address')
            || Schema::hasColumn('clients', 'city')
            || Schema::hasColumn('clients', 'postal_code')
            || Schema::hasColumn('clients', 'country');

        if ($hasAddressColumns) {
            // Migrate existing client addresses to the addresses table
            $clients = DB::table('clients')
                ->whereNotNull('address')
                ->orWhereNotNull('city')
                ->orWhereNotNull('postal_code')
                ->orWhereNotNull('country')
                ->get();

            foreach ($clients as $client) {
                if ($client->address ?? null || $client->city ?? null || $client->postal_code ?? null || $client->country ?? null) {
                    DB::table('addresses')->insert([
                        'addressable_type' => \AppModules\Client\src\Models\Client::class,
                        'addressable_id' => $client->id,
                        'address' => $client->address ?? null,
                        'city' => $client->city ?? null,
                        'postal_code' => $client->postal_code ?? null,
                        'country' => $client->country ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Remove address columns from clients table
            Schema::table('clients', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('clients', 'address')) {
                    $columnsToDrop[] = 'address';
                }
                if (Schema::hasColumn('clients', 'city')) {
                    $columnsToDrop[] = 'city';
                }
                if (Schema::hasColumn('clients', 'postal_code')) {
                    $columnsToDrop[] = 'postal_code';
                }
                if (Schema::hasColumn('clients', 'country')) {
                    $columnsToDrop[] = 'country';
                }
                if (! empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add address columns back to clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->text('address')->nullable()->after('vat_number');
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('country')->nullable()->after('postal_code');
        });

        // Migrate addresses back to clients table
        $addresses = DB::table('addresses')
            ->where('addressable_type', \AppModules\Client\src\Models\Client::class)
            ->get();

        foreach ($addresses as $address) {
            DB::table('clients')
                ->where('id', $address->addressable_id)
                ->update([
                    'address' => $address->address,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                ]);
        }

        // Delete migrated addresses
        DB::table('addresses')
            ->where('addressable_type', \AppModules\Client\src\Models\Client::class)
            ->delete();
    }
};
