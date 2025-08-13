<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
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
        
        // Add index for better performance if it doesn't exist
        Schema::table('categories', function (Blueprint $table) {
            $indexes = DB::select("SHOW INDEX FROM categories");
            $indexExists = false;
            
            foreach ($indexes as $index) {
                if ($index->Key_name === 'categories__lft__rgt_parent_id_index') {
                    $indexExists = true;
                    break;
                }
            }
            
            if (!$indexExists) {
                $table->index(['_lft', '_rgt', 'parent_id'], 'categories__lft__rgt_parent_id_index');
            }
        });
        
        // Rebuild the tree structure if there are categories
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
        // We won't remove the columns in the down method to prevent data loss
        // Just drop the index if it exists
        Schema::table('categories', function (Blueprint $table) {
            $indexes = DB::select("SHOW INDEX FROM categories");
            $indexExists = false;
            
            foreach ($indexes as $index) {
                if ($index->Key_name === 'categories__lft__rgt_parent_id_index') {
                    $indexExists = true;
                    break;
                }
            }
            
            if ($indexExists) {
                $table->dropIndex('categories__lft__rgt_parent_id_index');
            }
        });
    }
};
