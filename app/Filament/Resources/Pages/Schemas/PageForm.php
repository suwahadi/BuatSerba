<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Page Information')
                            ->description('Basic page details and URL')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(191)
                                    ->label('Title')
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                                \Filament\Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(191)
                                    ->unique(ignoreRecord: true)
                                    ->label('Slug'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Status & Display')
                            ->description('Manage page visibility and display order')
                            ->schema([
                                \Filament\Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->label('Active')
                                    ->inline(false),

                                \Filament\Forms\Components\TextInput::make('sort')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Sort Order'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                \Filament\Schemas\Components\Section::make('Content')
                    ->description('Page content (supports rich text formatting)')
                    ->schema([
                        \Filament\Forms\Components\RichEditor::make('content')
                            ->required()
                            ->label('Content')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                                'undo',
                                'redo',
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
