<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Class IndustryTaxonomyService
 *
 * Intent: Provides the authoritative enterprise global industry taxonomy for JERO ATS.
 * Organizes 140+ global industries from `td_industries` into 10 structured functional sectors
 * with curated search synonyms and keywords for 0ms client-first browser execution (10K concurrency target).
 *
 * Tenancy: Serves global master reference catalog with tenant-isolated caching keys.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class IndustryTaxonomyService
{
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
     * Map each industry ID / name to its appropriate sector and search synonyms.
     */
    public const INDUSTRY_METADATA = [
        1 => ['sector' => 2, 'keywords' => 'accounting, audit, tax, cpa, bookkeeping, financial reporting, ledger, payroll'],
        2 => ['sector' => 9, 'keywords' => 'airlines, aviation, flight, airport, aerospace, aerospace transport, commercial aviation'],
        3 => ['sector' => 5, 'keywords' => 'alternative dispute resolution, adr, arbitration, mediation, legal negotiation'],
        4 => ['sector' => 3, 'keywords' => 'alternative medicine, holistic, wellness, naturopathy, acupuncture, homeopathy'],
        5 => ['sector' => 10, 'keywords' => 'animation, 3d, cgi, motion graphics, vfx, cartoon, multimedia, visual effects'],
        6 => ['sector' => 6, 'keywords' => 'apparel, fashion, clothing, garments, textiles, retail fashion, couture, footwear'],
        7 => ['sector' => 5, 'keywords' => 'architecture, planning, urban planning, design, blueprint, landscape, interior design'],
        8 => ['sector' => 10, 'keywords' => 'arts, crafts, artisan, handmade, painting, creative, design'],
        9 => ['sector' => 4, 'keywords' => 'automotive, vehicles, cars, automobile, oem, motors, ev, electric vehicles'],
        10 => ['sector' => 4, 'keywords' => 'aviation, aerospace, defense, aircraft, satellites, rocketry, propulsion'],
        11 => ['sector' => 2, 'keywords' => 'banking, retail banking, commercial banking, deposits, loans, credit, finance'],
        12 => ['sector' => 3, 'keywords' => 'biotechnology, biotech, genomics, clinical research, life sciences, bio pharma'],
        13 => ['sector' => 10, 'keywords' => 'broadcast media, television, radio, broadcasting, streaming, cable, news'],
        14 => ['sector' => 4, 'keywords' => 'building materials, cement, timber, steel, glass, concrete, construction supplies'],
        15 => ['sector' => 5, 'keywords' => 'business supplies, equipment, office supplies, b2b equipment, photocopiers, furniture'],
        16 => ['sector' => 2, 'keywords' => 'capital markets, equities, bonds, securities, trading, derivatives, exchange'],
        17 => ['sector' => 4, 'keywords' => 'chemicals, chemical manufacturing, polymers, petrochemicals, industrial chemicals'],
        18 => ['sector' => 10, 'keywords' => 'civic, social organization, ngo, non-profit, community, public interest'],
        19 => ['sector' => 8, 'keywords' => 'civil engineering, infrastructure, bridges, roads, dams, public works, survey'],
        20 => ['sector' => 8, 'keywords' => 'commercial real estate, cre, office leasing, retail properties, logistics parks'],
        21 => ['sector' => 1, 'keywords' => 'computer security, network security, cybersecurity, infosec, firewall, threat intelligence'],
        22 => ['sector' => 1, 'keywords' => 'computer games, video games, gaming, game dev, unity, unreal, esports'],
        23 => ['sector' => 1, 'keywords' => 'computer hardware, processors, chips, server hardware, circuits, motherboards'],
        24 => ['sector' => 1, 'keywords' => 'computer networking, routers, switches, lan, wan, sd-wan, telecommunications'],
        25 => ['sector' => 1, 'keywords' => 'computer software, software, saas, cloud, app development, programming, it'],
        26 => ['sector' => 8, 'keywords' => 'construction, builders, general contracting, civil works, site management'],
        27 => ['sector' => 1, 'keywords' => 'consumer electronics, smartphones, gadgets, appliances, wearable tech, audio'],
        28 => ['sector' => 6, 'keywords' => 'consumer goods, fmcg, packaged goods, household items, personal care'],
        29 => ['sector' => 6, 'keywords' => 'consumer services, b2c services, household services, repairs, customer care'],
        30 => ['sector' => 6, 'keywords' => 'cosmetics, beauty, skincare, makeup, fragrance, personal care'],
        31 => ['sector' => 7, 'keywords' => 'dairy, milk, dairy farming, cheese, pasteurization, dairy products'],
        32 => ['sector' => 4, 'keywords' => 'defense, space, military, defense aerospace, defense equipment, satellites'],
        33 => ['sector' => 5, 'keywords' => 'design, graphic design, ui ux, product design, creative direction, branding'],
        34 => ['sector' => 10, 'keywords' => 'e-learning, online education, edtech, lms, virtual learning, remote education'],
        35 => ['sector' => 10, 'keywords' => 'education management, schools, universities, academy, administration, tuition'],
        36 => ['sector' => 4, 'keywords' => 'electrical manufacturing, electronics, pcb, wiring, sensors, semiconductors'],
        37 => ['sector' => 10, 'keywords' => 'entertainment, cinema, theatre, theme parks, live entertainment, music'],
        38 => ['sector' => 7, 'keywords' => 'environmental services, waste management, recycling, water treatment, remediation'],
        39 => ['sector' => 5, 'keywords' => 'events services, conferences, exhibitions, event planning, trade shows, catering'],
        40 => ['sector' => 5, 'keywords' => 'executive office, c-suite, corporate management, leadership, governance'],
        41 => ['sector' => 8, 'keywords' => 'facilities services, building management, maintenance, hvac, janitorial'],
        42 => ['sector' => 7, 'keywords' => 'farming, agriculture, crops, agronomy, harvesting, organic farming'],
        43 => ['sector' => 2, 'keywords' => 'financial services, fintech, wealth management, advisory, credit union, loans'],
        44 => ['sector' => 10, 'keywords' => 'fine art, galleries, curation, painting, sculptures, auctions, exhibitions'],
        45 => ['sector' => 7, 'keywords' => 'fishery, aquaculture, seafood, marine fishing, fish processing'],
        46 => ['sector' => 6, 'keywords' => 'food and beverages, f&b, dining, beverages, food processing, culinary'],
        47 => ['sector' => 6, 'keywords' => 'food production, food manufacturing, packaging, cold chain, agriculture'],
        48 => ['sector' => 10, 'keywords' => 'fundraising, capital campaigns, donors, philanthropy, crowdfunding, grants'],
        49 => ['sector' => 6, 'keywords' => 'furniture, home decor, interior furnishings, woodworking, office furniture'],
        50 => ['sector' => 6, 'keywords' => 'gambling, casinos, betting, gaming, lottery, igaming'],
        51 => ['sector' => 4, 'keywords' => 'glass, ceramics, concrete, pottery, stoneware, industrial materials'],
        52 => ['sector' => 10, 'keywords' => 'government administration, public sector, municipal, civil service, federal'],
        53 => ['sector' => 5, 'keywords' => 'government relations, lobbying, public affairs, advocacy, regulatory affairs'],
        54 => ['sector' => 5, 'keywords' => 'graphic design, visual identity, branding, typography, digital illustration'],
        55 => ['sector' => 3, 'keywords' => 'health, wellness, fitness, gyms, nutrition, sports fitness, personal training'],
        56 => ['sector' => 10, 'keywords' => 'higher education, university, college, academia, postgraduate, campus'],
        57 => ['sector' => 3, 'keywords' => 'hospital, healthcare, clinics, nursing, medical care, patient care, doctors'],
        58 => ['sector' => 6, 'keywords' => 'hospitality, hotels, resorts, accommodation, tourism, lodging, guest services'],
        59 => ['sector' => 5, 'keywords' => 'human resources, hr, talent acquisition, recruitment, people operations, payroll'],
        60 => ['sector' => 9, 'keywords' => 'import, export, foreign trade, customs, tariffs, cross-border commerce'],
        61 => ['sector' => 10, 'keywords' => 'individual and family services, social work, counseling, family welfare, community care'],
        62 => ['sector' => 4, 'keywords' => 'industrial automation, robotics, scada, plc, process control, factory automation'],
        63 => ['sector' => 1, 'keywords' => 'information services, data providers, market research data, intelligence, archives'],
        64 => ['sector' => 1, 'keywords' => 'information technology, it services, managed services, system integration, cloud'],
        65 => ['sector' => 2, 'keywords' => 'insurance, underwriting, life insurance, health insurance, property casualty, reinsurance'],
        66 => ['sector' => 10, 'keywords' => 'international affairs, diplomacy, embassies, global policy, foreign relations'],
        67 => ['sector' => 9, 'keywords' => 'international trade, global development, multilateral commerce, foreign trade'],
        68 => ['sector' => 1, 'keywords' => 'internet, web services, e-commerce, digital platforms, search, online portals'],
        69 => ['sector' => 2, 'keywords' => 'investment banking, m&a, underwriting, corporate finance, equity research, ipo'],
        70 => ['sector' => 2, 'keywords' => 'investment management, asset management, hedge funds, mutual funds, portfolio management'],
        71 => ['sector' => 5, 'keywords' => 'judiciary, courts, judges, magistrate, legal justice, dispute resolution'],
        72 => ['sector' => 10, 'keywords' => 'law enforcement, police, investigation, public safety, corrections, homeland security'],
        73 => ['sector' => 5, 'keywords' => 'law practice, attorneys, litigation, corporate law, law firms, advocates'],
        74 => ['sector' => 5, 'keywords' => 'legal services, legal counsel, compliance, contracts, paralegal, advisory'],
        75 => ['sector' => 10, 'keywords' => 'legislative office, parliament, congress, policymaking, legislation'],
        76 => ['sector' => 6, 'keywords' => 'leisure, travel, tourism, tour operators, travel agency, vacations'],
        77 => ['sector' => 10, 'keywords' => 'libraries, archives, cataloging, academic resources, preservation'],
        78 => ['sector' => 9, 'keywords' => 'logistics, supply chain, freight, 3pl, warehousing, distribution, fulfillment'],
        79 => ['sector' => 6, 'keywords' => 'luxury goods, jewelry, gems, high-end watches, premium fashion, accessories'],
        80 => ['sector' => 4, 'keywords' => 'machinery, heavy equipment, industrial machinery, manufacturing tools'],
        81 => ['sector' => 5, 'keywords' => 'management consulting, strategy, advisory, business transformation, operations consulting'],
        82 => ['sector' => 9, 'keywords' => 'maritime, shipping, ocean freight, ports, vessels, naval transport'],
        83 => ['sector' => 5, 'keywords' => 'market research, consumer insights, polling, analytics, survey analysis'],
        84 => ['sector' => 5, 'keywords' => 'marketing, advertising, digital marketing, ad agency, media buying, seo'],
        85 => ['sector' => 4, 'keywords' => 'mechanical engineering, industrial engineering, plant engineering, fabrication'],
        86 => ['sector' => 10, 'keywords' => 'media production, video production, studio, film crew, audio recording, post-production'],
        87 => ['sector' => 3, 'keywords' => 'medical devices, diagnostics, surgical equipment, implants, medical instruments'],
        88 => ['sector' => 3, 'keywords' => 'medical practice, physicians, outpatient care, specialized clinics, healthcare providers'],
        89 => ['sector' => 3, 'keywords' => 'mental health care, psychiatry, psychology, behavioral health, therapy, counseling'],
        90 => ['sector' => 4, 'keywords' => 'mining, metals, extraction, ores, smelting, steel, metallurgy'],
        91 => ['sector' => 10, 'keywords' => 'motion pictures, film, movie studio, cinematography, theatrical distribution'],
        92 => ['sector' => 10, 'keywords' => 'museums, institutions, heritage, historical sites, curatorial'],
        93 => ['sector' => 10, 'keywords' => 'music, record labels, artists, sound recording, streaming, concerts'],
        94 => ['sector' => 1, 'keywords' => 'nanotechnology, nanomaterials, molecular engineering, advanced physics'],
        95 => ['sector' => 10, 'keywords' => 'newspapers, journalism, news publishing, press, editorial, reporting'],
        96 => ['sector' => 10, 'keywords' => 'nonprofit organization, npo, ngo, charity, public welfare, advocacy'],
        97 => ['sector' => 7, 'keywords' => 'oil and energy, petroleum, gas, drilling, refineries, power generation'],
        98 => ['sector' => 10, 'keywords' => 'online media, digital journalism, blogs, podcasts, social media, web portals'],
        99 => ['sector' => 5, 'keywords' => 'outsourcing, offshoring, bpo, business process outsourcing, shared services'],
        100 => ['sector' => 9, 'keywords' => 'package delivery, freight delivery, courier, express shipping, parcel delivery'],
        101 => ['sector' => 4, 'keywords' => 'packaging, containers, boxes, corrugated packaging, bottling, industrial packing'],
        102 => ['sector' => 4, 'keywords' => 'paper, forest products, timber, pulp, paper manufacturing, forestry'],
        103 => ['sector' => 10, 'keywords' => 'performing arts, theatre, dance, opera, live performance, concerts'],
        104 => ['sector' => 3, 'keywords' => 'pharmaceuticals, pharma, drugs, therapeutics, clinical trials, medicine development'],
        105 => ['sector' => 10, 'keywords' => 'philanthropy, foundations, charitable giving, endowments, grantmaking'],
        106 => ['sector' => 10, 'keywords' => 'photography, studio photography, commercial photography, imaging'],
        107 => ['sector' => 4, 'keywords' => 'plastics, polymers, injection molding, synthetic materials, resin'],
        108 => ['sector' => 10, 'keywords' => 'political organization, parties, campaigns, public policy advocacy'],
        109 => ['sector' => 10, 'keywords' => 'primary education, secondary education, k-12, elementary, high school, teachers'],
        110 => ['sector' => 4, 'keywords' => 'printing, commercial printing, publishing prints, 3d printing, lithography'],
        111 => ['sector' => 5, 'keywords' => 'professional training, coaching, corporate training, executive coaching, upskilling'],
        112 => ['sector' => 5, 'keywords' => 'program development, project management, program governance, development initiative'],
        113 => ['sector' => 10, 'keywords' => 'public policy, policy research, government analysis, think tank, regulatory policy'],
        114 => ['sector' => 5, 'keywords' => 'public relations, pr, corporate communications, media relations, press release'],
        115 => ['sector' => 10, 'keywords' => 'public safety, emergency services, fire service, disaster management, rescue'],
        116 => ['sector' => 10, 'keywords' => 'publishing, book publishing, magazine publishing, digital publishing, editorial'],
        117 => ['sector' => 4, 'keywords' => 'railroad manufacture, trains, locomotive, railway equipment, rolling stock'],
        118 => ['sector' => 7, 'keywords' => 'ranching, cattle, livestock, animal husbandry, pastoral farming'],
        119 => ['sector' => 8, 'keywords' => 'real estate, property, residential real estate, brokerage, property management'],
        120 => ['sector' => 6, 'keywords' => 'recreational facilities, sports clubs, theme parks, fitness clubs, leisure centers'],
        121 => ['sector' => 10, 'keywords' => 'religious institutions, faith-based, church, ministry, spiritual organizations'],
        122 => ['sector' => 7, 'keywords' => 'renewables, clean energy, solar, wind energy, sustainability, green tech'],
        123 => ['sector' => 5, 'keywords' => 'research, scientific research, r&d, lab research, innovation, institutes'],
        124 => ['sector' => 6, 'keywords' => 'restaurants, dining, food service, fast food, cafe, bistros, culinary'],
        125 => ['sector' => 6, 'keywords' => 'retail, stores, merchandising, shopping, supermarkets, e-commerce retail'],
        126 => ['sector' => 5, 'keywords' => 'security, investigations, private security, surveillance, risk management, loss prevention'],
        127 => ['sector' => 1, 'keywords' => 'semiconductors, microchips, silicon, wafer fabrication, integrated circuits'],
        128 => ['sector' => 4, 'keywords' => 'shipbuilding, naval architecture, shipyards, marine engineering, boat manufacture'],
        129 => ['sector' => 6, 'keywords' => 'sporting goods, athletic apparel, sports equipment, outdoor gear'],
        130 => ['sector' => 6, 'keywords' => 'sports, sports teams, athletics, leagues, sports management, athletes'],
        131 => ['sector' => 5, 'keywords' => 'staffing, recruiting, headhunting, executive search, talent placement, contingent labor'],
        132 => ['sector' => 6, 'keywords' => 'supermarkets, grocery stores, food retail, hypermarkets, provisions'],
        133 => ['sector' => 1, 'keywords' => 'telecommunications, telecom, cellular, broadband, mobile network operators, 5g'],
        134 => ['sector' => 4, 'keywords' => 'textiles, fabrics, spinning, weaving, yarn, apparel materials'],
        135 => ['sector' => 10, 'keywords' => 'think tanks, policy institutes, strategic research, economic studies'],
        136 => ['sector' => 6, 'keywords' => 'tobacco, cigarettes, cigars, vapor, tobacco products'],
        137 => ['sector' => 5, 'keywords' => 'translation, localization, language services, interpretation, subtitles'],
        138 => ['sector' => 9, 'keywords' => 'transportation, trucking, railroad, haulage, fleet, intermodal freight'],
        139 => ['sector' => 7, 'keywords' => 'utilities, electric grid, water utility, gas distribution, power utilities'],
        140 => ['sector' => 2, 'keywords' => 'venture capital, private equity, vc, pe, growth equity, startup investment'],
        141 => ['sector' => 3, 'keywords' => 'veterinary, animal hospital, pet care, veterinary medicine, animal clinic'],
        142 => ['sector' => 9, 'keywords' => 'warehousing, storage, distribution centers, inventory management, fulfillment'],
        143 => ['sector' => 6, 'keywords' => 'wholesale, bulk trade, distributors, b2b commerce, supply distribution'],
        144 => ['sector' => 6, 'keywords' => 'wine and spirits, alcohol, brewery, distillery, winery, liquor'],
        145 => ['sector' => 1, 'keywords' => 'wireless, radio communication, mobile communications, wi-fi, cellular'],
        146 => ['sector' => 10, 'keywords' => 'writing, editing, copywriting, technical writing, author, content creation'],
        147 => ['sector' => 5, 'keywords' => 'other, miscellaneous, diversified, unlisted, general, multi-industry, cross-sector, conglomerate'],
    ];

    /**
     * Get the active global immutable taxonomy manifest.
     * Checks Redis/Dragonfly L1 cache, then filesystem L2 cache, then compiles.
     * Guaranteed 0% MySQL reads on standard request paths.
     */
    public static function getGlobalManifest(): array
    {
        // 1. Redis/Dragonfly L1 Cache
        try {
            $cached = Cache::get(IndustryTaxonomyCompiler::CACHE_KEY_ACTIVE);
            if ($cached && is_array($cached) && ! empty($cached['industries'])) {
                return $cached;
            }
        } catch (\Throwable $e) {
            \Log::warning('IndustryTaxonomyService: Cache read error: '.$e->getMessage());
        }

        // 2. Filesystem L2 Cache
        try {
            $filePath = storage_path('app/taxonomy/industry_manifest_v'.IndustryTaxonomyCompiler::SCHEMA_VERSION.'.json');
            if (file_exists($filePath)) {
                $raw = file_get_contents($filePath);
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && ! empty($decoded['industries'])) {
                    // Repopulate Redis cache in background
                    try {
                        Cache::forever(IndustryTaxonomyCompiler::CACHE_KEY_ACTIVE, $decoded);
                    } catch (\Throwable $err) {
                    }

                    return $decoded;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('IndustryTaxonomyService: File read error: '.$e->getMessage());
        }

        // 3. Compile once and cache
        return IndustryTaxonomyCompiler::compile();
    }

    /**
     * Build a complete tenant manifest bundle combining standard global library and search taxonomy.
     * Backward-compatible signature that consumes the global compiled manifest with 0% DB reads.
     */
    public static function buildTenantManifest(int $companyId, int $version): array
    {
        $manifest = self::getGlobalManifest();

        // Inject tenant-specific manifest identity while sharing the exact same compiled payload
        $manifest['manifest_id'] = 'ind-tenant-'.$companyId.'-v'.$version.'-'.substr($manifest['content_hash'] ?? 'default', 0, 8);
        $manifest['tenant_id'] = $companyId;
        $manifest['version'] = $version;

        return $manifest;
    }

    /**
     * Fallback detector for any new or unknown industry names using tokenized boundaries.
     */
    public static function detectSector(string $name): array
    {
        $classification = IndustryTaxonomyCompiler::classify($name);
        $sectorId = $classification['sector_id'] ?? 5; // Default to professional services if unclassified
        $sector = self::SECTORS[$sectorId] ?? self::SECTORS[5];

        return [
            'id' => $sector['id'],
            'name' => $sector['name'],
            'confidence' => $classification['confidence'] ?? 0.0,
            'matched_term' => $classification['matched_term'] ?? null,
        ];
    }
}
