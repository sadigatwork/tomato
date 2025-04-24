<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
// Removed unused import
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Exports\StudentExport; // Ensure this matches the actual namespace of StudentExport
use App\Models\Classes;
use App\Models\Section;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

use function Laravel\Prompts\select;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationGroup = 'Student Management';
    // protected static ?string $navigationLabel = 'Students';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->autofocus()
                    ->unique()
                    ->placeholder('Enter student name')
                    ->label('Student Name'),
                TextInput::make('email')
                    ->required()
                    ->unique()
                    ->placeholder('Enter student email')
                    ->label('Student Email'),
                TextInput::make('phone')
                    ->required()
                    ->unique()
                    ->placeholder('Enter student phone number')
                    ->label('Student Phone'),
                TextInput::make('address')
                    ->required()
                    ->placeholder('Enter student address')
                    ->label('Student Address'),
                Select::make('class_id')
                    ->relationship('class', 'name')
                    ->required()
                    ->label('Class')
                    ->reactive()
                    ->placeholder('Select a class')
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('section_id', null);
                    }),
                Select::make('section_id')
                    ->options(
                        fn (callable $get) => \App\Models\Section::where('class_id', $get('class_id'))->pluck('name', 'id')
                    )
                    ->required()
                    ->label('Section')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Student Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Student Phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Student Address')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('class.name')
                    ->label('Class Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('section.name')
                    ->label('Section Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('class-section-filter')
                    ->form([
                        Select::make('class_id')
                            ->label('Filter by Class')
                            ->placeholder('Select a class')
                            ->options(
                                Classes::all()->pluck('name', 'id')
                            )
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('section_id', null);
                            }),
                        Select::make('section_id')
                            ->label('Filter by Section')
                            ->placeholder('Select a section')
                            ->options(
                                function (callable $get) {
                                    return Section::where('class_id', $get('class_id'))->pluck('name', 'id')->toArray();
                                }
                            )
                        
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['class_id'], fn (Builder $query, $classId) => $query->where('class_id', $classId))
                        ->when($data['section_id'], fn (Builder $query, $sectionId) => $query->where('section_id', $sectionId))
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                        ->label('Export')
                        ->icon('fluentui-document-arrow-down-20-o')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => (new StudentExport($records))->download('students.xlsx'))
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return self::$model::count();
    }
}
