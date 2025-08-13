<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add _lft column if it doesn't exist
        if (!Schema::hasColumn('categories', '_lft')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('_lft')->default(0)->after('parent_id');
            });
        }
        
        // Add _rgt column if it doesn't exist
        if (!Schema::hasColumn('categories', '_rgt')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedInteger('_rgt')->default(0)->after('_lft');
            });
        }
        
        // Add index for better performance
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['_lft', '_rgt', 'parent_id'], 'categories__lft__rgt_parent_id_index');
        });
        
        // Rebuild the tree structure
        if (Schema::hasTable('categories')) {
            $categories = \App\Models\Category::all();
            if ($categories->isNotEmpty()) {
                \App\Models\Category::fixTree();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the index
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories__lft__rgt_parent_id_index');
        });
        
        // Remove the columns if they exist
        if (Schema::hasColumn('categories', '_rgt')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('_rgt');
            });
        }
        
        if (Schema::hasColumn('categories', '_lft')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('_lft');
            });
        }
    }
};
