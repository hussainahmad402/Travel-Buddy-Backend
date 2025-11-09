<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Move first_name after id
            $table->string('first_name')->nullable()->after('id')->change();
            
            // Move last_name after first_name
            $table->string('last_name')->nullable()->after('first_name')->change();
            
            // Move phone after email
            $table->string('phone')->nullable()->after('email')->change();
            
            // Move address after phone
            $table->string('address')->nullable()->after('phone')->change();
            
            // Move otp_code after address
            $table->string('otp_code')->nullable()->after('address')->change();
            
            // Move profile_picture after otp_code
            $table->string('profile_picture')->nullable()->after('otp_code')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally, you can reorder columns back to original if needed
    }
};
