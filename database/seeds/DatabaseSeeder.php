<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Schema::disableForeignKeyConstraints();
        Permission::query()->truncate();
        Role::query()->truncate();
        Schema::enableForeignKeyConstraints();
        // create permissions
        Permission::create(['name' => 'add supermarche']);
        Permission::create(['name' => 'edit supermarche']);
        Permission::create(['name' => 'view supermarche']);
        Permission::create(['name' => 'delete supermarche']);
        
        Permission::create(['name' => 'add gestionnaire']);
        Permission::create(['name' => 'edit gestionnaire']);
        Permission::create(['name' => 'view gestionnaire']);
        Permission::create(['name' => 'delete gestionnaire']);

        Permission::create(['name' => 'add livreur']);
        Permission::create(['name' => 'edit livreur']);
        Permission::create(['name' => 'view livreur']);
        Permission::create(['name' => 'delete livreur']);

        
        Permission::create(['name' => 'add rubrique']);
        Permission::create(['name' => 'edit rubrique']);
        Permission::create(['name' => 'view rubrique']);
        Permission::create(['name' => 'delete rubrique']);

        Permission::create(['name' => 'add produit']);
        Permission::create(['name' => 'edit produit']);
        Permission::create(['name' => 'view produit']);
        Permission::create(['name' => 'delete produit']);
        Permission::create(['name' => 'set promotion']);

        Permission::create(['name' => 'add delivery']);
        Permission::create(['name' => 'edit delivery']);
        Permission::create(['name' => 'view delivery']);
        Permission::create(['name' => 'delete delivery']);

        Permission::create(['name' => 'add commande']);
        Permission::create(['name' => 'validate reception commande']);
        Permission::create(['name' => 'evaluate commande']);

        Permission::create(['name' => 'edit status']);
        Permission::create(['name' => 'view assigned commandes']);
        Permission::create(['name' => 'validate delivery commande']);
    
        // this can be done as separate statements
        $role = Role::create(['name' => 'administrator']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'gestionnaire']);
        $role->givePermissionTo([
            'add rubrique','delete rubrique','edit rubrique', 'view rubrique',
            'add produit','delete produit','edit produit', 'view produit', 'set promotion',
        ]);

        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo([
            'view produit', 'add commande', 'validate reception commande', 'evaluate commande'
        ]);

        $role = Role::create(['name' => 'livreur']);
        $role->givePermissionTo([
            'edit status', 'view assigned commandes', 'validate delivery commande'
        ]);
    }
}
