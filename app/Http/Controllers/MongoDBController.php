<?php

namespace App\Http\Controllers;

use App\Services\MongoDBService;
use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use MongoDB\Laravel\Eloquent\Model;

use function Laravel\Prompts\select;

class MongoDBController extends Controller
{
    protected $openAIService;


    public function __construct(MongoDBService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function store()
    {
        set_time_limit(300);
        // $records = [
        //     [
        //         'content' => 'Damac Lagoons - Costa Brava 2 is a luxurious residential project by DAMAC, located near Hessa Street, Dubai. Offering 497 total units with 320 still available, this development brings Caribbean-inspired waterfront living. Launched on February 1, 2021, and expected to be completed by July 29, 2025, it provides world-class amenities and breathtaking views. Prices start from AED 1,535,000, with a price per square meter of AED 1,200. Conveniently positioned near major roads like Mohammed Bin Zayed Road, Emirates Road, and Al Khail Road, it offers seamless connectivity. Landmarks nearby include DAMAC Lagoons projects.',

        //         'project_name' => 'Damac Lagoons - Costa Brava 2',
        //         'developer_name' => 'DAMAC',
        //         'location' => 'Near Hessa Street, Dubai',
        //         'total_units' => 497,
        //         'available_units' => 320,
        //         'launch_date' => '2021-02-01',
        //         'completion_date' => '2025-07-29',
        //         'price_range' => 'Starting From AED 1,535,000',
        //         'price_per_sqm' => 1200,
        //         'project_size' => '93,195.46 sq.mt',
        //         'google_map_link' => 'https://maps.app.goo.gl/PPy26LFT9P7h2EQMA',
        //         'landmark' => 'Close to DAMAC Lagoons projects',
        //         'location_id' => 1,
        //         'project_id' => 1

        //     ],
        //     [
        //         'content' => 'Binghatti Amber is a residential project located in the heart of Jumeirah Village Circle (JVC). Developed by Bin Ghatti, it comprises 726 units, with 650 still available. Launched on January 1, 2023, and set to complete by November 1, 2027, this project offers modern apartments with high-end finishes. Prices start from AED 577,000, with a price per square meter of AED 1,500. The community is known for its family-friendly environment, parks, schools, and easy access to Dubai’s main attractions.',

        //         'project_name' => 'Binghatti Amber',
        //         'developer_name' => 'Bin Ghatti',
        //         'location' => 'JVC, Dubai',
        //         'total_units' => 726,
        //         'available_units' => 650,
        //         'launch_date' => '2023-01-01',
        //         'completion_date' => '2027-11-01',
        //         'price_range' => 'Starting From AED 577,000',
        //         'price_per_sqm' => 1500,
        //         'project_size' => '54,010.40 sq.mt',
        //         'google_map_link' => 'https://maps.app.goo.gl/CaVsKVWTEipbDuSf7',
        //         'location_id' => 2,
        //         'project_id' => 2

        //     ],
        //     [
        //         'content' => 'Diamondz By Danube is an opulent residential tower rising 62 stories high, developed by Danube. Located near First Al Khail Street, it offers 1,219 total units, with 950 still available. Launched on January 1, 2024, and expected to complete by December 31, 2024, this project is synonymous with luxury living. Prices start from AED 1.12M, with a price per square meter of AED 1,700.',

        //         'project_name' => 'Diamondz By Danube',
        //         'developer_name' => 'Danube',
        //         'location' => 'Near First Al Khail St, Dubai',
        //         'total_units' => 1219,
        //         'available_units' => 950,
        //         'launch_date' => '2024-01-01',
        //         'completion_date' => '2024-12-31',
        //         'price_range' => 'Starting From AED 1.12 M',
        //         'price_per_sqm' => 1700,
        //         'project_size' => '84,117.24 sq.mt',
        //         'google_map_link' => 'https://maps.app.goo.gl/LwWsBbycfDPNRs1J7',
        //         'location_id' => 3,
        //         'project_id' => 3

        //     ],
        //     [
        //         'content' => 'The Bristol Emaar Beachfront is an iconic seaside development by Emaar, featuring 229 units, with 130 available. Launched on July 1, 2024, and set for completion by September 30, 2029, it offers breathtaking sea views, upscale amenities, and a prime location near Palm Jumeirah. Prices start from AED 2.4M, with a price per square meter of AED 800.',

        //         'project_name' => 'The Bristol Emaar Beachfront',
        //         'developer_name' => 'Emaar',
        //         'location' => 'Emaar Beachfront, Dubai',
        //         'total_units' => 229,
        //         'available_units' => 130,
        //         'launch_date' => '2024-07-01',
        //         'completion_date' => '2029-09-30',
        //         'price_range' => 'Starting From AED 2.4 M',
        //         'price_per_sqm' => 800,
        //         'project_size' => '67,430.36 sq.mt',
        //         'google_map_link' => 'https://maps.app.goo.gl/HirdVD3yKRSxqFRw5',
        //         'location_id' => 4,
        //         'project_id' => 4

        //     ],
        //     [
        //         'content' => 'Sobha Hartland - The Crest is a grand development by Sobha, offering 1,518 units, with 1,002 still available. Launched on November 1, 2020, and planned for completion by December 31, 2025, this project is designed for luxury waterfront living. Prices start from AED 1.1M, with a price per square meter of AED 700.',

        //         'project_name' => 'Sobha Hartland - The Crest',
        //         'developer_name' => 'Sobha',
        //         'location' => 'Sobha Hartland, Dubai',
        //         'total_units' => 1518,
        //         'available_units' => 1002,
        //         'launch_date' => '2020-11-01',
        //         'completion_date' => '2025-12-31',
        //         'price_range' => 'Starting From AED 1.1 Million',
        //         'price_per_sqm' => 700,
        //         'project_size' => '121,044.96 sq.mt',
        //         'google_map_link' => 'https://maps.app.goo.gl/BuGKLCetir1WZu4U6',
        //         'location_id' => 5,
        //         'project_id' => 5

        //     ],
        //     [
        //         'content' => 'Discover Caribbean-inspired waterfront living at Damac Lagoons - Costa Brava 2, where breathtaking views and world-class amenities create an unparalleled lifestyle. This three-bedroom, two-bathroom property spans 2,700 sq. ft. with a modern, spacious design. Ideal for families or investors, this residence offers serene surroundings and luxurious comforts in Yalayes.',

        //         'property_name' => 'Damac Lagoons - Costa Brava 2',
        //         'project_name' => 'Damac Lagoons - Costa Brava 2',
        //         "zone_name" => "Yalayes",
        //         "property_type" => "Residential",
        //         "availability_status" => "available",
        //         "construction_status" => "off-plan",
        //         "bedrooms" => 3,
        //         "bathrooms" => 2,
        //         'project_id' => 1,


        //     ],
        //     [
        //         'content' => 'Experience elegant living at Binghatti Amber, featuring modern apartments with stunning finishes in the heart of JVC. This two-bedroom, one-bathroom residence offers 550 sq. ft. of stylish living space, making it an excellent choice for city dwellers seeking contemporary comfort.',

        //         'property_name' => 'Binghatti Amber',
        //         'project_name' => 'Binghatti Amber',
        //         "zone_name" => "JVC",
        //         "property_type" => "Residential",
        //         "availability_status" => "available",
        //         "construction_status" => "under-construction",
        //         "bedrooms" => 2,
        //         "bathrooms" => 1,
        //         'project_id' => 2

        //     ],
        //     [
        //         'content' => 'Discover unparalleled luxury at Diamondz By Danube, a 62-story tower offering world-class amenities and breathtaking apartments. This fully furnished one-bedroom, one-bathroom residence spans 380 sq. ft., providing stylish and comfortable urban living in Barsha South.',

        //         'property_name' => 'Diamondz By Danube',
        //         'project_name' => 'Diamondz By Danube',
        //         "zone_name" => "Barsha South",
        //         "property_type" => "Residential",
        //         "availability_status" => "available",
        //         "construction_status" => "off-plan",
        //         "bedrooms" => 1,
        //         "bathrooms" => 1,
        //         'project_id' => 3

        //     ],
        //     [
        //         'content' => 'The Bristol Emaar Beachfront offers stunning sea-view apartments in an iconic tower. This one-bedroom, one-bathroom unit spans 768 sq. ft., blending contemporary elegance with prime beachfront living in JVC.',

        //         'property_name' => 'The Bristol Emaar Beachfront',
        //         'project_name' => 'The Bristol Emaar Beachfront',
        //         "zone_name" => "JVC",
        //         "property_type" => "Residential",
        //         "availability_status" => "available",
        //         "construction_status" => "under-construction",
        //         "bedrooms" => 1,
        //         "bathrooms" => 1,
        //         'project_id' => 4

        //     ],
        //     [
        //         'content' => 'Sobha Hartland - The Crest presents Caribbean-inspired luxury with breathtaking lagoon views and world-class amenities. This two-bedroom, one-bathroom apartment spans 540 sq. ft. and offers a serene yet vibrant lifestyle in JVC.',

        //         'property_name' => 'Sobha Hartland - The Crest',
        //         'project_name' => 'Sobha Hartland - The Crest',
        //         "zone_name" => "JVC",
        //         "property_type" => "Residential",
        //         "availability_status" => "available",
        //         "construction_status" => "ready",
        //         "bedrooms" => 2,
        //         "bathrooms" => 1,
        //         'project_id' => 5

        //     ],
        //     [
        //         'content' => 'Discover Caribbean-inspired waterfront living at Damac Lagoons - Costa Brava 2, where breathtaking views and world-class amenities create an unparalleled lifestyle. This three-bedroom, two-bathroom property spans 2,700 sq. ft. with a modern, spacious design. Ideal for families or investors, this residence offers serene surroundings and luxurious comforts in Yalayes.',

        //         'property_name' => 'Sunset Villas',
        //         'project_name' => 'Damac Lagoons - Costa Brava 2',
        //         "zone_name" => "Yalayes",
        //         "property_type" => "Residential",
        //         "availability_status" => "available",
        //         "construction_status" => "off-plan",
        //         "bedrooms" => 3,
        //         "bathrooms" => 2,
        //         'project_id' => 1

        //     ],
        //     [
        //         'content' => 'Enjoy modern waterfront living in Damac Lagoons - Costa Brava 2. This beautiful two-bedroom, two-bathroom apartment offers 1,800 sq. ft. of space, located in a vibrant, community-focused area, with breathtaking views and premium amenities.',

        //         'property_name' => 'Azure Residences',
        //         'project_name' => 'Damac Lagoons - Costa Brava 2',
        //         "zone_name" => "Yalayes",
        //         "property_type" => "Residential",
        //         "availability_status" => "available",
        //         "construction_status" => "off-plan",
        //         "bedrooms" => 2,
        //         "bathrooms" => 2,
        //         'project_id' => 1

        //     ],
        //     [
        //         'content' => 'This luxurious waterfront villa in Damac Lagoons - Costa Brava 2 offers four bedrooms, four bathrooms, and 3,500 sq. ft. of opulent living space. The villa is equipped with world-class amenities and spectacular views, perfect for those seeking the ultimate in luxury.',

        //         'property_name' => 'Oceanfront Estates',
        //         'project_name' => 'Damac Lagoons - Costa Brava 2',
        //         "zone_name" => "Yalayes",
        //         "property_type" => "Villa",
        //         "availability_status" => "available",
        //         "construction_status" => "off-plan",
        //         "bedrooms" => 4,
        //         "bathrooms" => 4,
        //         'project_id' => 1

        //     ],
        //     [
        //         'content' => 'A spacious one-bedroom, one-bathroom apartment in Damac Lagoons - Costa Brava 2. The apartment spans 1,200 sq. ft., offering a modern design and access to premium amenities, ideal for young professionals or couples.',

        //         'property_name' => 'Lagoon View Apartments',
        //         'project_name' => 'Damac Lagoons - Costa Brava 2',
        //         "zone_name" => "Yalayes",
        //         "property_type" => "Apartment",
        //         "availability_status" => "available",
        //         "construction_status" => "off-plan",
        //         "bedrooms" => 1,
        //         "bathrooms" => 1,
        //         'project_id' => 1

        //     ],
        //     [
        //         'content' => 'Indulge in an exclusive waterfront property in Damac Lagoons - Costa Brava 2. This three-bedroom, two-bathroom townhouse spans 2,200 sq. ft., providing a perfect combination of comfort and luxury, with access to a variety of top-tier amenities.',

        //         'property_name' => 'Crystal Bay Townhouses',
        //         'project_name' => 'Damac Lagoons - Costa Brava 2',
        //         "zone_name" => "Yalayes",
        //         "property_type" => "Townhouse",
        //         "availability_status" => "available",
        //         "construction_status" => "off-plan",
        //         "bedrooms" => 3,
        //         "bathrooms" => 2,
        //         'project_id' => 1

        //     ],
        //     [
        //         'content' => 'Located near Mall of the Emirates, this thriving community offers a diverse mix of residences in Al Barsha 1, Dubai. It is surrounded by Golf city to the north, Emirates road to the south, Remraam to the east, and Oasis by Emaar to the west. The location is close to DAMAC Lagoons projects and major landmarks like Mall of the Emirates.',

        //         'location_name' => 'Al Barsha 1',
        //         'area_name' => 'Al Barsha 1',
        //         'region' => 'Dubai',
        //         'google_map_link' => 'https://maps.app.goo.gl/PPy26LFT9P7h2EQMA',
        //         'north_side' => 'Golf city',
        //         'south_side' => 'Emirates road',
        //         'east_side' => 'Remraam',
        //         'west_side' => 'Oasis by Emaar',
        //         'landmark' => 'Close to DAMAC Lagoons projects',
        //         'description' => 'Located near Mall of the Emirates, this thriving community offers a diverse mix of residences.',
        //         'major_landmarks' => 'Mall of the Emirates',
        //         'dld_area_id' => 2700,
        //         'population' => 0,
        //         'area_id' => 1

        //     ],
        //     [
        //         'content' => 'JVC is a thriving master development by Nakheel with a mix of villas, townhouses, and apartments. This vibrant community in Dubai offers a range of living options and is conveniently located near major highways. It is surrounded by JVC district 16 to the north, Mu\'allaqat blvd to the south, BinGhatti Ruby to the east, and Meatology Burgers JVC to the west. The major landmark is Circle Mall.',

        //         'location_name' => 'JVC',
        //         'area_name' => 'JVC',
        //         'region' => 'Dubai',
        //         'google_map_link' => 'https://maps.app.goo.gl/CaVsKVWTEipbDuSf7?g_st=com.google.maps.preview.copy',
        //         'north_side' => 'JVC district 16',
        //         'south_side' => 'Mu\'allaqat blvd',
        //         'east_side' => 'BinGhatti Ruby',
        //         'west_side' => 'Meatology Burgers JVC',
        //         'landmark' => 'In the heart of Jumeirah Village Circle',
        //         'description' => 'JVC is a thriving master development by Nakheel with a mix of villas, townhouses, and apartments. This vibrant community offers a range of living options and is conveniently located near major highways.',
        //         'major_landmarks' => 'Circle Mall',
        //         'dld_area_id' => 550,
        //         'population' => 0,
        //         'area_id' => 2

        //     ],
        //     [
        //         'content' => 'Experience modern living in Jumeriah Lake Towers (JLT), a vibrant community in Dubai with stunning lakes, parks, and world-class amenities. The location is bordered by First Al Khail street to the north, GRIP sports to the south, and Odeonbeds DMCC to the east, with Almas Tower being a major landmark in the area.',

        //         'location_name' => 'Jumeriah Lake Towers',
        //         'area_name' => 'Jumeriah Lake Towers',
        //         'region' => 'Dubai',
        //         'google_map_link' => 'https://maps.app.goo.gl/LwWsBbycfDPNRs1J7?g_st=com.google.maps.preview.copy',
        //         'north_side' => 'First Al Khail st',
        //         'south_side' => 'GRIP sports',
        //         'east_side' => 'Odeonbeds DMCC',
        //         'west_side' => '',
        //         'landmark' => '',
        //         'description' => 'Experience modern living in JLT, a vibrant community with stunning lakes, parks, and world-class amenities.',
        //         'major_landmarks' => 'Almas Tower',
        //         'dld_area_id' => 380,
        //         'population' => 0,
        //         'area_id' => 3

        //     ],
        //     [
        //         'content' => 'Experience luxurious beachside living in Marina Vista Tower 1 at Emaar Beachfront in Dubai. Enjoy fully-furnished apartments, world-class amenities, and hassle-free management with Ease. The location is surrounded by Palm Jumeirah to the north, EMAAR Palace Beach Residence to the south, and is near the Dubai Harbour landmark.',

        //         'location_name' => 'Emaar Beachfront',
        //         'area_name' => 'Emaar Beachfront',
        //         'region' => 'Dubai',
        //         'google_map_link' => 'https://maps.app.goo.gl/HirdVD3yKRSxqFRw5',
        //         'north_side' => 'Palm Jumeirah',
        //         'south_side' => 'EMAAR Palace Beach Residence',
        //         'east_side' => '',
        //         'west_side' => '',
        //         'landmark' => '',
        //         'description' => 'Experience luxurious beachside living in Marina Vista Tower 1 at Emaar Beachfront. Enjoy fully-furnished apartments, world-class amenities, and hassle-free management with Ease.',
        //         'major_landmarks' => 'Dubai Harbour',
        //         'dld_area_id' => 768,
        //         'population' => 0,
        //         'area_id' => 4

        //     ],
        //     [
        //         'content' => 'Sobha Hartland: A Luxurious Oasis in Dubai. Discover a vibrant community with stunning residences, lush greenery, and world-class amenities. The location is near Riyadh Avenue, offering a serene environment in a luxurious setting.',
        //         'location_name' => 'Sobha Hartland',
        //         'area_name' => 'Sobha Hartland',
        //         'region' => 'Dubai',
        //         'google_map_link' => 'https://maps.app.goo.gl/BuGKLCetir1WZu4U6',
        //         'north_side' => '',
        //         'south_side' => '',
        //         'east_side' => '',
        //         'west_side' => '',
        //         'landmark' => '',
        //         'description' => 'Sobha Hartland: A Luxurious Oasis in Dubai. Discover a vibrant community with stunning residences, lush greenery, and world-class amenities.',
        //         'major_landmarks' => 'Riyadh Avenue',
        //         'dld_area_id' => 540,
        //         'population' => 0,
        //         'area_id' => 5

        //     ]
        // ];

        // $convertedRecords = array_map(function ($record) {
        //     if (isset($record['launch_date'])) {
        //         $record['launch_date'] = strtotime($record['launch_date']);
        //     }
        //     if (isset($record['completion_date'])) {
        //         $record['completion_date'] = strtotime($record['completion_date']);
        //     }
        //     return $record;
        // }, $records);

        // $convertedRecords;

        // $projects = DB::table('projects')
        //     ->leftJoin('locations', 'projects.location_id', '=', 'locations.location_id')
        //     ->leftJoin('areas', 'locations.area_id', '=', 'areas.area_id')
        //     ->leftJoin('dld_areas', 'areas.dld_area_id', '=', 'dld_areas.dld_area_id')
        //     ->leftJoin('developers', 'projects.developer_id', '=', 'developers.developer_id')
        //     ->select(
        //         'projects.*',
        //         'locations.description as location_description',
        //         'locations.longitude',
        //         'locations.latitude',
        //         'locations.landmark',
        //         'locations.west_side',
        //         'locations.east_side',
        //         'locations.south_side',
        //         'locations.north_side',
        //         'locations.google_map_link',
        //         'areas.area_name',
        //         'areas.region',
        //         'dld_areas.dld_area_name',
        //         'developers.name as developer_name'
        //     )->distinct('project_name')
        //     ->get();


        //     foreach ($projects as $project) {
        //         $content = '';
        //         foreach ($project as $key => $value) {
        //             if (in_array($key, [
        //                 "developer_id",
        //                 "location_id",
        //                 "project_size_sqmt",
        //                 "min_price_range_SQ",
        //                 "starting_price_range",
        //                 "location_description",
        //                 "table_name",
        //                 "created_at",
        //                 "updated_at",
        //                 "deleted_at"
        //             ])) {
        //                 continue;
        //             }

        //             $content .= $key . ':' . $value . ',';
        //         }
        //         $project->content = $content;
        //         $project->embedding = $this->openAIService->generateEmbedding($content);
        //         $project->area_name = $project->area_name . '/' . $project->region;
        //         $project->table_name = 'projects';

        //         if (isset($project->launch_date)) {
        //             $project->launch_date = strtotime($project->launch_date);
        //         }
        //         if (isset($project->completion_date)) {
        //             $project->completion_date = strtotime($project->completion_date);
        //         }

        //         $project->starting_price_range =  (int) filter_var($project->price_range, FILTER_SANITIZE_NUMBER_INT);
        //         $project->min_price_range_SQ =  (int) filter_var($project->price_range_SQ, FILTER_SANITIZE_NUMBER_INT);
        //         $project->project_size_sqmt =  (float) filter_var($project->project_size, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        //         // preg_match_all('/\d+/', $project->price_range_SQ, $matches);
        //     }

        //     $projects = collect($projects)->map(function ($item) {

        //         return collect($item)->except([
        //             'created_at',
        //             'updated_at',
        //             'deleted_at',
        //             'location_id',
        //             'developer_id',
        //             'project_size',
        //             'price_range_SQ',
        //             'price_range',
        //             'google_map_link',
        //             'north_side',
        //             'south_side',
        //             'east_side',
        //             'west_side',
        //             'location_description',
        //             'description',
        //             'region'


        //             // 'longitude',
        //             // 'latitude',
        //         ]);
        //     });

        //   return $projects;

        //     $realestate_ai_data = $projects->toArray();
        //     // Insert into MongoDB
        //     foreach ($realestate_ai_data as $record) {
        //         DB::connection('mongodb')->table('realestate_ai_test')->insert($record);
        //     }


        //     return response()->json([
        //         'message' => 'Records inserted successfully!',
        //         //'data' => $properties
        //     ]);


        // $areas = DB::table('areas')
        //     ->join('dld_areas', 'areas.dld_area_id', '=', 'dld_areas.dld_area_id')
        //     ->select(
        //         'areas.*',
        //         'dld_areas.dld_area_name',

        //     )->distinct('area_name')
        //     ->get();

        // foreach ($projects as $project) {
        //     $project->table_name = 'areas';



        //     $project->starting_price_range =  (int) filter_var($project->price_range, FILTER_SANITIZE_NUMBER_INT);
        //     $project->min_price_range_SQ =  (int) filter_var($project->price_range_SQ, FILTER_SANITIZE_NUMBER_INT);
        //     $project->project_size_sqmt =  (float) filter_var($project->project_size, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        //     // preg_match_all('/\d+/', $project->price_range_SQ, $matches);


        //     // //$numbers = array_map('intval', $matches[0]);
        //     // $project->min_price_range_SQ = $matches;
        //     // $project->max_price_range_SQ = $matches;
        // }



        // foreach ($areas as $area) {
        //     $content = '';
        //     foreach ($area as $key => $value) {
        //         if (in_array($key, ["area_id", "dld_area_id", "created_at", "updated_at", "deleted_at"])) {
        //             continue;
        //         }

        //         $content .= $key . ':' . $value . ',';
        //     }
        //     $area->content = $content;
        //     $area->area_name = $area->area_name . '/' . $area->region;
        // }

        // $areas = collect($areas)->map(function ($item) {

        //     return collect($item)->except([
        //         'created_at',
        //         'updated_at',
        //         'deleted_at',
        //         // 'longitude',
        //         // 'latitude',
        //         'dld_area_id',
        //         'area_id'
        //     ]);
        // });


        // return $areas;


          return $properties = DB::table('properties')
            ->leftJoin('projects', 'properties.project_id', '=', 'projects.project_id')
            ->leftJoin('developers', 'projects.developer_id', '=', 'developers.developer_id')
           
            //->leftJoin('addresses', 'properties.address_id', '=', 'addresses.address_id')
            ->leftJoin('locations', 'projects.location_id', '=', 'locations.location_id')
            ->leftJoin('areas', 'locations.area_id', '=', 'areas.area_id')
            ->leftJoin('buildings', 'properties.building_id', '=', 'buildings.building_id')
            ->leftJoin('property_types', 'properties.property_type_id', '=', 'property_types.id')
            ->leftJoin('property_subtypes', 'properties.property_subtype_id', '=', 'property_subtypes.id')
            ->select(
                'properties.*',
                'developers.name as developer_name',
                'projects.project_name',

                //'addresses.address',
                'buildings.building_name',
                'property_types.name as property_type',
                'property_subtypes.name as property_subtype',
                'locations.landmark',
                'areas.area_name as area_name',
                'areas.region as region'

            )
            // ->groupBy('property_name')
            // ->orderBy('property_id','asc')
            // ->skip(0)
            // ->take(522)
            ->get();

           $property_embeddings =  DB::table('embeddings')->whereNotNull('property_id')->get();
                $property_embeddings = collect($property_embeddings);
        foreach ($properties as $property) {
            $property->table_name = 'properties';
            // $project->starting_price_range =  (int) filter_var($project->price_range, FILTER_SANITIZE_NUMBER_INT);
            // $project->min_price_range_SQ =  (int) filter_var($project->price_range_SQ, FILTER_SANITIZE_NUMBER_INT);
            $property->plot_size =  (float) filter_var($property->plot_size, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $property->bua_size =  (float) filter_var($property->bua_size, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $property->property_price =  (float) filter_var($property->price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $property->property_size =  (float) filter_var($property->size, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            $content = '';
            foreach ($property as $key => $value) {
                if (in_array($key, [
                    //"project_id",
                    "developer_id",
                    "location_id",
                    "building_id",
                    "address_id",
                    "dld_barcode",
                    "dld_permit_number",
                    "agent_license",
                    "broker_license",
                    "property_type_id",
                    "property_subtype_id",
                    "table_name",
                    "created_at",
                    "updated_at",
                    "deleted_at",
                    
                ])) {
                    continue;
                }

                $content .= $key . ':' . $value . ',';
            }
            $property->content = $content;
            $embedding = $property_embeddings->where('property_id',$property->property_id)->first();
            $vector = json_decode($embedding->embedding,true);
            $property->embedding = $vector;
            //$property->embedding = $this->openAIService->generateEmbedding($content);
            $property->area_name = $property->area_name .'/'.$property->region;

            // preg_match_all('/\d+/', $project->price_range_SQ, $matches);


            // //$numbers = array_map('intval', $matches[0]);
            // $project->min_price_range_SQ = $matches;
            // $project->max_price_range_SQ = $matches;
        }


         $properties = collect($properties)->map(function ($item) {
            return collect($item)->except([
                'created_at',
                'updated_at',
                'deleted_at',
                'dld_permit_number',
                'dld_barcode',
                'project_id',
                'property_type_id',
                'property_subtype_id',
                'parking_spaces',
                //'reference_listed',
                'broker_license',
                'agent_license',
                'building_id',
                'building_id',
                'address_id',
                'region'
            ]);
        });

        //$realestate_ai_data = $projects->concat($properties);

        $realestate_ai_data = $properties->toArray();
        // Insert into MongoDB
        foreach ($realestate_ai_data as $record) {
            DB::connection('mongodb')->table('realestate')->insert($record);
        }


        return response()->json([
            'message' => 'Records inserted successfully!',
            //'data' => $properties
        ]);
    }

    public function search(Request $request)
    {

        $query = 'رشحلى افضل شقه من حيث الفرص الاستثماريه خلال خمس سنوات فى منطقة دبى';

        // $results = DB::table('projects')->get();
        // return $results;

        // Generate MongoDB pipeline dynamically using OpenAI
        $res = $this->openAIService->generatePipeline($query);
        // Ensure `$match` is an actual array, not a string
        $match = $res['pipeline'];
        if (is_string($match)) {
            // $cleanedString = str_replace('json', '', $match);
            // $jsonString = trim($cleanedString, "```");

            // $match = json_decode($jsonString, true); // Convert JSON string to PHP array
            // Remove ```json and ``` from the string
            //$jsonString = preg_replace('/^```json|\n```$/', '', trim($match));
            $jsonString = preg_replace('/^```json\s*|\s*```$/', '', trim($match));


            // Decode JSON string to PHP array
            $match = json_decode($jsonString, true);
        }
        $pipeline = $match;
        // // Validate pipeline structure
        // if (!is_array($match)) {
        //     return response()->json(['error' => 'Invalid pipeline format generated'], 400);
        // }

        // // Ensure $match is a valid aggregation stage
        // if (!isset($match['$match'])) {
        //     $match = ['$match' => $match]; // Wrap it in a $match stage if necessary
        // }

        // // Create the pipeline array
        // // $pipeline = [$match];
        // // Execute the aggregation query in MongoDB
        // // $results = DB::connection('mongodb')
        // //     ->getMongoDB()
        // //     ->selectCollection('realestate')
        // //     ->aggregate($pipeline)
        // //     ->toArray();
        // $pipeline = [
        //     [
        //         '$match' => [
        //             'table_name' => 'projects',
        //             'project_completion_date' => [
        //                 '$lte' => [
        //                     '$add' => ['$project_launch_date', 157680000]
        //                 ]
        //             ]
        //         ]
        //     ],
        //     [
        //         '$sort' => [
        //             'starting_price_range' => -1
        //         ]
        //     ]
        // ];
        $relative_context = '';
        $pipeline_result = '';
        if (!empty($pipeline)) {
            $results = DB::connection('mongodb')
                ->getMongoDB()
                ->selectCollection('realestate')
                ->aggregate($pipeline)
                ->toArray();

            if (!empty($results)) {
                $keysToExclude = ["_id", "embedding"];

                $pipeline_result = array_map(function ($item) use ($keysToExclude) {
                    // Convert BSONDocument to an array
                    $itemArray = (array) $item;

                    // Remove unwanted keys
                    return array_diff_key($itemArray, array_flip($keysToExclude));
                }, iterator_to_array($results)); // Convert MongoDB cursor to array



            } else {
                $pipeline_result = $this->mongoVectorSearch($res['translated_question']);
            }
        } else {
            //$relative_context = 'No Relative Data';
            $pipeline_result = $this->mongoVectorSearch($res['translated_question']);
        }

        $matches = $pipeline_result;
        $relative_context = [];
        foreach ($matches as $match) {
            $relative_context[] = $match['content'];
        }
        $relative_context = implode("\n", $relative_context);


        //return $relative_context;

        // $pipeline_result = collect($pipeline_result)->map(function ($item) {

        //     return collect($item)->except([
        //         '_id',
        //         // 'longitude',
        //         // 'latitude',
        //     ]);
        // });

        return response()->json([
            'prompt' => $res['prompt'],
            'query' => $query,
            'translated_query' => $res['translated_question'],
            'llm_pipeline' => $res['pipeline'],
            'pipeline' => $pipeline,
            'results' => $pipeline_result
        ]);
    }

    //     public function search(Request $request)
    // {
    //     // User query for filters and sorting
    //     $query = 'عايز اعرف المشاريع اللى فيها شقق متاحه تتعدى 500 والمشاريع اللى اسعارها بتبدأ من 1000';

    //     // Generate MongoDB pipeline dynamically using OpenAI
    //     $match = $this->openAIService->generatePipeline($query);

    //     // Ensure `$match` is an actual array, not a string
    //     if (is_string($match)) {
    //         $match = json_decode(trim(str_replace('json', '', $match), "```"), true);
    //     }

    //     // Validate pipeline structure
    //     if (!is_array($match)) {
    //         return response()->json(['error' => 'Invalid pipeline format generated'], 400);
    //     }

    //     // Execute the aggregation query in MongoDB
    //     $pipeline_result = DB::connection('mongodb')
    //         ->getMongoDB()
    //         ->selectCollection('realestate')
    //         ->aggregate($match)
    //         ->toArray();

    //     // Extract `content` efficiently using `array_column()`
    //     $relative_context = implode("\n", array_column($pipeline_result, 'content'));

    //     return response()->json([
    //         'query' => $query,
    //         'pipeline' => $match,
    //         'results' => $relative_context
    //     ]);
    // }


    public function DeleteAllDocuments()
    {

        DB::connection('mongodb')->table('realestate')->where('property_id', '>=', 1)->delete();
        return response()->json([
            'message' => 'Records deleted successfully!',

        ]);
    }

    public function RenameCollection()
    {

        DB::connection('mongodb')->getMongoDB()
            ->selectCollection('realestatev')->rename('realestate_ai_test');
        return response()->json([
            'message' => 'Collection Renamed successfully!',

        ]);
    }

    public function CountAllDocuments()
    {
        $count = DB::connection('mongodb')->table('realestate')->count();
        return response()->json([
            'count' => $count,

        ]);
    }

    public function backupEmbeddingOnMysql()
    {

        $results = DB::connection('mongodb')
            ->table('realestate') // Use collection() instead of table()
            ->where('table_name', 'properties')
            ->get(); // No need for select('*')

        foreach ($results as $result) {
            DB::table('embeddings')->insert([
                'project_id'  => null, // Directly access the object properties
                'property_id' => $result->property_id,
                'embedding'   => json_encode($result->embedding),
            ]);
        }
        return response()->json([
            'message' => 'Records stored successfully!',
            //'data'  => $result[0]->embedding
        ]);
    }

    public function retreiveData(Request $request)
    {
        $match = $request->match;
        if (is_string($match)) {
            $cleanedString = str_replace('json', '', $match);
            $jsonString = trim($cleanedString, "```");

            $match = json_decode($jsonString, true); // Convert JSON string to PHP array
        }
        $pipeline = $match;
        // // Validate pipeline structure
        // if (!is_array($match)) {
        //     return response()->json(['error' => 'Invalid pipeline format generated'], 400);
        // }

        // // Ensure $match is a valid aggregation stage
        // if (!isset($match['$match'])) {
        //     $match = ['$match' => $match]; // Wrap it in a $match stage if necessary
        // }

        // // Create the pipeline array
        // // $pipeline = [$match];
        // // Execute the aggregation query in MongoDB
        // // $results = DB::connection('mongodb')
        // //     ->getMongoDB()
        // //     ->selectCollection('realestate')
        // //     ->aggregate($pipeline)
        // //     ->toArray();

        $pipeline_result = DB::connection('mongodb')
            ->getMongoDB()
            ->selectCollection('realestate')
            ->aggregate($pipeline)
            ->toArray();

        $matches = $pipeline_result;
        $relative_context = [];
        foreach ($matches as $match) {
            $relative_context[] = $match['content'];
        }
        $relative_context = implode("\n", $relative_context);


        //return $relative_context;

        return response()->json([
            'pipeline' => $pipeline,
            'results' => $relative_context
        ]);
    }

    public function createVectorSearchIndex()
    {

        DB::connection('mongodb')->getMongoDB()->selectCollection('realestate_ai_test')->createSearchIndex([
            'name' => 'vector_index', // Index name
            'definition' => [
                'type' => 'vectorSearch',
                'fields' => [
                    [
                        'type' => 'vector',
                        'path' => 'embedding',
                        'numDimensions' => 1536,
                        'similarity' => 'cosine',
                        'quantization' => 'scalar',
                    ]
                ]
            ]
        ]);

        echo "Vector search index created successfully!";
    }

    public function mongoVectorSearch($userQuery)
    {
        //$userQuery = 'Tell me about property A303 (Ocean Pearl by SD)';
        $userEmbedding = $this->openAIService->generateEmbedding($userQuery);
        $searchResults = DB::connection('mongodb')->getMongoDB()->selectCollection('realestate')->aggregate([
            [
                '$vectorSearch' => [
                    'index' => 'vector_index1',
                    'path' => 'embedding',
                    'queryVector' => $userEmbedding,
                    'numCandidates' => 100,
                    'limit' => 5
                ]
            ],
            [
                '$project' => [
                    'embedding' => 0 // Exclude the embedding field
                ]
            ]
        ]);
        return iterator_to_array($searchResults);

        //return response()->json($searchResults);

        // foreach ($searchResults as $result) {
        //     print_r($result);
        // }
    }

    public function mongoReRankedSearch()
    {
        $userQuery = 'tell me about damac';

        // Step 1: Generate the vector embedding
        $userEmbedding = $this->openAIService->generateEmbedding($userQuery);

        // Step 2: Perform vector search to get candidate results
        $vectorSearchResults = DB::connection('mongodb')->getMongoDB()
            ->selectCollection('realestate_ai_test')
            ->aggregate([
                [
                    '$vectorSearch' => [
                        'index' => 'vector_index',
                        'path' => 'embedding',
                        'queryVector' => $userEmbedding,
                        'numCandidates' => 100, // Fetch more candidates
                        'limit' => 10 // Get more results for better re-ranking
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 1, // Keep only the ID for filtering
                    ]
                ]
            ]);

        // Step 3: Extract document IDs
        $documentIds = array_map(fn($doc) => $doc->_id, iterator_to_array($vectorSearchResults));

        if (empty($documentIds)) {
            return []; // No results found
        }

        // Step 4: Perform full-text search within the vector search results
        $reRankedResults = DB::connection('mongodb')->getMongoDB()
            ->selectCollection('realestate_ai_test')
            ->aggregate([
                [
                    '$search' => [
                        'index' => 'default', // Ensure you have a full-text search index
                        'text' => [
                            'query' => $userQuery,
                            'path' => [
                                'wildcard' => '*' // Search across all fields
                            ], // Adjust based on your schema
                            'fuzzy' => ['maxEdits' => 2] // Typo tolerance
                        ]
                    ]
                ],
                [
                    '$match' => [
                        '_id' => ['$in' => $documentIds] // Filter only vector search results
                    ]
                ],
                [
                    '$project' => [
                        'embedding' => 0, // Exclude the vector field
                        'score' => ['$meta' => 'searchScore'] // Get the search score for ranking
                    ]
                ],
                [
                    '$sort' => ['score' => -1] // Sort by full-text relevance score
                ],
                [
                    '$limit' => 5 // Return the final top-ranked results
                ]
            ]);

        return iterator_to_array($reRankedResults);
    }


    public function mongoVectorSearchTest()
    {
        $userQuery = 'list me the movies in which the hero lives in england';
        $userEmbedding = $this->openAIService->generateEmbedding($userQuery);
        $searchResults = DB::connection('mongodb')->getMongoDB()->selectCollection('embedded_movies')->aggregate([
            [
                '$vectorSearch' => [
                    'index' => 'movies_index',
                    'path' => 'plot_embedding',
                    'queryVector' => $userEmbedding,
                    'numCandidates' => 100,
                    'limit' => 5
                ]
            ],
            [
                '$project' => [
                    'plot_embedding' => 0 // Exclude the embedding field
                ]
            ]
        ]);
        return iterator_to_array($searchResults);

        //return response()->json($searchResults);

        // foreach ($searchResults as $result) {
        //     print_r($result);
        // }
    }
}
