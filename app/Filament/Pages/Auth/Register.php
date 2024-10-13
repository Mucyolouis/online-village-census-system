<?php

namespace App\Filament\Pages\Auth;

use Closure;
use Filament\Forms;
use App\Models\Cell;
use App\Models\User;
use App\Models\Family;
use App\Models\Sector;
use App\Models\Village;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use App\Models\Province;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Str;
use Forms\Components\Select;
use Filament\Support\Assets\Css;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Section;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Contracts\Auth\Authenticatable;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Http\Responses\Auth\RegistrationResponse as DefaultRegistrationResponse;

class Register extends BaseRegister
{

    protected static string $view = 'filament.pages.auth.register';

    //protected static string $view = 'filament.pages.auth.custom-register';

    public function getStyles(): array
    {
        return [
            ...parent::getStyles(),
            Css::make('custom-register', resource_path('css/filament/custom-register.css')),
        ];
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Family Info')
                        ->schema([
                            Forms\Components\Select::make('is_new_family')
                                ->label('Are you creating a new family?')
                                ->options([
                                    '1' => 'Yes',
                                    '0' => 'No',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn (Set $set) => $set('existing_family_code', null)),
                            Forms\Components\Select::make('existing_family_code')
                                ->label('Existing Family Code')
                                ->required(fn (Get $get): bool => $get('is_new_family') === '0')
                                ->visible(fn (Get $get): bool => $get('is_new_family') === '0')
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search) {
                                    return Family::where('family_code', 'LIKE', "%{$search}%")
                                        ->orWhereHas('headOfFamily', function ($query) use ($search) {
                                            $query->where('firstname', 'LIKE', "%{$search}%")
                                                ->orWhere('lastname', 'LIKE', "%{$search}%");
                                        })
                                        ->with('headOfFamily:id,firstname,lastname')
                                        ->take(10)
                                        ->get()
                                        ->mapWithKeys(function ($family) {
                                            $label = $family->family_code;
                                            if ($family->headOfFamily) {
                                                $label .= " - " . $family->headOfFamily->firstname . " " . $family->headOfFamily->lastname;
                                            }
                                            return [$family->family_code => $label];
                                        })
                                        ->toArray();
                                })
                                ->getOptionLabelUsing(fn ($value): ?string => Family::where('family_code', $value)->first()?->family_code),
                        ]),
                    Forms\Components\Wizard\Step::make('Address Info')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                            Section::make('Address')
                                ->schema([
                                    Forms\Components\Select::make('province_id')
                                        ->label('Province')
                                        ->options(fn () => Province::all()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->required()
                                        ->afterStateUpdated(function (Set $set) {
                                            $set('district_id', null);
                                            $set('sector_id', null);
                                            $set('cell_id', null);
                                            $set('village_id', null);
                                        }),
                                    Forms\Components\Select::make('district_id')
                                        ->label('District')
                                        ->options(fn(Get $get): Collection => District::all()
                                        ->where('province_id', $get('province_id'))
                                        ->pluck('name','id'))
                                        ->preload()
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set) {
                                         $set('sector_id',null);
                                         $set('cell_id',null);
                                         $set('village_id',null);
                                         })
                                        ->required(),
                                    Forms\Components\Select::make('sector_id')
                                        ->label('Sector')
                                        ->options(fn(Get $get): Collection => Sector::all()
                                        ->where('district_id', $get('district_id'))
                                        ->pluck('name','id'))
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set) {
                                         $set('cell_id',null);
                                         $set('village_id',null);
                                         }),
                                    Forms\Components\Select::make('cell_id')
                                        ->label('Cell')
                                        ->options(fn(Get $get): Collection => Cell::all()
                                        ->where('sector_id', $get('sector_id'))
                                        ->pluck('name','id'))
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set) => $set('village_id',null)),
                                    Forms\Components\Select::make('village_id')
                                        ->label('Village')
                                        ->options(fn(Get $get): Collection => Village::all()
                                            ->where('cell_id', $get('cell_id'))
                                            ->pluck('name','id'))
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->live(),
                                ])
                                ->columns(5),
                            ])
                            
                        ]),
                    Forms\Components\Wizard\Step::make('User Info')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Names')
                                            ->schema([
                                                Forms\Components\TextInput::make('firstname')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('lastname')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\Select::make('nationality')
                                                    ->label('Nationality')
                                                    ->options(
                                                        ['afghan' => 'Afghan','albanian' => 'Albanian','algerian' => 'Algerian','andorran' => 'Andorran','angolan' => 'Angolan','antiguan_or_barbudan' => 'Antiguan or Barbudan','argentine' => 'Argentine','armenian' => 'Armenian','australian' => 'Australian','austrian' => 'Austrian','azerbaijani' => 'Azerbaijani','bahamian' => 'Bahamian', 'bahraini' => 'Bahraini','bangladeshi' => 'Bangladeshi','barbadian' => 'Barbadian','belarusian' => 'Belarusian','belgian' => 'Belgian','belizean' => 'Belizean','beninese' => 'Beninese','bhutanese' => 'Bhutanese','bolivian' => 'Bolivian','bosnian_or_herzegovinian' => 'Bosnian or Herzegovinian', 'motswana' => 'Motswana','brazilian' => 'Brazilian','bruneian' => 'Bruneian','bulgarian' => 'Bulgarian','burkinabe' => 'Burkinabe','burundian' => 'Burundian','cabo_verdean' => 'Cabo Verdean','cambodian' => 'Cambodian','cameroonian' => 'Cameroonian','canadian' => 'Canadian','central_african' => 'Central African','chadian' => 'Chadian','chilean' => 'Chilean','chinese' => 'Chinese','colombian' => 'Colombian','comoran' => 'Comoran','congolese' => 'Congolese','congolese_2' => 'Congolese','costa_rican' => 'Costa Rican','ivorian' => 'Ivorian','croatian' => 'Croatian','cuban' => 'Cuban','cypriot' => 'Cypriot','czech' => 'Czech','danish' => 'Danish','djiboutian' => 'Djiboutian','dominican' => 'Dominican','dominican_2' => 'Dominican','ecuadorian' => 'Ecuadorian','egyptian' => 'Egyptian','salvadoran' => 'Salvadoran','equatorial_guinean' => 'Equatorial Guinean','eritrean' => 'Eritrean','estonian' => 'Estonian','swazi' => 'Swazi','ethiopian' => 'Ethiopian','fijian' => 'Fijian','finnish' => 'Finnish','french' => 'French','gabonese' => 'Gabonese','gambian' => 'Gambian','georgian' => 'Georgian','german' => 'German','ghanaian' => 'Ghanaian','greek' => 'Greek','grenadian' => 'Grenadian','guatemalan' => 'Guatemalan','guinean' => 'Guinean','bissau_guinean' => 'Bissau-Guinean','guyanese' => 'Guyanese','haitian' => 'Haitian','honduran' => 'Honduran','hungarian' => 'Hungarian','icelandic' => 'Icelandic','indian' => 'Indian','indonesian' => 'Indonesian','iranian' => 'Iranian','iraqi' => 'Iraqi','irish' => 'Irish','israeli' => 'Israeli','italian' => 'Italian','jamaican' => 'Jamaican','japanese' => 'Japanese','jordanian' => 'Jordanian','kazakh' => 'Kazakh','kenyan' => 'Kenyan','i_kiribati' => 'I-Kiribati','kosovan' => 'Kosovan','kuwaiti' => 'Kuwaiti','kyrgyz' => 'Kyrgyz','lao' => 'Lao','latvian' => 'Latvian','lebanese' => 'Lebanese','basotho' => 'Basotho','liberian' => 'Liberian','libyan' => 'Libyan','liechtenstein' => 'Liechtenstein','lithuanian' => 'Lithuanian','luxembourger' => 'Luxembourger','malagasy' => 'Malagasy','malawian' => 'Malawian','malaysian' => 'Malaysian','maldivian' => 'Maldivian','malian' => 'Malian','maltese' => 'Maltese','marshallese' => 'Marshallese','mauritanian' => 'Mauritanian','mauritian' => 'Mauritian','mexican' => 'Mexican','micronesian' => 'Micronesian','moldovan' => 'Moldovan','monegasque' => 'Monegasque','mongolian' => 'Mongolian','montenegrin' => 'Montenegrin','moroccan' => 'Moroccan','mozambican' => 'Mozambican','burmese' => 'Burmese','namibian' => 'Namibian','nauruan' => 'Nauruan','nepali' => 'Nepali','dutch' => 'Dutch','new_zealander' => 'New Zealander','nicaraguan' => 'Nicaraguan','nigerien' => 'Nigerien','nigerian' => 'Nigerian','north_korean' => 'North Korean','macedonian' => 'Macedonian','norwegian' => 'Norwegian','omani' => 'Omani','pakistani' => 'Pakistani','palauan' => 'Palauan','palestinian' => 'Palestinian','panamanian' => 'Panamanian','papua_new_guinean' => 'Papua New Guinean','paraguayan' => 'Paraguayan','peruvian' => 'Peruvian','filipino' => 'Filipino','polish' => 'Polish','portuguese' => 'Portuguese','qatari' => 'Qatari','romanian' => 'Romanian','russian' => 'Russian','rwandan' => 'Rwandan','kittitian_or_nevisian' => 'Kittitian or Nevisian','saint_lucian' => 'Saint Lucian','vincentian' => 'Vincentian','samoan' => 'Samoan','sammarinese' => 'Sammarinese','sao_tomean' => 'Sao Tomean','saudi' => 'Saudi','senegalese' => 'Senegalese','serbian' => 'Serbian','seychellois' => 'Seychellois','sierra_leonean' => 'Sierra Leonean','singaporean' => 'Singaporean','slovak' => 'Slovak','slovenian' => 'Slovenian','solomon_islander' => 'Solomon Islander','somali' => 'Somali','south_african' => 'South African','south_korean' => 'South Korean','south_sudanese' => 'South Sudanese','spanish' => 'Spanish','sri_lankan' => 'Sri Lankan','sudanese' => 'Sudanese','surinamese' => 'Surinamese','swedish' => 'Swedish','swiss' => 'Swiss','syrian' => 'Syrian','taiwanese' => 'Taiwanese','tajik' => 'Tajik','tanzanian' => 'Tanzanian','thai' => 'Thai','timorese' => 'Timorese','togolese' => 'Togolese','tongan' => 'Tongan','trinidadian_or_tobagonian' => 'Trinidadian or Tobagonian','tunisian' => 'Tunisian','turkish' => 'Turkish','turkmen' => 'Turkmen','tuvaluan' => 'Tuvaluan','ugandan' => 'Ugandan','ukrainian' => 'Ukrainian','emirati' => 'Emirati','british' => 'British','american' => 'American','uruguayan' => 'Uruguayan','uzbek' => 'Uzbek','ni_vanuatu' => 'Ni-Vanuatu','vatican' => 'Vatican','venezuelan' => 'Venezuelan','vietnamese' => 'Vietnamese','yemeni' => 'Yemeni','zambian' => 'Zambian','zimbabwean' => 'Zimbabwean'
                                                        ])
                                                    ->required()
                                                    ->reactive()
                                                    ->afterStateUpdated(fn (callable $set) => $set('national_ID', null)),
                                                Forms\Components\TextInput::make('national_ID')
                                                    ->numeric()
                                                    ->label('National ID')
                                                    ->required(fn (Get $get): bool => $get('nationality') === 'rwandan')
                                                    ->visible(fn (Get $get): bool => $get('nationality') === 'rwandan')
                                                    ->length(16)
                                                    ->maxLength(16)
                                                    ->rule('digits:16'),
                                                
                                                Forms\Components\TextInput::make('passport_number')
                                                    ->label('Passport Number')
                                                    ->required(fn (Get $get): bool => $get('nationality') !== 'rwandan')
                                                    ->visible(fn (Get $get): bool => $get('nationality') !== 'rwandan')
                                                    ->maxLength(255),
                                                    Forms\Components\DatePicker::make('date_of_birth')
                                                    ->maxDate(now())
                                                    ->required(),
                                                Forms\Components\Select::make('occupation')
                                                    ->label('Occupation')
                                                    ->options([
                                                        'student' => 'Student',
                                                        'farmer' => 'Farmer','software_engineer' => 'Software Engineer','teacher' => 'Teacher','accountant' => 'Accountant','rancher' => 'Rancher','graphic_designer' => 'Graphic Designer','veterinarian' => 'Veterinarian','marketing_manager' => 'Marketing Manager','beekeeper' => 'Beekeeper','electrician' => 'Electrician','agronomist' => 'Agronomist','carpenter' => 'Carpenter','vineyard_worker' => 'Vineyard Worker','human_resources_specialist' => 'Human Resources Specialist','orchard_manager' => 'Orchard Manager','plumber' => 'Plumber','greenhouse_operator' => 'Greenhouse Operator','writer' => 'Writer','dairy_farmer' => 'Dairy Farmer','mechanic' => 'Mechanic','forester' => 'Forester','chef' => 'Chef','poultry_farmer' => 'Poultry Farmer','web_developer' => 'Web Developer','horticulturist' => 'Horticulturist','nurse' => 'Nurse','crop_duster' => 'Crop Duster','interior_designer' => 'Interior Designer','agricultural_engineer' => 'Agricultural Engineer','paralegal' => 'Paralegal'
                                                    ])
                                                    ->required(),
                                                
                                                Forms\Components\Select::make('disability')
                                                    ->options([
                                                        'no' => 'No',
                                                        'yes' => 'Yes',])
                                                    ->required(),
                                                Forms\Components\Select::make('religion')
                                                    ->options([
                                                        'christian' => 'Christian',
                                                        'Muslim' => 'Muslim',
                                                    ])
                                                    ->required(),
                                                Forms\Components\Select::make('gender')
                                                    ->options([
                                                        'male' => 'Male',
                                                        'female' => 'Female',
                                                    ])
                                                    ->required(),
                                                Forms\Components\Select::make('education_level') // Replace TextInput with Select
                                                    ->required()
                                                    ->options([
                                                        'illiteracy' => 'Illiteracy',
                                                        'basic_education' => 'Basic Education',
                                                        'secondary_education' => 'Secondary Education',
                                                        'vocational_technical_education' => 'Vocational/Technical Education',
                                                        'associates_degree' => "Associate's Degree",
                                                        'bachelors_degree' => "Bachelor's Degree",
                                                        'masters_degree' => "Master's Degree",
                                                        'doctorate_phd' => 'Doctorate (Ph.D.)',
                                                    ])
                                                    ->label('Education Level'),
                                                Forms\Components\Select::make('insurance')
                                                    ->label('Medical Insurance')
                                                    ->options([
                                                        'none' => 'None',
                                                        'mituweli' => 'Mituweli',
                                                        'rssb' => 'RSSB(RAMA)',
                                                        'mmi' => 'MMI',
                                                        'uap' => 'UAP',
                                                        'radiant insurance' => 'Radiant insurance',
                                                        'mis ur' => 'Mis UR',
                                                    ])
                                                    ->required(),   
                                                Forms\Components\Select::make('marital_status')
                                                        ->label('Marital Status')
                                                        ->options([
                                                            'single' => 'Single',
                                                            'married' => 'Married',
                                                            'divorced' => 'Divorced',
                                                            'widowed' => 'Widowed',
                                                        ])
                                                        ->required()
                                                        ->live()
                                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                                            if ($get('marital_status') === 'single') {
                                                                $set('spouse_name', null);
                                                                $set('spouse_national_id', null);
                                                                $set('number_of_children', null);
                                                            }
                                                        }),
                                                    Forms\Components\TextInput::make('spouse_name')
                                                        ->label('Spouse Name')
                                                        ->required(fn (Get $get): bool => $get('marital_status') === 'married')
                                                        ->visible(fn (Get $get): bool => $get('marital_status') === 'married'),

                                                    Forms\Components\TextInput::make('spouse_national_id')
                                                        ->label('Spouse National ID')
                                                        ->numeric()
                                                        ->length(16)
                                                        ->required(fn (Get $get): bool => $get('marital_status') === 'married')
                                                        ->visible(fn (Get $get): bool => $get('marital_status') === 'married'),
                                                    
                                                    Forms\Components\TextInput::make('number_of_children')
                                                        ->label('Number of Children')
                                                        ->numeric()
                                                        //->min(0)
                                                        ->required(fn (Get $get): bool => $get('marital_status') === 'married')
                                                        ->visible(fn (Get $get): bool => $get('marital_status') === 'married'),

                                            ])->columns(2),
                                    ]),
                            ])->columns(2),
                    Forms\Components\Wizard\Step::make('Login & Contact Info')
                        ->schema([
                            Forms\Components\TextInput::make('username')
                                ->required()
                                ->maxLength(255)
                                ->unique('users', 'username'),
                            Forms\Components\TextInput::make('phone_number')
                                ->tel()
                                ->required()
                                ->rules(['digits:10'])
                                ->placeholder('0787654321')
                                ->validationAttribute('phone number')
                                ->helperText('Enter a 10-digit phone number'),
                            Forms\Components\TextInput::make('email')
                                ->label('Email address')
                                ->required()
                                ->email()
                                ->unique('users', 'email')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->same('password_confirmation'),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->label('Confirm Password'),
                        ])->columns(2),
                    Forms\Components\Wizard\Step::make('Photo')
                        ->schema([
                            Forms\Components\FileUpload::make('profile_photo_path')
                                ->image()
                                ->directory('profile-photos')
                                ->maxSize(1024)
                                ->label('Profile Photo'),
                        ])->columns(2),
                ])
            ]);
    }

    
    
    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        // Create or get the family
        if ($data['is_new_family'] === '1') {
            $family = Family::create([
                //'family_code' => $data['family_code'],
                'village_id' => $data['village_id'],
            ]);
        } else {
            $family = Family::where('family_code', $data['existing_family_code'])->firstOrFail();
        }


        $user = $this->getUserModel()::create([
            'username' => $data['username'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'name' => $data['firstname']." ".$data['lastname'],
            'date_of_birth' => $data['date_of_birth'],
            'national_ID' => $data['national_ID'] ?? null,
            'occupation' => $data['occupation'],
            'nationality' => $data['nationality'],
            'insurance' => $data['insurance'],
            'marital_status' => $data['marital_status'],
            'spouse_name' => $data['spouse_name'] ?? null,
            'spouse_national_id' => $data['spouse_national_id'] ?? null,
            'number_of_children' => $data['number_of_children'] ?? null,
            'disability' => $data['disability'],
            'religion' => $data['religion'],
            'education_level' => $data['education_level'],
            'passport_number' => $data['passport_number'] ?? null,
            'phone_number' => $data['phone_number'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile_photo_path' => $data['profile_photo_path'] ?? null,
            'village_id' => $data['village_id'],
            'family_id' => $family->id,
            'is_head_of_family' => $data['is_new_family'] === '1', // Set as head of family if creating a new family
        ]);

        if ($data['is_new_family'] === '1') {
            $family->update(['head_of_family_id' => $user->id]);
        }
        
        event(new Registered($user));

        // Send welcome email with family code
        Mail::to($user->email)->send(new WelcomeEmail($user, $family->family_code));

        Notification::make()
            ->title('Registered successfully')
            ->success()
            ->send();

            return new DefaultRegistrationResponse($user);
    }
}
