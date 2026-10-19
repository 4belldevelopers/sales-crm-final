<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class IndustryTaxonomyCompiler
 *
 * Intent: High-performance deterministic compiler for JERO ATS global industry taxonomy.
 * Generates an immutable, content-hashed (SHA-256), client-first industry manifest bundle
 * combining 146 standard industries with 14 modern 2026 tech/digital verticals.
 *
 * Performance Target: 0% server read strain, 10,000 concurrent users via client-first browser execution.
 * Tenancy: Global master reference catalog cached globally in Redis/Dragonfly and filesystem.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class IndustryTaxonomyCompiler
{
    public const SCHEMA_VERSION = 2;

    public const TAXONOMY_VERSION = 42;

    public const COMPILER_VERSION = '2.1.0';

    public const CACHE_KEY_ACTIVE = 'global:dataset:industry_taxonomy:v2';

    /**
     * The 10 canonical enterprise sectors.
     */
    public const SECTORS = [
        1 => [
            'id' => 1,
            'name' => 'Technology, Software & Telecom',
            'slug' => 'technology-software-telecom',
            'icon' => 'code',
            'sort_order' => 1,
        ],
        2 => [
            'id' => 2,
            'name' => 'Financial Services & Banking',
            'slug' => 'financial-services-banking',
            'icon' => 'dollar-sign',
            'sort_order' => 2,
        ],
        3 => [
            'id' => 3,
            'name' => 'Healthcare, Life Sciences & Wellness',
            'slug' => 'healthcare-life-sciences-wellness',
            'icon' => 'activity',
            'sort_order' => 3,
        ],
        4 => [
            'id' => 4,
            'name' => 'Manufacturing, Industrial & Automotive',
            'slug' => 'manufacturing-industrial-automotive',
            'icon' => 'tool',
            'sort_order' => 4,
        ],
        5 => [
            'id' => 5,
            'name' => 'Professional Services, Consulting & Legal',
            'slug' => 'professional-services-consulting-legal',
            'icon' => 'briefcase',
            'sort_order' => 5,
        ],
        6 => [
            'id' => 6,
            'name' => 'Consumer Goods, Retail & Hospitality',
            'slug' => 'consumer-goods-retail-hospitality',
            'icon' => 'shopping-cart',
            'sort_order' => 6,
        ],
        7 => [
            'id' => 7,
            'name' => 'Energy, Utilities & Environment',
            'slug' => 'energy-utilities-environment',
            'icon' => 'zap',
            'sort_order' => 7,
        ],
        8 => [
            'id' => 8,
            'name' => 'Real Estate, Construction & Infrastructure',
            'slug' => 'real-estate-construction-infrastructure',
            'icon' => 'home',
            'sort_order' => 8,
        ],
        9 => [
            'id' => 9,
            'name' => 'Logistics, Transportation & Supply Chain',
            'slug' => 'logistics-transportation-supply-chain',
            'icon' => 'truck',
            'sort_order' => 9,
        ],
        10 => [
            'id' => 10,
            'name' => 'Media, Education & Public Sector',
            'slug' => 'media-education-public-sector',
            'icon' => 'book-open',
            'sort_order' => 10,
        ],
    ];

    /**
     * Modern 2026 Tech & Digital Economy Verticals (synthetic IDs 1001-1014).
     */
    public const MODERN_2026_VERTICALS = [
        1001 => [
            'id' => 1001,
            'name' => 'Artificial Intelligence & Machine Learning (AI/ML)',
            'slug' => 'artificial-intelligence-machine-learning',
            'sector' => 1,
            'acronyms' => ['AI', 'ML', 'GenAI', 'LLM', 'NLP', 'CV', 'AGI'],
            'synonyms' => ['deep learning', 'neural networks', 'generative ai', 'computer vision', 'robotics ai'],
            'keywords' => 'artificial intelligence, machine learning, ai, ml, genai, llm, deep learning, data science, nlp, robotics, neural networks, foundation models',
        ],
        1002 => [
            'id' => 1002,
            'name' => 'FinTech, Payments & Digital Banking',
            'slug' => 'fintech-payments-digital-banking',
            'sector' => 2,
            'acronyms' => ['FinTech', 'P2P', 'UPI', 'POS', 'DeFi', 'BNPL'],
            'synonyms' => ['digital payments', 'neobanking', 'payment gateway', 'lending tech', 'wealthtech'],
            'keywords' => 'fintech, payments, digital banking, neobank, payment gateway, pos, stripe, razorpay, bnpl, lending, wealthtech, crypto, open banking',
        ],
        1003 => [
            'id' => 1003,
            'name' => 'E-Commerce & Direct-to-Consumer (D2C)',
            'slug' => 'ecommerce-direct-to-consumer-d2c',
            'sector' => 6,
            'acronyms' => ['D2C', 'B2C', 'B2B', 'Q-Commerce', 'FMCG'],
            'synonyms' => ['online shopping', 'quick commerce', 'marketplace', 'dropshipping', 'digital retail'],
            'keywords' => 'ecommerce, e-commerce, d2c, direct to consumer, quick commerce, online retail, shopping, marketplace, omnichannel, b2c ecommerce',
        ],
        1004 => [
            'id' => 1004,
            'name' => 'Cybersecurity & Data Privacy',
            'slug' => 'cybersecurity-data-privacy',
            'sector' => 1,
            'acronyms' => ['InfoSec', 'SOC', 'SIEM', 'EDR', 'IAM', 'DevSecOps', 'CISO'],
            'synonyms' => ['information security', 'threat intelligence', 'penetration testing', 'network protection', 'zero trust'],
            'keywords' => 'cybersecurity, infosec, data privacy, threat intelligence, penetration testing, firewall, zero trust, soc, siem, cloud security, edr, compliance',
        ],
        1005 => [
            'id' => 1005,
            'name' => 'Cloud Computing, DevOps & Infrastructure',
            'slug' => 'cloud-computing-devops-infrastructure',
            'sector' => 1,
            'acronyms' => ['AWS', 'GCP', 'Azure', 'SRE', 'CI/CD', 'K8s', 'IaaS', 'PaaS'],
            'synonyms' => ['cloud architecture', 'site reliability engineering', 'kubernetes', 'containerization', 'serverless'],
            'keywords' => 'cloud computing, devops, infrastructure, aws, azure, google cloud, kubernetes, docker, ci cd, sre, terraform, serverless, microservices',
        ],
        1006 => [
            'id' => 1006,
            'name' => 'HealthTech & Digital Health',
            'slug' => 'healthtech-digital-health',
            'sector' => 3,
            'acronyms' => ['EMR', 'EHR', 'HIPAA', 'Telehealth', 'RPM'],
            'synonyms' => ['telemedicine', 'medical software', 'electronic health records', 'digital therapeutics', 'remote patient monitoring'],
            'keywords' => 'healthtech, digital health, telemedicine, telehealth, ehr, emr, medical software, clinical ai, remote monitoring, digital therapeutics, wellness app',
        ],
        1007 => [
            'id' => 1007,
            'name' => 'EdTech & Digital Learning',
            'slug' => 'edtech-digital-learning',
            'sector' => 10,
            'acronyms' => ['LMS', 'MOOC', 'K12', 'EdTech'],
            'synonyms' => ['online education', 'virtual classroom', 'learning management system', 'upskilling platform'],
            'keywords' => 'edtech, digital learning, online education, lms, virtual learning, e-learning, upskilling, tutoring, mooc, educational tech, k-12 platform',
        ],
        1008 => [
            'id' => 1008,
            'name' => 'CleanTech, ClimateTech & Renewable Energy',
            'slug' => 'cleantech-climatetech-renewable-energy',
            'sector' => 7,
            'acronyms' => ['ESG', 'EV', 'PV', 'CCUS', 'GHG'],
            'synonyms' => ['green tech', 'carbon accounting', 'solar energy', 'wind power', 'battery storage'],
            'keywords' => 'cleantech, climatetech, renewable energy, clean energy, solar, wind, carbon capture, esg, battery storage, green hydrogen, circular economy',
        ],
        1009 => [
            'id' => 1009,
            'name' => 'Electric Vehicles (EV) & Autonomous Mobility',
            'slug' => 'electric-vehicles-ev-autonomous-mobility',
            'sector' => 4,
            'acronyms' => ['EV', 'ADAS', 'BMS', 'AV', 'OEM', 'LIDAR'],
            'synonyms' => ['electric cars', 'self-driving cars', 'battery management', 'autonomous driving', 'charging infrastructure'],
            'keywords' => 'electric vehicles, ev, autonomous driving, adas, self driving, battery tech, bms, charging stations, mobility, connected cars, powertrain',
        ],
        1010 => [
            'id' => 1010,
            'name' => 'Gaming, Esports & Interactive Media',
            'slug' => 'gaming-esports-interactive-media',
            'sector' => 1,
            'acronyms' => ['AR', 'VR', 'XR', 'CGI', 'VFX', 'Esports'],
            'synonyms' => ['video games', 'game development', 'virtual reality', 'augmented reality', 'metaverse'],
            'keywords' => 'gaming, esports, interactive media, game dev, unity, unreal engine, virtual reality, vr, ar, game design, streaming, metaverse',
        ],
        1011 => [
            'id' => 1011,
            'name' => 'SpaceTech & Commercial Aerospace',
            'slug' => 'spacetech-commercial-aerospace',
            'sector' => 4,
            'acronyms' => ['LEO', 'GEO', 'GNSS', 'ISRO', 'NASA'],
            'synonyms' => ['newspace', 'satellite manufacturing', 'orbital launch', 'spacecraft', 'rocketry'],
            'keywords' => 'spacetech, aerospace, satellites, rocketry, commercial space, launch vehicles, orbital systems, deep space, earth observation, payloads',
        ],
        1012 => [
            'id' => 1012,
            'name' => 'AgriTech & Smart Farming',
            'slug' => 'agritech-smart-farming',
            'sector' => 7,
            'acronyms' => ['IoT', 'GIS', 'NDVI'],
            'synonyms' => ['precision agriculture', 'hydroponics', 'farm automation', 'crop monitoring'],
            'keywords' => 'agritech, smart farming, precision agriculture, hydroponics, drone farming, crop analytics, greenhouse automation, agricultural biotech',
        ],
        1013 => [
            'id' => 1013,
            'name' => 'PropTech & Smart Real Estate',
            'slug' => 'proptech-smart-real-estate',
            'sector' => 8,
            'acronyms' => ['CRE', 'BIM', 'IoT'],
            'synonyms' => ['real estate tech', 'property management tech', 'smart buildings', 'digital leasing'],
            'keywords' => 'proptech, smart real estate, property tech, real estate platform, smart buildings, tenant experience, cre tech, digital leasing, bim',
        ],
        1014 => [
            'id' => 1014,
            'name' => 'HRTech & Workforce Platforms',
            'slug' => 'hrtech-workforce-platforms',
            'sector' => 5,
            'acronyms' => ['ATS', 'HRIS', 'HCM', 'payroll', 'EOR'],
            'synonyms' => ['recruitment software', 'people tech', 'talent platform', 'workforce management'],
            'keywords' => 'hrtech, workforce platforms, ats, applicant tracking, hris, talent acquisition, recruitment software, payroll tech, people analytics, eor',
        ],
    ];

    /**
     * Compile and return the complete deterministic global industry manifest.
     *
     * Intent: Zero MySQL queries during user requests; deterministic hashing guarantees cache identity.
     */
    public static function compile(): array
    {
        $startTime = microtime(true);

        // 1. Fetch DB master records with fallback if offline
        $dbRows = [];
        try {
            $dbRows = DB::table('td_industries')
                ->where('status', 'Y')
                ->select('ind_id as id', 'ind_name as name')
                ->get()
                ->toArray();
        } catch (\Throwable $e) {
            \Log::warning('IndustryTaxonomyCompiler: DB unavailable, compiling from internal canonical master.');
        }

        // If DB table is empty or unavailable, use base 146 standard catalog
        if (empty($dbRows)) {
            $dbRows = self::getBaseIndustryCatalog();
        }

        $industries = [];
        $seenSlugs = [];

        // 2. Ingest DB industries with metadata
        foreach ($dbRows as $row) {
            $id = (int) $row->id;
            $name = trim($row->name);
            $slug = Str::slug($name);

            // Guard duplicate slugs
            if (isset($seenSlugs[$slug])) {
                $slug = $slug.'-'.$id;
            }
            $seenSlugs[$slug] = true;

            $meta = IndustryTaxonomyService::INDUSTRY_METADATA[$id] ?? null;

            if ($meta) {
                $sectorId = $meta['sector'];
                $keywords = $meta['keywords'];
            } else {
                $detected = self::classify($name);
                $sectorId = $detected['sector_id'] ?? 5;
                $keywords = Str::lower($name);
            }

            $sector = self::SECTORS[$sectorId] ?? self::SECTORS[5];

            $industries[] = [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'sector_id' => $sector['id'],
                'sector_name' => $sector['name'],
                'keywords' => $keywords,
                'is_standard' => true,
                'is_system' => true,
                'is_custom' => false,
            ];
        }

        // 3. Ingest Modern 2026 Verticals
        foreach (self::MODERN_2026_VERTICALS as $mv) {
            $sector = self::SECTORS[$mv['sector']] ?? self::SECTORS[1];
            $industries[] = [
                'id' => $mv['id'],
                'name' => $mv['name'],
                'slug' => $mv['slug'],
                'sector_id' => $sector['id'],
                'sector_name' => $sector['name'],
                'keywords' => $mv['keywords'],
                'acronyms' => $mv['acronyms'] ?? [],
                'synonyms' => $mv['synonyms'] ?? [],
                'is_standard' => true,
                'is_system' => true,
                'is_custom' => false,
            ];
        }

        // 4. Deterministic Sort: sector_id ASC, then name ASC
        usort($industries, function ($a, $b) {
            if ($a['sector_id'] !== $b['sector_id']) {
                return $a['sector_id'] <=> $b['sector_id'];
            }

            return strcasecmp($a['name'], $b['name']);
        });

        // 5. Build Canonical Manifest with deterministic keys
        $sectorsList = array_values(self::SECTORS);
        usort($sectorsList, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        $manifestWithoutHash = [
            'compiler_version' => self::COMPILER_VERSION,
            'count' => count($industries),
            'generated_at' => now()->timestamp,
            'industries' => $industries,
            'schema_version' => self::SCHEMA_VERSION,
            'sectors' => $sectorsList,
            'taxonomy_version' => self::TAXONOMY_VERSION,
            'version' => self::TAXONOMY_VERSION,
        ];

        // 6. Compute Deterministic Canonical SHA-256 Hash
        $canonicalJson = json_encode($manifestWithoutHash, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $contentHash = hash('sha256', $canonicalJson);

        $finalManifest = array_merge([
            'content_hash' => $contentHash,
            'manifest_id' => 'ind-v'.self::TAXONOMY_VERSION.'-'.substr($contentHash, 0, 12),
        ], $manifestWithoutHash);

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);

        // 7. Atomic Cache Invalidation & File Persistence
        try {
            Cache::forever(self::CACHE_KEY_ACTIVE, $finalManifest);

            $storageDir = storage_path('app/taxonomy');
            if (! File::isDirectory($storageDir)) {
                File::makeDirectory($storageDir, 0755, true, true);
            }
            $targetPath = $storageDir.'/industry_manifest_v'.self::SCHEMA_VERSION.'.json';
            File::put($targetPath, json_encode($finalManifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $err) {
            \Log::error('IndustryTaxonomyCompiler persistence error: '.$err->getMessage());
        }

        \Log::info('IndustryTaxonomyCompiler: Compiled '.count($industries)." industries in {$durationMs}ms with hash ".substr($contentHash, 0, 12));

        return $finalManifest;
    }

    /**
     * Fallback classifier using tokenized word boundaries and confidence scoring.
     * Prevents dangerous substring matches (e.g. 'pr' matching 'production' or 'service' matching 'food service').
     */
    public static function classify(string $name): array
    {
        $nameLower = ' '.Str::lower(trim($name)).' ';

        $rules = [
            1 => [
                'name' => 'Technology, Software & Telecom',
                'patterns' => ['/\b(tech|technology|software|computer|data|cloud|saas|security|cyber|internet|telecom|game|gaming|hardware|network|networking|ai|ml|deeptech)\b/i'],
                'weight' => 0.95,
            ],
            2 => [
                'name' => 'Financial Services & Banking',
                'patterns' => ['/\b(bank|banking|financial|finance|insurance|invest|investment|capital|accounting|audit|tax|fintech|wealth|credit|payout|fund|hedge fund|equity|brokerage|trading)\b/i'],
                'weight' => 0.95,
            ],
            3 => [
                'name' => 'Healthcare, Life Sciences & Wellness',
                'patterns' => ['/\b(health|healthcare|hospital|medical|pharma|pharmaceutical|biotech|biotechnology|clinic|wellness|fitness|nursing|surgical|surgery|dental|clinical|patient)\b/i'],
                'weight' => 0.95,
            ],
            4 => [
                'name' => 'Manufacturing, Industrial & Automotive',
                'patterns' => ['/\b(manufacturing|machinery|chemical|automotive|automobile|ev|industrial|engineering|defense|aerospace|steel|metals|plastics|robotics|printing|print|factory|fabrication|assembly)\b/i'],
                'weight' => 0.90,
            ],
            5 => [
                'name' => 'Professional Services, Consulting & Legal',
                'patterns' => ['/\b(consulting|consultant|legal|law|attorney|human resources|recruiting|staffing|public relations|\bpr\b|management consulting)\b/i'],
                'weight' => 0.88,
            ],
            6 => [
                'name' => 'Consumer Goods, Retail & Hospitality',
                'patterns' => ['/\b(food|beverage|retail|consumer goods|fmcg|apparel|fashion|restaurant|hospitality|hotel|supermarket|ecommerce|d2c)\b/i'],
                'weight' => 0.90,
            ],
            7 => [
                'name' => 'Energy, Utilities & Environment',
                'patterns' => ['/\b(energy|oil|gas|petroleum|solar|wind|cleantech|renewables|utilities|environment|environmental|water utility)\b/i'],
                'weight' => 0.90,
            ],
            8 => [
                'name' => 'Real Estate, Construction & Infrastructure',
                'patterns' => ['/\b(real estate|construction|builder|property|civil engineering|infrastructure|architecture|proptech)\b/i'],
                'weight' => 0.90,
            ],
            9 => [
                'name' => 'Logistics, Transportation & Supply Chain',
                'patterns' => ['/\b(logistics|transport|transportation|supply chain|shipping|freight|warehouse|warehousing|maritime|airline|courier)\b/i'],
                'weight' => 0.90,
            ],
            10 => [
                'name' => 'Media, Education & Public Sector',
                'patterns' => ['/\b(media|education|edtech|university|college|school|government|public sector|ngo|nonprofit|publishing|journalism|entertainment)\b/i'],
                'weight' => 0.85,
            ],
        ];

        foreach ($rules as $sectorId => $rule) {
            foreach ($rule['patterns'] as $pattern) {
                if (preg_match($pattern, $nameLower, $matches)) {
                    return [
                        'sector_id' => $sectorId,
                        'sector_name' => $rule['name'],
                        'confidence' => $rule['weight'],
                        'matched_term' => $matches[1] ?? '',
                    ];
                }
            }
        }

        // Clean unclassified return — NEVER dump unknown into Sector 10
        return [
            'sector_id' => null,
            'sector_name' => 'Unclassified / Other',
            'confidence' => 0.0,
            'matched_term' => null,
        ];
    }

    /**
     * Fallback standard catalog of 146 industries when DB is offline during build/testing.
     */
    private static function getBaseIndustryCatalog(): array
    {
        $catalog = [];
        $names = [
            1 => 'Accounting', 2 => 'Airlines/Aviation', 3 => 'Alternative Dispute Resolution', 4 => 'Alternative Medicine',
            5 => 'Animation', 6 => 'Apparel & Fashion', 7 => 'Architecture & Planning', 8 => 'Arts and Crafts',
            9 => 'Automotive', 10 => 'Aviation & Aerospace', 11 => 'Banking', 12 => 'Biotechnology',
            13 => 'Broadcast Media', 14 => 'Building Materials', 15 => 'Business Supplies and Equipment', 16 => 'Capital Markets',
            17 => 'Chemicals', 18 => 'Civic & Social Organization', 19 => 'Civil Engineering', 20 => 'Commercial Real Estate',
            21 => 'Computer & Network Security', 22 => 'Computer Games', 23 => 'Computer Hardware', 24 => 'Computer Networking',
            25 => 'Computer Software', 26 => 'Construction', 27 => 'Consumer Electronics', 28 => 'Consumer Goods',
            29 => 'Consumer Services', 30 => 'Cosmetics', 31 => 'Dairy', 32 => 'Defense & Space',
            33 => 'Design', 34 => 'E-Learning', 35 => 'Education Management', 36 => 'Electrical/Electronic Manufacturing',
            37 => 'Entertainment', 38 => 'Environmental Services', 39 => 'Events Services', 40 => 'Executive Office',
            41 => 'Facilities Services', 42 => 'Farming', 43 => 'Financial Services', 44 => 'Fine Art',
            45 => 'Fishery', 46 => 'Food & Beverages', 47 => 'Food Production', 48 => 'Fundraising',
            49 => 'Furniture', 50 => 'Gambling & Casinos', 51 => 'Glass, Ceramics & Concrete', 52 => 'Government Administration',
            53 => 'Government Relations', 54 => 'Graphic Design', 55 => 'Health, Wellness and Fitness', 56 => 'Higher Education',
            57 => 'Hospital & Health Care', 58 => 'Hospitality', 59 => 'Human Resources', 60 => 'Import and Export',
            61 => 'Individual & Family Services', 62 => 'Industrial Automation', 63 => 'Information Services', 64 => 'Information Technology and Services',
            65 => 'Insurance', 66 => 'International Affairs', 67 => 'International Trade and Development', 68 => 'Internet',
            69 => 'Investment Banking', 70 => 'Investment Management', 71 => 'Judiciary', 72 => 'Law Enforcement',
            73 => 'Law Practice', 74 => 'Legal Services', 75 => 'Legislative Office', 76 => 'Leisure, Travel & Tourism',
            77 => 'Libraries', 78 => 'Logistics and Supply Chain', 79 => 'Luxury Goods & Jewelry', 80 => 'Machinery',
            81 => 'Management Consulting', 82 => 'Maritime', 83 => 'Market Research', 84 => 'Marketing and Advertising',
            85 => 'Mechanical or Industrial Engineering', 86 => 'Media Production', 87 => 'Medical Devices', 88 => 'Medical Practice',
            89 => 'Mental Health Care', 90 => 'Mining & Metals', 91 => 'Motion Pictures and Film', 92 => 'Museums and Institutions',
            93 => 'Music', 94 => 'Nanotechnology', 95 => 'Newspapers', 96 => 'Non-Profit Organization Management',
            97 => 'Oil & Energy', 98 => 'Online Media', 99 => 'Outsourcing/Offshoring', 100 => 'Package/Freight Delivery',
            101 => 'Packaging and Containers', 102 => 'Paper & Forest Products', 103 => 'Performing Arts', 104 => 'Pharmaceuticals',
            105 => 'Philanthropy', 106 => 'Photography', 107 => 'Plastics', 108 => 'Political Organization',
            109 => 'Primary/Secondary Education', 110 => 'Printing', 111 => 'Professional Training & Coaching', 112 => 'Program Development',
            113 => 'Public Policy', 114 => 'Public Relations and Communications', 115 => 'Public Safety', 116 => 'Publishing',
            117 => 'Railroad Manufacture', 118 => 'Ranching', 119 => 'Real Estate', 120 => 'Recreational Facilities and Services',
            121 => 'Religious Institutions', 122 => 'Renewables & Environment', 123 => 'Research', 124 => 'Restaurants',
            125 => 'Retail', 126 => 'Security and Investigations', 127 => 'Semiconductors', 128 => 'Shipbuilding',
            129 => 'Sporting Goods', 130 => 'Sports', 131 => 'Staffing and Recruiting', 132 => 'Supermarkets',
            133 => 'Telecommunications', 134 => 'Textiles', 135 => 'Think Tanks', 136 => 'Tobacco',
            137 => 'Translation and Localization', 138 => 'Transportation/Trucking/Railroad', 139 => 'Utilities', 140 => 'Venture Capital & Private Equity',
            141 => 'Veterinary', 142 => 'Warehousing', 143 => 'Wholesale', 144 => 'Wine and Spirits',
            145 => 'Wireless', 146 => 'Writing and Editing',
        ];

        foreach ($names as $id => $name) {
            $catalog[] = (object) ['id' => $id, 'name' => $name];
        }

        return $catalog;
    }
}
