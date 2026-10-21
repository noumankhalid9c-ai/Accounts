<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 100)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('users', 'user_code')) {
                $table->string('user_code', 30)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture', 255)->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->nullable()->after('profile_picture');
            }
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation', 100)->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('password_hash');
            }
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->unsignedInteger('failed_login_attempts')->default(0)->after('must_change_password');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->dateTime('locked_until')->nullable()->after('failed_login_attempts');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->dateTime('last_login_at')->nullable()->after('locked_until');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->dateTime('password_changed_at')->nullable()->after('last_login_ip');
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password_changed_at');
            }
            if (!Schema::hasColumn('users', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        // Modify status column in users table to varchar(30) to allow 'Active', 'Inactive', 'Suspended', 'Locked'
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN status VARCHAR(30) DEFAULT 'Active'");
        } catch (\Exception $e) {
            // Ignore if already modified
        }

        // 2. Update roles table
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'slug')) {
                $table->string('slug', 50)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('roles', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('description');
            }
            if (!Schema::hasColumn('roles', 'created_at')) {
                $table->timestamps();
            }
        });

        // 3. Create permissions table
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('module', 50)->index();
                $table->string('action', 50);
                $table->string('slug', 100)->unique(); // e.g. users.view, salary.financial
                $table->string('name', 150);
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
        }

        // 4. Create role_permissions pivot table
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('role_id');
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['role_id', 'permission_id']);
            });
        }

        // 5. Create user_permissions table (direct override)
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->boolean('is_granted')->default(true); // true = grant, false = deny
                $table->timestamps();

                $table->unique(['user_id', 'permission_id']);
            });
        }

        // 6. Create login_histories table
        if (!Schema::hasTable('login_histories')) {
            Schema::create('login_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->string('identifier', 150)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('status', 30); // Success, Failed, Locked
                $table->string('failure_reason', 255)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 7. Create security_settings table
        if (!Schema::hasTable('security_settings')) {
            Schema::create('security_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
        }

        // 8. Create password_reset_tokens table
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 9. Update audit_logs table to ensure IP & module fields exist
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'module')) {
                $table->string('module', 50)->nullable()->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('new_value');
            }
            if (!Schema::hasColumn('audit_logs', 'description')) {
                $table->string('description', 255)->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        // Safe backward-compatible rollback if needed
    }
};
