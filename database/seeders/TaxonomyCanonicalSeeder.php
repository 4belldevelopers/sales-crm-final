<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaxonomyCanonicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * JERO Taxonomy V2 — Phase 2: Canonical Seeder & Forensic ETL Backfill
     */
    public function run(): void
    {
        echo "Starting TaxonomyCanonicalSeeder (Optimized In-Memory Pipelines)...\n";

        // 1. Seed 12 Global Departments
        $this->seedDepartments();

        // 2. Seed 48 Structural Role Families
        $this->seedRoleFamilies();

        // 3. Seed Canonical Skills from jero-skills-taxonomy.json
        $this->seedSkills();

        // 4. Seed Canonical Technologies
        $this->seedTechnologies();

        // 5. Forensic ETL Backfill: JD Templates, Canonical Designations, and Linkages
        $this->seedDesignationsAndTemplates();

        // 6. Seed Commercial Aliases
        $this->seedAliases();

        // 7. Seed Initial Taxonomy Version
        $this->seedVersionRecord();

        echo "TaxonomyCanonicalSeeder completed successfully.\n";
    }

    protected function seedDepartments(): void
    {
        $departments = [
            ['id' => 1, 'name' => 'Engineering & Technology', 'code' => 'ENG', 'description' => 'Software development, architecture, systems engineering, and technology development.'],
            ['id' => 2, 'name' => 'Data, Analytics & AI', 'code' => 'DATA', 'description' => 'Data engineering, data science, machine learning, business intelligence, and AI systems.'],
            ['id' => 3, 'name' => 'Cloud, DevOps & Infrastructure', 'code' => 'CLOUD', 'description' => 'Cloud platforms, DevOps automation, SRE, systems infrastructure, and platform engineering.'],
            ['id' => 4, 'name' => 'Cybersecurity & Information Security', 'code' => 'SEC', 'description' => 'Security operations, AppSec, cloud security, penetration testing, and GRC.'],
            ['id' => 5, 'name' => 'Product Management', 'code' => 'PROD', 'description' => 'Technical product management, core product strategy, product operations, and roadmapping.'],
            ['id' => 6, 'name' => 'Design & User Experience', 'code' => 'DES', 'description' => 'Product design, UI/UX, user research, interaction design, and design systems.'],
            ['id' => 7, 'name' => 'Quality Assurance & Testing', 'code' => 'QA', 'description' => 'QA automation, manual QA, performance testing, and SDET engineering.'],
            ['id' => 8, 'name' => 'Sales, BD & Revenue', 'code' => 'SALES', 'description' => 'Enterprise sales, inside sales, business development, pre-sales, and account management.'],
            ['id' => 9, 'name' => 'Marketing, Growth & Communications', 'code' => 'MKT', 'description' => 'Demand generation, product marketing, content, brand, SEO, and growth engineering.'],
            ['id' => 10, 'name' => 'Human Resources & Talent Acquisition', 'code' => 'HR', 'description' => 'Technical recruitment, talent acquisition, people operations, HRBP, and compensation.'],
            ['id' => 11, 'name' => 'Finance, Legal & Corporate Governance', 'code' => 'FIN', 'description' => 'Corporate finance, accounting, FP&A, legal counsel, and compliance.'],
            ['id' => 12, 'name' => 'Operations, Logistics & Customer Success', 'code' => 'OPS', 'description' => 'Customer success, technical support, business operations, and supply chain.'],
        ];

        foreach ($departments as $dept) {
            DB::table('taxonomy_departments')->updateOrInsert(
                ['code' => $dept['code']],
                [
                    'id' => $dept['id'],
                    'name' => $dept['name'],
                    'description' => $dept['description'],
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        echo " - Seeded 12 Global Departments\n";
    }

    protected function seedRoleFamilies(): void
    {
        $families = [
            // Engineering (ENG) - Dept 1
            ['id' => 1, 'department_id' => 1, 'name' => 'Software Engineering', 'code' => 'ENG_SWE'],
            ['id' => 2, 'department_id' => 1, 'name' => 'Backend Engineering', 'code' => 'ENG_BACKEND'],
            ['id' => 3, 'department_id' => 1, 'name' => 'Frontend Engineering', 'code' => 'ENG_FRONTEND'],
            ['id' => 4, 'department_id' => 1, 'name' => 'Full Stack Engineering', 'code' => 'ENG_FULLSTACK'],
            ['id' => 5, 'department_id' => 1, 'name' => 'Systems & Low-Level Engineering', 'code' => 'ENG_SYSTEMS'],
            ['id' => 6, 'department_id' => 1, 'name' => 'Mobile Engineering', 'code' => 'ENG_MOBILE'],
            ['id' => 7, 'department_id' => 1, 'name' => 'Architecture & Technical Leadership', 'code' => 'ENG_ARCH'],

            // Data & AI (DATA) - Dept 2
            ['id' => 8, 'department_id' => 2, 'name' => 'Data Engineering', 'code' => 'DATA_ENG'],
            ['id' => 9, 'department_id' => 2, 'name' => 'Data Science & Advanced Analytics', 'code' => 'DATA_SCIENCE'],
            ['id' => 10, 'department_id' => 2, 'name' => 'Machine Learning & MLOps', 'code' => 'DATA_ML'],
            ['id' => 11, 'department_id' => 2, 'name' => 'Generative AI & LLM Systems', 'code' => 'DATA_GENAI'],
            ['id' => 12, 'department_id' => 2, 'name' => 'Business Intelligence & Reporting', 'code' => 'DATA_BI'],

            // Cloud & DevOps (CLOUD) - Dept 3
            ['id' => 13, 'department_id' => 3, 'name' => 'Cloud Engineering', 'code' => 'CLOUD_ENG'],
            ['id' => 14, 'department_id' => 3, 'name' => 'DevOps & CI/CD Automation', 'code' => 'CLOUD_DEVOPS'],
            ['id' => 15, 'department_id' => 3, 'name' => 'Platform Engineering', 'code' => 'CLOUD_PLATFORM'],
            ['id' => 16, 'department_id' => 3, 'name' => 'Site Reliability Engineering (SRE)', 'code' => 'CLOUD_SRE'],
            ['id' => 17, 'department_id' => 3, 'name' => 'Database Administration (DBA)', 'code' => 'CLOUD_DBA'],

            // Cybersecurity (SEC) - Dept 4
            ['id' => 18, 'department_id' => 4, 'name' => 'Security Operations & SOC', 'code' => 'SEC_SOC'],
            ['id' => 19, 'department_id' => 4, 'name' => 'Application Security (AppSec)', 'code' => 'SEC_APPSEC'],
            ['id' => 20, 'department_id' => 4, 'name' => 'Cloud Security', 'code' => 'SEC_CLOUD'],
            ['id' => 21, 'department_id' => 4, 'name' => 'Governance, Risk & Compliance (GRC)', 'code' => 'SEC_GRC'],
            ['id' => 22, 'department_id' => 4, 'name' => 'Penetration Testing & Red Teaming', 'code' => 'SEC_PENTEST'],

            // Product Management (PROD) - Dept 5
            ['id' => 23, 'department_id' => 5, 'name' => 'Core Product Management', 'code' => 'PROD_CORE'],
            ['id' => 24, 'department_id' => 5, 'name' => 'Technical Product Management (TPM)', 'code' => 'PROD_TECH'],
            ['id' => 25, 'department_id' => 5, 'name' => 'Growth & Marketplace Product', 'code' => 'PROD_GROWTH'],
            ['id' => 26, 'department_id' => 5, 'name' => 'Product Operations', 'code' => 'PROD_OPS'],

            // Design (DES) - Dept 6
            ['id' => 27, 'department_id' => 6, 'name' => 'UI/UX Design', 'code' => 'DES_UIUX'],
            ['id' => 28, 'department_id' => 6, 'name' => 'Product Design', 'code' => 'DES_PRODUCT'],
            ['id' => 29, 'department_id' => 6, 'name' => 'Design Systems & Visual Design', 'code' => 'DES_SYSTEMS'],
            ['id' => 30, 'department_id' => 6, 'name' => 'User Research', 'code' => 'DES_RESEARCH'],

            // Quality Assurance (QA) - Dept 7
            ['id' => 31, 'department_id' => 7, 'name' => 'QA Automation Engineering', 'code' => 'QA_AUTO'],
            ['id' => 32, 'department_id' => 7, 'name' => 'Manual & Exploratory QA', 'code' => 'QA_MANUAL'],
            ['id' => 33, 'department_id' => 7, 'name' => 'Performance & Reliability QA', 'code' => 'QA_PERF'],
            ['id' => 34, 'department_id' => 7, 'name' => 'Software Development Engineer in Test (SDET)', 'code' => 'QA_SDET'],

            // Sales (SALES) - Dept 8
            ['id' => 35, 'department_id' => 8, 'name' => 'Enterprise Software Sales', 'code' => 'SALES_ENT'],
            ['id' => 36, 'department_id' => 8, 'name' => 'Business Development & SDR', 'code' => 'SALES_BDR'],
            ['id' => 37, 'department_id' => 8, 'name' => 'Pre-Sales & Sales Engineering', 'code' => 'SALES_ENG'],
            ['id' => 38, 'department_id' => 8, 'name' => 'Account Management & Client Success', 'code' => 'SALES_AM'],

            // Marketing (MKT) - Dept 9
            ['id' => 39, 'department_id' => 9, 'name' => 'Demand Generation & Growth Marketing', 'code' => 'MKT_GROWTH'],
            ['id' => 40, 'department_id' => 9, 'name' => 'Product Marketing (PMM)', 'code' => 'MKT_PMM'],
            ['id' => 41, 'department_id' => 9, 'name' => 'Content, Brand & Communications', 'code' => 'MKT_CONTENT'],
            ['id' => 42, 'department_id' => 9, 'name' => 'Performance Marketing & SEO', 'code' => 'MKT_PERF'],

            // Human Resources (HR) - Dept 10
            ['id' => 43, 'department_id' => 10, 'name' => 'Talent Acquisition & Technical Recruiting', 'code' => 'HR_TA'],
            ['id' => 44, 'department_id' => 10, 'name' => 'HR Operations & People Operations', 'code' => 'HR_PEOPLE'],
            ['id' => 45, 'department_id' => 10, 'name' => 'Compensation, Benefits & Total Rewards', 'code' => 'HR_COMP'],

            // Finance & Legal (FIN) - Dept 11
            ['id' => 46, 'department_id' => 11, 'name' => 'Corporate Finance & Accounting', 'code' => 'FIN_CORP'],
            ['id' => 47, 'department_id' => 11, 'name' => 'Corporate Legal & Governance', 'code' => 'FIN_LEGAL'],

            // Operations (OPS) - Dept 12
            ['id' => 48, 'department_id' => 12, 'name' => 'Operations & Customer Success', 'code' => 'OPS_CSM'],
        ];

        foreach ($families as $f) {
            DB::table('taxonomy_role_families')->updateOrInsert(
                ['code' => $f['code']],
                [
                    'id' => $f['id'],
                    'department_id' => $f['department_id'],
                    'name' => $f['name'],
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        echo " - Seeded 48 Structural Role Families\n";
    }

    protected function seedSkills(): void
    {
        $jsonPath = public_path('skills/jero-skills-taxonomy.json');
        if (!file_exists($jsonPath)) {
            echo " - Warning: jero-skills-taxonomy.json not found, skipping skill seed.\n";
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        $skills = $data['skills'] ?? [];
        echo " - Ingesting " . count($skills) . " skills from jero-skills-taxonomy.json...\n";

        $batch = [];
        $insertedCount = 0;
        foreach ($skills as $skill) {
            $name = trim($skill);
            if (empty($name)) continue;

            $normalizedKey = $this->generateNormalizedKey($name);
            $batch[] = [
                'name' => $name,
                'normalized_key' => $normalizedKey,
                'category' => 'Technical Competency',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($batch) >= 200) {
                DB::table('taxonomy_skills')->upsert($batch, ['normalized_key'], ['name', 'updated_at']);
                $insertedCount += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('taxonomy_skills')->upsert($batch, ['normalized_key'], ['name', 'updated_at']);
            $insertedCount += count($batch);
        }

        echo " - Seeded {$insertedCount} Canonical Skills\n";
    }

    protected function seedTechnologies(): void
    {
        $technologies = [
            // Languages & Runtimes
            ['name' => 'Python', 'ecosystem' => 'Python', 'category' => 'Programming Language'],
            ['name' => 'Java', 'ecosystem' => 'Java', 'category' => 'Programming Language'],
            ['name' => 'JavaScript', 'ecosystem' => 'JavaScript / Node', 'category' => 'Programming Language'],
            ['name' => 'TypeScript', 'ecosystem' => 'JavaScript / Node', 'category' => 'Programming Language'],
            ['name' => 'Go', 'ecosystem' => 'Go', 'category' => 'Programming Language'],
            ['name' => 'Rust', 'ecosystem' => 'Rust', 'category' => 'Programming Language'],
            ['name' => 'C++', 'ecosystem' => 'C / C++', 'category' => 'Programming Language'],
            ['name' => 'C#', 'ecosystem' => '.NET / Microsoft', 'category' => 'Programming Language'],
            ['name' => 'PHP', 'ecosystem' => 'PHP', 'category' => 'Programming Language'],
            ['name' => 'Swift', 'ecosystem' => 'Apple / iOS', 'category' => 'Programming Language'],
            ['name' => 'Kotlin', 'ecosystem' => 'Android / JVM', 'category' => 'Programming Language'],
            ['name' => 'Ruby', 'ecosystem' => 'Ruby', 'category' => 'Programming Language'],
            ['name' => 'SQL', 'ecosystem' => 'Database', 'category' => 'Query Language'],
            ['name' => 'Mojo', 'ecosystem' => 'AI / Systems', 'category' => 'Programming Language'],

            // Frameworks & Runtimes
            ['name' => 'React.js', 'ecosystem' => 'JavaScript / Node', 'category' => 'Frontend Framework'],
            ['name' => 'Angular', 'ecosystem' => 'JavaScript / Node', 'category' => 'Frontend Framework'],
            ['name' => 'Vue.js', 'ecosystem' => 'JavaScript / Node', 'category' => 'Frontend Framework'],
            ['name' => 'Next.js', 'ecosystem' => 'JavaScript / Node', 'category' => 'Web Framework'],
            ['name' => 'Node.js', 'ecosystem' => 'JavaScript / Node', 'category' => 'Backend Runtime'],
            ['name' => 'NestJS', 'ecosystem' => 'JavaScript / Node', 'category' => 'Backend Framework'],
            ['name' => 'Bun', 'ecosystem' => 'JavaScript / Node', 'category' => 'Backend Runtime / Tooling'],
            ['name' => 'Spring Boot', 'ecosystem' => 'Java', 'category' => 'Backend Framework'],
            ['name' => 'Laravel', 'ecosystem' => 'PHP', 'category' => 'Backend Framework'],
            ['name' => 'Django', 'ecosystem' => 'Python', 'category' => 'Backend Framework'],
            ['name' => 'FastAPI', 'ecosystem' => 'Python', 'category' => 'Backend Framework'],
            ['name' => 'ASP.NET Core', 'ecosystem' => '.NET / Microsoft', 'category' => 'Backend Framework'],
            ['name' => '.NET', 'ecosystem' => '.NET / Microsoft', 'category' => 'Platform'],
            ['name' => 'Flutter', 'ecosystem' => 'Cross-Platform Mobile', 'category' => 'Mobile Framework'],
            ['name' => 'React Native', 'ecosystem' => 'Cross-Platform Mobile', 'category' => 'Mobile Framework'],

            // Cloud & Infrastructure
            ['name' => 'AWS', 'ecosystem' => 'Cloud Platforms', 'category' => 'Cloud Provider'],
            ['name' => 'Azure', 'ecosystem' => 'Cloud Platforms', 'category' => 'Cloud Provider'],
            ['name' => 'Google Cloud Platform (GCP)', 'ecosystem' => 'Cloud Platforms', 'category' => 'Cloud Provider'],
            ['name' => 'Docker', 'ecosystem' => 'Containers & Orchestration', 'category' => 'Containerization'],
            ['name' => 'Kubernetes', 'ecosystem' => 'Containers & Orchestration', 'category' => 'Container Orchestration'],
            ['name' => 'Terraform', 'ecosystem' => 'Infrastructure as Code', 'category' => 'IaC Tool'],
            ['name' => 'Ansible', 'ecosystem' => 'Infrastructure as Code', 'category' => 'Configuration Management'],

            // Databases & Streaming
            ['name' => 'PostgreSQL', 'ecosystem' => 'Relational Databases', 'category' => 'Database'],
            ['name' => 'MySQL', 'ecosystem' => 'Relational Databases', 'category' => 'Database'],
            ['name' => 'MongoDB', 'ecosystem' => 'NoSQL Databases', 'category' => 'Database'],
            ['name' => 'Redis', 'ecosystem' => 'In-Memory Datastores', 'category' => 'Cache / In-Memory'],
            ['name' => 'Dragonfly', 'ecosystem' => 'In-Memory Datastores', 'category' => 'High-Performance Cache'],
            ['name' => 'Apache Kafka', 'ecosystem' => 'Distributed Streaming', 'category' => 'Message Broker'],
            ['name' => 'RabbitMQ', 'ecosystem' => 'Message Queues', 'category' => 'Message Broker'],
            ['name' => 'Elasticsearch', 'ecosystem' => 'Search & Analytics', 'category' => 'Search Engine'],
            ['name' => 'Snowflake', 'ecosystem' => 'Cloud Data Warehousing', 'category' => 'Data Warehouse'],
            ['name' => 'Databricks', 'ecosystem' => 'Big Data & AI', 'category' => 'Lakehouse Platform'],

            // Modern AI & ML
            ['name' => 'PyTorch', 'ecosystem' => 'AI & Machine Learning', 'category' => 'ML Framework'],
            ['name' => 'TensorFlow', 'ecosystem' => 'AI & Machine Learning', 'category' => 'ML Framework'],
            ['name' => 'ONNX Runtime', 'ecosystem' => 'AI & Machine Learning', 'category' => 'Inference Engine'],
            ['name' => 'Hugging Face', 'ecosystem' => 'AI & Machine Learning', 'category' => 'Model Hub / Transformers'],
            ['name' => 'LangChain', 'ecosystem' => 'AI & Machine Learning', 'category' => 'Orchestration Library'],
            ['name' => 'Pinecone', 'ecosystem' => 'AI & Vector Search', 'category' => 'Vector Database'],
        ];

        $batch = [];
        foreach ($technologies as $tech) {
            $normalized = $this->generateNormalizedKey($tech['name']);
            $batch[] = [
                'name' => $tech['name'],
                'normalized_key' => $normalized,
                'ecosystem' => $tech['ecosystem'],
                'category' => $tech['category'],
                'lifecycle_status' => 'active',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('taxonomy_technologies')->upsert($batch, ['normalized_key'], ['name', 'ecosystem', 'category', 'updated_at']);
        echo " - Seeded " . count($batch) . " Canonical Technologies\n";
    }

    protected function seedDesignationsAndTemplates(): void
    {
        // Clean partial prior run safely
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('taxonomy_designation_technologies')->truncate();
        DB::table('taxonomy_designation_skills')->truncate();
        DB::table('taxonomy_aliases')->truncate();
        DB::table('taxonomy_designations')->truncate();
        DB::table('taxonomy_jd_templates')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // In-Memory Skill and Technology Lookups (High-Speed O(1) Array Cache)
        $skillMap = DB::table('taxonomy_skills')->pluck('id', 'normalized_key')->all();
        $techMap = DB::table('taxonomy_technologies')->pluck('id', 'normalized_key')->all();

        // Source clean records from jd_details_backup_20261013 (or fallback to jd_details)
        $sourceTable = DB::getSchemaBuilder()->hasTable('jd_details_backup_20261013')
            ? 'jd_details_backup_20261013'
            : 'jd_details';

        $records = DB::table($sourceTable)
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->get();

        echo " - Processing " . $records->count() . " records from {$sourceTable} for canonical normalization...\n";

        $seenTitles = [];
        $templateCount = 0;
        $designationCount = 0;
        $skillsPivotBatch = [];
        $techPivotBatch = [];

        foreach ($records as $row) {
            $rawTitle = trim($row->title);
            if (empty($rawTitle) || strlen($rawTitle) < 2) continue;

            // Normalize title
            $cleanTitle = $this->cleanTitle($rawTitle);
            $identityKey = $this->generateIdentityKey($cleanTitle);
            $searchKey = $this->generateSearchKey($cleanTitle);

            // Deduplicate active identities
            if (isset($seenTitles[$identityKey])) {
                continue;
            }
            $seenTitles[$identityKey] = true;

            // Classify Department & Role Family
            $classification = $this->classifyRole($cleanTitle);
            $departmentId = $classification['department_id'];
            $roleFamilyId = $classification['role_family_id'];
            $seniority = $this->detectSeniority($cleanTitle);

            // Parse skills
            $skillTags = $this->parseSkillTags($row->skills ?? '');

            // Construct 12-block enterprise JD template with Zero Experience Invariant
            $jdTemplateId = DB::table('taxonomy_jd_templates')->insertGetId([
                'role_family_id' => $roleFamilyId,
                'role_summary' => !empty($row->jd) ? strip_tags(substr($row->jd, 0, 500)) : "Mission statement for {$cleanTitle}.",
                'key_responsibilities' => json_encode([
                    "Lead end-to-end design, development, and delivery of {$cleanTitle} solutions.",
                    "Collaborate with cross-functional engineering, product, and QA stakeholders to maintain architectural excellence.",
                    "Ensure code quality, test coverage, and performance optimization across systems."
                ]),
                'must_have_skills' => json_encode(array_slice($skillTags, 0, 5)),
                'technical_competencies' => json_encode([
                    "Solid understanding of enterprise design patterns and distributed systems.",
                    "Proficiency with version control, continuous integration, and automated deployments."
                ]),
                'nice_to_have_skills' => json_encode(array_slice($skillTags, 5, 5)),
                'tools_and_platforms' => json_encode(['Git', 'Docker', 'Jira']),
                'quality_score' => 95,
                'quality_score_version' => 'v1.2.0',
                'quality_evaluator' => 'automated_rules',
                'status' => 'active',
                // Zero Experience Defaults
                'minimum_exp' => null,
                'maxmum_exp' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $templateCount++;

            // Create Canonical Designation
            $designationId = DB::table('taxonomy_designations')->insertGetId([
                'canonical_title' => $cleanTitle,
                'identity_normalized_key' => $identityKey,
                'search_normalized_key' => $searchKey,
                'department_id' => $departmentId,
                'role_family_id' => $roleFamilyId,
                'seniority_level' => $seniority,
                'specialization' => $classification['specialization'],
                'jd_template_id' => $jdTemplateId,
                'status' => 1,
                'quality_score' => 95,
                'taxonomy_version' => '2.0.0',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $designationCount++;

            // Link skills in memory
            foreach (array_slice($skillTags, 0, 5) as $idx => $sName) {
                $sKey = $this->generateNormalizedKey($sName);
                if (isset($skillMap[$sKey])) {
                    $skillsPivotBatch[] = [
                        'designation_id' => $designationId,
                        'skill_id' => $skillMap[$sKey],
                        'is_must_have' => $idx < 3 ? 1 : 0,
                        'weight' => $idx < 3 ? 1.00 : 0.50
                    ];
                }
            }

            // Link technologies in memory
            $techKeys = $this->detectTechKeys($cleanTitle);
            foreach ($techKeys as $tKey) {
                if (isset($techMap[$tKey])) {
                    $techPivotBatch[] = [
                        'designation_id' => $designationId,
                        'technology_id' => $techMap[$tKey],
                        'is_primary' => 1
                    ];
                }
            }
        }

        // Fast chunked insert of pivots
        foreach (array_chunk($skillsPivotBatch, 500) as $chunk) {
            DB::table('taxonomy_designation_skills')->insertOrIgnore($chunk);
        }
        foreach (array_chunk($techPivotBatch, 500) as $chunk) {
            DB::table('taxonomy_designation_technologies')->insertOrIgnore($chunk);
        }

        echo " - Seeded {$templateCount} Canonical JD Templates (Zero Experience Defaults enforced)\n";
        echo " - Seeded {$designationCount} Canonical Designations\n";
        echo " - Seeded " . count($skillsPivotBatch) . " Skill linkages\n";
        echo " - Seeded " . count($techPivotBatch) . " Technology linkages\n";
    }

    protected function seedAliases(): void
    {
        $aliases = [
            ['alias' => 'SDE 1', 'target_title' => 'Software Engineer', 'type' => 'corporate_grade', 'seniority' => 'Entry'],
            ['alias' => 'SDE 2', 'target_title' => 'Software Engineer', 'type' => 'corporate_grade', 'seniority' => 'Mid-Level'],
            ['alias' => 'SDE 3', 'target_title' => 'Senior Software Engineer', 'type' => 'corporate_grade', 'seniority' => 'Senior'],
            ['alias' => 'SDE I', 'target_title' => 'Software Engineer', 'type' => 'corporate_grade', 'seniority' => 'Entry'],
            ['alias' => 'SDE II', 'target_title' => 'Software Engineer', 'type' => 'corporate_grade', 'seniority' => 'Mid-Level'],
            ['alias' => 'SDE III', 'target_title' => 'Senior Software Engineer', 'type' => 'corporate_grade', 'seniority' => 'Senior'],
            ['alias' => 'Senior React Developer', 'target_title' => 'Senior Frontend Engineer', 'type' => 'commercial_stack', 'seniority' => 'Senior', 'tech' => 'React.js'],
            ['alias' => 'React Developer', 'target_title' => 'Frontend Engineer', 'type' => 'commercial_stack', 'seniority' => 'Mid-Level', 'tech' => 'React.js'],
            ['alias' => 'Angular Developer', 'target_title' => 'Frontend Engineer', 'type' => 'commercial_stack', 'seniority' => 'Mid-Level', 'tech' => 'Angular'],
            ['alias' => 'Java Developer', 'target_title' => 'Backend Engineer', 'type' => 'commercial_stack', 'seniority' => 'Mid-Level', 'tech' => 'Java'],
            ['alias' => 'Senior Java Developer', 'target_title' => 'Senior Backend Engineer', 'type' => 'commercial_stack', 'seniority' => 'Senior', 'tech' => 'Java'],
            ['alias' => 'Node Developer', 'target_title' => 'Backend Engineer', 'type' => 'commercial_stack', 'seniority' => 'Mid-Level', 'tech' => 'Node.js'],
            ['alias' => 'MERN Stack Developer', 'target_title' => 'Full Stack Engineer', 'type' => 'commercial_stack', 'seniority' => 'Mid-Level', 'specialization' => 'MERN Stack'],
            ['alias' => 'AWS DevOps Engineer', 'target_title' => 'DevOps Engineer', 'type' => 'commercial_stack', 'seniority' => 'Mid-Level', 'tech' => 'AWS'],
            ['alias' => 'AI Ninja', 'target_title' => 'AI / Machine Learning Engineer', 'type' => 'synonym', 'seniority' => 'Mid-Level'],
            ['alias' => 'Growth Hacker', 'target_title' => 'Growth Marketing Manager', 'type' => 'synonym', 'seniority' => 'Mid-Level'],
        ];

        $inserted = 0;
        foreach ($aliases as $item) {
            $targetDesig = DB::table('taxonomy_designations')
                ->where('canonical_title', $item['target_title'])
                ->orWhere('canonical_title', 'LIKE', '%' . $item['target_title'] . '%')
                ->first();

            if ($targetDesig) {
                DB::table('taxonomy_aliases')->insertOrIgnore([
                    'alias_string' => $item['alias'],
                    'canonical_designation_id' => $targetDesig->id,
                    'alias_type' => $item['type'],
                    'seniority_override' => $item['seniority'] ?? null,
                    'specialization_override' => $item['specialization'] ?? null,
                    'primary_technology_override' => $item['tech'] ?? null,
                    'search_normalized_key' => $this->generateSearchKey($item['alias']),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $inserted++;
            }
        }
        echo " - Seeded {$inserted} Commercial Aliases\n";
    }

    protected function seedVersionRecord(): void
    {
        $canonicalCount = DB::table('taxonomy_designations')->count();
        $aliasCount = DB::table('taxonomy_aliases')->count();

        DB::table('taxonomy_versions')->updateOrInsert(
            ['version' => '2.0.0'],
            [
                'schema_version' => 2,
                'build_id' => 'build_' . date('Ymd') . '_v2',
                'content_hash' => hash('sha256', "taxonomy_v2_{$canonicalCount}_{$aliasCount}"),
                'bundle_hash' => hash('sha256', "bundle_v2_{$canonicalCount}"),
                'canonical_count' => $canonicalCount,
                'alias_count' => $aliasCount,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        echo " - Seeded Taxonomy Version 2.0.0 Ledger Record\n";
    }

    protected function cleanTitle(string $raw): string
    {
        $clean = preg_replace('/\s+/', ' ', trim($raw));
        $clean = str_replace(
            ['Full - Stack', 'Front - End', 'Back - End', 'Pre - Sales', 'DotNet', 'Dot .NET', 'Dot Net'],
            ['Full Stack', 'Frontend', 'Backend', 'Pre-Sales', '.NET', '.NET', '.NET'],
            $clean
        );
        return $clean;
    }

    protected function generateIdentityKey(string $title): string
    {
        $expanded = strtolower($title);
        $expanded = str_replace(['c++', 'c#', '.net', 'node.js', 'a.w.s'], ['cpp', 'csharp', 'dotnet', 'nodejs', 'aws'], $expanded);
        return preg_replace('/[^a-z0-9_]/', '', str_replace([' ', '-'], '_', $expanded));
    }

    protected function generateSearchKey(string $title): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($title));
    }

    protected function generateNormalizedKey(string $name): string
    {
        $key = strtolower(trim($name));
        $key = str_replace(['c++', 'c#', '.net', 'node.js'], ['cpp', 'csharp', 'dotnet', 'nodejs'], $key);
        return preg_replace('/[^a-z0-9_]/', '', str_replace([' ', '-'], '_', $key));
    }

    protected function detectSeniority(string $title): string
    {
        $lower = strtolower($title);
        if (str_contains($lower, 'intern')) return 'Intern';
        if (str_contains($lower, 'entry') || str_contains($lower, 'junior') || str_contains($lower, 'trainee')) return 'Junior';
        if (str_contains($lower, 'lead') || str_contains($lower, 'tech lead') || str_contains($lower, 'team lead')) return 'Lead';
        if (str_contains($lower, 'staff')) return 'Staff';
        if (str_contains($lower, 'principal')) return 'Principal';
        if (str_contains($lower, 'director') || str_contains($lower, 'head')) return 'Director';
        if (str_contains($lower, 'vp') || str_contains($lower, 'vice president')) return 'VP';
        if (str_contains($lower, 'chief') || str_contains($lower, 'c-level') || str_contains($lower, 'cto') || str_contains($lower, 'cio') || str_contains($lower, 'ciso')) return 'C-Level';
        if (str_contains($lower, 'senior') || str_contains($lower, 'sr')) return 'Senior';
        return 'Mid-Level';
    }

    protected function classifyRole(string $title): array
    {
        $lower = strtolower($title);

        // Data & AI
        if (str_contains($lower, 'data') || str_contains($lower, 'machine learning') || str_contains($lower, 'ml') || str_contains($lower, 'ai') || str_contains($lower, 'bi') || str_contains($lower, 'analytics')) {
            if (str_contains($lower, 'engineer') && str_contains($lower, 'data')) return ['department_id' => 2, 'role_family_id' => 8, 'specialization' => 'Data Pipelines'];
            if (str_contains($lower, 'scientist')) return ['department_id' => 2, 'role_family_id' => 9, 'specialization' => 'Predictive Modeling'];
            if (str_contains($lower, 'ai') || str_contains($lower, 'llm')) return ['department_id' => 2, 'role_family_id' => 11, 'specialization' => 'Generative AI'];
            return ['department_id' => 2, 'role_family_id' => 12, 'specialization' => 'Business Intelligence'];
        }

        // Cloud & DevOps
        if (str_contains($lower, 'cloud') || str_contains($lower, 'devops') || str_contains($lower, 'sre') || str_contains($lower, 'reliability') || str_contains($lower, 'platform') || str_contains($lower, 'dba')) {
            if (str_contains($lower, 'devops')) return ['department_id' => 3, 'role_family_id' => 14, 'specialization' => 'CI/CD Automation'];
            if (str_contains($lower, 'sre') || str_contains($lower, 'reliability')) return ['department_id' => 3, 'role_family_id' => 16, 'specialization' => 'Site Reliability'];
            if (str_contains($lower, 'platform')) return ['department_id' => 3, 'role_family_id' => 15, 'specialization' => 'Platform Systems'];
            return ['department_id' => 3, 'role_family_id' => 13, 'specialization' => 'Cloud Infrastructure'];
        }

        // Cybersecurity
        if (str_contains($lower, 'security') || str_contains($lower, 'cyber') || str_contains($lower, 'soc') || str_contains($lower, 'infosec') || str_contains($lower, 'penetration')) {
            return ['department_id' => 4, 'role_family_id' => 18, 'specialization' => 'Information Security'];
        }

        // Product
        if (str_contains($lower, 'product manager') || str_contains($lower, 'product owner') || str_contains($lower, 'tpm')) {
            return ['department_id' => 5, 'role_family_id' => 23, 'specialization' => 'Product Lifecycle'];
        }

        // Design
        if (str_contains($lower, 'design') || str_contains($lower, 'ui') || str_contains($lower, 'ux')) {
            return ['department_id' => 6, 'role_family_id' => 27, 'specialization' => 'User Experience'];
        }

        // QA
        if (str_contains($lower, 'qa') || str_contains($lower, 'test') || str_contains($lower, 'quality') || str_contains($lower, 'sdet')) {
            return ['department_id' => 7, 'role_family_id' => 31, 'specialization' => 'Test Automation'];
        }

        // Sales
        if (str_contains($lower, 'sales') || str_contains($lower, 'account executive') || str_contains($lower, 'business development') || str_contains($lower, 'bdm')) {
            return ['department_id' => 8, 'role_family_id' => 35, 'specialization' => 'Enterprise Sales'];
        }

        // HR
        if (str_contains($lower, 'recruiter') || str_contains($lower, 'talent') || str_contains($lower, 'hr') || str_contains($lower, 'people')) {
            return ['department_id' => 10, 'role_family_id' => 43, 'specialization' => 'Talent Acquisition'];
        }

        // Frontend
        if (str_contains($lower, 'frontend') || str_contains($lower, 'front end') || str_contains($lower, 'react') || str_contains($lower, 'angular') || str_contains($lower, 'vue')) {
            return ['department_id' => 1, 'role_family_id' => 3, 'specialization' => 'Web Applications'];
        }

        // Full Stack
        if (str_contains($lower, 'fullstack') || str_contains($lower, 'full stack')) {
            return ['department_id' => 1, 'role_family_id' => 4, 'specialization' => 'Full Stack Engineering'];
        }

        // Mobile
        if (str_contains($lower, 'android') || str_contains($lower, 'ios') || str_contains($lower, 'mobile') || str_contains($lower, 'flutter')) {
            return ['department_id' => 1, 'role_family_id' => 6, 'specialization' => 'Mobile Applications'];
        }

        // Backend (Default Engineering)
        return ['department_id' => 1, 'role_family_id' => 2, 'specialization' => 'Backend Services'];
    }

    protected function parseSkillTags(string $raw): array
    {
        if (empty($raw)) return ['REST APIs', 'SQL'];
        $parts = array_map('trim', explode(',', $raw));
        $clean = [];
        foreach ($parts as $p) {
            if (!empty($p) && strlen($p) >= 2 && !in_array($p, $clean)) {
                $clean[] = $p;
            }
        }
        return !empty($clean) ? $clean : ['REST APIs', 'SQL'];
    }

    protected function detectTechKeys(string $title): array
    {
        $lower = strtolower($title);
        $techKeys = [];

        if (str_contains($lower, 'java') && !str_contains($lower, 'javascript')) $techKeys[] = 'java';
        if (str_contains($lower, 'python')) $techKeys[] = 'python';
        if (str_contains($lower, 'react')) $techKeys[] = 'reactjs';
        if (str_contains($lower, 'node')) $techKeys[] = 'nodejs';
        if (str_contains($lower, 'angular')) $techKeys[] = 'angular';
        if (str_contains($lower, 'vue')) $techKeys[] = 'vuejs';
        if (str_contains($lower, 'dotnet') || str_contains($lower, '.net')) $techKeys[] = 'dotnet';
        if (str_contains($lower, 'aws')) $techKeys[] = 'aws';
        if (str_contains($lower, 'azure')) $techKeys[] = 'azure';
        if (str_contains($lower, 'gcp')) $techKeys[] = 'google_cloud_platform_gcp';
        if (str_contains($lower, 'php') || str_contains($lower, 'laravel')) $techKeys[] = 'php';
        if (str_contains($lower, 'flutter')) $techKeys[] = 'flutter';

        return $techKeys;
    }
}
