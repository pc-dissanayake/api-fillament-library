<?php

namespace App\Filament\Resources\APIJobsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TableStructureRelationManager extends RelationManager
{
    protected static string $relationship = 'tableStructure';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('column_name')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),                
                Forms\Components\Select::make('column_type')
                    ->required()
                    ->options([
                        'varchar' => 'VARCHAR',
                        'char' => 'CHAR',
                        'text' => 'TEXT',
                        'tinytext' => 'TINYTEXT',
                        'mediumtext' => 'MEDIUMTEXT',
                        'longtext' => 'LONGTEXT',
                        'int' => 'INT',
                        'tinyint' => 'TINYINT',
                        'smallint' => 'SMALLINT',
                        'mediumint' => 'MEDIUMINT',
                        'bigint' => 'BIGINT',
                        'decimal' => 'DECIMAL',
                        'float' => 'FLOAT',
                        'double' => 'DOUBLE',
                        'boolean' => 'BOOLEAN',
                        'date' => 'DATE',
                        'datetime' => 'DATETIME',
                        'timestamp' => 'TIMESTAMP',
                        'time' => 'TIME',
                        'year' => 'YEAR',
                        'binary' => 'BINARY',
                        'varbinary' => 'VARBINARY',
                        'tinyblob' => 'TINYBLOB',
                        'blob' => 'BLOB',
                        'mediumblob' => 'MEDIUMBLOB',
                        'longblob' => 'LONGBLOB',
                        'enum' => 'ENUM',
                        'set' => 'SET',
                        'json' => 'JSON',
                        'uuid' => 'UUID',
                    ])
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $set('attributes', []);
                        $set('length', null);
                    }),
                
                Forms\Components\TextInput::make('length')
                    ->maxLength(255)
                    ->visible(fn (Get $get) => in_array($get('column_type'), ['varchar', 'char', 'int', 'decimal'])),
                Forms\Components\Select::make('attributes')
                    ->multiple()
                    ->options(function (Get $get) {
                        $type = $get('column_type');
                        $options = [];
                
                        if (in_array($type, ['int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'decimal', 'float', 'double'])) {
                            $options['unsigned'] = 'Unsigned';
                            $options['zerofill'] = 'Zerofill';
                        }
                
                        if (in_array($type, ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
                            $options['auto_increment'] = 'Auto Increment';
                        }
                
                        if (in_array($type, ['char', 'varchar', 'binary', 'varbinary'])) {
                            $options['binary'] = 'Binary';
                        }
                
                        if ($type === 'timestamp') {
                            $options['on_update_current_timestamp'] = 'On Update Current Timestamp';
                        }
                
                        return $options;
                    })
                    ->helperText('Select applicable attributes for this column.')
                    ->columnSpanFull()
                    ->afterStateHydrated(function (Select $component, $state) {
                        if (is_string($state)) {
                            $component->state(json_decode($state, true) ?? []);
                        } elseif (is_array($state)) {
                            $component->state($state);
                        } else {
                            $component->state([]);
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? json_encode($state) : $state),
                                Forms\Components\Toggle::make('is_required')
                    ->required(),
                Forms\Components\Toggle::make('is_nullable')
                    ->required(),
                Forms\Components\Group::make([
                    Forms\Components\Select::make('default_value_type')
                        ->label('Default Value Type')
                        ->options([
                            'none' => 'None',
                            'null' => 'NULL',
                            'custom' => 'Custom Value',
                            'expression' => 'Expression',
                        ])
                        ->default('none')
                        ->reactive(),
                
                    Forms\Components\TextInput::make('default_value')
                        ->label('Default Value')
                        ->maxLength(255)
                        ->hidden(fn (Get $get) => $get('default_value_type') === 'none' || $get('default_value_type') === 'null'),
                
                    Forms\Components\Select::make('default_expression')
                        ->label('Default Expression')
                        ->options([
                            'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP',
                            'CURRENT_DATE' => 'CURRENT_DATE',
                            'CURRENT_TIME' => 'CURRENT_TIME',
                            'UUID()' => 'UUID()',
                        ])
                        ->hidden(fn (Get $get) => $get('default_value_type') !== 'expression'),
                ])
                    ->columns(2)
                    ->columnSpanFull(),
                Forms\Components\Select::make('table_key')
                    ->options([
                        'primary' => 'Primary Key',
                        'unique' => 'Unique Key',
                        'index' => 'Index',
                        // Add more options as needed
                    ]),
                Forms\Components\Textarea::make('comments')
                    ->maxLength(65535),
                Forms\Components\Select::make('laravel_validation_rule')
                    ->label('Laravel Validation Rules')
                    ->multiple()
                    ->options([
                        'required' => 'Required',
                        'nullable' => 'Nullable',
                        'string' => 'String',
                        'integer' => 'Integer',
                        'numeric' => 'Numeric',
                        'boolean' => 'Boolean',
                        'array' => 'Array',
                        'date' => 'Date',
                        'email' => 'Email',
                        'url' => 'URL',
                        'ip' => 'IP Address',
                        'json' => 'JSON',
                        'alpha' => 'Alpha',
                        'alpha_num' => 'Alpha-numeric',
                        'alpha_dash' => 'Alpha-dash',
                        'in' => 'In',
                        'not_in' => 'Not In',
                        'min' => 'Minimum',
                        'max' => 'Maximum',
                        'between' => 'Between',
                        'regex' => 'Regular Expression',
                        'unique' => 'Unique',
                        'exists' => 'Exists',
                        'custom' => 'Custom Rule',
                    ])
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $set('laravel_validation_rule_value', null))
                    ->afterStateHydrated(function (Select $component, $state) {
                        $component->state(is_string($state) ? explode('|', $state) : $state ?? []);
                    })
                    ->dehydrateStateUsing(fn ($state) => is_array($state) ? implode('|', $state) : $state),
                            
                Forms\Components\TextInput::make('laravel_validation_rule_value')
                    ->label('Rule Value')
                    ->helperText('Enter the value for the selected rule (e.g., min:8, max:255, regex:/pattern/)')
                    ->visible(fn (Get $get) => in_array($get('laravel_validation_rule'), ['in', 'not_in', 'min', 'max', 'between', 'regex', 'unique', 'exists', 'custom']))
                    ->required(fn (Get $get) => in_array($get('laravel_validation_rule'), ['in', 'not_in', 'min', 'max', 'between', 'regex', 'unique', 'exists', 'custom'])),
                
                Forms\Components\TextInput::make('custom_validation_rule')
                    ->label('Custom Validation Rule')
                    ->helperText('Enter a custom Laravel validation rule (e.g., "required|min:8|max:255")')
                    ->visible(fn (Get $get) => in_array('custom', $get('laravel_validation_rule') ?? []))
                    ->required(fn (Get $get) => in_array('custom', $get('laravel_validation_rule') ?? [])),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('column_name'),
                Tables\Columns\TextColumn::make('column_type'),
                Tables\Columns\BooleanColumn::make('is_required'),
                Tables\Columns\BooleanColumn::make('is_nullable'),
                Tables\Columns\TextColumn::make('table_key'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->disabled(fn () => $this->getOwnerRecord()->locked),
                Action::make('createTable')
                    ->label('Create MySQL Table')
                    ->disabled(fn () => $this->getOwnerRecord()->locked)
                    ->action(function () {
                        $this->createMySQLTable();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Create MySQL Table')
                    ->modalDescription('Are you sure you want to create a MySQL table based on this structure? This is irreversible.')
                    ->modalSubmitActionLabel('Yes, create table')
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn () => $this->getOwnerRecord()->locked),
                Tables\Actions\DeleteAction::make()
                    ->disabled(fn () => $this->getOwnerRecord()->locked),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->disabled(fn () => $this->getOwnerRecord()->locked),
                ]),
            
            ]);
    }
   
        protected function createMySQLTable()
        {
            $tableName = $this->getOwnerRecord()->table_uuid ?? 'default_table_name';
            $columns = $this->getOwnerRecord()->tableStructure()->orderBy('created_at')->get();
        
            $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (";
            $columnDefinitions = [];
            $primaryKey = null;
        
            foreach ($columns as $column) {
                $columnName = $column->column_name;
                $columnType = $this->getColumnType($column->column_type);
                $length = $column->length ? "({$column->length})" : '';
                $nullable = $column->is_nullable ? 'NULL' : 'NOT NULL';
                $default = $this->getDefaultValue($column);
                $attributes = $this->getColumnAttributes($column);
        
                $columnDefinition = "`$columnName` $columnType$length";
                
                if (strpos($attributes, 'on_update_current_timestamp') !== false) {
                    $columnDefinition .= " ON UPDATE CURRENT_TIMESTAMP";
                    $attributes = str_replace('on_update_current_timestamp', '', $attributes);
                }
                
                $columnDefinition .= " $attributes $nullable $default";
                $columnDefinitions[] = trim($columnDefinition);
        
                if ($column->table_key === 'primary') {
                    $primaryKey = $columnName;
                } elseif ($column->table_key === 'unique') {
                    $columnDefinitions[] = "UNIQUE KEY `{$columnName}_unique` (`$columnName`)";
                } elseif ($column->table_key === 'index') {
                    $columnDefinitions[] = "INDEX `{$columnName}_index` (`$columnName`)";
                }
            }
        
            if ($primaryKey) {
                $columnDefinitions[] = "PRIMARY KEY (`$primaryKey`)";
            }
        
            $columnDefinitions[] = "`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
            $columnDefinitions[] = "`updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";

            
            $sql .= implode(', ', $columnDefinitions);
            $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
            Log::debug('Generated SQL: ' . $sql);
            //dd($sql);
        
            try {
                DB::statement($sql);
                Notification::make()
                    ->title('Success')
                    ->body('MySQL table created successfully.')
                    ->success()
                    ->send();

                    $tableName = $this->getOwnerRecord()->locked = true ;
                    $tableName = $this->getOwnerRecord()->save();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Error')
                    ->body('Error creating MySQL table: ' . $e->getMessage())
                    ->danger()
                    ->send();
            }
        }
        
        protected function getColumnType($type)
        {
            $typeMap = [
                'id' => 'INT',
                // Add more mappings as needed
            ];
        
            return $typeMap[$type] ?? $type;
        }
        
        protected function getDefaultValue($column)
        {
            switch ($column->default_value_type) {
                case 'null':
                    return 'DEFAULT NULL';
                case 'custom':
                    return "DEFAULT '" . addslashes($column->default_value) . "'";
                case 'expression':
                    return "DEFAULT " . $column->default_expression;
                default:
                    return '';
            }
        }
        
        protected function getColumnAttributes($column)
        {
            $attributes = json_decode($column->attributes, true) ?? [];
            return implode(' ', array_filter($attributes));
        }
}