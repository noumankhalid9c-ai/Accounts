<?php
$models = [
    'Client' => [
        'table' => 'clients',
        'hasMany' => ['Invoice', 'Domain', 'Hosting', 'Service', 'Payment', 'Receipt']
    ],
    'Invoice' => [
        'table' => 'invoices',
        'belongsTo' => ['Client'],
        'hasMany' => ['Payment', 'Receipt']
    ],
    'Expense' => [
        'table' => 'expenses'
    ],
    'Hosting' => [
        'table' => 'hosting_accounts',
        'belongsTo' => ['Client']
    ],
    'Domain' => [
        'table' => 'domains',
        'belongsTo' => ['Client']
    ],
    'Payment' => [
        'table' => 'payments',
        'belongsTo' => ['Client', 'Invoice']
    ],
    'Receipt' => [
        'table' => 'receipts',
        'belongsTo' => ['Client', 'Invoice', 'Payment']
    ],
    'Renewal' => [
        'table' => 'renewals'
    ],
    'Service' => [
        'table' => 'services',
        'belongsTo' => ['Client']
    ],
    'Setting' => [
        'table' => 'settings',
        'timestamps' => false
    ],
    'User' => [
        'table' => 'users'
    ]
];

$appPath = __DIR__ . '/app';

foreach ($models as $name => $config) {
    $table = $config['table'];
    $timestamps = isset($config['timestamps']) ? $config['timestamps'] : true;
    
    $relations = '';
    
    if (isset($config['belongsTo'])) {
        foreach ($config['belongsTo'] as $rel) {
            $func = strtolower($rel);
            $relations .= "
    public function $func()
    {
        return \$this->belongsTo($rel::class);
    }
";
        }
    }
    
    if (isset($config['hasMany'])) {
        foreach ($config['hasMany'] as $rel) {
            $func = strtolower($rel) . 's'; // plural
            if ($func == 'hostings') $func = 'hosting_accounts';
            $relations .= "
    public function $func()
    {
        return \$this->hasMany($rel::class);
    }
";
        }
    }

    $tsCode = $timestamps ? "    const UPDATED_AT = null;\n" : "    public \$timestamps = false;\n";

    $modelCode = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass $name extends Model\n{\n    use HasFactory;\n\n    protected \$table = '$table';\n    protected \$guarded = [];\n\n$tsCode$relations}\n";

    if ($name === 'User') {
        $modelCode = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Foundation\Auth\User as Authenticatable;\nuse Illuminate\Notifications\Notifiable;\nuse Laravel\Sanctum\HasApiTokens;\n\nclass User extends Authenticatable\n{\n    use HasApiTokens, HasFactory, Notifiable;\n\n    protected \$table = 'users';\n    protected \$guarded = [];\n    const UPDATED_AT = null;\n}\n";
    }

    file_put_contents("$appPath/Models/$name.php", $modelCode);

    // Repository Interface
    $interfaceName = "{$name}RepositoryInterface";
    $interfaceCode = "<?php\n\nnamespace App\Interfaces;\n\ninterface $interfaceName\n{\n    public function all();\n    public function find(\$id);\n    public function create(array \$data);\n    public function update(\$id, array \$data);\n    public function delete(\$id);\n}\n";
    file_put_contents("$appPath/Interfaces/$interfaceName.php", $interfaceCode);

    // Repository
    $repoName = "{$name}Repository";
    $repoCode = "<?php\n\nnamespace App\Repositories;\n\nuse App\Interfaces\\$interfaceName;\nuse App\Models\\$name;\n\nclass $repoName implements $interfaceName\n{\n    public function all() { return $name::all(); }\n    public function find(\$id) { return $name::findOrFail(\$id); }\n    public function create(array \$data) { return $name::create(\$data); }\n    public function update(\$id, array \$data) { \$record = \$this->find(\$id); \$record->update(\$data); return \$record; }\n    public function delete(\$id) { return \$this->find(\$id)->delete(); }\n}\n";
    file_put_contents("$appPath/Repositories/$repoName.php", $repoCode);

    // Service
    $serviceName = "{$name}Service";
    $serviceCode = "<?php\n\nnamespace App\Services;\n\nuse App\Interfaces\\$interfaceName;\n\nclass $serviceName\n{\n    protected \$repository;\n\n    public function __construct($interfaceName \$repository)\n    {\n        \$this->repository = \$repository;\n    }\n\n    public function getAll() { return \$this->repository->all(); }\n    public function getById(\$id) { return \$this->repository->find(\$id); }\n    public function create(array \$data) { return \$this->repository->create(\$data); }\n    public function update(\$id, array \$data) { return \$this->repository->update(\$id, \$data); }\n    public function delete(\$id) { return \$this->repository->delete(\$id); }\n}\n";
    file_put_contents("$appPath/Services/$serviceName.php", $serviceCode);
}

echo "Scaffold generated successfully.";
