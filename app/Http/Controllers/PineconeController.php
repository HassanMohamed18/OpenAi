<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PineconeService;
use Illuminate\Support\Facades\DB;
use OpenAI;

use function Laravel\Prompts\select;

class PineconeController extends Controller
{
    protected $embeddingService;
    protected $client;

    public function __construct(PineconeService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    /**
     * Generate embeddings and store in Pinecone
     */
    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'id' => 'required|string',
        //     'text' => 'required|string',
        //     'metadata' => 'nullable|array',
        // ]);

        // $table = 'projects';
        // $columns = [
        //     'project_name',
        //     'project_type',
        //     'total_units',
        //     'available_units',
        //     'launch_date',
        //     'completion_date',
        //     'status',
        //     'price_range',
        //     'price_range_SQ',
        //     'description',
        //     'project_size'
        // ];


        //return $records = DB::table($table)->select($columns)->get();

        // $records = [
        //     (object)[
        //         'id' => 1,
        //         'content' => 'Damac Lagoons - Costa Brava 2 is a residential project with 497 total units and 320 available units. The project was launched on 2021-02-01 and is expected to complete by 2025-07-29. The price range starts from AED 1,535,000 with a price per square meter of AED 1,200. The project size is 93,195.46 sq.mt. Experience Caribbean-inspired waterfront living with stunning views and world-class amenities.'
        //     ],
        //     (object)[
        //         'id' => 2,
        //         'content' => 'Binghatti Amber is a residential project with 726 total units and 650 available units. The project was launched on 2023-01-01 and is expected to complete by 2027-11-01. The price range starts from AED 577,000 with a price per square meter of AED 1,500. The project size is 54,010.40 sq.mt. Experience elegant living in Binghatti Amber, offering stunning apartments with modern finishes and a prime location in JVC. Enjoy world-class amenities and a convenient lifestyle in this vibrant community.'
        //     ],
        //     (object)[
        //         'id' => 3,
        //         'content' => 'Diamondz By Danube is a residential project with 1,219 total units and 950 available units. The project was launched on 2024-01-01 and is expected to complete by 2024-12-31. The price range starts from AED 1.12 M with a price per square meter of AED 1,700. The project size is 84,117.24 sq.mt. Experience unparalleled luxury with stunning apartments and world-class amenities in this iconic 62-story tower.'
        //     ],
        //     (object)[
        //         'id' => 4,
        //         'content' => 'The Bristol Emaar Beachfront is a residential project with 229 total units and 130 available units. The project was launched on 2024-07-01 and is expected to complete by 2029-09-30. The price range starts from AED 2.4 M with a price per square meter of AED 800. The project size is 67,430.36 sq.mt. Experience stunning apartments with breathtaking sea views in this iconic tower.'
        //     ],
        //     (object)[
        //         'id' => 5,
        //         'content' => 'Sobha Hartland - The Crest is a residential project with 1,518 total units and 1,002 available units. The project was launched on 2020-11-01 and is expected to complete by 2025-12-31. The price range starts from AED 1.1 Million with a price per square meter of AED 700. The project size is 121,044.96 sq.mt. Experience Caribbean-inspired luxury with stunning lagoon views and world-class amenities.'
        //     ]
        // ];
        // $locations = DB::table('locations')
        //     ->join('areas', 'locations.area_id', '=', 'areas.area_id')
        //     ->select('locations.*', 'areas.*')
        //     ->get();

        // return  $locations = collect($locations)->map(function ($item) {
        //     return collect($item)->except([
        //         'created_at',
        //         'updated_at',
        //         'longitude',
        //         'latitude',
        //     ]);
        // });

        // $projects = DB::table('projects')
        //     ->join('locations', 'projects.location_id', '=', 'locations.location_id')
        //     ->join('developers', 'projects.developer_id', '=', 'developers.developer_id')
        //     ->select('projects.*', 'locations.*','developers.name as developer_name')
        //     ->get();

        // return  $projects = collect($projects)->map(function ($item) {
        //     return collect($item)->except([
        //         'created_at',
        //         'updated_at',
        //         'longitude',
        //         'latitude',
        //     ]);
        // });

        // $properties = DB::table('properties')
        //     ->leftJoin('projects', 'properties.project_id', '=', 'projects.project_id')
        //     //->leftJoin('addresses', 'properties.address_id', '=', 'addresses.address_id')
        //     //->leftJoin('buildings', 'properties.building_id', '=', 'buildings.building_id')
        //     ->leftJoin('property_types', 'properties.property_type_id', '=', 'property_types.property_type_id')
        //     // ->leftJoin('property_subtypes', 'properties.property_subtype_id', '=', 'property_subtypes.property_subtype_id')
        //     ->select(
        //         'properties.*',
        //         'projects.project_name',
        //         //'addresses.address',
        //         //'buildings.building_name',
        //         'property_types.name as property_type',
        //         //'property_subtypes.subtype_name'
        //         // Excluded: properties.created_at, properties.updated_at, properties.dld_permit_number, properties.dld_barcode
        //     )
        //     ->get();

        // $properties = collect($properties)->map(function ($item) {
        //     return collect($item)->except([
        //         'created_at',
        //         'updated_at',
        //         'dld_permit_number',
        //         'dld_barcode',
        //         'project_id',
        //         'property_type_id',
        //         'property_subtype_id',
        //         'parking_spaces',
        //         'reference_listed',
        //         'broker_license',
        //         'agent_license',
        //         'property_id',
        //         'building_id',
        //         'building_id',
        //         'address_id'
        //     ]);
        // });

        // return response()->json($properties);

        // $pro_records = [ 
        //     (object)[
        //         'id' => 6,
        //         'metadata' => [
        //             'content' => 'Discover Caribbean-inspired waterfront living at Damac Lagoons - Costa Brava 2, where breathtaking views and world-class amenities create an unparalleled lifestyle. This three-bedroom, two-bathroom property spans 2,700 sq. ft. with a modern, spacious design. Ideal for families or investors, this residence offers serene surroundings and luxurious comforts in Yalayes.',
        //             'property_name' => 'Damac Lagoons - Costa Brava 2',
        //             'project_name' => 'Damac Lagoons - Costa Brava 2',
        //             "zone_name" => "Yalayes",
        //             "property_type" => "Residential",
        //             "availability_status" => "available",
        //             "construction_status" => "off-plan",
        //             "bedrooms" => 3,
        //             "bathrooms" => 2,
        //             'project_id' => 1
        //         ]
        //     ],
        //     (object)[
        //         'id' => 7,
        //         'metadata' => [
        //             'content' => 'Experience elegant living at Binghatti Amber, featuring modern apartments with stunning finishes in the heart of JVC. This two-bedroom, one-bathroom residence offers 550 sq. ft. of stylish living space, making it an excellent choice for city dwellers seeking contemporary comfort.',
        //             'property_name' => 'Binghatti Amber',
        //             'project_name' => 'Binghatti Amber',
        //             "zone_name" => "JVC",
        //             "property_type" => "Residential",
        //             "availability_status" => "available",
        //             "construction_status" => "under-construction",
        //             "bedrooms" => 2,
        //             "bathrooms" => 1,
        //             'project_id' => 2
        //         ]
        //     ],
        //     (object)[
        //         'id' => 8,
        //         'metadata' => [
        //             'content' => 'Discover unparalleled luxury at Diamondz By Danube, a 62-story tower offering world-class amenities and breathtaking apartments. This fully furnished one-bedroom, one-bathroom residence spans 380 sq. ft., providing stylish and comfortable urban living in Barsha South.',
        //             'property_name' => 'Diamondz By Danube',
        //             'project_name' => 'Diamondz By Danube',
        //             "zone_name" => "Barsha South",
        //             "property_type" => "Residential",
        //             "availability_status" => "available",
        //             "construction_status" => "off-plan",
        //             "bedrooms" => 1,
        //             "bathrooms" => 1,
        //             'project_id' => 3
        //         ]
        //     ],
        //     (object)[
        //         'id' => 9,
        //         'metadata' => [
        //             'content' => 'The Bristol Emaar Beachfront offers stunning sea-view apartments in an iconic tower. This one-bedroom, one-bathroom unit spans 768 sq. ft., blending contemporary elegance with prime beachfront living in JVC.',
        //             'property_name' => 'The Bristol Emaar Beachfront',
        //             'project_name' => 'The Bristol Emaar Beachfront',
        //             "zone_name" => "JVC",
        //             "property_type" => "Residential",
        //             "availability_status" => "available",
        //             "construction_status" => "under-construction",
        //             "bedrooms" => 1,
        //             "bathrooms" => 1,
        //             'project_id' => 4
        //         ]
        //     ],
        //     (object)[
        //         'id' => 10,
        //         'metadata' => [
        //             'content' => 'Sobha Hartland - The Crest presents Caribbean-inspired luxury with breathtaking lagoon views and world-class amenities. This two-bedroom, one-bathroom apartment spans 540 sq. ft. and offers a serene yet vibrant lifestyle in JVC.',
        //             'property_name' => 'Sobha Hartland - The Crest',
        //             'project_name' => 'Sobha Hartland - The Crest',
        //             "zone_name" => "JVC",
        //             "property_type" => "Residential",
        //             "availability_status" => "available",
        //             "construction_status" => "ready",
        //             "bedrooms" => 2,
        //             "bathrooms" => 1,
        //             'project_id' => 5
        //         ]
        //     ],
        // ];

        // $pro_records = [
        //     (object)[
        //         'id' => 11,
        //         'metadata' => [
        //             'content' => 'Discover Caribbean-inspired waterfront living at Damac Lagoons - Costa Brava 2, where breathtaking views and world-class amenities create an unparalleled lifestyle. This three-bedroom, two-bathroom property spans 2,700 sq. ft. with a modern, spacious design. Ideal for families or investors, this residence offers serene surroundings and luxurious comforts in Yalayes.',
        //             'property_name' => 'Sunset Villas',
        //             'project_name' => 'Damac Lagoons - Costa Brava 2',
        //             "zone_name" => "Yalayes",
        //             "property_type" => "Residential",
        //             "availability_status" => "available",
        //             "construction_status" => "off-plan",
        //             "bedrooms" => 3,
        //             "bathrooms" => 2,
        //             'project_id' => 1
        //         ]
        //     ],
        //     (object)[
        //         'id' => 12,
        //         'metadata' => [
        //             'content' => 'Enjoy modern waterfront living in Damac Lagoons - Costa Brava 2. This beautiful two-bedroom, two-bathroom apartment offers 1,800 sq. ft. of space, located in a vibrant, community-focused area, with breathtaking views and premium amenities.',
        //             'property_name' => 'Azure Residences',
        //             'project_name' => 'Damac Lagoons - Costa Brava 2',
        //             "zone_name" => "Yalayes",
        //             "property_type" => "Residential",
        //             "availability_status" => "available",
        //             "construction_status" => "off-plan",
        //             "bedrooms" => 2,
        //             "bathrooms" => 2,
        //             'project_id' => 1
        //         ]
        //     ],
        //     (object)[
        //         'id' => 13,
        //         'metadata' => [
        //             'content' => 'This luxurious waterfront villa in Damac Lagoons - Costa Brava 2 offers four bedrooms, four bathrooms, and 3,500 sq. ft. of opulent living space. The villa is equipped with world-class amenities and spectacular views, perfect for those seeking the ultimate in luxury.',
        //             'property_name' => 'Oceanfront Estates',
        //             'project_name' => 'Damac Lagoons - Costa Brava 2',
        //             "zone_name" => "Yalayes",
        //             "property_type" => "Villa",
        //             "availability_status" => "available",
        //             "construction_status" => "off-plan",
        //             "bedrooms" => 4,
        //             "bathrooms" => 4,
        //             'project_id' => 1
        //         ]
        //     ],
        //     (object)[
        //         'id' => 14,
        //         'metadata' => [
        //             'content' => 'A spacious one-bedroom, one-bathroom apartment in Damac Lagoons - Costa Brava 2. The apartment spans 1,200 sq. ft., offering a modern design and access to premium amenities, ideal for young professionals or couples.',
        //             'property_name' => 'Lagoon View Apartments',
        //             'project_name' => 'Damac Lagoons - Costa Brava 2',
        //             "zone_name" => "Yalayes",
        //             "property_type" => "Apartment",
        //             "availability_status" => "available",
        //             "construction_status" => "off-plan",
        //             "bedrooms" => 1,
        //             "bathrooms" => 1,
        //             'project_id' => 1
        //         ]
        //     ],
        //     (object)[
        //         'id' => 15,
        //         'metadata' => [
        //             'content' => 'Indulge in an exclusive waterfront property in Damac Lagoons - Costa Brava 2. This three-bedroom, two-bathroom townhouse spans 2,200 sq. ft., providing a perfect combination of comfort and luxury, with access to a variety of top-tier amenities.',
        //             'property_name' => 'Crystal Bay Townhouses',
        //             'project_name' => 'Damac Lagoons - Costa Brava 2',
        //             "zone_name" => "Yalayes",
        //             "property_type" => "Townhouse",
        //             "availability_status" => "available",
        //             "construction_status" => "off-plan",
        //             "bedrooms" => 3,
        //             "bathrooms" => 2,
        //             'project_id' => 1
        //         ]
        //     ]
        // ];


        $records = [
            (object)[
                'id' => 1,
                'metadata' => [
                    'content' => 'Damac Lagoons - Costa Brava 2 is a luxurious residential project by DAMAC, located near Hessa Street, Dubai. Offering 497 total units with 320 still available, this development brings Caribbean-inspired waterfront living. Launched on February 1, 2021, and expected to be completed by July 29, 2025, it provides world-class amenities and breathtaking views. Prices start from AED 1,535,000, with a price per square meter of AED 1,200. Conveniently positioned near major roads like Mohammed Bin Zayed Road, Emirates Road, and Al Khail Road, it offers seamless connectivity. Landmarks nearby include DAMAC Lagoons projects.',
                    'project_name' => 'Damac Lagoons - Costa Brava 2',
                    'developer_name' => 'DAMAC',
                    'location' => 'Near Hessa Street, Dubai',
                    'total_units' => 497,
                    'available_units' => 320,
                    'launch_date' => '2021-02-01',
                    'completion_date' => '2025-07-29',
                    'price_range' => 'Starting From AED 1,535,000',
                    'price_per_sqm' => 'AED 1,200',
                    'project_size' => '93,195.46 sq.mt',
                    'google_map_link' => 'https://maps.app.goo.gl/PPy26LFT9P7h2EQMA',
                    'landmark' => 'Close to DAMAC Lagoons projects',
                    'location_id' => 1,
                    'project_id' => 1
                ]
            ],
            (object)[
                'id' => 2,
                'metadata' => [
                    'content' => 'Binghatti Amber is a residential project located in the heart of Jumeirah Village Circle (JVC). Developed by Bin Ghatti, it comprises 726 units, with 650 still available. Launched on January 1, 2023, and set to complete by November 1, 2027, this project offers modern apartments with high-end finishes. Prices start from AED 577,000, with a price per square meter of AED 1,500. The community is known for its family-friendly environment, parks, schools, and easy access to Dubai’s main attractions.',
                    'project_name' => 'Binghatti Amber',
                    'developer_name' => 'Bin Ghatti',
                    'location' => 'JVC, Dubai',
                    'total_units' => 726,
                    'available_units' => 650,
                    'launch_date' => '2023-01-01',
                    'completion_date' => '2027-11-01',
                    'price_range' => 'Starting From AED 577,000',
                    'price_per_sqm' => 'AED 1,500',
                    'project_size' => '54,010.40 sq.mt',
                    'google_map_link' => 'https://maps.app.goo.gl/CaVsKVWTEipbDuSf7',
                    'location_id' => 2,
                    'project_id' => 2
                ]
            ],
            (object)[
                'id' => 3,
                'metadata' => [
                    'content' => 'Diamondz By Danube is an opulent residential tower rising 62 stories high, developed by Danube. Located near First Al Khail Street, it offers 1,219 total units, with 950 still available. Launched on January 1, 2024, and expected to complete by December 31, 2024, this project is synonymous with luxury living. Prices start from AED 1.12M, with a price per square meter of AED 1,700.',
                    'project_name' => 'Diamondz By Danube',
                    'developer_name' => 'Danube',
                    'location' => 'Near First Al Khail St, Dubai',
                    'total_units' => 1219,
                    'available_units' => 950,
                    'launch_date' => '2024-01-01',
                    'completion_date' => '2024-12-31',
                    'price_range' => 'Starting From AED 1.12 M',
                    'price_per_sqm' => 'AED 1,700',
                    'project_size' => '84,117.24 sq.mt',
                    'google_map_link' => 'https://maps.app.goo.gl/LwWsBbycfDPNRs1J7',
                    'location_id' => 3,
                    'project_id' => 3
                ]
            ],
            (object)[
                'id' => 4,
                'metadata' => [
                    'content' => 'The Bristol Emaar Beachfront is an iconic seaside development by Emaar, featuring 229 units, with 130 available. Launched on July 1, 2024, and set for completion by September 30, 2029, it offers breathtaking sea views, upscale amenities, and a prime location near Palm Jumeirah. Prices start from AED 2.4M, with a price per square meter of AED 800.',
                    'project_name' => 'The Bristol Emaar Beachfront',
                    'developer_name' => 'Emaar',
                    'location' => 'Emaar Beachfront, Dubai',
                    'total_units' => 229,
                    'available_units' => 130,
                    'launch_date' => '2024-07-01',
                    'completion_date' => '2029-09-30',
                    'price_range' => 'Starting From AED 2.4 M',
                    'price_per_sqm' => 'AED 800',
                    'project_size' => '67,430.36 sq.mt',
                    'google_map_link' => 'https://maps.app.goo.gl/HirdVD3yKRSxqFRw5',
                    'location_id' => 4,
                    'project_id' => 4
                ]
            ],
            (object)[
                'id' => 5,
                'metadata' => [
                    'content' => 'Sobha Hartland - The Crest is a grand development by Sobha, offering 1,518 units, with 1,002 still available. Launched on November 1, 2020, and planned for completion by December 31, 2025, this project is designed for luxury waterfront living. Prices start from AED 1.1M, with a price per square meter of AED 700.',
                    'project_name' => 'Sobha Hartland - The Crest',
                    'developer_name' => 'Sobha',
                    'location' => 'Sobha Hartland, Dubai',
                    'total_units' => 1518,
                    'available_units' => 1002,
                    'launch_date' => '2020-11-01',
                    'completion_date' => '2025-12-31',
                    'price_range' => 'Starting From AED 1.1 Million',
                    'price_per_sqm' => 'AED 700',
                    'project_size' => '121,044.96 sq.mt',
                    'google_map_link' => 'https://maps.app.goo.gl/BuGKLCetir1WZu4U6',
                    'location_id' => 5,
                    'project_id' => 5
                ]
            ],
            (object) [
                'id' => 6,
                'metadata' => [
                    'content' => 'Discover Caribbean-inspired waterfront living at Damac Lagoons - Costa Brava 2, where breathtaking views and world-class amenities create an unparalleled lifestyle. This three-bedroom, two-bathroom property spans 2,700 sq. ft. with a modern, spacious design. Ideal for families or investors, this residence offers serene surroundings and luxurious comforts in Yalayes.',
                    'property_name' => 'Damac Lagoons - Costa Brava 2',
                    'project_name' => 'Damac Lagoons - Costa Brava 2',
                    "zone_name" => "Yalayes",
                    "property_type" => "Residential",
                    "availability_status" => "available",
                    "construction_status" => "off-plan",
                    "bedrooms" => 3,
                    "bathrooms" => 2,
                    'project_id' => 1,
                    
                ]
            ],
            (object) [
                'id' => 7,
                'metadata' => [
                    'content' => 'Experience elegant living at Binghatti Amber, featuring modern apartments with stunning finishes in the heart of JVC. This two-bedroom, one-bathroom residence offers 550 sq. ft. of stylish living space, making it an excellent choice for city dwellers seeking contemporary comfort.',
                    'property_name' => 'Binghatti Amber',
                    'project_name' => 'Binghatti Amber',
                    "zone_name" => "JVC",
                    "property_type" => "Residential",
                    "availability_status" => "available",
                    "construction_status" => "under-construction",
                    "bedrooms" => 2,
                    "bathrooms" => 1,
                    'project_id' => 2
                ]
            ],
            (object)[
                'id' => 8,
                'metadata' => [
                    'content' => 'Discover unparalleled luxury at Diamondz By Danube, a 62-story tower offering world-class amenities and breathtaking apartments. This fully furnished one-bedroom, one-bathroom residence spans 380 sq. ft., providing stylish and comfortable urban living in Barsha South.',
                    'property_name' => 'Diamondz By Danube',
                    'project_name' => 'Diamondz By Danube',
                    "zone_name" => "Barsha South",
                    "property_type" => "Residential",
                    "availability_status" => "available",
                    "construction_status" => "off-plan",
                    "bedrooms" => 1,
                    "bathrooms" => 1,
                    'project_id' => 3
                ]
            ],
            (object)[
                'id' => 9,
                'metadata' => [
                    'content' => 'The Bristol Emaar Beachfront offers stunning sea-view apartments in an iconic tower. This one-bedroom, one-bathroom unit spans 768 sq. ft., blending contemporary elegance with prime beachfront living in JVC.',
                    'property_name' => 'The Bristol Emaar Beachfront',
                    'project_name' => 'The Bristol Emaar Beachfront',
                    "zone_name" => "JVC",
                    "property_type" => "Residential",
                    "availability_status" => "available",
                    "construction_status" => "under-construction",
                    "bedrooms" => 1,
                    "bathrooms" => 1,
                    'project_id' => 4
                ]
            ],
            (object)[
                'id' => 10,
                'metadata' => [
                    'content' => 'Sobha Hartland - The Crest presents Caribbean-inspired luxury with breathtaking lagoon views and world-class amenities. This two-bedroom, one-bathroom apartment spans 540 sq. ft. and offers a serene yet vibrant lifestyle in JVC.',
                    'property_name' => 'Sobha Hartland - The Crest',
                    'project_name' => 'Sobha Hartland - The Crest',
                    "zone_name" => "JVC",
                    "property_type" => "Residential",
                    "availability_status" => "available",
                    "construction_status" => "ready",
                    "bedrooms" => 2,
                    "bathrooms" => 1,
                    'project_id' => 5
                ]
            ],
            (object)[
                'id' => 11,
                'metadata' => [
                    'content' => 'Discover Caribbean-inspired waterfront living at Damac Lagoons - Costa Brava 2, where breathtaking views and world-class amenities create an unparalleled lifestyle. This three-bedroom, two-bathroom property spans 2,700 sq. ft. with a modern, spacious design. Ideal for families or investors, this residence offers serene surroundings and luxurious comforts in Yalayes.',
                    'property_name' => 'Sunset Villas',
                    'project_name' => 'Damac Lagoons - Costa Brava 2',
                    "zone_name" => "Yalayes",
                    "property_type" => "Residential",
                    "availability_status" => "available",
                    "construction_status" => "off-plan",
                    "bedrooms" => 3,
                    "bathrooms" => 2,
                    'project_id' => 1
                ]
            ],
            (object) [
                'id' => 12,
                'metadata' => [
                    'content' => 'Enjoy modern waterfront living in Damac Lagoons - Costa Brava 2. This beautiful two-bedroom, two-bathroom apartment offers 1,800 sq. ft. of space, located in a vibrant, community-focused area, with breathtaking views and premium amenities.',
                    'property_name' => 'Azure Residences',
                    'project_name' => 'Damac Lagoons - Costa Brava 2',
                    "zone_name" => "Yalayes",
                    "property_type" => "Residential",
                    "availability_status" => "available",
                    "construction_status" => "off-plan",
                    "bedrooms" => 2,
                    "bathrooms" => 2,
                    'project_id' => 1
                ]
            ],
            (object) [
                'id' => 13,
                'metadata' => [
                    'content' => 'This luxurious waterfront villa in Damac Lagoons - Costa Brava 2 offers four bedrooms, four bathrooms, and 3,500 sq. ft. of opulent living space. The villa is equipped with world-class amenities and spectacular views, perfect for those seeking the ultimate in luxury.',
                    'property_name' => 'Oceanfront Estates',
                    'project_name' => 'Damac Lagoons - Costa Brava 2',
                    "zone_name" => "Yalayes",
                    "property_type" => "Villa",
                    "availability_status" => "available",
                    "construction_status" => "off-plan",
                    "bedrooms" => 4,
                    "bathrooms" => 4,
                    'project_id' => 1
                ]
            ],
            (object) [
                'id' => 14,
                'metadata' => [
                    'content' => 'A spacious one-bedroom, one-bathroom apartment in Damac Lagoons - Costa Brava 2. The apartment spans 1,200 sq. ft., offering a modern design and access to premium amenities, ideal for young professionals or couples.',
                    'property_name' => 'Lagoon View Apartments',
                    'project_name' => 'Damac Lagoons - Costa Brava 2',
                    "zone_name" => "Yalayes",
                    "property_type" => "Apartment",
                    "availability_status" => "available",
                    "construction_status" => "off-plan",
                    "bedrooms" => 1,
                    "bathrooms" => 1,
                    'project_id' => 1
                ]
            ],
            (object)[
                'id' => 15,
                'metadata' => [
                    'content' => 'Indulge in an exclusive waterfront property in Damac Lagoons - Costa Brava 2. This three-bedroom, two-bathroom townhouse spans 2,200 sq. ft., providing a perfect combination of comfort and luxury, with access to a variety of top-tier amenities.',
                    'property_name' => 'Crystal Bay Townhouses',
                    'project_name' => 'Damac Lagoons - Costa Brava 2',
                    "zone_name" => "Yalayes",
                    "property_type" => "Townhouse",
                    "availability_status" => "available",
                    "construction_status" => "off-plan",
                    "bedrooms" => 3,
                    "bathrooms" => 2,
                    'project_id' => 1
                ]
            ],
            (object)[
                'id' => 16,
                'metadata' => [
                    'content' => 'Located near Mall of the Emirates, this thriving community offers a diverse mix of residences in Al Barsha 1, Dubai. It is surrounded by Golf city to the north, Emirates road to the south, Remraam to the east, and Oasis by Emaar to the west. The location is close to DAMAC Lagoons projects and major landmarks like Mall of the Emirates.',
                    'location_name' => 'Al Barsha 1',
                    'area_name' => 'Al Barsha 1',
                    'region' => 'Dubai',
                    'google_map_link' => 'https://maps.app.goo.gl/PPy26LFT9P7h2EQMA',
                    'north_side' => 'Golf city',
                    'south_side' => 'Emirates road',
                    'east_side' => 'Remraam',
                    'west_side' => 'Oasis by Emaar',
                    'landmark' => 'Close to DAMAC Lagoons projects',
                    'description' => 'Located near Mall of the Emirates, this thriving community offers a diverse mix of residences.',
                    'major_landmarks' => 'Mall of the Emirates',
                    'dld_area_id' => 2700,
                    'population' => 0,
                    'area_id' => 1
                ]
            ],
            (object)[
                'id' => 17,
                'metadata' => [
                    'content' => 'JVC is a thriving master development by Nakheel with a mix of villas, townhouses, and apartments. This vibrant community in Dubai offers a range of living options and is conveniently located near major highways. It is surrounded by JVC district 16 to the north, Mu\'allaqat blvd to the south, BinGhatti Ruby to the east, and Meatology Burgers JVC to the west. The major landmark is Circle Mall.',
                    'location_name' => 'JVC',
                    'area_name' => 'JVC',
                    'region' => 'Dubai',
                    'google_map_link' => 'https://maps.app.goo.gl/CaVsKVWTEipbDuSf7?g_st=com.google.maps.preview.copy',
                    'north_side' => 'JVC district 16',
                    'south_side' => 'Mu\'allaqat blvd',
                    'east_side' => 'BinGhatti Ruby',
                    'west_side' => 'Meatology Burgers JVC',
                    'landmark' => 'In the heart of Jumeirah Village Circle',
                    'description' => 'JVC is a thriving master development by Nakheel with a mix of villas, townhouses, and apartments. This vibrant community offers a range of living options and is conveniently located near major highways.',
                    'major_landmarks' => 'Circle Mall',
                    'dld_area_id' => 550,
                    'population' => 0,
                    'area_id' => 2
                ]
            ],
            (object)[
                'id' => 18,
                'metadata' => [
                    'content' => 'Experience modern living in Jumeriah Lake Towers (JLT), a vibrant community in Dubai with stunning lakes, parks, and world-class amenities. The location is bordered by First Al Khail street to the north, GRIP sports to the south, and Odeonbeds DMCC to the east, with Almas Tower being a major landmark in the area.',
                    'location_name' => 'Jumeriah Lake Towers',
                    'area_name' => 'Jumeriah Lake Towers',
                    'region' => 'Dubai',
                    'google_map_link' => 'https://maps.app.goo.gl/LwWsBbycfDPNRs1J7?g_st=com.google.maps.preview.copy',
                    'north_side' => 'First Al Khail st',
                    'south_side' => 'GRIP sports',
                    'east_side' => 'Odeonbeds DMCC',
                    'west_side' => '',
                    'landmark' => '',
                    'description' => 'Experience modern living in JLT, a vibrant community with stunning lakes, parks, and world-class amenities.',
                    'major_landmarks' => 'Almas Tower',
                    'dld_area_id' => 380,
                    'population' => 0,
                    'area_id' => 3
                ]
            ],
            (object)[
                'id' => 19,
                'metadata' => [
                    'content' => 'Experience luxurious beachside living in Marina Vista Tower 1 at Emaar Beachfront in Dubai. Enjoy fully-furnished apartments, world-class amenities, and hassle-free management with Ease. The location is surrounded by Palm Jumeirah to the north, EMAAR Palace Beach Residence to the south, and is near the Dubai Harbour landmark.',
                    'location_name' => 'Emaar Beachfront',
                    'area_name' => 'Emaar Beachfront',
                    'region' => 'Dubai',
                    'google_map_link' => 'https://maps.app.goo.gl/HirdVD3yKRSxqFRw5',
                    'north_side' => 'Palm Jumeirah',
                    'south_side' => 'EMAAR Palace Beach Residence',
                    'east_side' => '',
                    'west_side' => '',
                    'landmark' => '',
                    'description' => 'Experience luxurious beachside living in Marina Vista Tower 1 at Emaar Beachfront. Enjoy fully-furnished apartments, world-class amenities, and hassle-free management with Ease.',
                    'major_landmarks' => 'Dubai Harbour',
                    'dld_area_id' => 768,
                    'population' => 0,
                    'area_id' => 4
                ]
            ],
            (object)[
                'id' => 20,
                'metadata' => [
                    'content' => 'Sobha Hartland: A Luxurious Oasis in Dubai. Discover a vibrant community with stunning residences, lush greenery, and world-class amenities. The location is near Riyadh Avenue, offering a serene environment in a luxurious setting.',
                    'location_name' => 'Sobha Hartland',
                    'area_name' => 'Sobha Hartland',
                    'region' => 'Dubai',
                    'google_map_link' => 'https://maps.app.goo.gl/BuGKLCetir1WZu4U6',
                    'north_side' => '',
                    'south_side' => '',
                    'east_side' => '',
                    'west_side' => '',
                    'landmark' => '',
                    'description' => 'Sobha Hartland: A Luxurious Oasis in Dubai. Discover a vibrant community with stunning residences, lush greenery, and world-class amenities.',
                    'major_landmarks' => 'Riyadh Avenue',
                    'dld_area_id' => 540,
                    'population' => 0,
                    'area_id' => 5
                ]
            ]
        ];

        // $search = 'damac';

        // return $filtered = collect($records)->filter(function ($item) use ($search) {
        //     return preg_match("/$search/i", $item['metadata']['project_name']) && preg_match("/$search/i", $item['metadata']['property_name']); // Case-insensitive match
        // });

        // $records = [
        //     (object)[
        //         'id' => 1,
        //         'content' => 'Damac Lagoons - Costa Brava 2 is a residential project with 497 total units and 320 available units. The project was launched on 2021-02-01 and is expected to complete by 2025-07-29. The price range starts from AED 1,535,000 with a price per square meter of AED 1,200. The project size is 93,195.46 sq.mt. Experience Caribbean-inspired waterfront living with stunning views and world-class amenities.',
        //         'metadata' => [
        //             'project_name' => 'Damac Lagoons - Costa Brava 2',
        //             'content' => 'Damac Lagoons - Costa Brava 2 is a residential project with 497 total units and 320 available units. The project was launched on 2021-02-01 and is expected to complete by 2025-07-29. The price range starts from AED 1,535,000 with a price per square meter of AED 1,200. The project size is 93,195.46 sq.mt. Experience Caribbean-inspired waterfront living with stunning views and world-class amenities.',
        //         ]
        //     ],
        //     (object)[
        //         'id' => 2,
        //         'content' => 'Binghatti Amber is a residential project with 726 total units and 650 available units. The project was launched on 2023-01-01 and is expected to complete by 2027-11-01. The price range starts from AED 577,000 with a price per square meter of AED 1,500. The project size is 54,010.40 sq.mt. Experience elegant living in Binghatti Amber, offering stunning apartments with modern finishes and a prime location in JVC. Enjoy world-class amenities and a convenient lifestyle in this vibrant community.',
        //         'metadata' => [
        //             'project_name' => 'Binghatti Amber',
        //             'content' => 'Binghatti Amber is a residential project with 726 total units and 650 available units. The project was launched on 2023-01-01 and is expected to complete by 2027-11-01. The price range starts from AED 577,000 with a price per square meter of AED 1,500. The project size is 54,010.40 sq.mt. Experience elegant living in Binghatti Amber, offering stunning apartments with modern finishes and a prime location in JVC. Enjoy world-class amenities and a convenient lifestyle in this vibrant community.',
        //         ]
        //     ],
        //     (object)[
        //         'id' => 3,
        //         'content' => 'Diamondz By Danube is a residential project with 1,219 total units and 950 available units. The project was launched on 2024-01-01 and is expected to complete by 2024-12-31. The price range starts from AED 1.12 M with a price per square meter of AED 1,700. The project size is 84,117.24 sq.mt. Experience unparalleled luxury with stunning apartments and world-class amenities in this iconic 62-story tower.',
        //         'metadata' => [
        //             'project_name' => 'Diamondz By Danube',
        //             'content' => 'Diamondz By Danube is a residential project with 1,219 total units and 950 available units. The project was launched on 2024-01-01 and is expected to complete by 2024-12-31. The price range starts from AED 1.12 M with a price per square meter of AED 1,700. The project size is 84,117.24 sq.mt. Experience unparalleled luxury with stunning apartments and world-class amenities in this iconic 62-story tower.',
        //         ]
        //     ],
        //     (object)[
        //         'id' => 4,
        //         'content' => 'The Bristol Emaar Beachfront is a residential project with 229 total units and 130 available units. The project was launched on 2024-07-01 and is expected to complete by 2029-09-30. The price range starts from AED 2.4 M with a price per square meter of AED 800. The project size is 67,430.36 sq.mt. Experience stunning apartments with breathtaking sea views in this iconic tower.',
        //         'metadata' => [
        //             'project_name' => 'The Bristol Emaar Beachfront',
        //             'content' => 'The Bristol Emaar Beachfront is a residential project with 229 total units and 130 available units. The project was launched on 2024-07-01 and is expected to complete by 2029-09-30. The price range starts from AED 2.4 M with a price per square meter of AED 800. The project size is 67,430.36 sq.mt. Experience stunning apartments with breathtaking sea views in this iconic tower.',
        //         ]
        //     ],
        //     (object)[
        //         'id' => 5,
        //         'content' => 'Sobha Hartland - The Crest is a residential project with 1,518 total units and 1,002 available units. The project was launched on 2020-11-01 and is expected to complete by 2025-12-31. The price range starts from AED 1.1 Million with a price per square meter of AED 700. The project size is 121,044.96 sq.mt. Experience Caribbean-inspired luxury with stunning lagoon views and world-class amenities.',
        //         'metadata' => [
        //             'project_name' => 'Sobha Hartland - The Crest',
        //             'content' => 'Sobha Hartland - The Crest is a residential project with 1,518 total units and 1,002 available units. The project was launched on 2020-11-01 and is expected to complete by 2025-12-31. The price range starts from AED 1.1 Million with a price per square meter of AED 700. The project size is 121,044.96 sq.mt. Experience Caribbean-inspired luxury with stunning lagoon views and world-class amenities.',
        //         ]
        //     ]
        // ];

        foreach ($records as $record) {
            // Convert dates to Unix timestamps
            if (isset($record->metadata['launch_date'])) {
                $record->metadata['launch_date'] = strtotime($record->metadata['launch_date']);
            }
            if (isset($record->metadata['completion_date'])) {
                $record->metadata['completion_date'] = strtotime($record->metadata['completion_date']);
            }
        }

        foreach ($records as $record) {
            try {
                // Convert the record to a string format for embedding generation

                // Generate embedding vector from content
                $embedding = $this->embeddingService->generateEmbedding($record->metadata['content']);
                if (!$embedding) {
                    continue; // Skip if embedding generation failed
                }

                // Prepare metadata (can include relevant fields from the record)
                $metadata = $record->metadata;

                // Upsert vector to Pinecone
                $response = $this->embeddingService->upsertVector(
                    (string) $record->id,  // Use project_id as the vector ID
                    $embedding,
                    $metadata
                );
            } catch (\Exception $e) {
                return $e->getMessage();
                //return ['error' => "Error processing row ID: {$record->project_id} - {$e->getMessage()}"];
            }
        }

        return ['message' => 'Embeddings Inserted Successfully into Pinecone'];


        // Generate embedding from text
        // $vector = $this->embeddingService->generateEmbedding($validated['text']);

        // if (!$vector) {
        //     return response()->json(['error' => 'Embedding generation failed'], 500);
        // }

        // // Store vector in Pinecone
        // $response = $this->embeddingService->upsertVector(
        //     $validated['id'],
        //     $vector,
        //     $validated['metadata'] ?? []
        // );

        // return response()->json($response);
    }

    /**
     * Search similar vectors in Pinecone
     */
    public function search(Request $request)
    {



        $userMessage = 'ايه المشروع اللى مكن يحقق اكبر فايده اقتصاديه فى خلال خمس سنين';
        $topk = 5;

         $filters = $this->embeddingService->parseNaturalLanguageFilters($userMessage);
        $filter = $filters['filters'];
        $translatedQuery = $filters['translatedQuery'];
        $pincone_filter = count($filter) > 0 ? $filter : null;
        $userEmbedding = $this->embeddingService->generateEmbedding($translatedQuery);

        if (!$userEmbedding) {
            return response()->json(['error' => 'Embedding generation failed'], 500);
        }

        // Perform search in Pinecone
         $RelativeContext = $this->embeddingService->queryVector($userEmbedding, $pincone_filter, 15);
        $matches = $RelativeContext['matches'];
        $results = '';
        foreach ($matches as $match) {
            $results = $results . $match['metadata']['content'] . ',';
        }
        return response()->json($results);
    }
}
