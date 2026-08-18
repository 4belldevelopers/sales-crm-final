<?php

/***########################################################
Controller    	: 	AdminJobsController
Purpose			:
Author			:	Abu
Company			:	4Bell
Date			:	05-April-2021
########################################################***/

namespace App\Http\Controllers\ServiceCompany;

use App\Helper\GeneralMethod;
use App\Helper\Reply;
use App\Http\Requests\StoreJob;
use App\Jobs\ElasticCompanyIndex;
use App\Jobs\ElasticJobsIndex;
use App\Jobs\QueueNotificationMailService;
use App\Mail\RequisitionUpdateStatusMail;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\Skill;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\JobService;
use App\Services\MailService;
use App\Services\MassMailService;
use App\Services\NotificationMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ServiceCompanyJobsController extends ServiceCompanyBaseController
{
    /** Company-commercial policy options shared by drawer rendering and save validation. */
    private const COMMERCIAL_REPLACEMENT_PERIODS = [30 => '30 Days', 60 => '60 Days', 90 => '90 Days', 180 => '180 Days'];

    private const COMMERCIAL_REPLACEMENT_TYPES = ['free_replacement' => 'Free Replacement', 'credit_note' => 'Credit Note', 'none' => 'No Replacement'];

    private const COMMERCIAL_REPLACEMENT_ELIGIBILITY = ['early_exit' => 'Candidate Resignation / Early Exit', 'employer_termination' => 'Employer Termination', 'any_separation' => 'Any Early Separation', 'any_conditions' => 'Any conditions'];

    // Intent: provide the validated billing cadence choices used by both the commercial drawer and save handler.
    private const COMMERCIAL_BILLING_FREQUENCIES = ['one_time' => 'One Time', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly'];

    private const COMMERCIAL_BILLING_TRIGGERS = ['candidate_joined' => 'Candidate Joined', 'offer_accepted' => 'Offer Accepted', 'invoice_raised' => 'Invoice Raised'];

    private const COMMERCIAL_PAYMENT_TERMS = [0 => 'Due Immediately', 15 => '15 Days', 30 => '30 Days', 45 => '45 Days', 60 => '60 Days', 90 => '90 Days', 120 => '120 Days', 150 => '150 Days', 180 => '180 Days', 210 => '210 Days', 240 => '240 Days'];

    protected $mailService;

    protected $massMailService;

    protected $notificationMailService;

    protected $elasticService;

    public function __construct(MailService $mailService, MassMailService $massMailService, NotificationMailService $notificationMailService, ElasticService $elasticService)
    {
        parent::__construct();
        $this->pageTitle = 'Requisition';
        $this->pageIcon = 'icon-badge';
        $this->mailService = $mailService;
        $this->massMailService = $massMailService;
        $this->notificationMailService = $notificationMailService;
        $this->elasticService = $elasticService;

    }

    public function getJobSuggestions(Request $request)
    {
        $search = $request->input('search');

        // Fetch job titles from the database that match the query
        $jobTitles = tenant_table('jd_details')
            ->where('title', 'LIKE', '%'.$search.'%')
            ->where('status', '=', 1)
            ->pluck('title')->toArray(); // Assuming your table is 'jobs' and column is 'title'

        // dd(response()->json($jobTitles));
        return response()->json($jobTitles);
    }

    public function jdDetails(Request $request)
    {

        // dd($request->title);
        if (! is_null($request->gdDetails)) {

            $text = strip_tags($request->gdDetails);

            // Technology Dictionary
            $dictionary = [
                // Salesforce
                'Salesforce',
                'Sales Cloud',
                'Service Cloud',
                'Experience Cloud',
                'Marketing Cloud',
                'Marketing Cloud Account Engagement',
                'Pardot',
                'Commerce Cloud',
                'Revenue Cloud',
                'Salesforce CPQ',
                'Data Cloud',
                'Field Service Lightning',
                'FSL',
                'Vlocity',
                'OmniStudio',
                'Einstein Analytics',
                'Tableau CRM',
                'Einstein AI',
                'Einstein Bot',
                'Einstein Discovery',
                'Apex',
                'Visualforce',
                'Lightning',
                'Lightning Web Components',
                'LWC',
                'Aura Components',
                'Flows',
                'SOQL',
                'SOSL',
                'Salesforce Integration',
                'Salesforce Admin',
                'Platform App Builder',
                'AppExchange',
                'Platform Events',
                'Change Data Capture',
                'Custom Metadata',
                'Salesforce Connect',
                'Salesforce Functions',
                'Salesforce IoT',
                'Journey Builder',
                'Automation Studio',
                'Interaction Studio',
                'Mobile Publisher',
                'Service Cloud Voice',
                'Financial Services Cloud',
                'Health Cloud',
                'Nonprofit Success Pack',
                'NPSP',
                'Education Data Architecture',
                'EDA',

                // Backend
                'PHP',
                'Laravel',
                'CodeIgniter',
                'Symfony',
                'CakePHP',
                'Yii',
                'Zend',
                'Slim',
                'Lumen',
                'WordPress',
                'Drupal',
                'Magento',
                'Shopify',
                'Java',
                'Spring',
                'Spring Boot',
                'Spring Cloud',
                'Spring Security',
                'Hibernate',
                'Quarkus',
                'Micronaut',
                'Java EE',
                'Jakarta EE',
                'Node.js',
                'Express.js',
                'NestJS',
                'Fastify',
                'Koa',
                'Hapi',
                'Sails.js',
                'AdonisJS',
                'Strapi',
                'Python',
                'Django',
                'Flask',
                'FastAPI',
                'Pyramid',
                'Tornado',
                'Web2Py',
                '.NET',
                '.NET Core',
                '.NET 5',
                '.NET 6',
                '.NET 7',
                '.NET 8',
                'ASP.NET',
                'ASP.NET Core',
                'C#',
                'F#',
                'VB.NET',
                'Blazor',
                'Entity Framework',
                'EF Core',
                'Ruby',
                'Ruby on Rails',
                'Sinatra',
                'Go',
                'Golang',
                'Gin',
                'Echo',
                'Fiber',
                'Rust',
                'Actix',
                'Rocket',
                'Scala',
                'Play Framework',
                'Akka',
                'Elixir',
                'Phoenix',
                'Clojure',
                'Haskell',
                'Dart',
                'Groovy',
                'Grails',
                'Perl',
                'Lua',

                // Frontend
                'JavaScript',
                'TypeScript',
                'React',
                'React Native',
                'Next.js',
                'Remix',
                'Gatsby',
                'Angular',
                'AngularJS',
                'Vue',
                'Nuxt.js',
                'Svelte',
                'SvelteKit',
                'SolidJS',
                'Astro',
                'jQuery',
                'Alpine.js',
                'HTMX',
                'Backbone.js',
                'Ember.js',
                'Meteor',
                'Polymer',
                'Lit',
                'Web Components',
                'EMD',
                'GDS',
                'NDC',
                'JSON',
                'XML',
                'YAML',
                'CSV',
                'SDLC',
                'HTML',
                'HTML5',
                'XHTML',
                'CSS',
                'CSS3',
                'SASS',
                'SCSS',
                'LESS',
                'Stylus',
                'PostCSS',
                'Bootstrap',
                'Tailwind',
                'Tailwind CSS',
                'Foundation',
                'Bulma',
                'Material UI',
                'MUI',
                'Ant Design',
                'Chakra UI',
                'Semantic UI',
                'Shadcn UI',
                'Radix UI',
                'Headless UI',
                'Mantine',
                'Styled Components',
                'Emotion',
                'CSS Modules',
                'CSS-in-JS',
                'Redux',
                'Redux Toolkit',
                'Vuex',
                'Pinia',
                'MobX',
                'Zustand',
                'Recoil',
                'Jotai',
                'Context API',
                'RxJS',
                'D3.js',
                'Chart.js',
                'Three.js',
                'GSAP',
                'Framer Motion',
                'Lottie',
                'Storybook',
                'Webpack',
                'Vite',
                'Rollup',
                'Parcel',
                'esbuild',
                'SWC',
                'Babel',
                'ESLint',
                'Prettier',
                'Postman',
                'Insomnia',
                'Swagger',
                'OpenAPI',
                'Lodash',
                'Underscore.js',
                'Axios',
                'Moment.js',
                'Day.js',
                'date-fns',
                'Formik',
                'React Hook Form',
                'Yup',
                'Zod',
                'Joi',
                'i18n',
                'React Intl',
                'PWA',
                'Service Worker',
                'Web Workers',
                'WebSocket',
                'Server-Sent Events',

                // Mobile
                'iOS',
                'Android',
                'Swift',
                'SwiftUI',
                'Objective-C',
                'Kotlin',
                'Jetpack Compose',
                'Flutter',
                'Dart',
                'React Native',
                'Ionic',
                'Capacitor',
                'Xamarin',
                '.NET MAUI',
                'Cordova',
                'Expo',
                'React Navigation',
                'Flutter Navigation',
                'Mobile UI/UX',

                // Database
                'MySQL',
                'PostgreSQL',
                'MongoDB',
                'Redis',
                'Oracle',
                'SQL Server',
                'MSSQL',
                'MariaDB',
                'SQLite',
                'Cassandra',
                'CouchDB',
                'Couchbase',
                'DynamoDB',
                'Firebase Realtime Database',
                'Firestore',
                'Supabase',
                'PlanetScale',
                'CockroachDB',
                'TiDB',
                'Neo4j',
                'ArangoDB',
                'OrientDB',
                'GraphDB',
                'InfluxDB',
                'TimescaleDB',
                'ClickHouse',
                'Snowflake',
                'BigQuery',
                'Redshift',
                'Synapse',
                'Databricks',
                'Delta Lake',
                'Apache Iceberg',
                'Prisma',
                'TypeORM',
                'Sequelize',
                'Hibernate',
                'Doctrine',
                'Eloquent',
                'SQLAlchemy',
                'Mongoose',
                'Drizzle',
                'Knex.js',
                'Query Builder',
                'Database Design',
                'Data Modeling',
                'Normalization',
                'Indexing',
                'Query Optimization',
                'Database Administration',
                'DBA',
                'Replication',
                'Sharding',
                'Partitioning',

                // Cloud
                'AWS',
                'Amazon Web Services',
                'Azure',
                'Microsoft Azure',
                'GCP',
                'Google Cloud Platform',
                'DigitalOcean',
                'Heroku',
                'Vercel',
                'Netlify',
                'Cloudflare',
                'Linode',
                'Vultr',
                'OVH',
                'Alibaba Cloud',
                'Oracle Cloud',
                'IBM Cloud',
                'EC2',
                'ECS',
                'EKS',
                'Fargate',
                'Lambda',
                'S3',
                'RDS',
                'DynamoDB',
                'CloudFront',
                'Route 53',
                'CloudWatch',
                'CloudFormation',
                'CDK',
                'Terraform',
                'SNS',
                'SQS',
                'Kinesis',
                'Step Functions',
                'Secrets Manager',
                'Parameter Store',
                'IAM',
                'VPC',
                'ALB',
                'ELB',
                'API Gateway',
                'Cognito',
                'Azure Functions',
                'Azure DevOps',
                'Azure App Service',
                'Azure SQL',
                'Azure Blob Storage',
                'Azure Active Directory',
                'Entra ID',
                'Azure Kubernetes Service',
                'AKS',
                'GCP Cloud Run',
                'GCP Cloud Functions',
                'GCP BigQuery',
                'GCP Pub/Sub',
                'GCP Cloud Storage',
                'GCP GKE',
                'Firebase',
                'Amplify',
                'Serverless Framework',

                // DevOps
                'Docker',
                'Kubernetes',
                'K8s',
                'Jenkins',
                'Terraform',
                'Ansible',
                'Puppet',
                'Chef',
                'SaltStack',
                'Git',
                'GitHub',
                'GitLab',
                'Bitbucket',
                'CI/CD',
                'GitHub Actions',
                'GitLab CI',
                'CircleCI',
                'Travis CI',
                'Bitbucket Pipelines',
                'Bamboo',
                'ArgoCD',
                'FluxCD',
                'Helm',
                'Kustomize',
                'Prometheus',
                'Grafana',
                'Datadog',
                'New Relic',
                'Dynatrace',
                'AppDynamics',
                'Splunk',
                'ELK Stack',
                'Elastic Stack',
                'Loki',
                'Jaeger',
                'Zipkin',
                'OpenTelemetry',
                'Nagios',
                'Zabbix',
                'PagerDuty',
                'Opsgenie',
                'Victoriametrics',
                'Uptime Kuma',
                'Healthchecks',
                'Infrastructure as Code',
                'IaC',
                'GitOps',
                'DevSecOps',
                'SRE',
                'Site Reliability',
                'Incident Management',
                'On-Call',
                'Runbooks',
                'Linux',
                'Ubuntu',
                'CentOS',
                'RHEL',
                'Debian',
                'Alpine',
                'Amazon Linux',
                'Shell Scripting',
                'Bash',
                'PowerShell',
                'Systemd',
                'Nginx',
                'Apache',
                'Caddy',
                'HAProxy',
                'Traefik',
                'Envoy',
                'ISC DHCP',
                'BIND',
                'DNS',
                'SSL/TLS',
                'Let\'s Encrypt',
                'Certbot',

                // API
                'REST',
                'REST API',
                'RESTful',
                'SOAP',
                'GraphQL',
                'gRPC',
                'API',
                'APIs',
                'Web API',
                'API Design',
                'API Development',
                'API Gateway',
                'API Management',
                'API Versioning',
                'API Documentation',
                'API Testing',
                'API Security',
                'Rate Limiting',
                'Throttling',
                'Caching',
                'Pagination',
                'HATEOAS',
                'OpenAPI',
                'Swagger',
                'API Blueprint',
                'RAML',
                'Postman',
                'Insomnia',
                'SoapUI',
                'REST Assured',
                'WireMock',
                'Mockoon',

                // Search
                'Elasticsearch',
                'OpenSearch',
                'Solr',
                'Algolia',
                'Meilisearch',
                'Typesense',
                'Lucene',
                'Sphinx',
                'Azure Cognitive Search',
                'Amazon OpenSearch Service',

                // Messaging
                'Kafka',
                'Apache Kafka',
                'Confluent',
                'RabbitMQ',
                'ActiveMQ',
                'Amazon SQS',
                'Amazon SNS',
                'Azure Service Bus',
                'Google Pub/Sub',
                'Redis Streams',
                'Redis Pub/Sub',
                'NATS',
                'MQTT',
                'Apache Pulsar',
                'Event Streaming',
                'Message Queue',
                'Pub/Sub',
                'Event-Driven',

                // Testing
                'PHPUnit',
                'Pest',
                'JUnit',
                'TestNG',
                'Selenium',
                'Cypress',
                'Playwright',
                'Puppeteer',
                'Jest',
                'Vitest',
                'Mocha',
                'Chai',
                'Jasmine',
                'Karma',
                'Cucumber',
                'Behat',
                'RSpec',
                'PyTest',
                'Unittest',
                'NUnit',
                'xUnit',
                'Robot Framework',
                'Appium',
                'Detox',
                'XCUITest',
                'Espresso',
                'JMeter',
                'LoadRunner',
                'K6',
                'Gatling',
                'Locust',
                'TDD',
                'BDD',
                'Unit Testing',
                'Integration Testing',
                'E2E Testing',
                'End-to-End Testing',
                'Functional Testing',
                'Regression Testing',
                'Smoke Testing',
                'Performance Testing',
                'Load Testing',
                'Stress Testing',
                'Security Testing',
                'Penetration Testing',
                'UAT',
                'User Acceptance Testing',
                'Manual Testing',
                'QA',
                'Quality Assurance',
                'Test Automation',
                'Test Planning',
                'Test Cases',
                'Test Strategy',

                // AI
                'Machine Learning',
                'ML',
                'Artificial Intelligence',
                'AI',
                'Deep Learning',
                'Neural Networks',
                'NLP',
                'Natural Language Processing',
                'Computer Vision',
                'OpenAI',
                'GPT',
                'GPT-3',
                'GPT-4',
                'ChatGPT',
                'LLM',
                'Large Language Models',
                'BERT',
                'Transformers',
                'Hugging Face',
                'LangChain',
                'LlamaIndex',
                'RAG',
                'Retrieval Augmented Generation',
                'Prompt Engineering',
                'Fine-tuning',
                'TensorFlow',
                'PyTorch',
                'Keras',
                'Scikit-learn',
                'OpenCV',
                'MLflow',
                'Kubeflow',
                'SageMaker',
                'Vertex AI',
                'Azure ML',
                'Azure OpenAI',
                'Amazon Bedrock',
                'Databricks ML',
                'Data Science',
                'Data Analysis',
                'Feature Engineering',
                'Model Training',
                'Model Deployment',
                'MLOps',
                'Reinforcement Learning',
                'Generative AI',
                'AI Ethics',
                'Responsible AI',

                // Architecture
                'Microservices',
                'MVC',
                'MVVM',
                'MVP',
                'Design Patterns',
                'SOLID Principles',
                'SOLID',
                'Clean Architecture',
                'Hexagonal Architecture',
                'Onion Architecture',
                'Domain-Driven Design',
                'DDD',
                'Event-Driven Architecture',
                'CQRS',
                'Event Sourcing',
                'Serverless',
                'SOA',
                'Monolith',
                'Multi-tenant',
                'High Availability',
                'HA',
                'Scalability',
                'Load Balancing',
                'Fault Tolerance',
                'Resilience',
                'Circuit Breaker',
                'Retry Pattern',
                'Saga Pattern',
                'Outbox Pattern',
                'Twelve-Factor App',
                'API First',
                'Design First',
                'Contract Testing',
                'Technical Debt',
                'Refactoring',
                'Code Review',
                'Pair Programming',
                'TDD',
                'BDD',
                'Agile',
                'Scrum',
                'Kanban',
                'Lean',
                'Waterfall',
                'SAFe',
                'DevOps',
                'DevSecOps',
                'Shift-Left',
                'Observability',
                'Distributed Systems',
                'Concurrency',
                'Multithreading',
                'Async Programming',

                // Security
                'OAuth',
                'OAuth2',
                'JWT',
                'JSON Web Token',
                'SAML',
                'SSO',
                'Single Sign-On',
                'MFA',
                'Multi-Factor Authentication',
                '2FA',
                'Two-Factor Authentication',
                'HTTPS',
                'SSL',
                'TLS',
                'OWASP',
                'OWASP Top 10',
                'XSS',
                'CSRF',
                'SQL Injection',
                'Injection',
                'Authentication',
                'Authorization',
                'RBAC',
                'Role-Based Access Control',
                'ABAC',
                'Attribute-Based Access Control',
                'Encryption',
                'Hashing',
                'Cryptography',
                'AES',
                'RSA',
                'bcrypt',
                'Argon2',
                'Password Hashing',
                'Secrets Management',
                'Vault',
                'AWS Secrets Manager',
                'Azure Key Vault',
                'Penetration Testing',
                'Pentesting',
                'Vulnerability Assessment',
                'Security Audit',
                'Compliance',
                'GDPR',
                'HIPAA',
                'PCI DSS',
                'SOC 2',
                'ISO 27001',
                'Zero Trust',
                'CSP',
                'Content Security Policy',
                'CORS',
                'CSP',
                'Rate Limiting',
                'WAF',
                'Web Application Firewall',
                'DDoS Protection',
                'CAPTCHA',
                'reCAPTCHA',

                // Data Engineering
                'Apache Spark',
                'Spark',
                'PySpark',
                'Hadoop',
                'HDFS',
                'Hive',
                'Presto',
                'Trino',
                'Apache Flink',
                'Apache Beam',
                'Airflow',
                'Dagster',
                'Prefect',
                'Luigi',
                'dbt',
                'Fivetran',
                'Stitch',
                'Airbyte',
                'Matillion',
                'Hevo Data',
                'ETL',
                'ELT',
                'Data Pipeline',
                'Data Warehouse',
                'Data Lake',
                'Data Lakehouse',
                'Data Mesh',
                'Data Governance',
                'Data Quality',
                'Data Lineage',
                'Master Data Management',
                'MDM',
                'Data Catalog',
                'Metadata Management',
                'Data Modeling',
                'Star Schema',
                'Snowflake Schema',
                'Slowly Changing Dimensions',
                'SCD',

                // IBM AS400
                'AS400',
                'AS/400',
                'IBM i',
                'IBM iSeries',
                'IBM System i',
                'RPG',
                'RPGLE',
                'RPG IV',
                'ILE RPG',
                'CL',
                'CLLE',
                'DDS',
                'DDL',
                'DB2',
                'DB2 for i',
                'SQL',
                'Embedded SQL',
                'Free Form RPGLE',
                'Fixed Format RPG',
                'OS400',
                'IBM i Access',
                'RDi',
                'Rational Developer for i',
                'WDSC',
                'SEU',
                'PDM',
                'WRKDBG',
                'Job Description',
                'Subsystem',
                'Object Authority',
                'Library List',
                'Spool File',
                'Data Queue',
                'User Space',
                'Journaling',
                'Commitment Control',

                // ERP
                'SAP',
                'SAP S/4HANA',
                'SAP ECC',
                'SAP ABAP',
                'SAP Fiori',
                'SAP BW',
                'SAP HANA',
                'Oracle ERP',
                'Oracle E-Business Suite',
                'EBS',
                'Oracle Fusion',
                'PeopleSoft',
                'NetSuite',
                'Microsoft Dynamics 365',
                'Dynamics 365',
                'Dynamics AX',
                'Dynamics NAV',
                'Dynamics GP',
                'Infor',
                'Workday',
                'Odoo',
                'Sage',
                'Epicor',
                'JD Edwards',

                // Mainframe
                'COBOL',
                'JCL',
                'CICS',
                'VSAM',
                'IMS',
                'DB2',
                'RACF',
                'TSO',
                'ISPF',
                'z/OS',
                'MVS',
                'Assembler',
                'PL/I',
                'REXX',
                'Mainframe',

                // BI
                'Power BI',
                'Tableau',
                'QlikView',
                'Qlik Sense',
                'Looker',
                'LookML',
                'Metabase',
                'Superset',
                'Redash',
                'Grafana',
                'Microsoft Reporting Services',
                'SSRS',
                'Crystal Reports',
                'MicroStrategy',
                'SAP Analytics Cloud',
                'Domo',
                'Sisense',
                'ThoughtSpot',
                'Business Intelligence',
                'BI',
                'Data Visualization',
                'Dashboard',
                'Reporting',
                'Analytics',
                'OLAP',
                'Data Discovery',
                'Self-Service BI',

                // CMS
                'WordPress',
                'WordPress Development',
                'WooCommerce',
                'ACF',
                'Advanced Custom Fields',
                'Drupal',
                'Joomla',
                'Magento',
                'Magento 2',
                'Shopify',
                'Shopify Plus',
                'BigCommerce',
                'Squarespace',
                'Wix',
                'Webflow',
                'Contentful',
                'Strapi',
                'Sanity',
                'Payload CMS',
                'Headless CMS',
                'Ghost',
                'Typo3',
                'Craft CMS',
                'HubSpot CMS',

                // Project Management & Collaboration
                'Jira',
                'Confluence',
                'Trello',
                'Asana',
                'Monday.com',
                'Slack',
                'Microsoft Teams',
                'Notion',
                'ClickUp',
                'Linear',
                'Basecamp',
                'Azure DevOps',
                'ADO',
                'Rally',
                'VersionOne',
                'Project Management',
                'Product Management',
                'Scrum Master',
                'Product Owner',
                'Agile Coach',
                'Release Management',
                'Change Management',
                'Risk Management',
                'Stakeholder Management',

                // Design & Prototyping
                'Figma',
                'Sketch',
                'Adobe XD',
                'InVision',
                'Zeplin',
                'Framer',
                'Principle',
                'ProtoPie',
                'Balsamiq',
                'Wireframe',
                'Mockup',
                'Prototype',
                'UI Design',
                'UX Design',
                'User Experience',
                'User Interface',
                'Design System',
                'Design Thinking',
                'User Research',
                'Usability Testing',
                'A/B Testing',
                'Accessibility',
                'WCAG',
                'ADA Compliance',

                // Low-Code/No-Code
                'Salesforce Flow',
                'Power Apps',
                'Power Automate',
                'OutSystems',
                'Mendix',
                'Appian',
                'Bubble',
                'Webflow',
                'Zapier',
                'Make',
                'Integromat',
                'Retool',
                'Internal Tools',
                'Low-Code',
                'No-Code',

                // Blockchain
                'Blockchain',
                'Ethereum',
                'Solidity',
                'Web3',
                'Smart Contracts',
                'DeFi',
                'NFT',
                'IPFS',
                'Hardhat',
                'Truffle',
                'Remix',

                // IoT
                'IoT',
                'Internet of Things',
                'MQTT',
                'Azure IoT Hub',
                'AWS IoT Core',
                'Google Cloud IoT',
                'Arduino',
                'Raspberry Pi',
                'Embedded Systems',
                'Edge Computing',

                // Gaming
                'Unity',
                'Unreal Engine',
                'Godot',
                'Cocos2d',
                'Phaser',
                'Three.js',
                'WebGL',
                'WebGPU',
                'Game Development',
                '2D Games',
                '3D Games',

                // RPA
                'UiPath',
                'Automation Anywhere',
                'Blue Prism',
                'Robotic Process Automation',
                'RPA',
                'Process Automation',
                'Bot Development',

                // Networking
                'TCP/IP',
                'HTTP',
                'HTTPS',
                'FTP',
                'SFTP',
                'SSH',
                'DNS',
                'DHCP',
                'VPN',
                'CDN',
                'Load Balancer',
                'Proxy',
                'Reverse Proxy',
                'Firewall',
                'Network Security',
                'Networking',

                // Other Skills
                'Agile',
                'Scrum',
                'Kanban',
                'Waterfall',
                'SDLC',
                'Software Development Life Cycle',
                'Requirement Analysis',
                'System Analysis',
                'Technical Writing',
                'Documentation',
                'Code Review',
                'Mentoring',
                'Coaching',
                'Training',
                'Presentation Skills',
                'Communication',
                'Problem Solving',
                'Analytical Skills',
                'Team Lead',
                'Tech Lead',
                'Architecture',
                'Solution Architecture',
                'Enterprise Architecture',
                'Cloud Architecture',
                'System Design',
                'Integration',
                'Data Migration',
                'System Integration',
                'Third-Party Integration',
                'Legacy Systems',
                'Legacy Modernization',
                'Code Refactoring',
                'Performance Optimization',
                'Troubleshooting',
                'Debugging',
                'Monitoring',
                'Logging',
                'Alerting',
                'Incident Response',
                'Disaster Recovery',
                'Web development',
                'Business Continuity',
                'Backup',
                'High Availability',
                'Scalability',
                'Reliability',
                'Technical Debt',
                'Code Quality',
                'Best Practices',
                'Standards Compliance',
                'Regulatory Compliance',
            ];

            // dd(count($dictionary));

            $skills = [];

            foreach ($dictionary as $skill) {

                if (preg_match('/\b'.preg_quote($skill, '/').'\b/i', $text)) {

                    $count = preg_match_all(
                        '/\b'.preg_quote($skill, '/').'\b/i',
                        $text
                    );

                    $score = 50 + ($count * 10);

                    // Must Have Boost
                    if (preg_match(
                        '/(required|must have|mandatory|essential|expertise in|experience with).{0,100}'.
                        preg_quote($skill, '/').'/is',
                        $text
                    )) {
                        $score += 20;
                    }

                    // Job Title Boost
                    if (! empty($request->title) &&
                        stripos($request->title, $skill) !== false) {
                        $score += 20;
                    }

                    $skills[] = [
                        'skill' => $skill,
                        'confidence' => min($score, 100),
                    ];
                }
            }

            $result = collect($skills)
                ->unique('skill')
                ->sortByDesc('confidence')
                ->take(25)
                ->values()
                ->toArray();

            return Reply::dataOnly([
                'status' => 'success',
                'jd' => '',
                'minimum_exp' => '',
                'maxmum_exp' => '',
                'skills' => $result,
            ]);
        }

        $search = $request->title;
        $jobTitles = tenant_table('jd_details')
            ->where('title', '=', $search)->where('status', '=', 1)->first();

        return Reply::dataOnly(['status' => 'success', 'jd' => @$jobTitles->jd, 'minimum_exp' => round(@$jobTitles->minimum_exp), 'maxmum_exp' => round(@$jobTitles->maxmum_exp), 'skills' => @$jobTitles->skills]);

    }

    public function index()
    {
        // Step 9b: ETag derived from user+company+permissions. Unchanged page load → 304, zero DB hit.
        $etagSource = $this->user->id.':'.$this->user->company_id.':'.implode(',', $this->userPermissions ?? []);
        $etag = '"'.hash('sha256', $etagSource).'"';
        if (request()->header('If-None-Match') === $etag) {
            return response()->noContent(304)->header('ETag', $etag);
        }

        // BUG-03: was Company::all() — full table scan every request. Now single-tenant lookup with 1h cache.
        $this->company = Cache::remember('company:'.$this->user->company_id, 3600, fn () => Company::find($this->user->company_id));
        $this->totalJobs = Job::where('id', '>', '0')->count();
        $this->activeJobs = Job::where('status', 'active')->count();
        $this->inactiveJobs = Job::where('status', 'inactive')->count();

        $chkAdminRole = $this->user->hasRole('admin');

        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if ($chkAdminRole > 0) {
            $this->totalJobs = Job::where('id', '>', '0')->count();
            $this->activeJobs = Job::where('status', 'active')->count();
            $this->inactiveJobs = Job::where('status', 'inactive')->count();
            $this->holdJobs = Job::where('status', 'hold')->count();

        } else {

            $this->totalJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.id', '>', '0')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->activeJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.status', 'active')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->inactiveJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.status', 'inactive')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->holdJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.status', 'hold')->where('job_assign.user_id', '=', $this->user->id)->count();

        }
        $this->dataCnt = $this->dataCnt();

        if (session()->has('UrlMailJobId')) {
            $this->UrlMailJobId = session('UrlMailJobId');
        } else {
            $this->UrlMailJobId = '';
        }
        // GeneralMethod::elasticJobsAddEditUpdateIndex(482);

        // dd("djdw");

        return response()
            ->view('service_company.jobs.index', $this->data)
            ->header('ETag', $etag)
            ->header('Cache-Control', 'private, must-revalidate');
    }

    public function dataCnt()
    {

        $company_id = auth()->user()->company_id;

        $dataCnt = [];

        $dataCnt['allRequisition'] = 0;
        $dataCnt['clientRequisition'] = 0;
        $dataCnt['customRequisition'] = 0;
        $dataCnt['findJobs'] = 0;
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if (($chkAdminRole > 0) && (in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
            // All Jobs (ES Aggregation)
            $filter[] = ['term' => ['status' => 'active']];
            // 1. COMPANY ACCESS CONDITIONS
            $must[] = [
                'bool' => [
                    'should' => [
                        [
                            'term' => [
                                'comId' => $company_id,
                            ],
                        ],

                        [
                            'nested' => [
                                'path' => 'recommendation_relation',
                                'query' => [
                                    'bool' => [
                                        'must' => [
                                            [
                                                'term' => [
                                                    'recommendation_relation.to_company_id' => $company_id,
                                                ],
                                            ],
                                            [
                                                'terms' => [
                                                    'recommendation_relation.final_status' => [1, 2],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
            // 2. RESTRICT TO ASSIGNED USERS (if not admin)
            if (! (($chkAdminRole > 0) && in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
                $must[] = [
                    'nested' => [
                        'path' => 'job_assign',
                        'query' => [
                            'term' => ['job_assign.user_id' => auth()->user()->id],
                        ],
                    ],
                ];
            }
            // FINAL ELASTIC PARAMS
            $params = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $must,
                            'filter' => $filter,
                        ],
                    ],
                ],
            ];
            $response = $this->elasticService->getClient()->count($params);
            $total = $response['count'] ?? 0;
            $dataCnt['allRequisition'] = $total; // $this->jobsAll;

            // BUG-08: dead SQL $clientRequisition count removed — overwritten by ES count below anyway.

            $filter_client[] = ['term' => ['status' => 'active']];
            // 1. COMPANY ACCESS CONDITIONS
            $must_client[] = [
                'bool' => [
                    'should' => [
                        [
                            'nested' => [
                                'path' => 'recommendation_relation',
                                'query' => [
                                    'bool' => [
                                        'must' => [
                                            [
                                                'term' => [
                                                    'recommendation_relation.to_company_id' => $company_id,
                                                ],
                                            ],
                                            [
                                                'terms' => [
                                                    'recommendation_relation.final_status' => [1, 2],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
            // 2. RESTRICT TO ASSIGNED USERS (if not admin)
            if (! (($chkAdminRole > 0) && in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
                $must_client[] = [
                    'nested' => [
                        'path' => 'job_assign',
                        'query' => [
                            'term' => ['job_assign.user_id' => auth()->user()->id],
                        ],
                    ],
                ];
            }
            // FINAL ELASTIC PARAMS
            $params_client = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $must_client,
                            'filter' => $filter_client,
                        ],
                    ],
                ],
            ];
            $response = $this->elasticService->getClient()->count($params_client);
            $total = $response['count'] ?? 0;
            $dataCnt['clientRequisition'] = $total; // $clientRequisition;

            // BUG-08: dead SQL $customRequisition count removed — overwritten by ES count below anyway.

            $filter_internal[] = ['term' => ['status' => 'active']];
            // 1. COMPANY ACCESS CONDITIONS
            $must_internal[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['comId' => $company_id]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
            // 2. RESTRICT TO ASSIGNED USERS (if not admin)
            if (! (($chkAdminRole > 0) && in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
                $must_internal[] = [
                    'nested' => [
                        'path' => 'job_assign',
                        'query' => [
                            'term' => ['job_assign.user_id' => auth()->user()->id],
                        ],
                    ],
                ];
            }
            // FINAL ELASTIC PARAMS
            $params_internal = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $must_internal,
                            'filter' => $filter_internal,
                        ],
                    ],
                ],
            ];
            $response = $this->elasticService->getClient()->count($params_internal);
            $total_internal = $response['count'] ?? 0;
            $dataCnt['customRequisition'] = $total_internal; // $customRequisition;

            // dd("djddf");
        } else {
            //    dd("djd");
            // All Jobs
            $this->jobsAll = DB::table('jobs')->select('jobs.id');
            $this->jobsAll = $this->jobsAll->join('job_assign', function ($join) {
                $join->on('job_assign.job_id', '=', 'jobs.id');
                $join->on('job_assign.company_id', '=', DB::raw("'".auth()->user()->company_id."'"));
                $join->on('job_assign.user_id', '=', DB::raw("'".$this->user->id."'"));
            });
            $this->jobsAll = $this->jobsAll->leftJoin('recommendation_relation', function ($join) use ($company_id) {
                $join->on('recommendation_relation.job_id', '=', 'jobs.id');
                $join->on('recommendation_relation.to_company_id', '=', DB::raw("'".$company_id."'"));
            });

            // BUG-10: grouped closure prevents orWhere from leaking cross-tenant rows
            $this->jobsAll = $this->jobsAll->where(function ($query) use ($company_id) {
                $query->where(function ($inner) use ($company_id) {
                    $inner->where('jobs.company_id', '=', $company_id)->where('jobs.status', '=', 'active');
                })->orWhere(function ($inner) use ($company_id) {
                    $inner->where('recommendation_relation.final_status', '=', 1)
                        ->where('recommendation_relation.to_company_id', '=', $company_id)
                        ->where('jobs.status', '=', 'active');
                })->orWhere(function ($inner) use ($company_id) {
                    $inner->where('recommendation_relation.final_status', '=', 2)
                        ->where('recommendation_relation.to_company_id', '=', $company_id)
                        ->where('jobs.status', '=', 'active');
                });
            });
            $this->jobsAll = $this->jobsAll->count();
            $dataCnt['allRequisition'] = $this->jobsAll;

            // BUG-10: grouped closure prevents orWhere from leaking cross-tenant rows
            $clientRequisition = DB::table('recommendation_relation')
                ->join('job_assign', function ($join) {
                    $join->on('job_assign.job_id', '=', 'recommendation_relation.job_id');
                    $join->on('job_assign.company_id', '=', DB::raw("'".auth()->user()->company_id."'"));
                    $join->on('job_assign.user_id', '=', DB::raw("'".$this->user->id."'"));
                })
                ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                ->select('recommendation_relation.id')
                ->where(function ($query) use ($company_id) {
                    $query->where(function ($inner) use ($company_id) {
                        $inner->where('recommendation_relation.final_status', '=', 1)
                            ->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('jobs.status', '=', 'active');
                    })->orWhere(function ($inner) use ($company_id) {
                        $inner->where('recommendation_relation.final_status', '=', 2)
                            ->where('recommendation_relation.to_company_id', '=', $company_id)
                            ->where('jobs.status', '=', 'active');
                    });
                })
                ->count();
            $dataCnt['clientRequisition'] = $clientRequisition;

            // Custom Client
            $customRequisition = DB::table('jobs')
                ->join('job_assign', function ($join) {
                    $join->on('job_assign.job_id', '=', 'jobs.id');
                    $join->on('job_assign.company_id', '=', DB::raw("'".auth()->user()->company_id."'"));
                    $join->on('job_assign.user_id', '=', DB::raw("'".$this->user->id."'"));
                })
                ->select('jobs.id')
                ->where('jobs.company_id', '=', $company_id)->where('jobs.status', '=', 'active')
                ->count();
            $dataCnt['customRequisition'] = $customRequisition;

        }

        // BUG-09 + BUG-18: replaced materialized pluck+PHP count() with a single DB count using
        // whereNotExists subquery — eliminates the 100k-ID IN-list and the full collection fetch.
        $dataCnt['findJobs'] = DB::table('jobs')
            ->where('jobs.company_id', '!=', $company_id)
            ->where('jobs.public_market_status', '=', 2)
            ->where('jobs.status', '=', 'active')
            ->whereNotExists(function ($query) use ($company_id) {
                $query->select(DB::raw(1))
                    ->from('recommendation_relation')
                    ->whereColumn('recommendation_relation.job_id', 'jobs.id')
                    ->where('recommendation_relation.to_company_id', '=', $company_id);
            })
            ->distinct()
            ->count('jobs.id');

        // sent request
        $dataCnt['jobRequest'] = 0;
        $jobRequest = DB::table('recommendation_relation')
            ->select('recommendation_relation.id')
            ->where('recommendation_relation.to_company_id', '=', $company_id)
            ->whereIn('recommendation_relation.final_status', [0, 3])
            ->count();

        $dataCnt['jobRequest'] = $jobRequest;

        // Tab counts — same access conditions as all_data_ajax, filtered by status
        $tabMust = [];
        $tabMust[] = [
            'bool' => [
                'should' => [
                    ['term' => ['comId' => $company_id]],
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['term' => ['recommendation_relation.to_company_id' => $company_id]],
                                        ['terms' => ['recommendation_relation.final_status' => [1, 2]]],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];
        if (! (($chkAdminRole > 0) && in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
            $tabMust[] = [
                'nested' => [
                    'path' => 'job_assign',
                    'query' => ['term' => ['job_assign.user_id' => auth()->user()->id]],
                ],
            ];
        }
        $tabStatusCounts = ['active' => 0, 'hold' => 0, 'inactive' => 0];
        if ($this->elasticService->indexExists(ELASTIC_JOBS)) {
            foreach ($tabStatusCounts as $status => $val) {
                $res = $this->elasticService->getClient()->count([
                    'index' => ELASTIC_JOBS,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $tabMust,
                                'filter' => [['term' => ['status' => $status]]],
                            ],
                        ],
                    ],
                ]);
                $tabStatusCounts[$status] = $res['count'] ?? 0;
            }
        }
        $dataCnt['hiringCount'] = $tabStatusCounts['active'];
        $dataCnt['holdCount'] = $tabStatusCounts['hold'];
        $dataCnt['closedCount'] = $tabStatusCounts['inactive'];
        $dataCnt['allTabCount'] = $tabStatusCounts['active'] + $tabStatusCounts['hold'] + $tabStatusCounts['inactive'];

        return $dataCnt;

    }

    public function client_job_data_ajax(Request $request)
    {
        //    dd("k");
        // abort_if(Gate::denies('view_jobs'), 403);
        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;

        $chkAdminRole = $this->user->hasRole('admin');

        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $client_id = request('client_id');
        $requisitionRow = DB::table('clients')->where('id', $client_id)->first();
        $toCompanyId = (int) $requisitionRow->company_id;
        // dd($toCompanyId);
        $custom = request('custom');

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $custom_status = request('custom_status');
        $custom_hiringtype = request('custom_hiringtype');
        $custom_search = trim(request('custom_search'));

        if (request('custom') == 'true') {
            $must[] = [
                'bool' => [
                    'should' => [
                        ['term' => ['client_id' => $client_id]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];

            // FINAL ELASTIC PARAMS
            $params = [
                'index' => ELASTIC_JOBS,
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    'sort' => [],
                    'query' => [
                        'bool' => [
                            'must' => $must,
                            'filter' => [],
                        ],
                    ],
                ],
            ];
        } else {
            // 4. COMPANY ACCESS CONDITIONS
            $must[] = [
                'bool' => [
                    'should' => [
                        [
                            'nested' => [
                                'path' => 'recommendation_relation',
                                'query' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ['recommendation_relation.to_company_id' => $company_id]],
                                            ['term' => ['recommendation_relation.from_company_id' => $toCompanyId]],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];

            // FINAL ELASTIC PARAMS
            $params = [
                'index' => ELASTIC_JOBS,
                'request_cache' => true,
                'body' => [
                    'from' => $from,
                    'size' => $size,
                    'sort' => [],
                    'query' => [
                        'bool' => [
                            'must' => $must,
                            'filter' => [],
                        ],
                    ],
                ],
            ];

        }
        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }
        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];
        $userPermissions = $this->userPermissions;

        return response()->json([
            'html' => view('service_company.ajax_pages.client_jobs_side_bar_ajax', compact('data', 'userPermissions', 'company_id', 'salaryMode', 'currencySymbole', 'custom'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function data(Request $request)
    {
        //    dd("k");
        // abort_if(Gate::denies('view_jobs'), 403);
        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;

        $chkAdminRole = $this->user->hasRole('admin');

        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $client_id = request('client_id');
        $requisitionRow = DB::table('clients')->where('id', $client_id)->first();
        $toCompanyId = (int) $requisitionRow->company_id;
        // dd($toCompanyId);
        $custom = request('custom');

        if (request('custom') == 'true') {
            // dd("custom",$custom);
            $alldatalist = DB::table('jobs')
                ->select(
                    'jobs.id',
                    'jobs.company_id as comId',
                    'jobs.title',
                    'jobs.total_positions',
                    'jobs.work_type',
                    'jobs.show_vendor',
                    'jobs.employment_type',
                    'jobs.priority',
                    'jobs.status',
                    'jobs.min_exp',
                    'jobs.max_exp',
                    'jobs.created_at',
                    'jobs.start_date',
                    'jobs.end_date',
                    'jobs.min_salary',
                    'jobs.max_salary',
                    'jobs.min_sal_mode',
                    'jobs.max_sal_mode',
                    'jobs.min_salary_currency',
                    'jobs.max_salary_currency',
                    'clients.name as clients_name',
                    'recommendation_relation.id as recommendation_id',
                    'recommendation_relation.from_company_id',
                    'recommendation_relation.final_status',
                    'recommendation_relation.to_company_id',
                    'companies.company_name',
                    'companies.logo',
                    'clients.name as client_company',
                    'clients.logo_logical_path',
                    'clients.self_company',
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS allCandidate'),
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),       // MEANS Evaluation
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                    DB::raw('SUM(CASE WHEN job_applications.company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview')
                )
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->join('companies', 'companies.id', 'jobs.company_id')
                ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id');
            if (\request('client_id') != '') {
                $client_id = request('client_id');
                $alldatalist = $alldatalist->where('jobs.client_id', $client_id);
            }
            // Step 7a: Replace MySQL LIKE full-scan with ES multi_match → whereIn(job_ids).
            // LIKE '%q%' had no index and scanned the full jobs table on every keystroke.
            if (\request('search') != '') {
                $searchTerm = trim(request('search'));
                $esMatchedIds = [];
                try {
                    $esResp = $this->elasticService->getClient()->search([
                        'index' => ELASTIC_JOBS,
                        'body' => [
                            'size' => 500,
                            '_source' => ['id'],
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        ['multi_match' => [
                                            'query' => $searchTerm,
                                            'fields' => ['title^3', 'job_description', 'skills'],
                                            'fuzziness' => 'AUTO',
                                        ]],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                    $esMatchedIds = collect($esResp['hits']['hits'])->pluck('_source.id')->all();
                } catch (\Throwable) {
                    // ES unavailable — fall back to MySQL LIKE
                    $alldatalist = $alldatalist->where('title', 'LIKE', "%{$searchTerm}%");
                }
                if (! empty($esMatchedIds)) {
                    $alldatalist = $alldatalist->whereIn('jobs.id', $esMatchedIds);
                }
            }
            // $alldatalist = $alldatalist->where('recommendation_relation.from_company_id', '=', $toCompanyId)
            // ->where('recommendation_relation.to_company_id', '=', $company_id);
            $alldatalist = $alldatalist->groupBy('jobs.id');
            $alldatalist = $alldatalist->orderBy('jobs.id', 'DESC');

            // dd($toCompanyId,$company_id);
        } else {
            // dd("direct",$custom);
            $alldatalist = DB::table('jobs')
                ->select(
                    'jobs.id',
                    'jobs.company_id as comId',
                    'jobs.title',
                    'jobs.total_positions',
                    'jobs.work_type',
                    'jobs.show_vendor',
                    'jobs.employment_type',
                    'jobs.priority',
                    'jobs.status',
                    'jobs.min_exp',
                    'jobs.max_exp',
                    'jobs.created_at',
                    'jobs.start_date',
                    'jobs.end_date',
                    'jobs.min_salary',
                    'jobs.max_salary',
                    'jobs.min_sal_mode',
                    'jobs.max_sal_mode',
                    'jobs.min_salary_currency',
                    'recommendation_relation.id as recommendation_id',
                    'recommendation_relation.from_company_id',
                    'recommendation_relation.final_status',
                    'recommendation_relation.to_company_id',
                    'companies.company_name',
                    'companies.logo',
                    'clients.name as client_company',
                    'clients.logo_logical_path',
                    'clients.self_company',
                    'jobs.max_salary_currency',
                    // DB::raw("COUNT(job_applications.id) as allCandidate"),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id != 8 THEN 1 ELSE 0 END) AS allCandidate'),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing'),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 2 THEN 1 ELSE 0 END) AS Screening'),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),       // MEANS Evaluation
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation'),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 5 THEN 1 ELSE 0 END) AS Offered'),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined'),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected'),
                    DB::raw('SUM(CASE WHEN job_applications.thirdparty_company_id = '.$company_id.' AND job_applications.master_status_id = 8 THEN 1 ELSE 0 END) AS InReview')
                )
                ->join('companies', 'companies.id', 'jobs.company_id')
                ->join('clients', 'clients.id', 'jobs.client_id')
                ->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', '=', 'jobs.id')
                ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
                ->where('recommendation_relation.from_company_id', '=', $toCompanyId)
                ->where('recommendation_relation.to_company_id', '=', $company_id);

            // if (\request('client_id') != "") {
            // 	$client_id = request('client_id');
            // 	$alldatalist = $alldatalist->where('jobs.client_id', $client_id);
            // }
            // if (\request('search') != "") {
            // 	$search = request('search');
            // 	$alldatalist = $alldatalist->where('title', 'LIKE', "%$search%");
            // }

            // $alldatalist = $alldatalist->where('jobs.company_id', '=', $company_id);

            $alldatalist = $alldatalist->groupBy('jobs.id');
            $alldatalist = $alldatalist->orderBy('jobs.id', 'DESC');

        }

        // $data = $alldatalist->get();
        // dd($data);
        // dd($alldatalist->get());
        return DataTables::of($alldatalist)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" />
                  </div>';
            })
            ->editColumn('Requisition', function ($row) use ($company_id) {

                $bgTextDecore = $company_name = $strVal = '';
                $str = trim(@$row->title);
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }

                $profile_detail_class = 'show-detail';

                if ($row->self_company > 0 && $row->comId == $company_id) {
                    $company_name = ucwords(substr(@$row->company_name, 0, 30));
                } elseif ($row->comId != $company_id) {
                    $company_name = ucwords(substr(@$row->company_name, 0, 30));
                } elseif ($row->self_company == 0) {
                    $company_name = ucwords(substr(@$row->client_company, 0, 30));
                }

                $icon = '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="'.$profile_detail_class.'">
                      <div class="symbol-label '.$bgTextDecore.' border-radius-5">'.ucwords($strVal).'</div>
                    </a>
                  </div>';

                if (! empty($row->logo)) {
                    $icon = '<div data-row-id="'.$row->id.'"  class="symbol symbol-circle symbol-40px overflow-hidden me-2  '.$profile_detail_class.' border-radius-5">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            	<img src="'.@$row->logo.'" alt="Company Logo" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
                }

                return $icon.'<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="text-dark fw-bolder text-hover-primary fs-6 '.$profile_detail_class.'"  data-toggle="tooltip1" title="'.ucwords(@$row->title).'" >'.ucwords(substr(@$row->title, 0, 15)).'</a> 
                    <a href="javascript:void(0);" data-row-id="'.$row->id.'" class="fw-bold text-muted d-block fs-7 text-hover-primary '.$profile_detail_class.'">'.$company_name.'</a>
                  </div>
                  ';
            })
            ->editColumn('Exp', function ($row) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.@$row->max_exp.'+</span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">'.@$row->min_exp.'+</span>
                  </div>';
            })
            ->editColumn('Salary', function ($row) use ($salaryMode, $currencySymbole) {

                $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = '';
                $min_sal_mode = strtoupper(substr($salaryMode[$row->min_sal_mode], 0, 1));
                $max_sal_mode = strtoupper(substr($salaryMode[$row->max_sal_mode], 0, 1));

                $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
                $max_salary_currency = $currencySymbole[@$row->max_salary_currency];

                $max_salary = '';
                $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
                $min_salary = '';
                $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);

                return '<div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6">'.$min_salary_currency.$max_salary.'/'.$min_sal_mode.'</span>                    
                    <span class="fw-bold d-block fs-7">'.$min_salary_currency.$min_salary.'/'.$max_sal_mode.'</span>
                  </div>';
            })
            ->editColumn('Location', function ($row) {

                // dd($row);

                $country_id = $country_name = '';
                $jobid = $row->id;
                $client_loc = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                    ->where('client_location_jobs.job_id', $jobid)
                    ->first();

                $country_id = @$client_loc->country_id;
                if (@$country_id > 0) {
                    $country_name = GeneralMethod::getCountryName($country_id);
                    foreach ($country_name as $cval) {
                        $country_name = ucwords($cval['sortname']);
                    }
                }

                $city = substr(@$client_loc->city, 0, 6);
                // if (@$client_loc->country_id == 101) {
                $city = substr(@$client_loc->city_name, 0, 6);
                // }

                $jobLocation = DB::table('client_location_jobs')
                    ->where('client_location_jobs.job_id', $jobid)->count();

                $jobCitySelected = DB::table('client_city_jobs')
                    ->where('client_city_jobs.job_id', $jobid)->count();

                $employment_type = 'Full-time';
                if ($row->employment_type == 1) {
                    $employment_type = 'Contract';
                }

                if ($row->work_type == 'yes') {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6">Remote</span>
						<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                } elseif ($row->work_type == 'no') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							  <span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
						  </div>
						</div>
					  </div>';
                    }

                } elseif ($row->work_type == 'any') {

                    if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                        $client_loc = DB::table('client_city_jobs')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                            ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                            ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                            ->select('city_list.city as city_name', 'countries.country_name')
                            ->where('client_city_jobs.job_id', $jobid)
                            ->first();
                        $city = substr(@$client_loc->city_name, 0, 21);
                        $country_name = substr(@$client_loc->country_name, 0, 6);

                        return ' <div class="d-flex flex-column">
							<div class="d-flex align-items-center">
								  <span class="text-dark fw-bolder fs-6">'.ucwords(substr(@$city, 0, 10)).'</span>
								</div>
								
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>
							  </div>
							</div>
						  </div>';
                    } else {
                        return ' <div class="d-flex flex-column">
						<div class="d-flex align-items-center">
						  <span class="text-dark fw-bolder fs-6">Remote</span>
						  &nbsp;|&nbsp;
								  <span class="text-dark fw-bolder fs-6">Multiple</span>
								</div>
							<span class="fw-bold text-muted d-block fs-7" data-toggle="tooltip4" title="'.ucwords(@$employment_type).'" >'.ucwords(substr(@$employment_type, 0, 10)).'</span>

						  </div>
						</div>
					  </div>';
                    }

                }

            })

            ->editColumn('Status', function ($row) use ($job_status, $company_id) {

                $status = $active = $hold = $inactive = $class = '';
                $status = ucwords($row->status);
                $class = $job_status[$row->status];

                $recommendation_text = 'Client';
                if ($row->comId == $company_id) {
                    $recommendation_text = 'Custom';
                }
                if ($status == 'Inactive') {
                    $status = 'Closed';
                }
                if ($row->status == 'active') {
                    $status = 'Hiring';
                } elseif ($row->status == 'inactive') {
                    $status = 'Closed';
                } else {
                    $status = 'Hold';
                }

                return '<div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="fs-6 '.@$class.' text-hover-success">'.$status.'</a>
                      <span class="fw-bold text-muted d-block fs-7">R'.@$row->id.' -'.$recommendation_text.'</span>
                    </div>';
            })
            ->editColumn('Date', function ($row) {

                $total_positions = '';
                $total_positions = number_format($row->total_positions);

                $created_at = '';
                $created_at = $row->created_at;
                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));

                return '<div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">'.$newDate.'</span>
                        <span class="text-muted fw-bold d-block fs-7">'.$total_positions.' Opening</span>
                    </div>';
            })

            ->editColumn('HM', function ($row) use ($company_id) {
                $html = '';
                if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {							// Direct Client Job

                    $from_company_id = $row->from_company_id;

                    $partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $from_company_id)->where('users.company_id', '=', $from_company_id)->count();
                    // dd($partnersCount);
                    $partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $from_company_id)->where('users.company_id', '=', $from_company_id)->orderBy('users.name', 'ASC')->skip(0)->take(1)->get();
                    // dd($partnersData);

                    $jobId = $row->id;
                    $html = $temp = '';
                    $showCount = 0;
                    $html .= '<div class="d-flex flex-column">
					 <div data-row-id="'.$row->id.'" class="symbol-group symbol-hover client-hm-listing">';
                    foreach ($partnersData as $pval) {
                        $bgTextDecore = GeneralMethod::icoinText(@$pval->name);
                        $image_path = ''; // url('public/avatar.png')

                        if (! empty(@$pval->image) && strlen(@$pval->image) > 10) {
                            $image_path = @$pval->image;
                        }
                        // if($showCount < 1){

                        if (! empty($image_path)) {
                            $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
						    <img alt="Pic" src="'.@$image_path.'">
						</div>';
                        } else {
                            $html .=
                            '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="'.ucwords(@$pval->name).'" >
							<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
							<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
								<div class="symbol-label fs-8 fs-8 '.$bgTextDecore.' border-radius-100">'.substr($pval->name, 0, 1).'</div>
							</a>		 
							</div>
							</div>';
                        }
                        // }
                        $showCount++;

                    }
                    if (in_array('add_team_on_requisition', $this->userPermissions)) {
                        $html .= '<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More Comtact list.."><span class="symbol-label font-weight-bold">'.$partnersCount.'+</span></div>';
                    }
                    $html .= '</div></div><div class="fs-7 fw-bold text-muted">Client</div>';

                } else {	//	Custom Client Job
                    $partnersCount = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->count();
                    $partnersData = DB::table('job_assign')->join('users', 'users.id', '=', 'job_assign.user_id')->select('users.id', 'users.name', 'users.image')->where('job_assign.job_id', '=', $row->id)->where('job_assign.company_id', '=', $company_id)->where('users.company_id', '=', $company_id)->orderBy('users.name', 'ASC')->skip(0)->take(1)->get();

                    $jobId = $row->id;
                    $html = $temp = '';
                    $showCount = 0;
                    $html .= '<div class="d-flex flex-column">
					<div data-row-id="'.$row->id.'"  class="symbol-group symbol-hover hm-listing">';
                    foreach ($partnersData as $pval) {
                        $bgTextDecore = GeneralMethod::icoinText(@$pval->name);
                        $image_path = ''; // url('public/avatar.png');
                        if (! empty(@$pval->image) && strlen(@$pval->image) > 10) {
                            $image_path = @$pval->image;
                        }

                        // if($showCount < 1){
                        if (! empty($image_path)) {
                            $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
						     <img alt="Pic" src="'.@$image_path.'">
						</div>';
                        } else {
                            $html .=
                            '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="'.ucwords(@$pval->name).'" >
							<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
							<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
								<div class="symbol-label fs-8 fs-8 '.$bgTextDecore.' border-radius-100">'.substr($pval->name, 0, 1).'</div>
							</a>		 
							</div>
							</div>';
                        }
                        // }
                        $showCount++;
                    }
                    $html .= '<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More users list.."><span class="symbol-label font-weight-bold" >'.$partnersCount.'+</span></div>';
                    $html .= '</div></div><div class="fs-7 fw-bold text-muted">Team</div>';
                }

                return $html;
            })
            ->editColumn('Partners', function ($row) use ($company_id) {

                $html = '';
                if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {							// Direct Client Job

                    $partnersCount = DB::table('job_assign')
                        ->join('users', 'users.id', '=', 'job_assign.user_id')
                        ->select('users.id', 'users.name', 'users.image')
                        ->where('job_assign.job_id', '=', $row->id)
                        ->where('job_assign.company_id', '=', $company_id)
                        ->where('users.company_id', '=', $company_id)
                        ->count();

                    $partnersData = DB::table('job_assign')
                        ->join('users', 'users.id', '=', 'job_assign.user_id')
                        ->select('users.id', 'users.name', 'users.image')
                        ->where('job_assign.job_id', '=', $row->id)
                        ->where('job_assign.company_id', '=', $company_id)
                        ->where('users.company_id', '=', $company_id)
                        ->orderBy('users.name', 'ASC')
                        ->skip(0)->take(1)->get();

                    $jobId = $row->id;

                    $html .= '<div class="d-flex flex-column">
                    						<div data-row-id="'.$row->id.'" class="symbol-group symbol-hover team-listing">';
                    $temp = '';
                    foreach ($partnersData as $pval) {
                        $bgTextDecore = GeneralMethod::icoinText(@$pval->name);
                        $logo_path = '';
                        if (! empty(@$pval->image) && strlen(@$pval->image) > 10) {
                            $logo_path = @$pval->image;
                            $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->name.'">
                            						<img alt="Pic" src="'.@$logo_path.'">
                          						</div>';
                        } else {
                            $html .=
                            '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="'.ucwords(@$pval->name).'" >
							<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
							<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
								<div class="symbol-label fs-8 fs-8 '.$bgTextDecore.' border-radius-100">'.substr($pval->name, 0, 1).'</div>
							</a>		 
							</div>
							</div>';
                        }

                    }
                    $html .= '<div data-row-id="'.$row->id.'"   class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More team list..">
														<span class="symbol-label font-weight-bold">'.$partnersCount.'+</span>
													  </div>';
                    $html .= '</div>
                    					</div><div class="fs-7 fw-bold text-muted">Team</div>';

                } else { //	Custom Client Job

                    $jobId = '';
                    $jobId = $row->id;

                    $recommendedDataListCnt = DB::table('recommendation_relation');
                    $recommendedDataListCnt = $recommendedDataListCnt->select('companies.id', 'companies.company_name', 'companies.logo');
                    $recommendedDataListCnt = $recommendedDataListCnt->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
                    $recommendedDataListCnt = $recommendedDataListCnt->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
                    // $recommendedDataListCnt 	=	$recommendedDataListCnt->leftJoin('td_industries', 'td_industries.ind_id', 'companies.industries');
                    /*$recommendedDataListCnt 	=	$recommendedDataListCnt->leftJoin('job_applications', function($join) use ($jobId)
                                               {
                                                       $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
                                                       $join->on('job_applications.job_id', '=', 'jobs.id' );
                                               });*/
                    $recommendedDataListCnt = $recommendedDataListCnt->where('companies.id', '!=', $company_id);
                    $recommendedDataListCnt = $recommendedDataListCnt->where('recommendation_relation.final_status', 1);
                    $recommendedDataListCnt = $recommendedDataListCnt->where('recommendation_relation.job_id', '=', $jobId);
                    $recommendedDataListCnt = $recommendedDataListCnt->groupBy('companies.id');
                    $recommendedDataListCnt = $recommendedDataListCnt->count();

                    $recommendedDataList = DB::table('recommendation_relation');
                    $recommendedDataList = $recommendedDataList->select('companies.id', 'companies.company_name', 'companies.logo');
                    $recommendedDataList = $recommendedDataList->join('companies', 'companies.id', 'recommendation_relation.to_company_id');
                    $recommendedDataList = $recommendedDataList->join('jobs', 'jobs.id', 'recommendation_relation.job_id');
                    // $recommendedDataList 	=	$recommendedDataList->leftJoin('td_industries', 'td_industries.ind_id', 'companies.industries');
                    $recommendedDataList = $recommendedDataList->leftJoin('job_applications', function ($join) {
                        $join->on('job_applications.thirdparty_company_id', '=', 'companies.id');
                        $join->on('job_applications.job_id', '=', 'jobs.id');
                    });
                    $recommendedDataList = $recommendedDataList->where('companies.id', '!=', $company_id);
                    $recommendedDataList = $recommendedDataList->where('recommendation_relation.final_status', 1);
                    $recommendedDataList = $recommendedDataList->where('recommendation_relation.job_id', '=', $jobId);
                    $recommendedDataList = $recommendedDataList->groupBy('companies.id');
                    $recommendedDataList = $recommendedDataList->skip(0)->take(3)->get();

                    $html = '<div class="d-flex flex-column">
			  <div data-row-id="'.$row->id.'" class="symbol-group symbol-hover spartner-listing">';

                    foreach ($recommendedDataList as $pval) {

                        $image_path = url('img/client-default.png');
                        if (! empty(@$pval->logo) && strlen(@$pval->logo) > 10) {
                            $image_path = url('public/user-uploads/company-logo/'.@$pval->logo);
                        }

                        $html .= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip3" title="'.@$pval->company_name.'"><img alt="Pic" src="'.@$image_path.'"></div>';
                    }

                    $html .= '
						<div data-row-id="'.$row->id.'" class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="" data-bs-original-title="More users list..">
							<span class="symbol-label font-weight-bold" >'.@$recommendedDataListCnt.'+</span>
						</div>
						</div>
					</div><div class="fs-7 fw-bold text-muted">Partners</div>';

                }

                return $html;

            })
            ->editColumn('TOT', function ($row) use ($custom, $company_id) {

                $url_path = $total_application = $inreview_counter = '';

                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($row->id).'/all');
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/all');
                }
                $applicationCounter = @$row->allCandidate;
                $inreview_counter = @$row->InReview;

                $applicationCounter = number_format($applicationCounter);
                $inreview_counter = number_format($inreview_counter);

                return '<div class="d-flex flex-column">
                        <a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$applicationCounter.'</a>
                        <a href="'.$url_path.'" class="text-muted fw-bold d-block fs-7 text-hover-primary d-none">'.$inreview_counter.'</a>
                    </div>';
            })
            ->editColumn('SOU', function ($row) use ($custom, $company_id) {

                $job_id = '';
                $master_status_id = 1;
                $cnt = 0;
                $job_id = $row->id;
                $cnt = $row->Sourcing;
                $url_path = $total_application = $inreview_counter = '';
                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($job_id).'/'.$master_status_id);
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }

                $cnt = number_format($cnt);
                if ($cnt == 0) {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('SCR', function ($row) use ($custom, $company_id) {

                $master_status_id = 2;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Screening;
                $url_path = '';

                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($job_id).'/'.$master_status_id);
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                $cnt = number_format($cnt);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('EVA', function ($row) use ($custom, $company_id) {

                $master_status_id = 3;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Interview;
                $url_path = '';

                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($job_id).'/'.$master_status_id);
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                $cnt = number_format($cnt);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('NEO', function ($row) use ($custom, $company_id) {

                $master_status_id = 4;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Negotiation;
                $url_path = '';

                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($job_id).'/'.$master_status_id);
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                $cnt = number_format($cnt);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('OFF', function ($row) use ($custom, $company_id) {

                $master_status_id = 5;
                $cnt = 0;
                $job_id = '';
                $job_id = $row->id;
                $cnt = $row->Offered;
                $url_path = '';

                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($job_id).'/'.$master_status_id);
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                $cnt = number_format($cnt);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })

            ->editColumn('JOI', function ($row) use ($custom, $company_id) {
                $master_status_id = 6;
                $job_id = '';
                $job_id = $row->id;
                $cnt = 0;
                $cnt = $row->Joined;
                $url_path = '';

                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                $cnt = number_format($cnt);
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->editColumn('REJ', function ($row) use ($custom, $company_id) {
                $master_status_id = 7;
                $job_id = '';
                $cnt = 0;
                $job_id = $row->id;
                $cnt = number_format($row->Rejected);

                if ($custom == 'true') {
                    $url_path = url('/service_company/apptracker/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
                    $url_path = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($row->id).'/'.$master_status_id);
                }
                if ($cnt == 0) {
                    $cnt = '<b>.</b>';

                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">0</a></div>';
                } else {
                    return '<div class="d-flex flex-column"><a href="'.$url_path.'" class="text-dark fw-bolder fs-6 text-hover-primary">'.@$cnt.'</a></div>';
                }
            })
            ->rawColumns(['chkbox', 'Requisition', 'Exp', 'Salary', 'Location', 'Status', 'Date', 'HM', 'Partners', 'TOT', 'SOU', 'SCR', 'EVA', 'NEO', 'OFF', 'JOI', 'REJ'])
            ->addIndexColumn()
            ->make(true);
    }

    public function all_data_assing(Request $request)
    {
        $id = decrypt($request->UrlMailJobId);
        session(['UrlMailJobId' => $id]);

        return redirect('/service_company/jobs');
    }

    public function all_data_ajax(Request $request)
    {

        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $limit = '';
        $userId = $this->user->id;

        $chkAdminRole = $this->user->hasRole('admin');

        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('per_page', $request->input('size', $request->input('limit', 10)));
        if ($size <= 0) {
            $size = 10;
        }
        $custom_status = request('custom_status');
        $custom_hiringtype = request('custom_hiringtype');
        $employment_type = request('employment_type');
        $custom_search = trim(request('custom_search'));

        // Step 7b: persist non-empty search terms per-user in a Redis sorted set (score = unix time).
        // zrevrange("user:searches:{$userId}", 0, 4) returns the 5 most-recent unique terms
        // which the front-end can show as "recent searches" in the search dropdown.
        if ($custom_search !== '') {
            $rk = 'user:searches:'.$userId;
            Redis::zadd($rk, time(), $custom_search);
            Redis::zremrangebyrank($rk, 0, -6); // keep only the 5 newest
            Redis::expire($rk, 86400);           // auto-expire the set after 24 h
        }

        // Step 9a: cursor (keyset) pagination — ?after_id=N eliminates OFFSET cost on deep pages.
        // Falls back to OFFSET when after_id is absent so existing callers keep working.
        $afterId = (int) $request->input('after_id', 0);
        $from = $afterId > 0 ? 0 : ($page - 1) * $size;

        $cachePayload = [
            'company' => $company_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'custom_status' => $custom_status,
            'custom_hiringtype' => $custom_hiringtype,
            'employment_type' => $employment_type,
            'user_id' => $this->user->id,
            'custom_search' => $custom_search,
        ];

        // Two-axis version key: user (personal filter changes) + company (job creates/updates by any team member).
        // JobObserver::saved bumps alljobslist_version_company_{id} so all team members see fresh data immediately.
        $userVersionKey = 'alljobslist_version_user_'.$this->user->id;
        $companyVersionKey = 'alljobslist_version_company_'.$company_id;
        $version = cache()->get($userVersionKey, 1).'_'.cache()->get($companyVersionKey, 1);
        $cacheKeySet = 'ats:alljobslist:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $final_status = [1, 2];
        if ($custom_status === 'removed') {
            $final_status = [2];
        }
        $rcommendationIds = $this->elasticService->getClient()->search([
            'index' => ELASTIC_JOB_REQUEST,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['to_company_id' => $company_id]],
                            ['terms' => ['final_status' => $final_status]],
                        ],
                    ],
                ],
                '_source' => ['job_id'],
            ],
        ]);

        // Extract company IDs
        $jobIds = collect($rcommendationIds['hits']['hits'])
            ->pluck('_source.job_id')
            ->unique()
            ->values()
            ->toArray();

        // dd($jobIds);

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];
        $sort = [];
        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // Always show active jobs first, then hold, then inactive/closed
        if ($custom_status === '' || $custom_status === 'all') {
            array_unshift($sort, [
                '_script' => [
                    'type' => 'number',
                    'order' => 'asc',
                    'script' => [
                        'lang' => 'painless',
                        'source' => "def s = doc['status'].size() > 0 ? doc['status'].value : ''; if (s == 'active') return 0; if (s == 'hold') return 1; return 2;",
                    ],
                ],
            ]);
        }

        // 1. STATUS FILTERING
        // if (!session()->has('UrlMailJobId')) {
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                // $must[] = [
                // 	'nested' => [
                // 		'path' => 'recommendation_relation',
                // 		'query' => [
                // 			'term' => ['recommendation_relation.final_status' => 2],
                // 		],
                // 	],
                // ];

            }
        }

        // }
        $filter[] = ['terms' => ['status' => $statusArr]];

        // 2. HIRING TYPE
        if ($employment_type != '' && $employment_type != 'all') {
            $filter[] = ['term' => ['employment_type' => (int) $employment_type]];
        }

        // 3. SEARCH CONDITIONS
        // Step 7a: multi_match replaces the old broken match_phrase+wildcard pattern.
        // match_phrase does not support wildcard patterns — '*query*' was treated as a literal
        // string. multi_match with fuzziness covers title/description/skills in one ES round-trip.
        // Nested wildcard clauses for user_name, city etc. are kept for exact sub-field matching.
        if (! empty($custom_search)) {
            // Store the last 5 searches per user in a Redis sorted set (score = epoch, so ZREVRANGE gives newest-first)
            try {
                \Illuminate\Support\Facades\Redis::zadd("user:searches:{$userId}", time(), $custom_search);
                \Illuminate\Support\Facades\Redis::zremrangebyrank("user:searches:{$userId}", 0, -6); // keep only 5
            } catch (\Throwable) {
                // Redis unavailable — non-fatal
            }

            $lowerSearch = strtolower($custom_search);
            $must[] = [
                'bool' => [
                    'should' => [
                        // Primary: full-text across title (boosted), job_description, skills with fuzziness
                        ['multi_match' => [
                            'query' => $custom_search,
                            'fields' => ['title^3', 'job_description', 'skills'],
                            'fuzziness' => 'AUTO',
                            'operator' => 'or',
                        ]],
                        // Exact ID match
                        ['term' => ['id' => (int) $custom_search]],
                        // Prefix-anchored wildcard for company/client name (cheaper than leading-wildcard)
                        ['wildcard' => ['client_name' => $lowerSearch.'*']],
                        ['wildcard' => ['company_name' => $lowerSearch.'*']],
                        // Nested sub-field wildcards (accept leading wildcard cost — fields are small)
                        ['nested' => ['path' => 'job_assign',              'query' => ['wildcard' => ['job_assign.user_name' => '*'.$lowerSearch.'*']]]],
                        ['nested' => ['path' => 'client_contact_info_jobs', 'query' => ['wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.$lowerSearch.'*']]]],
                        ['nested' => ['path' => 'client_city_jobs',         'query' => ['wildcard' => ['client_city_jobs.city_name' => '*'.$lowerSearch.'*']]]],
                        ['nested' => ['path' => 'client_city_jobs',         'query' => ['wildcard' => ['client_city_jobs.country_name' => '*'.$lowerSearch.'*']]]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        // 4. COMPANY ACCESS CONDITIONS

        // if (session()->has('UrlMailJobId')) {
        // 	$UrlMailJobId = session('UrlMailJobId');
        // 		$must[] = [
        // 		'bool' => [
        // 			'should' => [
        // 				['term' => ['id' => $UrlMailJobId]]
        // 			],
        // 			'minimum_should_match' => 1,
        // 		],
        // 	];

        // }else{
        $must[] = [
            'bool' => [
                'should' => [
                    [
                        'term' => [
                            'comId' => $company_id,
                        ],
                    ],
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        [
                                            'term' => [
                                                'recommendation_relation.to_company_id' => $company_id,
                                            ],
                                        ],
                                        [
                                            'terms' => [
                                                'recommendation_relation.final_status' => $final_status,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
            ],
        ];
        // }

        // 5. RESTRICT TO ASSIGNED USERS (if not admin)
        if (! (($chkAdminRole > 0) && in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
            $must[] = [
                'nested' => [
                    'path' => 'job_assign',
                    'query' => [
                        'term' => ['job_assign.user_id' => auth()->user()->id],
                    ],
                ],
            ];
        }

        // FINAL ELASTIC PARAMS
        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];

        // Step 9a: inject search_after when cursor mode requested (search_after + from are mutually exclusive)
        if ($afterId > 0) {
            $params['body']['search_after'] = [$afterId];
            unset($params['body']['from']);
        }

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
            $nextCursor = null;
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $hits = $response['hits']['hits'] ?? [];
            $data = array_map(fn ($hit) => $hit['_source'], $hits);
            $lastHit = end($hits);
            $nextCursor = $lastHit ? ($lastHit['sort'][0] ?? null) : null;
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'per_page' => $size,
            'listType' => 'allcandidate',
            'next_cursor' => $nextCursor,
        ];

        session()->forget('UrlMailJobId');
        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.all_jobs_ajax', compact('data', 'userPermissions', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
            'next_cursor' => $nextCursor,
        ];
        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_JOBS_LIST_TIME));

        return response()->json($responseData);
    }

    public function client_requisition($id)
    {
        $this->pageTitle = __('Client Requisition');
        // $this->companies = Company::all();
        $this->totalJobs = Job::where('id', '>', '0')->count();
        $this->activeJobs = Job::where('status', 'active')->count();
        $this->inactiveJobs = Job::where('status', 'inactive')->count();
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        if ($chkAdminRole > 0) {
            $this->totalJobs = Job::where('id', '>', '0')->count();
            $this->activeJobs = Job::where('status', 'active')->count();
            $this->inactiveJobs = Job::where('status', 'inactive')->count();
            $this->holdJobs = Job::where('status', 'hold')->count();
        } else {
            $this->totalJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.id', '>', '0')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->activeJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'active')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->inactiveJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'inactive')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->holdJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'hold')->where('job_assign.user_id', '=', $this->user->id)->count();
        }

        $this->dataCnt = $this->dataCnt();

        return view('service_company.jobs.client_requisition', $this->data);
    }

    public function client_requisition_data_ajax(Request $request)
    {
        // die;

        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('per_page', $request->input('size', $request->input('limit', 10)));
        if ($size <= 0) {
            $size = 10;
        }
        $from = ($page - 1) * $size;

        $custom_status = request('custom_status');
        $custom_hiringtype = request('custom_hiringtype');
        $employment_type = request('employment_type');
        $custom_search = trim(request('custom_search'));

        $cachePayload = [
            'company' => $company_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'custom_status' => $custom_status,
            'custom_hiringtype' => $custom_hiringtype,
            'employment_type' => $employment_type,
            'user_id' => $this->user->id,
            'custom_search' => $custom_search,
        ];

        $userVersionKey = 'allclientrequisition_version_user_'.$this->user->id;
        $companyVersionKey = 'allclientrequisition_version_company_'.$company_id;
        $version = cache()->get($userVersionKey, 1).'_'.cache()->get($companyVersionKey, 1);
        $cacheKeySet = 'ats:allclientrequisition:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $final_status = [1, 2];
        if ($custom_status === 'removed') {
            $final_status = [2];
        }
        $rcommendationIds = $this->elasticService->getClient()->search([
            'index' => ELASTIC_JOB_REQUEST,
            'body' => [
                'size' => 10000,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['to_company_id' => $company_id]],
                            ['terms' => ['final_status' => $final_status]],
                        ],
                    ],
                ],
                '_source' => ['job_id'],
            ],
        ]);

        // Extract company IDs
        $jobIds = collect($rcommendationIds['hits']['hits'])
            ->pluck('_source.job_id')
            ->unique()
            ->values()
            ->toArray();

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];

        $sort = [];

        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }

        // 1. STATUS FILTERING
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                // $must[] = [
                // 	'nested' => [
                // 		'path' => 'recommendation_relation',
                // 		'query' => [
                // 			'term' => ['recommendation_relation.final_status' => 2],
                // 		],
                // 	],
                // ];

            }
        }

        $filter[] = ['terms' => ['status' => $statusArr]];

        // 2. HIRING TYPE
        if ($employment_type != '' && $employment_type != 'all') {
            $filter[] = ['term' => ['employment_type' => (int) $employment_type]];
        }
        // if ($custom_hiringtype !== "" && $custom_hiringtype !== "All") {
        // 	if ($custom_hiringtype == 7) {
        // 		$filter[] = ['term' => ['employment_type' => 0]];
        // 	} elseif ($custom_hiringtype == 8) {
        // 		$filter[] = ['term' => ['employment_type' => 1]];
        // 	}
        // }

        // 3. SEARCH CONDITIONS (similar to LIKE, IN, etc.)
        if (! empty($custom_search)) {

            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['terms' => ['id' => $jobIds]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    ['wildcard' => ['title' => '*'.strtolower($custom_search).'*']],
                                    ['term' => ['id' => (int) $custom_search]],
                                    ['wildcard' => ['client_name' => '*'.strtolower($custom_search).'*']],
                                    ['wildcard' => ['company_name' => '*'.strtolower($custom_search).'*']],
                                    [
                                        'nested' => [
                                            'path' => 'job_assign',
                                            'query' => [
                                                'wildcard' => ['job_assign.user_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_contact_info_jobs',
                                            'query' => [
                                                'wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.city_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.country_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        // 4. COMPANY ACCESS CONDITIONS
        $must[] = [
            'bool' => [
                'must' => [
                    [
                        'nested' => [
                            'path' => 'recommendation_relation',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        [
                                            'term' => [
                                                'recommendation_relation.to_company_id' => $company_id,
                                            ],
                                        ],
                                        [
                                            'terms' => [
                                                'recommendation_relation.final_status' => $final_status,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 5. RESTRICT TO ASSIGNED USERS (if not admin)
        if (! (($chkAdminRole > 0) && in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
            $must[] = [
                'nested' => [
                    'path' => 'job_assign',
                    'query' => [
                        'term' => ['job_assign.user_id' => auth()->user()->id],
                    ],
                ],
            ];
        }

        // FINAL ELASTIC PARAMS
        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.client_jobs_ajax', compact('data', 'userPermissions', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CLIENT_REQUISITION_TIME));

        return response()->json($responseData);

    }

    public function custom_requisition($id)
    {
        $this->pageTitle = __('Custom Requisition');
        // $this->companies = Company::all();
        $this->totalJobs = Job::where('id', '>', '0')->count();
        $this->activeJobs = Job::where('status', 'active')->count();
        $this->inactiveJobs = Job::where('status', 'inactive')->count();

        $chkAdminRole = $this->user->hasRole('admin');

        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

        if ($chkAdminRole > 0) {
            $this->totalJobs = Job::where('id', '>', '0')->count();
            $this->activeJobs = Job::where('status', 'active')->count();
            $this->inactiveJobs = Job::where('status', 'inactive')->count();
            $this->holdJobs = Job::where('status', 'hold')->count();
        } else {
            $this->totalJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('jobs.id', '>', '0')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->activeJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'active')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->inactiveJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'inactive')->where('job_assign.user_id', '=', $this->user->id)->count();
            $this->holdJobs = Job::join('job_assign', 'job_assign.job_id', 'jobs.id')->where('status', 'hold')->where('job_assign.user_id', '=', $this->user->id)->count();
        }
        $this->dataCnt = $this->dataCnt();

        return view('service_company.jobs.custom_requisition', $this->data);
    }

    public function custom_requisition_data_ajax(Request $request)
    {
        abort_if(Gate::denies('view_jobs'), 403);

        $chkAdminRole = $this->user->hasRole('admin');

        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();
        $company_id = auth()->user()->company_id;

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('per_page', $request->input('size', $request->input('limit', 10)));
        if ($size <= 0) {
            $size = 10;
        }
        $from = ($page - 1) * $size;

        $custom_status = request('custom_status');
        $custom_hiringtype = request('custom_hiringtype');
        $custom_search = trim(request('custom_search'));

        $statusArr = ['active', 'hold', 'inactive', '2'];
        $must = [];
        $should = [];
        $filter = [];

        $cachePayload = [
            'company' => $company_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'custom_status' => $custom_status,
            'custom_hiringtype' => $custom_hiringtype,
            'user_id' => $this->user->id,
            'custom_search' => $custom_search,
        ];

        $userVersionKey = 'allcustomrequisition_version_user_'.$this->user->id;
        $companyVersionKey = 'allcustomrequisition_version_company_'.$company_id;
        $version = cache()->get($userVersionKey, 1).'_'.cache()->get($companyVersionKey, 1);
        $cacheKeySet = 'ats:allcustomrequisition:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $valCheck = request('custom_hiringtype');
        switch ($valCheck) {
            case 1:
                $sort[] = ['id' => ['order' => 'desc']];
                break;
            case 2:
                $sort[] = ['id' => ['order' => 'asc']];
                break;
            case 3:
                $sort[] = ['max_salary' => ['order' => 'asc']];
                break;
            case 4:
                $sort[] = ['max_salary' => ['order' => 'desc']];
                break;
            case 5:
                $sort[] = ['client_name' => ['order' => 'asc']];
                break;
            case 6:
                $sort[] = ['client_name' => ['order' => 'desc']];
                break;
            default:
                $sort[] = ['id' => ['order' => 'desc']];
        }
        // 1. STATUS FILTERING
        if ($custom_status !== '' && $custom_status !== 'all') {
            if (in_array($custom_status, ['active', 'hold', 'inactive'])) {
                $statusArr = [$custom_status];
            } elseif ($custom_status === 'removed') {
                $statusArr = [];
                $must[] = [
                    'nested' => [
                        'path' => 'recommendation_relation',
                        'query' => [
                            'term' => ['recommendation_relation.final_status' => 2],
                        ],
                    ],
                ];

            }
        }

        $filter[] = ['terms' => ['status' => $statusArr]];

        // 2. HIRING TYPE
        if ($custom_hiringtype !== '' && $custom_hiringtype !== 'All') {
            if ($custom_hiringtype == 7) {
                $filter[] = ['term' => ['employment_type' => 0]];
            } elseif ($custom_hiringtype == 8) {
                $filter[] = ['term' => ['employment_type' => 1]];
            }
        }

        // 3. SEARCH CONDITIONS (similar to LIKE, IN, etc.)
        if (! empty($custom_search)) {

            $must[] = [
                'bool' => [
                    'must' => [
                        [
                            'bool' => [
                                'should' => [
                                    ['term' => ['comId' => $company_id]],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                        [
                            'bool' => [
                                'should' => [
                                    ['wildcard' => ['title' => '*'.strtolower($custom_search).'*']],
                                    ['term' => ['id' => (int) $custom_search]],
                                    ['wildcard' => ['client_name' => '*'.strtolower($custom_search).'*']],
                                    ['wildcard' => ['company_name' => '*'.strtolower($custom_search).'*']],
                                    [
                                        'nested' => [
                                            'path' => 'job_assign',
                                            'query' => [
                                                'wildcard' => ['job_assign.user_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_contact_info_jobs',
                                            'query' => [
                                                'wildcard' => ['client_contact_info_jobs.contact_person_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.city_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'client_city_jobs',
                                            'query' => [
                                                'wildcard' => ['client_city_jobs.country_name' => '*'.strtolower($custom_search).'*'],
                                            ],
                                        ],
                                    ],
                                ],
                                'minimum_should_match' => 1,
                            ],
                        ],
                    ],
                ],
            ];
        }

        // 4. COMPANY ACCESS CONDITIONS
        $must[] = [
            'bool' => [
                'should' => [
                    ['term' => ['comId' => $company_id]],
                ],
                'minimum_should_match' => 1,
            ],
        ];

        // 5. RESTRICT TO ASSIGNED USERS (if not admin)
        if (! (($chkAdminRole > 0) && in_array('all_requisition', $this->userPermissions) && in_array('view_jobs', $this->userPermissions))) {
            $must[] = [
                'nested' => [
                    'path' => 'job_assign',
                    'query' => [
                        'term' => ['job_assign.user_id' => auth()->user()->id],
                    ],
                ],
            ];
        }

        // FINAL ELASTIC PARAMS
        $params = [
            'index' => ELASTIC_JOBS,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOBS)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];
        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.custom_jobs_ajax', compact('data', 'userPermissions', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_CUSTOM_REQUISITION_TIME));

        return response()->json($responseData);

    }

    public function job_request($id)
    {
        $this->pageTitle = __('Job Request');
        $this->dataCnt = $this->dataCnt();

        return view('service_company.jobs.job_request', $this->data);
    }

    public function marketJobs($id = null)
    {
        $company_id = auth()->user()->company_id;
        $this->pageTitle = __('Market Jobs');
        // Distinct key: this stores strings (plucked names), not objects — avoids collision with global_countries_list which stores full rows.
        $cities = cache()->remember('global_country_names_list', 86400, function () {
            return DB::table('countries')
                ->select('countries.country_name')
                ->where('countries.country_name', '!=', '')
                ->orderBy('countries.country_name')
                ->get()
                ->map(function ($city) {
                    return $city->country_name;
                });
        });
        $this->cities = $cities;
        // $this->cities = GeneralMethod::allCities();
        // 		$cities = DB::table('city_list')->pluck('city')->toArray();

        // $this->cities =  $cities;

        $this->min_exp = '';
        $this->max_exp = '';
        $this->min_salary_currency = '';
        $this->min_sal_mode = '';
        $this->min_salary = '';
        $this->max_salary = '';
        $this->dataCnt = $this->dataCnt();

        // 4. COMPANY ACCESS CONDITIONS

        $params = [
            'index' => ELASTIC_JOBS,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    'status' => 'active',
                                ],
                            ],
                        ],
                        'must_not' => [
                            [
                                'term' => [
                                    'comId' => $company_id,
                                ],
                            ],
                        ],
                    ],
                ],

                'aggs' => [
                    'all_jobs' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'fulltime_jobs' => [
                        'filter' => [
                            'term' => [
                                'employment_type' => 0,
                            ],
                        ],
                    ],
                    'contract_jobs' => [
                        'filter' => [
                            'term' => [
                                'employment_type' => 1,
                            ],
                        ],
                    ],
                    'agency_requests' => [
                        'filter' => [
                            'term' => [
                                'public_market_status' => 2,
                            ],
                        ],
                    ],
                    'all_jobs_new' => [
                        'filter' => [
                            'range' => [
                                'created_at' => [
                                    'gte' => 'now-7d/d',
                                    'lte' => 'now',
                                ],
                            ],
                        ],
                    ],
                    'fulltime_new' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['employment_type' => 0]],
                                    ['range' => ['created_at' => ['gte' => 'now-7d/d', 'lte' => 'now']]],
                                ],
                            ],
                        ],
                    ],
                    'contract_new' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['employment_type' => 1]],
                                    ['range' => ['created_at' => ['gte' => 'now-7d/d', 'lte' => 'now']]],
                                ],
                            ],
                        ],
                    ],
                    'agency_new' => [
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['public_market_status' => 2]],
                                    ['range' => ['created_at' => ['gte' => 'now-7d/d', 'lte' => 'now']]],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $aggs = $response['aggregations'] ?? [];

        $this->all_jobs = $aggs['all_jobs']['value'] ?? 0;
        $this->fulltime_jobs = $aggs['fulltime_jobs']['doc_count'] ?? 0;
        $this->contract_jobs = $aggs['contract_jobs']['doc_count'] ?? 0;
        $this->agency_requests = $aggs['agency_requests']['doc_count'] ?? 0;

        $this->all_jobs_new = $aggs['all_jobs_new']['doc_count'] ?? 0;
        $this->fulltime_new = $aggs['fulltime_new']['doc_count'] ?? 0;
        $this->contract_new = $aggs['contract_new']['doc_count'] ?? 0;
        $this->agency_new = $aggs['agency_new']['doc_count'] ?? 0;

        // Legacy compatibility aliases
        $this->market_place = $this->agency_requests;
        $this->newly_add = $this->all_jobs_new;

        return view('service_company.jobs.market-jobs', $this->data);
    }

    public function marketJobList_ajax(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $sal_mode = request('sal_mode') ?? 0;
        $min_salary_currency = request('min_salary_currency') ?? 0;
        $sal_mode_set = request('sal_mode') ?? null;
        $salary_currency_set = request('min_salary_currency') ?? null;

        $v2Service = app(\App\Services\MarketJobV2Service::class);
        $cacheKey = $v2Service->getCacheKey($request, $company_id);

        // Read-Through Cache Check
        if (cache()->has($cacheKey)) {
            return response()->json(cache()->get($cacheKey));
        }

        // Delegate to MarketJobV2Service for 0 DB queries & 1 ES search + single-pass bulk relation fetch
        $v2Result = $v2Service->fetchMarketJobs($request, $company_id);

        $data = $v2Result['data'];
        $total = $v2Result['total'];
        $page = $v2Result['page'];
        $size = $v2Result['size'];

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / max($size, 1)),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.market_jobs_ajax', compact('data', 'userPermissions', 'salaryMode', 'currencySymbole', 'sal_mode', 'sal_mode_set', 'salary_currency_set', 'min_salary_currency', 'job_status', 'company_id'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        $cacheTtlMinutes = (int) config('constants.ALL_MARKET_JOBS_TIME', 15);
        cache()->put($cacheKey, $responseData, now()->addMinutes($cacheTtlMinutes));

        return response()->json($responseData);
    }

    /**
     * marketJobListV2_ajax
     *
     * Intent: Phase 4 clean JSON API endpoint for Market Jobs listing.
     * Returns a structured JSON DTO payload (~5 KB) instead of server-rendered HTML snippets.
     * Tenancy: Scoped to tenant company via MarketJobV2Service.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function marketJobListV2_ajax(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $v2Service = app(\App\Services\MarketJobV2Service::class);
        $cacheKey = $v2Service->getCacheKey($request, $company_id).':v2json';

        if (cache()->has($cacheKey)) {
            return response()->json(cache()->get($cacheKey));
        }

        $v2Result = $v2Service->fetchMarketJobs($request, $company_id);

        $data = $v2Result['data'];
        $total = $v2Result['total'];
        $page = $v2Result['page'];
        $size = $v2Result['size'];

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / max($size, 1)),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];

        // Resolve ResourceCollection to a plain PHP array so that when encoded as JSON,
        // `response.items` is a flat JSON array [] — not the {data:[]} wrapper that
        // ResourceCollection adds by default (which breaks Array.isArray() check in JS).
        $itemsResource = \App\Http\Resources\MarketJobV2Resource::collection($data);
        $itemsArray = $itemsResource->resolve();

        $responseData = [
            'status' => 'success',
            'items'  => $itemsArray,
            'total'  => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        $cacheTtlMinutes = (int) config('constants.ALL_MARKET_JOBS_TIME', 15);
        cache()->put($cacheKey, $responseData, now()->addMinutes($cacheTtlMinutes));

        return response()->json($responseData);
    }

    public function marketrequestaadded(Request $request)
    {
        // dd($request->all());
        $this->job = Job::find($request->jobId);
        $from_company_id = $this->job->company_id;
        $from_user_id = $this->user->id;
        $to_company_id = auth()->user()->company_id;

        $current_company_id = $to_company_id;
        $jobid = $request->jobId;
        $jobRow = Job::find($request->jobId);

        // dd($from_company_id,$to_company_id );

        $jobidGet = $request->jobId;
        //  dd($jobidGet,$this->job,$from_company_id,$to_company_id);
        if (Job::where('id', '=', $jobidGet)->where('status', '=', 'inactive')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is closed. Please change the job status to active.
			"]);
        } elseif (Job::where('id', '=', $jobidGet)->where('status', '=', 'hold')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is hold. Please change the job status to active."]);
        }

        $resultCount = DB::table('market_job_added_count')->where('job_id', '=', $jobidGet)->where('status', '=', 0)->count();
        $exceeded = 20;
        if ($resultCount > $exceeded) {
            $jobsave = Job::find($jobidGet);
            $jobsave->public_market_status = 4;
            $jobsave->save();

            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't add a job because the limit has been exceeded"]);
        }

        $searchTermsLocation = request('kt_tagify_location');
        //    dd($searchTermsLocation);
        if (empty($searchTermsLocation)) {

            $country_id_job = GeneralMethod::companyMainAddress($to_company_id);

        } else {
            $custom_search_location = json_decode($searchTermsLocation, true);

            $currentJobLocation = array_column($custom_search_location, 'value');
            //    dd( $currentJobLocation[0]);
            $job_country_id = DB::table('countries')
                ->select('countries.id')
                ->where('countries.country_name', '=', $currentJobLocation[0])
                ->first();
            // dd($job_country_id);
            $country_id_job = $job_country_id->id;
        }

        $clientMainLocation = GeneralMethod::companyMainAddress($from_company_id);
        // dd($clientMainLocation==$country_id_job);

        $jobid = $request->jobId;
        $clientRow = DB::table('clients')->select('clients.id')->where('clients.company_id', '=', $from_company_id)->first();
        // dd($clientRow);
        // $jobRow = DB::table('jobs')->select('jobs.id', 'jobs.employment_type')->where('jobs.id', '=', $jobid)->first();
        $jobRow = DB::table('jobs')->join('job_countries', 'job_countries.job_id', '=', 'jobs.id')->select('jobs.id', 'jobs.created_at', 'jobs.status', 'jobs.total_positions', 'jobs.employment_type', 'job_countries.country_id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.employment_type', 'jobs.max_salary_currency', 'jobs.max_sal_mode', 'jobs.title', 'jobs.company_id')->where('jobs.id', '=', $jobid)->first();

        if ($jobRow->employment_type == 1) {
            //	CONTRACT_RECRUITMENT_LEAD   employment_type = 1
            if ($clientMainLocation == $country_id_job) {

                $eventId = 40;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

            } else {

                $eventId = 41;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

            }

        } elseif ($jobRow->employment_type == 0) {	//	FULLTIME_RECRUITMENT_LEAD   employment_type = 0
            if ($clientMainLocation == $country_id_job) {

                $eventId = 38;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

            } else {

                $eventId = 39;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

            }

        }

        $job_assign = DB::table('job_assign')->select('*')->where('job_id', $jobid)->where('company_id', $from_company_id)->where('spok_user_id', '!=', '')->first();
        $job_assign_d = DB::table('job_assign')->select('*')->where('job_id', $jobid)->where('company_id', $from_company_id)->first();
        // dd($job_assign,$from_company_id,$jobid,$job_assign->spok_user_id);

        // ###################################################         START CLIENT INVITATION

        // CHECK CLIENT INVITATION SENT OR NOT
        $clientInvitationCounter = DB::table('staffing_company_invitation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            // ->where('to_invitation_status', '!=', -1)
            ->count();
        if ($clientInvitationCounter == 0) {
            // INVITATION SENT :- IN CASE NO INVITATION AVAIL
            // GET COMPANY DETAILS
            $company_row = DB::table('companies')->select('companies.company_email')->where('companies.id', '=', $to_company_id)->first();
            // dd($company_row);
            // INVITATION
            // staffing_company_invitation
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_client_id' => $clientRow->id,
                'from_user_id' => ($job_assign) ? $job_assign->spok_user_id : $job_assign_d->user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_email' => $company_row->company_email,
                'to_status' => 1,
                'job_id' => $jobid,
                'to_invitation_status' => 1,
            ];
            // dd($insertArr);

            DB::table('staffing_company_invitation')->insert($insertArr);
            $invitationId = DB::getPdo()->lastInsertId();
            GeneralMethod::clientRequestIndexElastic($invitationId);

            $values = [
                'from_company_id' => $from_company_id,
                'from_client_id' => $clientRow->id,
                'from_user_id' => ($job_assign) ? $job_assign->spok_user_id : $job_assign_d->user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_client_id' => @$clientRow->id,
                'to_user_id' => auth()->user()->id,
                'to_status' => 1,
                'to_created_at' => date('Y-m-d'),

                'final_status' => 1,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ];
            DB::table('staffing_company_relation')->insert($values);
            $relationId = DB::getPdo()->lastInsertId();
            GeneralMethod::appendClientNestedData($relationId);

            $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $to_company_id);
            $companyClientAddress = GeneralMethod::companyClientAddress($from_company_id);
            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];

            $cc = [];
            $checkMailPermision = GeneralMethod::mailPermission(19);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(19, 2);

                $subject = str_replace(['[{CLIENT_COMPANY_NAME}]'], [$detailsVendorSide->client_company], $mailTypeSubject->subject);

                $template_description = $mailTypeSubject->body_description;

                $template_description = str_replace(['[{CLIENT_COMPANY_NAME}]', '[{VENDOR_COMPANY_NAME}]', '[{HR_NAME}]', '[{HR_EMAILl}]', '[{HR_PHONE}]', '[{CITY_NAME}]'], [$detailsVendorSide->client_company, $detailsVendorSide->vendor_company, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, $companyClientAddress], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($to_company_id, 'Sales'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $to_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //    dd($to_Mail_Id);
                $data = [
                    'message' => $message,
                ];

                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $to_company_id
                )->onConnection('emails');

                // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);

            }

        } else {
            $company_row = DB::table('companies')->select('companies.company_email')->where('companies.id', '=', $to_company_id)->first();
            $clientInvitation = DB::table('staffing_company_invitation')->select('*')
                ->where('from_company_id', $from_company_id)
                ->where('to_company_id', $to_company_id)
                ->first();

            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_client_id' => $clientRow->id,
                'from_user_id' => ($job_assign) ? $job_assign->spok_user_id : $job_assign_d->user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_email' => $company_row->company_email,
                'to_status' => 1,
                'job_id' => $jobid,
                'to_invitation_status' => 1,
            ];
            // dd($insertArr);

            DB::table('staffing_company_invitation')->where('id', '=', $clientInvitation->id)->update($insertArr);
            $invitationId = DB::getPdo()->lastInsertId();
            GeneralMethod::clientRequestIndexElastic($clientInvitation->id);

            $staffing_company = DB::table('staffing_company_relation')->select('*')
                ->where('from_company_id', $from_company_id)
                ->where('to_company_id', $to_company_id)
                ->first();
            if ($staffing_company) {
                $values = [
                    'from_company_id' => $from_company_id,
                    'from_client_id' => $clientRow->id,
                    'from_user_id' => ($job_assign) ? $job_assign->spok_user_id : $job_assign_d->user_id,
                    'from_status' => 1,
                    'from_created_at' => date('Y-m-d'),

                    'to_company_id' => $to_company_id,
                    'to_client_id' => @$clientRow->id,
                    'to_user_id' => auth()->user()->id,
                    'to_status' => 1,
                    'to_created_at' => date('Y-m-d'),

                    'final_status' => 1,
                    'updated_at' => date('Y-m-d'),
                ];
                DB::table('staffing_company_relation')->where('id', '=', $staffing_company->id)->update($values);

                GeneralMethod::appendClientNestedData($staffing_company->id);
            } else {
                $values = [
                    'from_company_id' => $from_company_id,
                    'from_client_id' => $clientRow->id,
                    'from_user_id' => ($job_assign) ? $job_assign->spok_user_id : $job_assign_d->user_id,
                    'from_status' => 1,
                    'from_created_at' => date('Y-m-d'),

                    'to_company_id' => $to_company_id,
                    'to_client_id' => @$clientRow->id,
                    'to_user_id' => auth()->user()->id,
                    'to_status' => 1,
                    'to_created_at' => date('Y-m-d'),

                    'final_status' => 1,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ];
                DB::table('staffing_company_relation')->insert($values);
                $relationId = DB::getPdo()->lastInsertId();
                GeneralMethod::appendClientNestedData($relationId);
            }
        }

        // ################################################             END CLIENT INVITATION

        // ###############################################				SEND JOB INVITATION

        $jobCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('job_id', $jobid)
            // ->where('final_status', '!=', -1)
            ->count();
        if ($jobCounter == 0) {
            // INSERT

            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_user_id' => ($job_assign) ? $job_assign->spok_user_id : $job_assign_d->user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),
                'to_company_id' => $to_company_id,
                'to_status' => 1,
                'to_user_id' => $from_user_id,
                'final_status' => 1,
                'add_status' => 1,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->insert($insertArr);
            $relationId = DB::getPdo()->lastInsertId();

            $rec_relation_id = (int) $relationId;
            $jobTerms = Job::findOrFail($jobid);
            $companyTerms = Company::withoutGlobalScope('company')->findOrFail($jobTerms->company_id);
            $global_settings = DB::table('global_settings')->first();
            $companiesDetails = DB::table('recommendation_relation')
                ->select('vendor_company.company_name as vendor_company_name', 'client_company.company_name as client_company_name', 'vendor_company.address as vendor_company_address', 'client_company.address as client_company_address')
                ->join('companies as vendor_company', 'recommendation_relation.to_company_id', '=', 'vendor_company.id')
                ->join('companies as client_company', 'recommendation_relation.from_company_id', '=', 'client_company.id')
                ->where('recommendation_relation.id', '=', $rec_relation_id)
                ->get();
            // dd($companiesDetails);
            $terms = '';
            // dd(count($companiesDetails));
            if (count($companiesDetails) > 0) {
                // dd("d");
                $terms .= "By clicking the accept button, {$companiesDetails[0]->vendor_company_name} will accept the rates, terms, and conditions outlined below for the job requirements of {$companiesDetails[0]->client_company_name}.";
            }

            $terms .= '<p>&nbsp;</p>';
            $terms .= ($companyTerms->terms) ? $companyTerms->terms : $global_settings->terms;

            $termsfinal = str_replace(['[TODAY_DATE]', '[AGENCY_NAME]', '[AGENCY_ADDRESS]', '[COMPANY_NAME]', '[COMPANY_ADDRESS]'], [date('d M, Y h:i:s'), $companiesDetails[0]->vendor_company_name, $companiesDetails[0]->vendor_company_address, $companiesDetails[0]->client_company_name, $companiesDetails[0]->client_company_address], $terms);

            $accepterms['accepterms'] = $termsfinal;

            // dd($data,@$rec_relation_id);
            DB::table('recommendation_relation')->where('id', @$rec_relation_id)->limit(1)->update($accepterms);
            GeneralMethod::jobRequestIndexElastic($relationId);

        } else {
            $recommendationrelation = DB::table('recommendation_relation')->select('*')
                ->where('from_company_id', $from_company_id)
                ->where('to_company_id', $to_company_id)
                ->where('job_id', $jobid)
                ->first();

            $jobTerms = Job::findOrFail($jobid);
            $companyTerms = Company::withoutGlobalScope('company')->findOrFail($jobTerms->company_id);
            $global_settings = DB::table('global_settings')->first();
            $companiesDetails = DB::table('recommendation_relation')
                ->select('vendor_company.company_name as vendor_company_name', 'client_company.company_name as client_company_name', 'vendor_company.address as vendor_company_address', 'client_company.address as client_company_address')
                ->join('companies as vendor_company', 'recommendation_relation.to_company_id', '=', 'vendor_company.id')
                ->join('companies as client_company', 'recommendation_relation.from_company_id', '=', 'client_company.id')
                ->where('recommendation_relation.id', '=', $recommendationrelation->id)
                ->get();
            // dd($companiesDetails);
            $terms = '';
            // dd(count($companiesDetails));
            if (count($companiesDetails) > 0) {
                // dd("d");
                $terms .= "By clicking the accept button, {$companiesDetails[0]->vendor_company_name} will accept the rates, terms, and conditions outlined below for the job requirements of {$companiesDetails[0]->client_company_name}.";
            }

            $terms .= '<p>&nbsp;</p>';
            $terms .= ($companyTerms->terms) ? $companyTerms->terms : $global_settings->terms;

            $termsfinal = str_replace(['[TODAY_DATE]', '[AGENCY_NAME]', '[AGENCY_ADDRESS]', '[COMPANY_NAME]', '[COMPANY_ADDRESS]'], [date('d M, Y h:i:s'), $companiesDetails[0]->vendor_company_name, $companiesDetails[0]->vendor_company_address, $companiesDetails[0]->client_company_name, $companiesDetails[0]->client_company_address], $terms);

            // dd($termsfinal,$recommendationrelation->id);

            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_user_id' => ($job_assign) ? $job_assign->spok_user_id : $job_assign_d->user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),
                'to_company_id' => $to_company_id,
                'to_status' => 1,
                'accepterms' => $termsfinal,
                'to_user_id' => $from_user_id,
                'final_status' => 1,
                'add_status' => 1,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->where('id', '=', $recommendationrelation->id)->update($insertArr);
            GeneralMethod::jobRequestIndexElastic($recommendationrelation->id);
            \App\Services\MarketJobV2Service::invalidateCacheForCompany($to_company_id);
            \App\Services\MarketJobV2Service::invalidateCacheForCompany($from_company_id);
        }

        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
        $attachments = [];

        $cc = [];
        $checkMailPermision = GeneralMethod::mailPermission(17);

        if ($checkMailPermision) {

            $mailTypeSubject = GeneralMethod::mailTypeSubject(17, 2);

            $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $to_company_id);
            $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
            $subject = str_replace(['[{JOB_TITLE}]', '[{COMPANY_NAME}]'], [$jobRow->title, $detailsVendorSide->client_company], $mailTypeSubject->subject);
            $employment_type = '';
            if ($jobRow->employment_type == 1) {
                $employment_type = 'CONTRACT';
            } else {
                $employment_type = 'FULLTIME';
            }

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currency();
            $sal_mode = $min_salary = $salary_currency = $max_salary = '';
            $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
            $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
            $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
            $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
            $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

            $status = ucwords($jobRow->status);

            if ($jobRow->status == 'active') {
                $status = 'Hiring';
            } elseif ($jobRow->status == 'inactive') {
                $status = 'Closed';
            } else {
                $status = 'Hold';
            }

            // dd($status);
            $recommendation_text = __('messages.internal_tag');
            $ulink =
            '<span class="fs-6 '.
            @$class.
            ' text-hover-success">'.
            $status.
            '</span>';
            $statusAndId = '<div class="d-flex flex-column">
										'.
                        $ulink.
                        '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                        @$jobRow->id.
                        ' -'.
                        $recommendation_text.
                        '</span>
										</div>';

            $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

            $TERMS_FIXED = '';

            $total_positions = '';
            $total_positions = number_format($jobRow->total_positions);

            $created_at = '';
            $created_at = $jobRow->created_at;
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
            $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                $newDate.
                '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                $total_positions.
                ' Opening</span>
							</div>';

            $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';
            $oprnlink = url('service_company/jobs/job_request/0');

            $jonTermsterms = "<h3><strong>Steps to Accept and Process the Job Request:</strong></h3>
								<ol>
									<li><strong>Log in</strong> to Jero.works using your email and password.</li>
									<li><strong>View the job request</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;Job Requests&quot;</strong> tab after logging in.</li>
									<li>Click <strong>&quot;Accept Job&quot;</strong> to confirm the request.</li>
									<li>Go to the <strong>&quot;All Jobs&quot;</strong> tab, locate the accepted job, and click on it.</li>
									<li>Click the <strong>&quot;Add Candidate&quot;</strong> button, upload resumes, and fill in the required details.</li>
									<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
									<li>Click on the below link to directly open the job request page<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
								</ol>

								<h3><strong>Important Note:</strong></h3>

								<ul>
									<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
									<li>If a candidate remains in <strong>&quot;In Review&quot; status, the profile will only be visible to you and your team</strong>, not the client.</li>
									<li>Ensure that you <strong>change the status to &quot;Sourcing&quot;</strong> for the client to review and take further action on the profiles.</li>
								</ul>
								';
            $template_description = str_replace(['[{VENDOR_NAME}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{HR_PHONE}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, @$jonTermsterms, $TERMS_FIXED, $statusAndId, $dateAdd], $template_description);

            $message = $template_description;

            $notificationemail = GeneralMethod::notification($to_company_id, 'Sales'); // Recuitment Sales
            $defailsEmail = DB::table('companies')->where('id', $to_company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
            //    dd($to_Mail_Id);
            $data = [
                'message' => $message,
            ];
            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.common.index',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $to_company_id
            )->onConnection('emails');

            // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);
        }

        $result = DB::table('market_job_added_count')->insert(['job_id' => $jobid, 'company_id' => $to_company_id, 'status' => 0, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        $resultCount = DB::table('market_job_added_count')->where('job_id', '=', $jobidGet)->where('status', '=', 0)->count();
        $exceeded = 20;
        if ($resultCount == $exceeded) {
            $jobsavefinal = Job::find($jobidGet);
            $jobsavefinal->public_market_status = 4;
            $jobsavefinal->save();
        }

        $companyData = DB::table('companies')
            ->select('companies.*')
            ->where('companies.id', '=', $to_company_id)
            ->first();
        if ($companyData) {
            // dd($companyData);
            $leadCount = $companyData->leadCount + 1;
            DB::table('companies')->where('id', '=', $to_company_id)->update(['leadCount' => $leadCount]);
        }

        $job_metronics_skills = DB::table('job_metronics_skills')->select('job_metronics_skills.skill_name')->where('job_id', '=', $jobidGet)->where('company_id', '=', $from_company_id)->get();
        if ($job_metronics_skills) {
            foreach ($job_metronics_skills as $skills) {
                $insertArr = ['company_id' => $to_company_id, 'job_id' => $jobidGet, 'skill_name' => $skills->skill_name];
                if (! DB::table('job_metronics_skills')->where('company_id', '=', $to_company_id)->where('skill_name', '=', $skills->skill_name)->where('job_id', '=', $jobidGet)->exists()) {
                    DB::table('job_metronics_skills')->insert($insertArr);
                    $rowId = DB::getPdo()->lastInsertId();

                    $skill_name = strtolower(trim($skills->skill_name));
                    $documentId = md5($to_company_id.'_'.$skill_name);
                    $response = $this->elasticService->getClient()->update([
                        'index' => ELASTIC_COMPANY_SKILLS,
                        'id' => $documentId,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => $rowId,
                                'company_id' => $to_company_id,
                                'job_id' => $jobidGet,
                                'skill_name' => $skill_name,
                                'created_at' => null,
                                'updated_at' => null,
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => true,
                        ],
                    ]);
                }
            }

        }

        $client = DB::table('clients')->select('clients.id')->join('companies', 'companies.id', '=', 'clients.company_id')->where('clients.company_id', $to_company_id)->where('clients.self_company', 1)->first();

        // ElasticCompanyIndex::dispatch($to_company_id)->onConnection('emails');
        // ElasticJobsIndex::dispatch($jobidGet)->onConnection('emails');
        if ($client) {
            GeneralMethod::clientStaffingPartner($client->id);
        }

        GeneralMethod::elasticJobsAddEditUpdateIndex($jobidGet);

        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
        $attachments = [];

        $to_notificationemail = GeneralMethod::notification($current_company_id, 'Recuitment'); // Recuitment Sales
        $to_defailsEmail = DB::table('companies')->where('id', $current_company_id)->first()->company_email;
        $com_to_Mail_Id = $to_notificationemail->email ?? $to_defailsEmail ?? '';

        $checkMailPermision = GeneralMethod::mailPermission(45);
        if ($checkMailPermision) {

            $mailTypeSubject = GeneralMethod::mailTypeSubject(45, 1);
            $cc = [];

            $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $current_company_id);
            $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
            $companyAsdress = GeneralMethod::companyClientAddress($from_company_id);
            $partnerAddess = GeneralMethod::companyClientAddress($current_company_id);

            $subject = str_replace(['[{CLIENT_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]'], [$detailsVendorSide->client_company, $jobRow->title, $jobRow->id], $mailTypeSubject->subject);
            $employment_type = '';
            if ($jobRow->employment_type == 1) {
                $employment_type = 'CONTRACT';
            } else {
                $employment_type = 'FULLTIME';
            }

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currency();
            $sal_mode = $min_salary = $salary_currency = $max_salary = '';
            $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
            $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
            $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
            $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
            $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

            $status = ucwords($jobRow->status);

            if ($jobRow->status == 'active') {
                $status = 'Hiring';
            } elseif ($jobRow->status == 'inactive') {
                $status = 'Closed';
            } else {
                $status = 'Hold';
            }

            $recommendation_text = __('messages.internal_tag');
            $ulink =
            '<a href="javascript:void(0);" class="fs-6 '.
            @$class.
            ' text-hover-success">'.
            $status.
            '</a>';
            $statusAndId = '<div class="d-flex flex-column">
										'.
                            $ulink.
                            '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                            @$jobRow->id.
                            ' -'.
                            $recommendation_text.
                            '</span>
										</div>';

            // $template_description = $mailTypeSubject->body_description;
            $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

            $TERMS_FIXED = "{$detailsVendorSide->vendor_company} has accepted the rates, terms, and conditions outlined below for the job requirements of  {$detailsVendorSide->client_company}";

            $total_positions = '';
            $total_positions = number_format($jobRow->total_positions);

            $created_at = '';
            $created_at = $jobRow->created_at;
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
            $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                $newDate.
                '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                $total_positions.
                ' Opening</span>
							</div>';

            $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';
            $id = encrypt($jobRow->id);
            $oprnlink = url('/service_company/mail_jobs_ajax/'.$id.'/');

            // $oprnlink =  url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($jobRow->id) . '/all');

            $afterAcceptJobsteps = "<h3><strong>Steps to Add Candidates/Profiles to a Job:</strong></h3>
										<ol>
											<li><strong>Log in</strong> to Jero.works using your email and password.</li>
											<li><strong>View the job</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;All Jobs&quot;</strong> tab after logging in.</li>
											<li>Click on <strong>&quot;Add Candidate&quot;</strong>, upload resumes, and fill in the required details.</li>
											<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
											<li>Track scheduled interviews for your candidates in the <strong>&quot;Interview Tracker&quot;</strong> tab from the menu.</li>
											<li>Click on the below link to directly open the job page to upload the candidate profile into the job<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
										</ol>

										<h3><strong>Important Notes:</strong></h3>

										<ul>
											<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
											<li>If a candidate remains in <strong>&quot;In Review&quot;</strong> status, the profile will only be visible to you and your team, <strong>not the client</strong>.</li>
											<li>Ensure you <strong>change the status to &quot;Sourcing&quot;</strong> so the client can review and proceed with the profiles.</li>
										</ul>
										';

            $partnersData = DB::table('job_assign')
                ->join('users', 'users.id', '=', 'job_assign.user_id')
                ->select('users.mobile as phone', 'users.name', 'users.email')
                ->where('job_assign.spok_user_id', '!=', '')
                ->where('users.company_id', '=', $from_company_id)
                ->first();

            $template_description = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{CLIENT_COMPANY_NAME}]', '[{CAPTAIN_NAME}]', '[{CAPTAIN_PHONE}]', '[{CAPTAIN_EMAIL}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $partnersData->name, $partnersData->phone, $partnersData->email, $afterAcceptJobsteps, $TERMS_FIXED, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $statusAndId, $dateAdd], $template_description);

            $message = $template_description;

            $notificationemail = GeneralMethod::notification($from_company_id, 'Recuitment'); // Recuitment Sales
            $defailsEmail = DB::table('companies')->where('id', $from_company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
            //    dd($to_Mail_Id);
            $data = [
                'message' => $message,
            ];
            $jonTermssssss = str_replace(['[TODAY_DATE]', '[AGENCY_NAME]', '[COMPANY_NAME]', '[COMPANY_ADDRESS]', '[AGENCY_ADDRESS]'], [date('d M y'), $detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $companyAsdress, $partnerAddess], $jonTerms->terms);
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('term_gen_pdf', compact('jonTermssssss'));

            // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
            $pdfContent = $pdf->output();
            $filename = 'terms-and-conditions-'.$jobRow->id.'.pdf';
            // Option 1: Save PDF to storage and attach file path
            Storage::disk('public')->makeDirectory('termConditionsSendRequest');
            $pdfPath = storage_path('app/public/termConditionsSendRequest/'.$filename.'');
            file_put_contents($pdfPath, $pdfContent);
            $hashname = str_replace('index.php', '', url('storage/app/public/termConditionsSendRequest/'.$filename.''));

            $attachments[] = [
                'path' => $hashname,
                'options' => [],
            ];

            QueueNotificationMailService::dispatch(
                $com_to_Mail_Id,
                $subject,
                'email.common.index',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $from_company_id
            )->onConnection('emails');

        }

        $cc = [];

        $checkMailPermision = GeneralMethod::mailPermission(18);
        if ($checkMailPermision) {

            $mailTypeSubject = GeneralMethod::mailTypeSubject(18, 1);

            $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $current_company_id);
            $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
            $companyAsdress = GeneralMethod::companyClientAddress($from_company_id);
            $partnerAddess = GeneralMethod::companyClientAddress($current_company_id);

            $subject = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]'], [$detailsVendorSide->vendor_company, $jobRow->title, $jobRow->id], $mailTypeSubject->subject);
            $employment_type = '';
            if ($jobRow->employment_type == 1) {
                $employment_type = 'CONTRACT';
            } else {
                $employment_type = 'FULLTIME';
            }

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currency();
            $sal_mode = $min_salary = $salary_currency = $max_salary = '';
            $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
            $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
            $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
            $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
            $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

            $status = ucwords($jobRow->status);

            if ($jobRow->status == 'active') {
                $status = 'Hiring';
            } elseif ($jobRow->status == 'inactive') {
                $status = 'Closed';
            } else {
                $status = 'Hold';
            }

            $recommendation_text = __('messages.internal_tag');
            $ulink =
            '<a href="javascript:void(0);" class="fs-6 '.
            @$class.
            ' text-hover-success">'.
            $status.
            '</a>';
            $statusAndId = '<div class="d-flex flex-column">
										'.
                            $ulink.
                            '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                            @$jobRow->id.
                            ' -'.
                            $recommendation_text.
                            '</span>
										</div>';

            // $template_description = $mailTypeSubject->body_description;
            $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

            $TERMS_FIXED = "{$detailsVendorSide->vendor_company} has accepted the rates, terms, and conditions outlined below for the job requirements of  {$detailsVendorSide->client_company}";

            $total_positions = '';
            $total_positions = number_format($jobRow->total_positions);

            $created_at = '';
            $created_at = $jobRow->created_at;
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
            $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                $newDate.
                '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                $total_positions.
                ' Opening</span>
							</div>';

            $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';

            $id = encrypt($jobRow->id);
            $oprnlink = url('/service_company/mail_jobs_ajax/'.$id.'/');

            // $oprnlink =  url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($jobRow->id) . '/all');

            $afterAcceptJobsteps = "<h3><strong>Steps to Add Candidates/Profiles to a Job:</strong></h3>
										<ol>
											<li><strong>Log in</strong> to Jero.works using your email and password.</li>
											<li><strong>View the job</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;All Jobs&quot;</strong> tab after logging in.</li>
											<li>Click on <strong>&quot;Add Candidate&quot;</strong>, upload resumes, and fill in the required details.</li>
											<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
											<li>Track scheduled interviews for your candidates in the <strong>&quot;Interview Tracker&quot;</strong> tab from the menu.</li>
											<li>Click on the below link to directly open the job page to upload the candidate profile into the job<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
										</ol>

										<h3><strong>Important Notes:</strong></h3>

										<ul>
											<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
											<li>If a candidate remains in <strong>&quot;In Review&quot;</strong> status, the profile will only be visible to you and your team, <strong>not the client</strong>.</li>
											<li>Ensure you <strong>change the status to &quot;Sourcing&quot;</strong> so the client can review and proceed with the profiles.</li>
										</ul>
										';

            $template_description = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{CLIENT_COMPANY_NAME}]', '[{CLIENT_NAME}]', '[{CLIENT_PHONE}]', '[{CLIENT_EMAIL}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, $afterAcceptJobsteps, $TERMS_FIXED, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $statusAndId, $dateAdd], $template_description);

            $message = $template_description;

            $notificationemail = GeneralMethod::notification($from_company_id, 'Recuitment'); // Recuitment Sales
            $defailsEmail = DB::table('companies')->where('id', $from_company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
            //    dd($to_Mail_Id);
            $data = [
                'message' => $message,
            ];
            $jonTermssssss = str_replace(['[TODAY_DATE]', '[AGENCY_NAME]', '[COMPANY_NAME]', '[COMPANY_ADDRESS]', '[AGENCY_ADDRESS]'], [date('d M y'), $detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $companyAsdress, $partnerAddess], $jonTerms->terms);
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('term_gen_pdf', compact('jonTermssssss'));

            // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
            $pdfContent = $pdf->output();
            $filename = 'terms-and-conditions-'.$jobRow->id.'.pdf';
            // Option 1: Save PDF to storage and attach file path
            Storage::disk('public')->makeDirectory('termConditionsSendRequest');
            $pdfPath = storage_path('app/public/termConditionsSendRequest/'.$filename.'');
            file_put_contents($pdfPath, $pdfContent);
            $hashname = str_replace('index.php', '', url('storage/app/public/termConditionsSendRequest/'.$filename.''));

            $attachments[] = [
                'path' => $hashname,
                'options' => [],
            ];

            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.common.index',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $from_company_id
            )->onConnection('emails');
        }

        $cachekeys = [
            'alljobslist_version_user_'.$this->user->id,
            'alljobslist_version_company_'.$current_company_id,   // invalidate list for all team members
            'allclientrequisition_version_user_'.$this->user->id,
            'allclients_version_user_'.$this->user->id,
            'alldirectclient_version_user_'.$this->user->id,
            'allconsultancy_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $data = ['status' => 'success', 'message' => ''];

        return response()->json($data);

        // return Reply::dataOnly(['status' => 'success']);

    }

    public function recommandedrequest_ajax(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $valCheck = $request->input('sortingfilter');
        $mstatus_id = $request->input('mstatus_id');
        $sort = [];
        $country = '';
        $filters = [];
        $customOrder = [8, 1, 2, 3, 4, 5, 6, 7];

        $cachePayload = [
            'company' => $company_id,
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'valCheck' => $valCheck,
            'user_id' => $this->user->id,
            'mstatus_id' => $mstatus_id,
        ];

        $versionKey = 'alljobrequest_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:alljobrequest:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        // Apply fallback sort by ID descending
        $sort[] = ['final_status' => ['order' => 'asc', 'unmapped_type' => 'long']];

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['to_company_id' => $company_id]],
                                ['term' => ['final_status' => 0]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => $filters, // We'll add all filters here
            ],
        ];

        // Build the final query
        $params = [
            'index' => ELASTIC_JOB_REQUEST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
            $total = 0;
            $data = [];
        } else {
            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.request_jobs_ajax', compact('data', 'userPermissions'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));

        return response()->json($responseData);

    }

    /**
     * Render the custom Add Requisition form pre-selected for a specific Client.
     *
     * Intent: Provide tenant-isolated client requisition creation with Redis-cached static data and active package limits.
     * Tenancy Note: Scoped strictly by company_id to prevent cross-tenant data leaks and high memory allocation.
     *
     * @param  int|string  $id  Client ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function curstomcreate($id)
    {
        $this->pageTitle = __('Add Requisition');

        abort_if(Gate::denies('add_jobs'), 403);

        $eventId = 2;
        $coinsRequ = 1;
        $companyId = (int) auth()->user()->company_id;
        $userId = (int) auth()->user()->id;

        // 1. Coin/package gate check — cached 30s
        $packageStatus = Cache::remember('coins_valid:'.$companyId.':event:'.$eventId, 30, function () use ($companyId, $eventId, $coinsRequ) {
            return GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        });
        if ($packageStatus->status) {
            session()->flash('packageStatus', $packageStatus->message);

            return redirect(url('service_company/jobs'));
        }

        // 2. User + company profile — cached 1 hour per user
        $userCompanyProfile = Cache::remember('user_profile:'.$userId, 3600, function () use ($userId) {
            return User::join('companies', 'companies.id', '=', 'users.company_id')
                ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                ->select('users_profile.country', 'companies.logo', 'companies.company_name')
                ->where('users.id', '=', $userId)
                ->first();
        });
        $this->country = @$userCompanyProfile->country;
        $this->companylogo = @$userCompanyProfile->logo;
        $this->company_name = @$userCompanyProfile->company_name;

        // 3. Active job limit check — tenant-scoped & cached 60s (was cross-tenant bug)
        $packageJobLimit = @$this->activePackage->package->no_of_job_openings;
        if ($packageJobLimit !== null) {
            $activeJobsCount = Cache::remember('company:'.$companyId.':active_jobs_count', 60, function () use ($companyId) {
                return Job::where('company_id', $companyId)->where('status', 'active')->count();
            });
            if ($activeJobsCount > 0 && $packageJobLimit <= $activeJobsCount) {
                $this->message = __('modules.subscription.jobPurchase');

                return view('service_company.subscription.ask_purchase', $this->data);
            }
        }

        // 4. Tenant-scoped data — cached 5 min
        $this->categories = Cache::remember('tenant:'.$companyId.':categories', 300, function () use ($companyId) {
            return JobCategory::where('company_id', $companyId)->get();
        });
        $this->client = Client::select('*')->where('company_id', $companyId)->where('id', $id)->orderBy('clients.id', 'ASC')->get();

        // 5. Zero-query placeholders for view properties unused on the create form
        $this->questions = collect();
        $this->companies = collect();
        $this->locations = collect();
        $this->skills = collect();
        $this->consultancy = collect();
        $this->team = collect();
        $this->other_consultancy = collect();
        $this->jobLocation = [];
        $this->jobCity = collect();
        $this->jobCitySelected = [];
        $this->cities = '';
        $this->default_loc = '';
        $this->default_qualification = '';
        $this->valSelected = [];

        // 6. Static helpers (in-memory, no DB)
        $this->employmentType = GeneralMethod::employmentType();
        $this->priority = GeneralMethod::priority();
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();

        // 7. Global reference data — cached 24 h
        $this->qualification = Cache::remember('global_qualification_master', 86400, function () {
            return DB::table('qualification_master')
                ->select('*')
                ->whereNotIn('id', [60])
                ->orderBy('qualification_master.display_order', 'ASC')
                ->get();
        });
        $this->qualificationTag = $this->qualification->pluck('name');

        $this->countries = Cache::rememberForever('global_countries_list', function () {
            return DB::table('countries')->get();
        });

        // 8. Recruiter list — cached 5 min per user email
        $email = auth()->user()->email;
        $this->recruiterlist = Cache::remember('user_recruiterlist:'.md5($email), 300, function () use ($email) {
            return DB::table('users')
                ->select('users.name', 'users.id')
                ->where('users.email', $email)
                ->where('users.type', '=', 0)
                ->get();
        });

        $this->page_event = 'ADD';
        $this->direct_add = false;

        // 9. Coin rates & balance — cached 5 min / 1 min
        $this->job_coins = Cache::remember('coin_event:operational:company:'.$companyId.':event:2', 300, function () use ($companyId) {
            return DB::table('coin_order_events')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $companyId)
                ->where('coin_event_id', '=', 2)
                ->orderBy('id', 'DESC')
                ->first();
        });
        $this->candidates_coins = Cache::remember('coin_event:operational:company:'.$companyId.':event:4', 300, function () use ($companyId) {
            return DB::table('coin_order_events')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $companyId)
                ->where('coin_event_id', '=', 4)
                ->orderBy('id', 'DESC')
                ->first();
        });
        $this->company_coins = Cache::remember('company_coins:company:'.$companyId, 60, function () use ($companyId) {
            return DB::table('company_coins')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $companyId)
                ->orderBy('id', 'DESC')
                ->first();
        });

        return view('service_company.jobs.create', $this->data);
    }

    /**
     * Render the standard Add Requisition form.
     *
     * Intent: Provide tenant-isolated job creation with Redis-cached static datasets and package limits.
     * Tenancy Note: Scoped strictly by company_id for categories, clients, and active job counters.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $this->pageTitle = __('Add Requisition');

        abort_if(Gate::denies('add_jobs'), 403);

        $eventId = 2;
        $coinsRequ = 1;
        $companyId = (int) auth()->user()->company_id;
        $userId = (int) auth()->user()->id;

        // 1. Coin/package gate — cached 30s (result changes only after a purchase)
        $packageStatus = Cache::remember('coins_valid:'.$companyId.':event:'.$eventId, 30, function () use ($companyId, $eventId, $coinsRequ) {
            return GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
        });
        if ($packageStatus->status) {
            session()->flash('packageStatus', $packageStatus->message);

            return redirect(url('service_company/jobs'));
        }

        // 2. User + company profile — cached 1 hour per user
        $userCompanyProfile = Cache::remember('user_profile:'.$userId, 3600, function () use ($userId) {
            return User::join('companies', 'companies.id', '=', 'users.company_id')
                ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                ->select('users_profile.country', 'companies.logo', 'companies.company_name')
                ->where('users.id', '=', $userId)
                ->first();
        });
        $this->country = @$userCompanyProfile->country;
        $this->companylogo = @$userCompanyProfile->logo;
        $this->company_name = @$userCompanyProfile->company_name;

        // 3. Active job limit check — tenant-scoped & cached 60s (was cross-tenant bug)
        $packageJobLimit = @$this->activePackage->package->no_of_job_openings;
        if ($packageJobLimit !== null) {
            $activeJobsCount = Cache::remember('company:'.$companyId.':active_jobs_count', 60, function () use ($companyId) {
                return Job::where('company_id', $companyId)->where('status', 'active')->count();
            });
            if ($activeJobsCount > 0 && $packageJobLimit <= $activeJobsCount) {
                $this->message = __('modules.subscription.jobPurchase');

                return view('service_company.subscription.ask_purchase', $this->data);
            }
        }

        // 4. Tenant-scoped data — cached 5 min; busted by JobCategoryObserver / ClientObserver
        $this->categories = Cache::remember('tenant:'.$companyId.':categories', 300, function () use ($companyId) {
            return JobCategory::where('company_id', $companyId)->get();
        });
        $this->client = Cache::remember('tenant:'.$companyId.':clients', 300, function () use ($companyId) {
            return Client::select('*')->where('company_id', $companyId)->orderBy('clients.name', 'ASC')->get();
        });

        // 5. Zero-query placeholders for view properties unused on the create form
        $this->questions = collect();
        $this->companies = collect();
        $this->locations = collect();
        $this->skills = collect();
        $this->consultancy = collect();
        $this->team = collect();
        $this->other_consultancy = collect();
        $this->jobLocation = [];
        $this->jobCity = collect();
        $this->jobCitySelected = [];
        $this->cities = '';
        $this->default_loc = '';
        $this->default_qualification = '';
        $this->valSelected = [];

        // 6. Static helpers (in-memory, no DB)
        $this->employmentType = GeneralMethod::employmentType();
        $this->priority = GeneralMethod::priority();
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();

        // 7. Global reference data — cached 24 h; second pluck done in-memory (was 2 identical queries)
        $this->qualification = Cache::remember('global_qualification_master', 86400, function () {
            return DB::table('qualification_master')
                ->select('*')
                ->whereNotIn('id', [60])
                ->orderBy('qualification_master.display_order', 'ASC')
                ->get();
        });
        $this->qualificationTag = $this->qualification->pluck('name');

        $this->countries = Cache::rememberForever('global_countries_list', function () {
            return DB::table('countries')->get();
        });

        // 8. Recruiter list — cached 5 min per user email
        $email = auth()->user()->email;
        $this->recruiterlist = Cache::remember('user_recruiterlist:'.md5($email), 300, function () use ($email) {
            return DB::table('users')
                ->select('users.name', 'users.id')
                ->where('users.email', $email)
                ->where('users.type', '=', 0)
                ->get();
        });

        $this->page_event = 'ADD';
        $this->direct_add = true;

        // 9. Coin rates & balance — cached 5 min / 1 min
        $this->job_coins = Cache::remember('coin_event:operational:company:'.$companyId.':event:2', 300, function () use ($companyId) {
            return DB::table('coin_order_events')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $companyId)
                ->where('coin_event_id', '=', 2)
                ->orderBy('id', 'DESC')
                ->first();
        });
        $this->candidates_coins = Cache::remember('coin_event:operational:company:'.$companyId.':event:4', 300, function () use ($companyId) {
            return DB::table('coin_order_events')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $companyId)
                ->where('coin_event_id', '=', 4)
                ->orderBy('id', 'DESC')
                ->first();
        });
        $this->company_coins = Cache::remember('company_coins:company:'.$companyId, 60, function () use ($companyId) {
            return DB::table('company_coins')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $companyId)
                ->orderBy('id', 'DESC')
                ->first();
        });

        return view('service_company.jobs.create', $this->data);
    }

    public function show($id)
    {
        $this->job = Job::find($id);

        return $this->job;
    }

    public function getContactPerson($client_id)
    {
        $jobLocation = explode(',', request()->jobLocation);
        // dd($jobLocation);
        $ccArr = '';
        $locArr = '';
        $address_show = false;
        $client_arr = DB::table('client_contact_info')->select('*')->where('client_id', $client_id)->get();
        foreach ($client_arr as $cArr) {
            $ccArr .= '<option  value="'.$cArr->id.'">'.ucwords($cArr->contact_person_name).'</option>';
        }

        $locInfo = DB::table('client_location')
            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select(
                'client_location.id',
                'client_location.city',
                'client_location.address',
                'client_location.postcode',
                'client_location.country_id',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name'
            )
            ->where('client_id', $client_id)
            ->get();

        // dd( count($locInfo ));

        if (count(@$locInfo) > 0) {
            foreach ($locInfo as $lArr) {

                $city_name = $lArr->city;
                $selected = '';
                if ($lArr->country_id == 101) {
                    $city_name = $lArr->city_name;
                }
                if (in_array($lArr->id, $jobLocation)) {
                    $selected = 'selected';
                }

                $locArr .= '<option   value="'.$lArr->id.'" '.$selected.'>'.ucwords($lArr->address).'&nbsp;-&nbsp;'.ucwords($city_name).' , '.ucwords($lArr->state_name).' , '.ucwords($lArr->country_name).'</option>';

            }
        } else {
            $locArr = ' <option value="" >No Address</option>';
            $address_show = true;
        }

        $job_id = @$_REQUEST['job_id'];
        if ($_REQUEST['job_id'] > 0) {
            $client_location_jobs = DB::table('client_location_jobs')
                ->select('client_location_jobs.id', 'client_location_jobs.client_location_id')
                ->where('client_location_jobs.job_id', $job_id)
                ->get()
                ->toArray();
        }

        $city = DB::table('client_location')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
            ->where('client_id', $client_id)
            ->groupBy('client_location.city')
            ->get();
        $clientcity = '';
        foreach ($city as $arr) {

            $sel = '';
            if ($job_id > 0) {
                foreach ($client_location_jobs as $locVal) {
                    if ($locVal->client_location_id == $arr->id) {
                        $sel = 'selected="selected"';
                    }
                }
            }

            $clientcity .= '<option   value="'.$arr->id.'"  '.@$sel.'>'.ucwords($arr->city).' '.$arr->postcode.' '.$arr->postcode.'</option>';
        }

        $addresses = DB::table('client_location')
            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
            ->select(
                'client_location.id',
                'client_location.city',
                'client_location.address',
                'client_location.postcode',
                'client_location.country_id',
                'city_list.city as city_name',
                'state_list.state as state_name',
                'countries.country_name'
            )
            ->where('client_id', $client_id)
            ->get()->map(function ($lArr) {

                // $locArr .= '<option   value="' . $lArr->id . '" ' . $selected . '>' . . '</option>';
                return ucwords($lArr->address).'&nbsp;-&nbsp;'.ucwords($lArr->city_name).' '.ucwords($lArr->state_name).' - '.ucwords($lArr->country_name);
            });

        // dd($addresses);

        return Reply::dataOnly(['status' => 'success', 'ccArr' => $ccArr, 'locArr' => $locArr, 'clientcity' => $clientcity, 'addresses' => $addresses, 'address_show' => $address_show]);

    }

    public function getlocationCity($location_id)
    {

        $locationId = explode(',', $location_id);
        $stateId = DB::table('client_location')->whereIn('client_location.id', $locationId)->pluck('state')->toArray();
        $ccArr = '';
        $locArr = '';
        $locInfo = DB::table('city_list')
            ->select('city_list.city_id', 'city_list.city as city_name')
            ->whereIn('city_list.state_id', $stateId)
            ->get();
        if ($locInfo) {

            foreach ($locInfo as $lArr) {

                $city_name = $lArr->city_name;

                $locArr .= '<option   value="'.$lArr->city_id.'">'.ucwords($city_name).'</option>';

            }

        }

        return Reply::dataOnly(['status' => 'success', 'ccArr' => $ccArr, 'locArr' => $locArr, 'clientcity' => '']);
    }

    public function hiring_manager($id)
    {

        $userId = auth()->user()->id;
        $this->team = DB::table('users')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select('users.root_user', 'users.email', 'users.name', 'users.working_status', 'users_profile.designation')
            ->where('users.id', '=', $userId)
            ->where('status', '=', 'active')
            ->first();

        $this->client_types = DB::table('clients')->where('id', $id)->first();
        $this->logo = $this->client_types->logo_logical_path ?? $this->client_types->logo;
        // dd($this->logo);

        $hiring_namager = '';
        $hiring_namager = $_REQUEST['hiring_namager'];
        $this->hiring_namagerArr = explode(',', $hiring_namager);

        $this->client_id = $id;

        // V2 Step 4: single query replaces 1+N per-designation queries; $contPersonArr
        // was never accessed by hiring_manager.blade.php — only $assigned is rendered.
        $this->assigned = DB::table('client_contact_info')
            ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.calling_code', 'client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
            ->orderBy('client_contact_info.contact_person_name', 'ASC')
            ->get()->toArray();

        // dd($this->assigned);

        // if(Client::where('id',$this->client_id)->where('self_company', 1)->exists()){

        // 		$company_id = auth()->user()->company_id;
        // 		$this->assigned = DB::table('users')
        // 			->leftjoin('role_user', 'role_user.user_id', 'users.id')
        // 			->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
        // 			->leftjoin('roles', 'roles.id', 'role_user.role_id')
        // 			->select('users.id', 'users.id as client_id', 'users.name as contact_person_name','users.email as contact_person_email','users.mobile as contact_person_number','roles.display_name as contact_person_designation', 'users.working_status')
        // 			->where('users.company_id', '=', $company_id)
        // 			->where('status', '=', 'active')
        // 			->orderBy('users.name', 'ASC')
        // 			->get();
        // }

        // dd("dk");

        $view = view('service_company.jobs.hiring_manager', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'logo' => $this->logo]);
    }

    public function hiring_contact_person(Request $request)
    {

        $userId = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'cp_email_0' => [
                'required',
                'email:rfc,dns',
                'disposable_email',
            ],
        ], [
            'cp_email_0.required' => 'Email is required',
            'cp_email_0.email' => 'Please enter a valid email',
            'cp_email_0.disposable_email' => 'Temporary emails are not allowed',
        ]);

        if ($validator->fails()) {
            return Reply::dataOnly(['status' => 'warning', 'message' => $validator->errors()->first()]);
        }

        $values = [
            'client_id' => $request->id,
            'contact_person_name' => $request->cp_name_0,
            'contact_person_email' => $request->cp_email_0,
            'contact_person_number' => $request->cp_number_0,
            'calling_code' => $request->phone_country_code,
            'contact_person_designation' => $request->cp_desigination_0,
        ];
        DB::table('client_contact_info')->insert($values);
        $lastInsertedId = DB::getPdo()->lastInsertId();
        $rowSection = '';
        if ($lastInsertedId > 0) {
            $rowSection = '<tr>
              <td><div class="d-flex align-items-center">
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                    <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">S</div>
                    </a> </div>
                  <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.@$request->cp_name_0.'</span> <span class="text-muted fw-bold d-block fs-7">'.@$request->cp_desigination_0.'</span> </div>
                </div></td>
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.@$request->cp_email_0.'</span> 
			  <div style="position: relative;display: flex;">
								    <div class="iti__flag-container" data-code="'.$request->phone_country_code.'" data-row="'.$lastInsertedId.'" id="phoneDisplay_'.$lastInsertedId.'" style="display: flex;position: relative;"> </div>
										<div style="margin-left: 6px;margin-top: 2px;">
											<a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.$request->cp_number_0.'</a>
										</div>
						</div>
						</div></td>
              <td><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Active</span> </div></td>
              <td><div class="d-flex flex-column"> <a id="hiring_manager_select_'.$lastInsertedId.'" href="javascript:void(0);" class="cp_select btn btn-sm w-130 btn-light-danger" rel="'.$lastInsertedId.'" data-assign="1" data-title="'.@$request->cp_name_0.'" data-email="'.@$request->cp_email_0.'">Remove</a> </div></td>
            </tr>';

        }

        return Reply::dataOnly(['status' => 'success', 'view' => $rowSection, 'contact_person_name' => $request->cp_name_0]);
    }

    public function mail_contact_person(Request $request)
    {

        $userId = auth()->user()->id;

        $values = [
            'client_id' => $request->id,
            'contact_person_name' => $request->cp_name_0,
            'contact_person_email' => $request->cp_email_0,
            'contact_person_number' => $request->cp_number_0,
            'calling_code' => $request->phone_country_code,
            'contact_person_designation' => $request->cp_desigination_0,
        ];
        DB::table('client_contact_info')->insert($values);
        $lastInsertedId = DB::getPdo()->lastInsertId();
        $rowSection = '';
        if ($lastInsertedId > 0) {
            $rowSection = '<tr>
              <td><div class="d-flex align-items-center">
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                    <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">S</div>
                    </a> </div>
                  <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.@$request->cp_name_0.'</span> <span class="text-muted fw-bold d-block fs-7">'.@$request->cp_desigination_0.'</span> </div>
                </div></td>
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.@$request->cp_email_0.'</span> </div></td>
              <td><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Active</span> </div></td>
              <td><div class="d-flex flex-column"> <a id="hiring_manager_select_'.$lastInsertedId.'" href="javascript:void(0);" class="cp_select btn btn-sm w-130 btn-light-danger" rel="'.$lastInsertedId.'" data-assign="1" data-title="'.@$request->cp_name_0.'" data-email="'.@$request->cp_email_0.'">Remove</a> </div></td>
            </tr>';

        }

        return Reply::dataOnly(['status' => 'success', 'view' => $rowSection, 'contact_person_name' => $request->cp_email_0]);
    }

    public function recruiter($id)
    {
        //    dd("dkd");
        $recruiter = '';
        $recruiter = $_REQUEST['recruiter'];
        $recruiter_id = $_REQUEST['recruiter_id'];

        $this->recruiter = explode(',', $recruiter);
        $recruiterId = explode(',', $recruiter_id);
        $recruiterId = array_values(
            array_filter(
                array_map(
                    fn ($id) => (int) trim($id),
                    explode(',', $recruiter_id)
                )
            )
        );

        $this->client_id = $id;
        $contPersonArr = [];

        $clientContactArr = DB::table('client_contact_info')
            ->select('client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
            ->orderBy('client_contact_info.contact_person_designation', 'ASC')
            ->groupBy('client_contact_info.contact_person_designation')
            ->get();

        $cnt = 0;
        foreach ($clientContactArr as $cval) {
            // $contPersonArr[$cnt]			=	$cval->contact_person_designation;

            $personDetail = DB::table('client_contact_info')
                ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
                ->where('client_contact_info.client_id', $this->client_id)
                ->where('client_contact_info.contact_person_designation', operator: $cval->contact_person_designation)
                ->orderBy('client_contact_info.contact_person_name', 'ASC')
                ->get();

            $i = 0;
            foreach ($personDetail as $pDetail) {
                $contPersonArr[$cval->contact_person_designation][$i]['id'] = $pDetail->id;
                $contPersonArr[$cval->contact_person_designation][$i]['client_id'] = $pDetail->client_id;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_name'] = $pDetail->contact_person_name;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_email'] = $pDetail->contact_person_email;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_number'] = $pDetail->contact_person_number;
                $contPersonArr[$cval->contact_person_designation][$i]['contact_person_designation'] = $pDetail->contact_person_designation;
                $i++;
            }

            $cnt++;

        }

        $this->contPersonArr = $contPersonArr;

        $this->assigned = DB::table('client_contact_info')
            ->select('client_contact_info.id', 'client_contact_info.client_id', 'client_contact_info.contact_person_name', 'client_contact_info.contact_person_email', 'client_contact_info.contact_person_number', 'client_contact_info.contact_person_designation')
            ->where('client_contact_info.client_id', $this->client_id)
            // ->where('client_contact_info.contact_person_designation',$cval->contact_person_designation)
            ->orderBy('client_contact_info.contact_person_name', 'ASC')
            ->get()->toArray();

        $company_id = auth()->user()->company_id;

        // $this->team = DB::table('users')
        // 	->leftjoin('role_user', 'role_user.user_id', 'users.id')
        // 	->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
        // 	->leftjoin('roles', 'roles.id', 'role_user.role_id')
        // 	->select('users.id', 'users.name','users.email', 'roles.display_name', 'users.working_status', 'users_profile.designation')
        // 	->where('users.company_id', '=', $company_id)
        // 	->where('status', '=', 'active')
        // 	->orderBy('users.name', 'ASC')
        // 	->get();

        $array = $trimmedArray = array_map('trim', $this->recruiter);

        // dd($array);

        // Get the top priority users
        // $topUsers = DB::table('users')
        // ->leftjoin('role_user', 'role_user.user_id', 'users.id')
        // ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
        // ->leftjoin('roles', 'roles.id', 'role_user.role_id')
        // ->select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.working_status', 'users_profile.designation')
        // ->where('users.company_id', '=', $company_id)
        // ->where('status', '=', 'active')
        // ->whereIn('users.name', $array) // Only users in the priority array
        // ->orderBy('users.name', 'ASC')
        // ->get();

        $params = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['status' => 'active']],
                            ['terms' => ['id' => $recruiterId]],
                        ],
                    ],
                ],
                'sort' => [
                    ['working_status' => ['order' => 'desc']],
                ],
                'size' => 10000, // optional: get more records
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        // return users
        $topUsers = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);

        // $topUsers = empty($response['hits']['hits']) ? null : array_map(fn($hit) => (object) $hit['_source'], $response['hits']['hits']);
        // $this->updateTimeLine = empty($response['hits']['hits']) ? [] : array_map(function ($hit) {
        // 	return (object) $hit['_source'];
        // }, $response['hits']['hits']);

        // dd($topUsers,$this->recruiter,$this->updateTimeLine);

        $params = [
            'index' => ELASTIC_USERS_LIST,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['status' => 'active']],
                        ],
                        'must_not' => [
                            ['terms' => ['id' => $recruiterId]],
                        ],
                    ],
                ],
                'sort' => [
                    ['working_status' => ['order' => 'desc']],
                ],
                'size' => 10000,
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);

        $remainingUsers = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);

        // $remainingUsers = empty($response['hits']['hits']) ? null : array_map(fn($hit) => (object) $hit['_source'], $response['hits']['hits']);

        // Get the remaining users
        // $remainingUsers = DB::table('users')
        // ->leftjoin('role_user', 'role_user.user_id', 'users.id')
        // ->leftjoin('users_profile', 'users_profile.user_id', 'users.id')
        // ->leftjoin('roles', 'roles.id', 'role_user.role_id')
        // ->select('users.id', 'users.name', 'users.email', 'roles.display_name', 'users.working_status', 'users_profile.designation')
        // ->where('users.company_id', '=', $company_id)
        // ->where('status', '=', 'active')
        // ->whereNotIn('users.name', $array) // Exclude the priority users
        // ->orderBy('users.name', 'ASC') // Order the remaining records by name
        // ->get();

        // Merge both collections
        $this->team = $topUsers->merge($remainingUsers);

        if (strpos(url()->previous(), 'bench-candidates') !== false) {
            $view = view('service_company.jobs.relationship-manager', $this->data)->render();
        } else {
            $view = view('service_company.jobs.recruiter', $this->data)->render();
        }

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    /**
     * Store a newly created Job Requisition.
     *
     * Intent: Delegates job creation, batch persistence, and async ES indexing to JobService.
     * Tenancy Note: Scoped strictly by company_id via authenticated user context.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, JobService $jobService)
    {
        abort_if(Gate::denies('add_jobs'), 403);

        try {
            $job = $jobService->createJob($request->all(), auth()->user());

            return Reply::redirect(route('service_company.jobs.index'), __('menu.jobs').' '.__('messages.createdSuccessfully'));
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e);
            \Log::error('jobs store() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Submission failed. Please try again.']);
        }
    }

    public function viewMore(Request $request, $id)
    {

        // $url = $request->fullUrl();

        // dd($request->all());
        $userId = $this->user->id;

        abort_if(Gate::denies('view_jobs'), 403);
        $company_id = auth()->user()->company_id;
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->currency = GeneralMethod::currency();
        $this->employmentType = GeneralMethod::employmentType();

        $cachePayload = [
            'company' => $company_id,
            'job_id' => $id,
            'bechshowtab' => @$request->bechshowtab,
            'user_id' => $this->user->id,
        ];

        $this->benchshowtabclose = true;
        if (strpos(url()->previous(), 'job_request') === false) {
            $this->benchshowtabclose = false;
        }

        $versionKey = 'jobDetails_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:jobDetails:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $params = [
            'index' => ELASTIC_JOBS,
            'id' => $id,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $this->job = (object) $response['_source'];

        // dd($this->job);

        $this->job->company_id = $this->job->comId;
        $params = [
            'index' => ELASTIC_CLIENT_LIST,
            'id' => $this->job->client_id,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $client_job = (object) $response['_source'];

        $this->job->client_description = '';
        $this->job->logo_logical_path = '';
        $this->job->self_company = '';
        $this->job->client_name = '';
        if ($client_job) {
            $this->job->client_description = $client_job->description;
            $this->job->logo_logical_path = $client_job->logo_logical_path;
            $this->job->self_company = $client_job->self_company;
            $this->job->client_name = $client_job->name;
        }

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'id' => $this->job->comId,
        ];

        $response = $this->elasticService->getClient()->get($params);
        $company_job = (object) $response['_source'];

        $this->job->company_id = $this->job->comId;
        $params = [
            'index' => ELASTIC_JOB_CATEGORIES,
            'id' => $this->job->category_id,
        ];
        $response = $this->elasticService->getClient()->get($params);
        $category_job = (object) $response['_source'];

        // dd($company_job);
        $this->job->cat_name = @$category_job ? @$category_job->name : null;
        $this->job->company_description = '';
        $this->job->company_name = '';
        $this->job->company_logo = '';
        if ($company_job) {

            $this->job->company_description = $company_job->description;
            $this->job->company_name = $company_job->company_name;
            $this->job->company_logo = $company_job->logo;

        }

        $qualifications = (object) $this->job->job_qualifications;

        if (! empty($qualifications)) {
            $first = reset($qualifications);
            $this->job->qualification_name = $first['qualification_name'] ?? '';
        }

        $job_metronics_skills = $this->job->job_metronics_skills;
        $this->skillArr = (object) collect($job_metronics_skills)->map(fn ($row) => (object) $row)
            ->values()->all();

        $this->category_name = 'N/A';

        $this->jobLocation = '';

        if ($this->job->id > 0) {

            $client_city_jobs = $this->job->client_city_jobs;
            $this->jobLocationCount = is_array($client_city_jobs) ? count($client_city_jobs) : 0;

            $client_contact_info_jobs = $this->job->client_contact_info_jobs;
            $clientContactInfoJobs = collect($client_contact_info_jobs);

            $this->clientContactPersonListing = $clientContactInfoJobs
                ->filter(fn ($row) => ! empty($row['contact_person_name']))
                ->pluck('contact_person_name')
                ->map(fn ($c) => ucwords($c))
                ->values()
                ->all();

        }

        $job_assign = $this->job->job_assign;
        $jobAssign = collect($job_assign);
        $this->assignTeamCount = is_array($job_assign) ? count($job_assign) : 0;

        $this->jobAssignList = $jobAssign->where('company_id', $company_id)
            ->filter()
            ->map(fn ($c) => ($c))
            ->values()
            ->all();
        $this->jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $this->job->comId)
            ->filter()
            ->map(fn ($c) => ($c))
            ->values()
            ->all();
        $this->clientjobAssignListCaptain = '';
        if (count($this->jobAssignListCaptain) > 0 && $this->job->comId != $company_id) {
            $this->clientjobAssignListCaptain = ucfirst($this->jobAssignListCaptain[0]['user_name']);
        }
        $this->agencyView = true;
        if ($this->job->comId != $company_id) {
            $this->agencyView = false;
        }
        // dd($this->job->comId,$company_id,$this->jobAssignList,$this->clientjobAssignListCaptain);//ssssst

        $this->captainAccess = $jobAssign->where('user_id', $this->user->id)->where('company_id', $this->job->comId)
            ->filter()
            ->map(fn ($c) => ($c))
            ->values()
            ->count();

        $this->captainAccessCheck = false;
        if ($this->captainAccess > 0) {
            $this->captainAccessCheck = true;
        }
        $this->alertmessage = '';
        if ($this->job->comId == auth()->user()->company_id) {
            $this->alertmessage = 'You do not have permission to perform this action. Only the captain can proceed';
        } else {
            $this->captainAccessCheck = false;
            $this->alertmessage = 'Only client can add questions';
        }

        $recruiterlist = [];

        if ($this->assignTeamCount > 0) {
            foreach ($this->jobAssignList as $key => $row) {
                $row = (object) $row;
                $params = [
                    'index' => ELASTIC_USERS_LIST,
                    'id' => $row->user_id,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $user_job = (object) $response['_source'];

                $industryName = $this->job->industry['industry_name'] ?? '';
                $industryId = $this->job->industry['ind_id'] ?? '';

                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'body' => [
                        'size' => 0,   // we only need counts
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['submitted_by' => $row->user_id]],
                                    ['term' => ['jobId' => $id]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_candidates' => [
                                'value_count' => [
                                    'field' => 'id',   // or any non-null field
                                ],
                            ],
                            'joined' => [
                                'filter' => [
                                    'term' => ['job_master_status_id' => 6],
                                ],
                            ],
                            'sourced' => [
                                'filter' => [
                                    'term' => [
                                        'job_master_status_id' => 1,
                                    ],
                                ],
                            ],
                            'evaluated' => [
                                'filter' => [
                                    'term' => [
                                        'job_master_status_id' => 3,
                                    ],
                                ],
                            ],
                            'offer' => [
                                'filter' => [
                                    'term' => [
                                        'job_master_status_id' => 5,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
                $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;
                $sourced = $response['aggregations']['sourced']['doc_count'] ?? 0;
                $evaluated = $response['aggregations']['evaluated']['doc_count'] ?? 0;
                $offer = $response['aggregations']['offer']['doc_count'] ?? 0;

                $params = [
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['submitted_by' => $row->user_id]],
                                    ['term' => ['jobId' => $id]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_candidate_interview' => [
                                'value_count' => [
                                    'field' => 'id',
                                ],
                            ],
                        ],
                    ],
                ];
                $responseIinterview = $this->elasticService->getClient()->search($params);
                $allCandidateInterview = $responseIinterview['aggregations']['all_candidate_interview']['value'] ?? 0;

                $recruiterlist[$key] = (object) [
                    'id' => $user_job->id,
                    'name' => $user_job->name,
                    'email' => $user_job->email,
                    'image' => $user_job->image,
                    'mobile' => $user_job->mobile,
                    'created_at' => $row->created_at,
                    'calling_code' => $user_job->calling_code,
                    'display_name' => $user_job->display_name,
                    'designation' => $user_job->designation,
                    'allCandidate' => $allCandidate,
                    'Interview' => $allCandidateInterview,
                    'Joined' => $Joined,
                    'sourced' => $sourced,
                    'evaluated' => $evaluated,
                    'offer' => $offer,
                ];

            }
        }

        $this->recruiterlist = $recruiterlist;
        // dd( $this->recruiterlist);

        // dd(auth()->user()->id,$company_id,$this->job->id);

        $client_city_jobs = $this->job->client_city_jobs;
        $this->jobLocationCount = is_array($client_city_jobs) ? count($client_city_jobs) : 0;
        $clientLocation = collect($client_city_jobs);
        $this->jobCitySel = $clientLocation
            ->filter(fn ($row) => ! empty($row['city_name']))
            ->pluck('city_name')
            ->map(fn ($c) => ucwords($c))
            ->values()
            ->all();

        if ($this->job->work_type == 'yes') {
            $this->jobCitySelected = 'Remote';
        } elseif ($this->job->work_type == 'no') {
            $this->jobCitySelected = implode(',', $this->jobCitySel);
        } elseif ($this->job->work_type == 'any') {
            $this->jobCitySelected = 'Remote/'.implode(',', $this->jobCitySel);
        } elseif ($this->job->work_type == 'other') {
            $this->jobCitySelected = ''.implode(',', $this->jobCitySel);
        }

        $jobId = $this->job->id;

        $recommendation_relation = $this->job->recommendation_relation;
        $recommendationRelation = collect($recommendation_relation);

        $recommendedDataList = $recommendationRelation->where('from_company_id', $company_id)->where('to_status', 1)->values()->all();

        // dd($recommendedDataList);
        $recommendedDataListArr = [];
        if ($recommendedDataList) {
            foreach ($recommendedDataList as $key => $row) {
                $row = (object) $row;
                //    dd($row);

                $params = [
                    'index' => ELASTIC_COMPANIES_INDEX,
                    'id' => $row->to_company_id,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $company_job = (object) $response['_source'];

                // dd($company_job->industry['industry_name']);

                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['ClientComID' => $row->to_company_id]],
                                    ['term' => ['jobId' => $id]],
                                ],
                                'must_not' => [
                                    ['terms' => ['job_master_status_id' => [8, 9]]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_candidates' => [
                                'value_count' => [
                                    'field' => 'id',
                                ],
                            ],
                            'joined' => [
                                'filter' => [
                                    'term' => [
                                        'job_master_status_id' => 6,
                                    ],
                                ],
                            ],
                            'sourced' => [
                                'filter' => [
                                    'term' => [
                                        'job_master_status_id' => 1,
                                    ],
                                ],
                            ],
                            'evaluated' => [
                                'filter' => [
                                    'term' => [
                                        'job_master_status_id' => 3,
                                    ],
                                ],
                            ],
                            'offer' => [
                                'filter' => [
                                    'term' => [
                                        'job_master_status_id' => 5,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
                $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;
                $sourced = $response['aggregations']['sourced']['doc_count'] ?? 0;
                $evaluated = $response['aggregations']['evaluated']['doc_count'] ?? 0;
                $offer = $response['aggregations']['offer']['doc_count'] ?? 0;
                $industryName = @$company_job->industry['industry_name']; // $this->job->industry['industry_name'] ?? '';
                $industryId = $this->job->industry['ind_id'] ?? '';

                $params = [
                    'index' => ELASTIC_INERTVIEW_TRACKER,
                    'body' => [
                        'size' => 0,
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['ClientComID' => $row->to_company_id]],
                                    ['term' => ['jobId' => $id]],
                                ],
                                'must_not' => [
                                    ['terms' => ['job_master_status_id' => [8, 9]]],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'all_candidate_interview' => [
                                'value_count' => [
                                    'field' => 'id',
                                ],
                            ],
                        ],
                    ],
                ];
                $responseIinterview = $this->elasticService->getClient()->search($params);
                $allCandidateInterview = $responseIinterview['aggregations']['all_candidate_interview']['value'] ?? 0;

                $recommendedDataListArr[$key] = (object) [
                    'id' => $company_job->id,
                    'company_name' => $company_job->company_name,
                    'logo' => $company_job->logo,
                    'created_at' => $row->created_at,
                    'status' => $row->final_status,
                    'industries' => $industryId,
                    'ind_name' => $industryName,
                    'title' => $this->job->title,
                    'relation_id' => $row->id,
                    'jobid' => $id,
                    'Upload' => $allCandidate,
                    'Interview' => $allCandidateInterview,
                    'Joined' => $Joined,
                    'sourced' => $sourced,
                    'evaluated' => $evaluated,
                    'offer' => $offer,
                ];

            }

        }

        $this->recommendedDataList = $recommendedDataListArr;

        $params = [
            'index' => ELASTIC_JOB_REQUEST,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['to_company_id' => $company_id]],
                            ['term' => ['job_id' => $jobId]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $intRow = isset($response['hits']['hits'][0]) ? (object) $response['hits']['hits'][0]['_source'] : null;

        $this->terms = $intRow ? @$intRow->accepterms : '';

        $chkAdminRole = $this->user->hasRole('admin');

        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

        $userId = $this->user->id;

        if (
            ($chkAdminRole > 0) && (in_array('all_requisition', $this->userPermissions) && in_array(
                'view_jobs',
                $this->userPermissions
            )
            )
        ) {
            // dd("lf");
            $this->interviewShedule = GeneralMethod::Interview('main', $company_id, $userId, $this->job->id, @$request->conid);

            if (! empty(@$request->conid)) {
                $params = [
                    'index' => ELASTIC_CANDIDATE_TIMELINE,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['application_id' => @$request->conid]],
                                    ['term' => ['job_id' => $this->job->id]],
                                ],
                            ],
                        ],
                        'sort' => [
                            ['id' => ['order' => 'desc']],
                        ],
                        'size' => 10000, // optional: get more records
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                // return users
                $this->updateTimeLine = empty($response['hits']['hits']) ? null : array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);

                // $this->updateTimeLine = GeneralMethod::condidate_timeline($this->job->id, @$request->conid);
            }

            // dd($this->jobfindhide);

            // dd($this->updateTimeLine);

        } else {
            // dd("dd");
            $this->interviewShedule = GeneralMethod::Interview('', $company_id, $userId, $this->job->id, @$request->conid);
            if (! empty(@$request->conid)) {

                $params = [
                    'index' => ELASTIC_CANDIDATE_TIMELINE,
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['application_id' => @$request->conid]],
                                    ['term' => ['job_id' => $this->job->id]],
                                ],
                            ],
                        ],
                        'sort' => [
                            ['id' => ['order' => 'desc']],
                        ],
                        'size' => 10000, // optional: get more records
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                // return users
                $this->updateTimeLine = empty($response['hits']['hits']) ? null : array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);

                // $this->updateTimeLine = GeneralMethod::condidate_timeline($this->job->id, @$request->conid);
            }

        }

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'body' => [
                'size' => 1,   // ✅ only one document
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['candidate_id' => (int) @$request->conid]],
                            ['term' => ['jobId' => (int) $this->job->id]],
                        ],
                    ],
                ],
                'sort' => [
                    ['created_at' => ['order' => 'asc']], // or desc
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);

        // first document
        $first = isset($response['hits']['hits'][0]['_source']) ? (object) $response['hits']['hits'][0]['_source'] : null;

        //   dd($first->submitted_by,(int) @$request->conid,$this->job->id);
        $bench_assign_Arr = [];
        $this->bench_assign_count = 0;
        $this->bench_assign_row = [];
        if (! empty(@$request->conid)) {
            if ($first) {
                $this->jobAssignListCaptain = $jobAssign
                    ->where('spok_user_id', '>', 0)
                    ->where('company_id', $this->job->comId)
                    ->pluck('spok_user_id')
                    ->first();   // 👈 only one value

                $params = [
                    'index' => ELASTIC_USERS_LIST,
                    'id' => $this->jobAssignListCaptain,
                ];

                $response = $this->elasticService->getClient()->get($params);
                $user_job = (object) $response['_source'];

                $params = [
                    'index' => ELASTIC_CANDIDATES,
                    'body' => [
                        'size' => 0,   // we only need counts
                        'query' => [
                            'bool' => [
                                'must' => [
                                    ['term' => ['submitted_by' => $this->jobAssignListCaptain]],
                                    ['term' => ['jobId' => $this->job->id]],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $allCandidate = $response['aggregations']['all_candidates']['value'] ?? 0;
                $Interview = $response['aggregations']['interview']['doc_count'] ?? 0;
                $Joined = $response['aggregations']['joined']['doc_count'] ?? 0;
                $bench_assign_Arr[0] = (object) [
                    'id' => $user_job->id,
                    'name' => $user_job->name,
                    'email' => $user_job->email,
                    'image' => $user_job->image,
                    'calling_code' => $user_job->calling_code,
                    'mobile' => $user_job->mobile,
                    'display_name' => $user_job->display_name,
                    'designation' => $user_job->designation,
                    'allCandidate' => $allCandidate,
                    'Interview' => $Interview,
                    'Joined' => $Joined,
                ];

                $this->bench_assign_count = count($bench_assign_Arr);
                $this->bench_assign_row = $bench_assign_Arr;
            }
        }

        // $params = [
        // 	'index' => ELASTIC_BENCH_ASSIGN,
        // 	'body'  => [
        // 		'query' => [
        // 			'bool' => [
        // 				'must' => [
        // 					['term' => ['bench_applications_id'   => (int) @$request->conid]]
        // 				]
        // 			]
        // 		],
        // 		'sort' => [
        // 			['id' => ['order' => 'desc']]
        // 		],
        // 		'size' => 10000 // optional: get more records
        // 	]
        // ];

        // $response = $this->elasticService->getClient()->search($params);
        // // return users
        // $this->bench_assign_count = $response['hits']['total']['value'] ?? 0;
        // $this->bench_assign_row = collect($response['hits']['hits'] ?? [])->map(fn ($hit) => (object) $hit['_source']);

        $this->CaptainCompanyid = auth()->user()->company_id;

        $this->tabshow = true;
        $this->benchshowtab = true;
        $this->benchshowtabComman = false;
        if (@$request->bechshowtab == 1) {
            $this->tabshow = false;
            $this->benchshowtab = false;
            $this->benchshowtabComman = true;
        }

        $this->jobfindhide = true;
        if (@$request->jobfindhide == 1) {
            $this->jobfindhide = false;
        }

        // dd($this->data);
        $this->viewmore = false;
        if ($request->viewmore == 'viewmore') {
            $this->viewmore = true;
        }

        // Hide hiring-manager, action-buttons and pipeline sections when opened from market-jobs
        $this->hideMarketSections = (bool) @$request->hideMarketSections;
        if (session()->has('UrlMailJobId')) {
            $this->viewmore = false;
        }

        // $startOfMonth = Carbon::now()->startOfMonth();
        $startOfMonth = Carbon::now()->startOfMonth()->subMonths(3);
        $endOfMonth = Carbon::now()->endOfMonth();

        // dd($startOfMonth,$endOfMonth);

        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['jobId' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['range' => ['jobId' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [],
            ],
        ];

        // dd($startOfMonth,$endOfMonth);

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'status_counts' => [
                        'terms' => [
                            'field' => 'job_master_status_id', // Use `.keyword` for exact match
                            'size' => 10, // Increase if you have more unique statuses
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $bucketData = $response['aggregations']['status_counts']['buckets'] ?? [];
        $this->result = collect($bucketData)->mapWithKeys(function ($item) {
            return [$item['key'] => $item['doc_count']];
        });

        $this->joined_count = $this->result->get('6', 0);
        $this->sourcing_count = $this->result->get('1', 0);
        $this->screening_count = $this->result->get('2', 0);
        $this->evaluation_count = $this->result->get('3', 0);
        $this->negotiation_count = $this->result->get('4', 0);
        $this->offer_count = $this->result->get('5', 0);
        $this->Rejected_count = $this->result->get('7', 0);
        $this->inreview_count = $this->result->get('8', 0);

        $this->total_candidates = $this->inreview_count + $this->sourcing_count + $this->screening_count + $this->evaluation_count + $this->negotiation_count + $this->offer_count + $this->joined_count + $this->Rejected_count;

        /** Start Candidate last 7 days */
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $paramsCandidates = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['company_id' => $company_id]],
                            ['term' => ['jobId' => $id]],
                        ],
                        'filter' => [
                            [
                                'range' => [
                                    'created_at' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'created_at',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $responseCandidates = $this->elasticService->getClient()->search($paramsCandidates);
        $candidateBuckets = $responseCandidates['aggregations']['uploads_over_time']['buckets'] ?? [];

        $labels = [];
        $candidateValues = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $formatted = $date->format('M d'); // e.g., 'Jul 16'
            $labels[] = $formatted;
            // Find matching doc_count from candidates
            $candidateBucket = collect($candidateBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

            $candidateValues[] = $candidateBucket['doc_count'] ?? 0;
        }
        $this->labels = $labels;
        $this->candidateValues = $candidateValues;
        $this->candidateValuesTotal = array_sum($candidateValues);

        /** End Candidate last 7 days */

        /** Start Offer Count Graph */
        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ], [
                                ['bool' => [
                                    'must_not' => [
                                        ['terms' => ['job_master_status_id' => [8, 9]]],
                                    ],
                                ]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'range' => [
                                        'of_created_date' => [
                                            'gte' => $startOfMonthcomman->toDateString(),
                                            'lte' => $endOfMonthcomman->toDateString(),
                                        ],
                                    ],
                                ],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 0,
                'query' => $boolQuery,
                'aggs' => [
                    'of_offer_count' => [
                        'date_histogram' => [
                            'field' => 'of_created_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startOfMonthcomman->format('Y-m'),
                                'max' => $endOfMonthcomman->format('Y-m'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $candidateBucketsoffer = $response['aggregations']['of_offer_count']['buckets'] ?? [];

        $candidateValuesCommoffer = [];
        $labelsCommoffer = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $endDate = $startDate->copy()->endOfMonth();
            // dd($startDate,$endDate);
            $formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
            $labelsCommoffer[] = $formattedComm;
            $candidateBucketoffer = collect($candidateBucketsoffer)->firstWhere('key_as_string', $startDate->format('Y-m'));
            $candidateValuesCommoffer[] = $candidateBucketoffer['doc_count'] ?? 0;

        }
        $this->labelsCommoffer = $labelsCommoffer;
        $this->candidateValuesCommoffer = $candidateValuesCommoffer;
        //  dd($this->candidateValuesCommoffer,$this->labelsCommoffer);
        $this->candidateValuesTotaloffer = array_sum($candidateValuesCommoffer);

        /** End Offer count Graph */

        /** Start Candidate list joined*/
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(6);
        $endOfMonthcomman = Carbon::now()->endOfMonth();

        $must = [];
        $should = [];
        $filter = [];
        $sort = [];

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['term' => ['job_master_status_id' => 6]],
                                ['term' => ['ClientComID' => 0]],
                            ]),
                        ],
                    ],
                    // Second condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['company_id' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['term' => ['job_master_status_id' => 6]],
                                ['range' => ['ClientComID' => ['gt' => 0]]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                    // Third condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['ClientComID' => $company_id]],
                                ['term' => ['jobId' => $id]],
                                ['term' => ['job_master_status_id' => 6]],
                                ['range' => ['job_id' => ['gt' => 0]]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => [
                    [
                        'bool' => [
                            'should' => [
                                [
                                    'range' => [
                                        'of_joined_date' => [
                                            'gte' => $startOfMonthcomman->toDateString(),
                                            'lte' => $endOfMonthcomman->toDateString(),
                                        ],
                                    ],
                                ],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                ],
            ],
        ];
        $sort[] = ['of_joined_date' => ['order' => 'desc']];
        $params = [
            'index' => ELASTIC_CANDIDATES,
            'request_cache' => true,
            'body' => [
                'track_total_hits' => true,
                'size' => 6,
                'sort' => $sort,
                'query' => $boolQuery,
                'aggs' => [
                    'of_joined_count' => [
                        'date_histogram' => [
                            'field' => 'of_joined_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startOfMonthcomman->format('Y-m'),
                                'max' => $endOfMonthcomman->format('Y-m'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->elasticService->getClient()->search($params);
        $this->total_candidates_join = $response['hits']['total']['value'] ?? 0;
        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);
        $this->data_list = $data;
        $candidateBucketsjoin = $response['aggregations']['of_joined_count']['buckets'] ?? [];

        $candidateValuesCommjoin = [];
        $labelsCommjoin = [];
        $startDateComm = Carbon::now();
        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            $endDate = $startDate->copy()->endOfMonth();
            // dd($startDate,$endDate);
            $formattedComm = $startDate->format('M Y'); // e.g., 'Jul 16'
            $labelsCommjoin[] = $formattedComm;
            $candidateBucketjoin = collect($candidateBucketsjoin)->firstWhere('key_as_string', $startDate->format('Y-m'));
            $candidateValuesCommjoin[] = $candidateBucketjoin['doc_count'] ?? 0;

        }
        $this->labelsCommjoin = $labelsCommjoin;
        $this->candidateValuesCommjoin = $candidateValuesCommjoin;
        //  dd($this->candidateValuesCommjoin,$this->labelsCommjoin);
        $this->candidateValuesTotaljoin = array_sum($candidateValuesCommjoin);

        /** End Candidate list joined*/

        /** Start Interview Section */
        $page = 1;
        $size = 5; // Default 20 results per page
        $from = ($page - 1) * $size;
        $must = [];
        $should = [];
        $filter = [];
        $nowMinus1Hour = now()->subHour()->toIso8601String();
        // dd($id);
        $must[] = [
            'bool' => [
                'must' => [
                    ['term' => ['company_id' => $company_id]],
                    ['term' => ['jobId' => $id]],
                ],
            ],
        ];

        $upcomingParams = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'asc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $nowMinus1Hour,
                                        'format' => 'strict_date_optional_time',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $upcomingData = [];
        $upcomingTotal = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $response = $this->elasticService->getClient()->search($upcomingParams);
            $upcomingTotal = $response['hits']['total']['value'] ?? 0;
            $upcomingData = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        }
        $this->upcomingData = $upcomingData;

        // ////////////////////////////////// last 7 days
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $recentParams = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'desc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'day',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $recentData = [];
        $recentTotal = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $response = $this->elasticService->getClient()->search($recentParams);
            $interviewBuckets = $response['aggregations']['uploads_over_time']['buckets'] ?? [];
            $recentTotal = $response['hits']['total']['value'] ?? 0;
            $recentData = array_map(fn ($hit) => $hit['_source'], $response['hits']['hits']);
        }
        $this->recentData = $recentData;

        $interviewlabels = [];
        $interviewValues = [];

        for ($i = 0; $i < 7; $i++) {
            // dd($date->format('Y-m-d'));
            $date = $startDate->copy()->addDays($i);
            $formatted = $date->format('M d'); // e.g., 'Jul 16'
            $interviewlabels[] = $formatted;
            // Find matching doc_count from candidates
            $interviewBucket = collect($interviewBuckets)->firstWhere('key_as_string', $date->format('Y-m-d'));

            $interviewValues[] = $interviewBucket['doc_count'] ?? 0;
        }
        $this->interviewlabels = $interviewlabels;
        $this->interviewValues = $interviewValues;
        // dd($this->interviewlabels);
        $this->interviewValuesTotal = array_sum($interviewValues);

        // ////////////////////////////////// last 6 months
        $startDateComm = Carbon::now();
        $startDate = $startDateComm->copy()->startOfMonth()->subMonths(5);
        $endDate = $startDateComm;
        $recentParamsMonth = [
            'index' => ELASTIC_INERTVIEW_TRACKER,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => [
                    ['schedule_date' => ['order' => 'desc']],
                ],
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => [
                            [
                                'range' => [
                                    'schedule_date' => [
                                        'gte' => $startDate->toDateString(),
                                        'lte' => $endDate->toDateString(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'uploads_over_time' => [
                        'date_histogram' => [
                            'field' => 'schedule_date',
                            'calendar_interval' => 'month',
                            'format' => 'yyyy-MM-dd',
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startDate->toDateString(),
                                'max' => $endDate->toDateString(),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $recentDataMonth = [];
        $recentTotalMonth = 0;

        if ($this->elasticService->indexExists(ELASTIC_INERTVIEW_TRACKER)) {
            $responseMonth = $this->elasticService->getClient()->search($recentParamsMonth);
            $interviewBucketsMonth = $responseMonth['aggregations']['uploads_over_time']['buckets'] ?? [];
            $recentTotalMonth = $responseMonth['hits']['total']['value'] ?? 0;
            $recentDataMonth = array_map(fn ($hit) => $hit['_source'], $responseMonth['hits']['hits']);
        }
        $this->recentDataMonth = $recentDataMonth;

        // dd($this->recentDataMonth);

        $interviewlabelsMonth = [];
        $interviewValuesMonth = [];

        for ($i = 5; $i >= 0; $i--) {
            $startDate = $startDateComm->copy()->startOfMonth()->subMonths($i);
            // dd($startDate->format('Y-m'),$this->recentDataMonth);
            $formatted = $startDate->format('M Y'); // e.g., 'Jul 16'
            $interviewlabelsMonth[] = $formatted;
            $interviewBucket = collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m-01'));
            // collect($interviewBucketsMonth)->firstWhere('key_as_string', $startDate->format('Y-m'));

            $interviewValuesMonth[] = $interviewBucket['doc_count'] ?? 0;
        }
        $this->interviewlabelsMonth = $interviewlabelsMonth;
        $this->interviewValuesMonth = $interviewValuesMonth;
        $this->interviewValuesTotalMonth = array_sum($interviewValuesMonth);

        /** End Interview Section */
        $this->interview_questions = DB::table('interview_questions')->where('job_id', $id)->first();

        $view = view('service_company.jobs.show', $this->data)->render();
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(JOB_DETAILS_TIME));

        return $responseData;

    }

    public function hmlisting($id)
    {
        $this->currentJobId = $id;
        $this->recruiter = [];
        $company_id = auth()->user()->company_id;
        $this->team = DB::table('users')
            ->leftJoin('job_assign', function ($join) use ($id) {
                $join->on('job_assign.user_id', '=', 'users.id');
                $join->on('job_assign.job_id', '=', DB::raw("'".$id."'"));
            })
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.working_status',
                'job_assign.id as job_assign_id',
                'job_assign.job_id',
                'job_assign.spok_user_id',
                'roles.display_name',
                'users_profile.designation'
            )
            ->where('users.company_id', $company_id)
            ->where('users.status', '=', 'active')
            ->groupBy('users.id')
            ->orderBy('job_assign.id', 'DESC')
            ->get();
        // dd($this->team);
        $view = view('service_company.jobs.hmlisting', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function hmlistingupdate(Request $request)
    {
        // dd("dfgg");
        $company_id = auth()->user()->company_id;

        $assignStatus = $request->assignStatus;
        $currentJobId = $request->currentJobId;
        $currentUserId = $request->currentUserId;
        $makespokesperson = @$request->makespokesperson;

        $id = encrypt($currentJobId);
        $oprnlink = url('/service_company/mail_jobs_ajax/'.$id.'/');

        $job = Job::find($currentJobId);
        $employmentType = GeneralMethod::employmentType();
        $employment_type = (int) $job->employment_type;
        $employment_type = $employmentType[$employment_type];

        $strAdded = DB::table('clients')->select('companies.company_name', 'clients.name', 'clients.self_company')->join('companies', 'companies.id', '=', 'clients.company_id')->where('clients.id', $job->client_id)->first();
        $contact_person_name = '';
        if ($strAdded->self_company == 1) {
            $contact_person_name = $strAdded->company_name;
        } else {
            $contact_person_name = $strAdded->company_name;
            if ($company_id == $job->company_id) {
                $contact_person_name = $strAdded->name;
            }

        }

        $currency = GeneralMethod::currencySymbole();
        $currencyGet = GeneralMethod::currencySymboleforexcell();
        $salaryMode = GeneralMethod::salaryMode();
        $max_salary = $min_salary = $max_sal_mode = $min_sal_mode = '';
        $max_salary = GeneralMethod::thousandsCurrencyFormat($job->max_salary);
        $min_salary = GeneralMethod::thousandsCurrencyFormat($job->min_salary);
        $sal_mode = strtoupper(mb_substr($salaryMode[$job->max_sal_mode], 0, 1));
        $salary_currency = $currency[@$job->max_salary_currency];

        $work_type = 'Work From Office';
        if ($job->work_type == 'no') {
            $work_type = 'Work From Office';
        } elseif ($job->work_type == 'yes') {
            $work_type = 'Remote';
        } elseif ($job->work_type == 'other') {
            $work_type = 'Hybrid';
        }
        $client_loc = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
            ->where('client_location_jobs.job_id', @$currentJobId)
            ->first();
        $client_loc = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->select('city_list.city', 'countries.id as country_id', 'countries.country_name', 'city_list.city as city_name', 'state_list.state as state_name')
            ->where('client_city_jobs.job_id', $currentJobId)
            ->get();

        // dd($client_loc);

        // $country_id = @$client_loc->country_id;
        $location = '';

        if (count($client_loc) > 0) {
            // $country_name = GeneralMethod::getCountryName($country_id);
            foreach ($client_loc as $key => $cval) {
                $location .= (++$key).') '.$cval->city_name.' | '.$cval->state_name.' | '.$cval->country_name.'<br>';
                // $country_name = ucwords($cval['sortname']);
            }
        }

        $jobAdditional = Job::Select('clients.name as client_name',
            'clients.description as client_description',
            'clients.logo_logical_path',
            'job_categories.name as cat_name',
            'qualification_master.name as qualification_name')
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->findOrFail(@$currentJobId);

        $cc = [];

        // && $company_id !=$job->company_id
        if ($assignStatus == 3 && $makespokesperson == true) {

            $job_assign_c = DB::table('job_assign')->where('job_id', $currentJobId)->where('user_id', $currentUserId)->where('company_id', $company_id)->first();

            $cachekeys = [
                'alljobslist_version_user_'.$currentUserId,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            if ($job_assign_c) {
                DB::table('job_assign')->where('job_id', $currentJobId)->where('company_id', $company_id)->update(['spok_user_id' => '']);

                DB::table('job_assign')->where('job_id', $currentJobId)->where('company_id', $company_id)->where('user_id', $currentUserId)->update(['spok_user_id' => $currentUserId]);
            } else {
                $values = [
                    'company_id' => $company_id,
                    'user_id' => $currentUserId,
                    'job_id' => $currentJobId,
                    'spok_user_id' => $currentUserId,
                ];
                DB::table('job_assign')->insert($values);
            }

            GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);

            $cachekeys = [
                'alljobslist_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            return Reply::dataOnly(['status' => 'success', 'msg' => 'Remove Record successfully.']);
        }

        // dd($job);
        if ($assignStatus == 1) {

            $cachekeys = [
                'alljobslist_version_user_'.$currentUserId,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            $job_assign_c = DB::table('job_assign')->where('job_id', $currentJobId)->where('spok_user_id', $currentUserId)->where('company_id', $company_id)->first();

            // dd($job_assign_c);

            DB::table('job_assign')->where('job_id', $currentJobId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();

            $assign_c_id = DB::table('job_assign')->where('job_id', $currentJobId)->where('company_id', $company_id)->max('id');

            if ($job_assign_c && $assign_c_id > 0) {

                $userdata = DB::table('job_assign')->where('id', $assign_c_id)->first();

                DB::table('job_assign')->where('job_id', $currentJobId)->where('company_id', $company_id)->update(['spok_user_id' => $userdata->user_id]);
            }

            GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);

            $cachekeys = [
                'alljobslist_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            return Reply::dataOnly(['status' => 'success', 'msg' => 'Remove Record successfully.']);
        } else {
            DB::table('job_assign')->where('job_id', $currentJobId)->where('user_id', $currentUserId)->where('company_id', $company_id)->delete();
            $cachekeys = [
                'alljobslist_version_user_'.$currentUserId,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            if ($makespokesperson == true && $company_id != $job->company_id) {

            }
            if ($makespokesperson == true && $company_id != $job->company_id) {
                $countTeam = DB::table('job_assign')->where('job_id', $currentJobId)->where('company_id', $company_id)->where('company_id', $company_id)->count();
                if ($countTeam > 0) {
                    $job_assign_c = DB::table('job_assign')->where('job_id', $currentJobId)->where('company_id', $company_id)->first();
                    $values = [
                        'company_id' => $company_id,
                        'user_id' => $currentUserId,
                        'job_id' => $currentJobId,
                        'spok_user_id' => $job_assign_c->spok_user_id,
                    ];
                } else {
                    $values = [
                        'company_id' => $company_id,
                        'user_id' => $currentUserId,
                        'job_id' => $currentJobId,
                        'spok_user_id' => $currentUserId,
                    ];
                }

            } else {
                $values = [
                    'company_id' => $company_id,
                    'user_id' => $currentUserId,
                    'job_id' => $currentJobId,
                ];

            }
            DB::table('job_assign')->insert($values);

            $recruiter = DB::table('users')
                ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
                ->where('job_assign.job_id', $currentJobId)->pluck('users.name')->toArray();

            $recruiterdata = (implode(',', $recruiter));

            $hiring_namager = DB::table('client_contact_info')
                ->join('client_contact_info_jobs', 'client_contact_info_jobs.client_contact_info_id', '=', 'client_contact_info.id')
                ->where('client_contact_info_jobs.job_id', $currentJobId)->pluck('client_contact_info.contact_person_name')->toArray();
            $hiring_namagerdata = (implode(',', $hiring_namager));

            $basecompany = GeneralMethod::baseCompany();
            $senderName = (auth()->user()->name) ? auth()->user()->name : '';
            $fromAddress = (auth()->user()->email) ? auth()->user()->email : '';
            $attachments = [];
            $subject = 'New Job Added';
            $checkMailPermision = GeneralMethod::mailPermission(10);
            if ($checkMailPermision) {
                $company_id = auth()->user()->company_id;
                $mailTypeSubject = GeneralMethod::mailTypeSubject(10, 1);
                $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');

                $subject = str_replace(['[{JOB_TITLE}]', '[{ClientCompany}]', '[{Location}]', '[{JobID}]'], [@$job->title, @$contact_person_name, @$location, @$job->id], $mailTypeSubject->subject);

                $maillink = "You may access and manage the job posting using the link below:<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>Click here to open the job</a> <br><br> If the above link does not open directly, you may copy and paste the below link it into your browser.<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a>';

                if ($job->status == 'active') {
                    $status_job = "<strong><span style='color:#27ae60'>Hiring</span></strong>";
                } elseif ($job->status == 'inactive') {
                    $status_job = "<strong><span style='color:#ff0202'>Closed</span></strong>";
                } else {
                    $status_job = "<strong><span style='color:#ffab00'>Hold</span></strong>";
                }

                $template_description = str_replace(['[{status}]', '[{clientCompany}]', '[{job_id}]', '[{job_title}]', '[{experience}]', '[{salaryrange}]', '[{NoofOpenings}]', '[{location}]', '[{remote}]', '[{JobType}]', '[{Department}]', '[{Qualification}]', '[{hiringManager}]', '[{recruiter}]', '[{addedby}]', '[{JobDescriptions}]', '[{maillink}]'], [@$status_job, @$contact_person_name, @$job->id, @$job->title, @$job->min_exp.'-'.@$job->max_exp.' Years', $currencyGet[@$job->min_salary_currency].' '.@$job->min_salary.' to '.@$job->max_salary.' '.$sal_mode,  @$job->total_positions, @$location, @$work_type, @$employment_type, $jobAdditional->cat_name, $jobAdditional->qualification_name, @$hiring_namagerdata, $recruiterdata, @$request->user()->name, @$job->job_description, $maillink], $mailTypeSubject->body_description);

                $data = [
                    'message' => $template_description,
                ];

                $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                $teamsEmail = DB::table('users')->where('users.id', $currentUserId)->where('company_id', $company_id)->first();

                QueueNotificationMailService::dispatch(
                    $teamsEmail->email,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $company_id
                )->onConnection('emails');

                // $result = $this->notificationMailService->sendEmail($teamsEmail->email, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);

                // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.requisitions.requisition-created',(object) $data, $attachments, $fromAddress, $senderName,$cc);
            }

            //  ElasticJobsIndex::dispatch($job->id)->onConnection('emails');
            GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);

            $cachekeys = [
                'alljobslist_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

            return Reply::dataOnly(['status' => 'success', 'msg' => 'Added Record successfully.']);

        }

    }

    public function spartnerlisting($id)
    {

        $cachePayload = [
            'id' => $id,
            'user_id' => $this->user->id,
        ];

        $versionKey = 'allsendinvitationtoagency_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allsendinvitationtoagency:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet));
        }

        $this->salaryMode = GeneralMethod::salaryMode();
        $this->employmentType = GeneralMethod::employmentType();

        $this->job = Job::Select(
            'jobs.id',
            'jobs.title',
            'jobs.slug',
            'jobs.company_id',
            'jobs.job_description',
            'jobs.job_requirement',
            'jobs.total_positions',
            'jobs.location_id',
            'jobs.category_id',
            'jobs.start_date',
            'jobs.end_date',
            'jobs.status',
            'jobs.required_columns',
            'jobs.meta_details',
            'jobs.section_visibility',
            'jobs.created_at',
            'jobs.updated_at',
            'jobs.priority',
            'jobs.client_id',
            'jobs.min_exp',
            'jobs.max_exp',
            'jobs.min_salary',
            'jobs.max_salary',
            'jobs.qualification',
            'jobs.work_type',
            'jobs.employment_type',
            'jobs.public_market_status',
            'jobs.min_salary_currency',
            'jobs.max_salary_currency',
            'jobs.min_sal_mode',
            'jobs.max_sal_mode',
            'jobs.carrier_website',
            'recommendation_relation.id as recommendation_id',
            'recommendation_relation.from_company_id',
            'recommendation_relation.final_status',
            'recommendation_relation.to_company_id',
            'companies.company_name',
            'companies.logo',
            'clients.name as client_company',
            'clients.logo_logical_path',
            'clients.self_company',
            'clients.name as client_name',
            'clients.description as client_description',
            'job_categories.name as cat_name',
            'qualification_master.name as qualification_name'
        )
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->join('companies', 'companies.id', 'jobs.company_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->leftJoin('recommendation_relation', 'recommendation_relation.job_id', '=', 'jobs.id')
            ->findOrFail($id);

        // dd($this->job);

        $this->skillArr = DB::table('job_metronics_skills')->select('*')->where('company_id', '=', $company_id = auth()->user()->company_id)->where('job_id', $this->job->id)->get();
        // Company Data
        // dd($this->job->company_id);
        $company_arr = DB::table('companies')->join('td_industries', 'td_industries.ind_id', '=', 'companies.industries')->select('*', 'td_industries.ind_name')->where('companies.id', $this->job->company_id)->first();

        // dd($company_arr,$this->job->company_id);
        $this->company_name = @$company_arr->company_name;
        $this->ind_name = @$company_arr->ind_name;
        $this->category_name = 'N/A';
        $this->jobLocation = '';

        if ($this->job->id > 0) {
            $this->jobLocation = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->first();

            $this->jobLocationCount = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->count();

        }

        $this->assignTeam = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.image')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->assignTeamCount = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->count();

        $company_id = auth()->user()->company_id;
        $this->spartnerArr = DB::table('users')
            ->leftjoin('job_assign', 'users.id', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email', 'users.image', 'job_assign.job_id as job_assign_id')
            ->where('users.company_id', $company_id)
            ->where('users.type', 1)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->job_id = $id;

        $countries = DB::table('countries')->select('*')->get();
        // dd($countries);
        $this->countries = $countries;

        $cities = DB::table('countries')
            ->select('countries.country_name')
            ->where('countries.country_name', '!=', '')
            ->orderBy('countries.country_name')
            ->get()
            ->map(function ($city) {
                return $city->country_name;
            });
        $this->cities = $cities;
        $gdFilterData = GeneralMethod::gdFilterData($id);
        // dd($gdFilterData);
        $this->min_exp = ($gdFilterData->min_exp >= 1) ? $gdFilterData->min_exp - 1 : $gdFilterData->min_exp;
        $this->max_exp = $gdFilterData->max_exp + 1;
        $this->min_salary_currency = $gdFilterData->min_salary_currency;
        $this->min_salary = $gdFilterData->min_salary;
        $this->max_salary = $gdFilterData->max_salary;
        $this->min_sal_mode = $gdFilterData->min_sal_mode;
        $this->default_skill = $gdFilterData->default_skill;

        $loc_data = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->join('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->where('client_location_jobs.job_id', $id)
            ->pluck('countries.country_name')
            ->toArray();
        $this->default_loc = implode(',', $loc_data);
        $loc_data_job = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
            ->join('countries', 'countries.id', '=', 'state_list.country_id')
            ->where('client_city_jobs.job_id', $id)
            ->pluck('countries.country_name')
            ->toArray();
        // dd($loc_data_job);
        if (count($loc_data_job) > 0) {
            $this->default_loc = implode(',', $loc_data_job);
        }

        $this->jobLocationCountselect = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->join('countries', 'countries.id', '=', 'client_location.country_id')
            ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name', 'countries.id')
            ->where('client_location_jobs.job_id', $this->job->id)
            ->orderBy('client_location_jobs.id', 'DESC')
            ->first();

        $this->vendor_limit = DB::table('global_settings')->select('vendor_limit')->first()->vendor_limit;

        if ($this->job->public_market_status == 1) {
            $this->class_send = '';
            $this->text_change = 'Approval Pending';
            $this->text_change_action = '';
            $this->color_change = 'text-warning btn-light-warning';
            $this->color_change_action = 'text-warning btn-light-warning';
        } elseif ($this->job->public_market_status == 2) {
            $this->class_send = '';
            $this->text_change = 'Published';
            $this->text_change_action = '';
            $this->color_change = 'text-success btn-light-success';
            $this->color_change_action = 'text-success btn-light-success';
        } elseif ($this->job->public_market_status == 3) {
            $this->class_send = 'post_to_public';
            $this->text_change = 'Unpublish Job';
            $this->text_change_action = 'Publish to Marketplace';
            $this->color_change = 'btn-light-primary';
            $this->color_change_action = 'text-black btn-light-black';
        } elseif ($this->job->public_market_status == 4) {
            $this->class_send = 'post_to_public';
            $this->text_change = 'Unpublish Job';
            $this->text_change_action = 'Publish to Marketplace';
            $this->color_change = 'btn-light-primary';
            $this->color_change_action = 'text-black btn-light-black';
        } else {
            $this->class_send = 'post_to_public';
            $this->text_change = 'Unpublish Job';
            $this->text_change_action = 'Publish to Marketplace';
            $this->color_change = 'btn-light-primary';
            $this->color_change_action = 'text-black btn-light-black';
        }

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must_not' => [
                            ['term' => ['id' => $company_id]],
                        ],
                    ],
                ],
                'aggs' => [
                    'all_companies' => [
                        'value_count' => [
                            'field' => 'id',
                        ],
                    ],
                    'active_companies' => [
                        'filter' => [
                            'term' => [
                                'status' => 'active',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->elasticService->getClient()->search($params);
        $allCompanies = $response['aggregations']['all_companies']['value'] ?? 0;
        $activeCompanies = $response['aggregations']['active_companies']['doc_count'] ?? 0;

        $this->allCompanies = $allCompanies;
        $this->activeCompanies = $activeCompanies;

        $this->td_industries = DB::table('td_industries')->select('ind_id as id', 'ind_name as name')->get();
        $view = view('service_company.jobs.spartnerlisting', $this->data)->render();
        // dd($view);
        $responseData = Reply::dataOnly(['status' => 'success', 'view' => $view]);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));

        return $responseData;

    }

    public function boostkeywords(Request $request)
    {

        //	Employment	Type     0 : Fulltime 		1 : Contract
        $employment_type = '';
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();
        $jobId = $request->jobid;
        // JOB DETAIL
        $currentRowJob = Job::select(
            'title',
            'min_salary',
            'max_salary',
            'min_salary_currency',
            'max_salary_currency',
            'min_sal_mode',
            'max_sal_mode',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();
        $parent_employment_type = $currentRowJob->employment_type;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->get();

        $boostkeywordsDataList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.logo',
                'companies.industries',
                'jobs.title',
                DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
                DB::raw('count(DISTINCT job_applications.id) as Profiles'),
                'jobs.employment_type',
                'job_metronics_skills.skill_name',
                'td_industries.ind_name',

                'staffing_company_relation.from_company_id',
                'staffing_company_relation.to_company_id',
                'staffing_company_relation.final_status',

                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.from_status as rel_from_status',
                'recommendation_relation.to_status as rel_to_status',
                'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id',
                'recommendation_relation.from_company_id as rel_from_company_id',
                'recommendation_relation.final_status as recommendation_final_status',

                'package_active.newjobrequest_status',
                'package_active.newjobrequest_balance',
                'package_active.newclientrequest_status',
                'package_active.newclientrequest_balance',
                'package_active.boostkeywords_status',
                'city_list.city as city_name',
                'state_list.state',
                'countries.country_name',
                'client_location.city',
                'client_location.address'
            );

        // ->join('package_active', 'package_active.company_id', '=', 'companies.id')
        // New Client Request 		:: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD
        // New Job Request	   		:: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD
        if ($parent_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $boostkeywordsDataList = $boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'));
            });
        } elseif ($parent_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $boostkeywordsDataList = $boostkeywordsDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'companies.id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_balance', '>', DB::raw('0'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('1'));
            });
        }

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
            $join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
            $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('jobs', function ($join) use ($company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
        });

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id');
        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id');
        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('countries', 'countries.id', '=', 'client_location.country_id');

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id');

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('state_list', 'state_list.id', '=', 'client_location.state');

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
        });

        $boostkeywordsDataList = $boostkeywordsDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');

        $findstring = explode(' ', $currentRowJob->title);
        if (! empty($findstring)) {
            $boostkeywordsDataList = $boostkeywordsDataList->orWhere(function ($subquery) use ($findstring, $company_id) {
                $subquery->whereIn('package_active.boostkeywords_text', $findstring);
                $subquery->whereIn('companies.company_type', [1, 2]);
                $subquery->where('companies.id', '!=', $company_id);
                $subquery->whereNull('recommendation_relation.final_status');
            });
        }

        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {

                $temp_skill_name = '';
                $temp_skill_name = $jskill->skill_name;
                if (! empty($temp_skill_name)) {
                    $boostkeywordsDataList = $boostkeywordsDataList->orWhere(function ($subquery) use ($temp_skill_name, $company_id) {
                        $subquery->where('package_active.boostkeywords_text', 'like', "%{$temp_skill_name}%");
                        $subquery->whereIn('companies.company_type', [1, 2]);
                        $subquery->where('companies.id', '!=', $company_id);
                        $subquery->whereNull('recommendation_relation.final_status');
                    });
                }

            }
        }

        // $boostkeywordsDataList 	=	$boostkeywordsDataList->where('recommendation_relation.final_status', '!=', 1);
        $boostkeywordsDataList = $boostkeywordsDataList->groupBy('companies.id');
        $boostkeywordsDataList = $boostkeywordsDataList->orderBy(DB::raw('RAND()'));
        $boostkeywordsDataList = $boostkeywordsDataList->take(3);

        return DataTables::of($boostkeywordsDataList)

            ->editColumn('chkbox', function ($row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>';
            })
            ->editColumn('Name', function ($row) {

                $companies_id = '';
                $companies_id = $row->id;

                $recommended_Skill_Name = DB::table('companies')
                    ->select('job_metronics_skills.skill_name')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');

                if (! empty($currentJobSkill)) {

                    foreach ($currentJobSkill as $jskill) {
                        // $recommended_Skill_Name 	=	$recommended_Skill_Name->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill_Name = $recommended_Skill_Name->orWhere('job_metronics_skills.skill_name', $jskill->skill_name);
                        $jskillset .= $jskill->skill_name;

                    }
                }
                $recommended_Skill_Name = $recommended_Skill_Name->where('jobs.company_id', $companies_id);
                $recommended_Skill_Name = $recommended_Skill_Name->get();
                $skillname = '';
                $scnt = 0;
                $tempArr = [];
                if (! empty($recommended_Skill_Name)) {
                    foreach ($recommended_Skill_Name as $sname) {
                        $tempArr[] = $sname->skill_name;
                        $scnt++;

                    }
                }
                if (! empty($tempArr)) {
                    $newArr = array_keys(array_flip($tempArr));
                    // $$tempArr	=	array_unique($tempArr);
                    $skillname = implode(',', $newArr);
                }

                $logo_path = url('img/client-default.png');
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>
							<div style="position:relative">  
							
							<div class="d-flex flex-column w-100 me-2" style="margin-top: 8px;!important;">
										<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 111.81 122.88" style="enable-background:new 0 0 111.81 122.88; height:30px; width:30px;" xml:space="preserve"><style type="text/css"><![CDATA[
							.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#51B53C;}
							.st1{fill:#FFFFFF;}
							.st2{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
						]]></style><g>
						<path class="st0" d="M55.71,0c20.85,13.21,39.68,19.47,55.85,17.99c2.82,57.1-18.26,90.82-55.63,104.89 C19.84,109.71-1.5,77.44,0.08,17.12C19.06,18.12,37.67,14.01,55.71,0L55.71,0L55.71,0z"/>
						<path class="st2" d="M55.73,7.05c18.45,11.7,35.13,17.23,49.43,15.92c2.5,50.54-16.17,80.39-49.24,92.85 C23.98,104.16,5.09,75.6,6.49,22.21C23.29,23.09,39.77,19.46,55.73,7.05L55.73,7.05L55.73,7.05z"/>
						<path class="st0" d="M56.24,19.54c14.22,9.01,27.06,13.27,38.08,12.27c1.92,38.94-12.45,61.93-37.94,71.53 c-0.16-0.06-0.32-0.12-0.48-0.18c-0.16,0.06-0.32,0.12-0.48,0.18c-25.48-9.6-39.86-32.59-37.94-71.53 c11.02,1.01,23.87-3.26,38.08-12.27l0,0l0.33,0.25L56.24,19.54L56.24,19.54L56.24,19.54z"/>
						<path class="st1" d="M35.44,58.28l7.47-0.1l0.56,0.14c1.51,0.87,2.93,1.86,4.26,2.99c0.96,0.81,1.87,1.69,2.74,2.65 c2.68-4.31,5.54-8.28,8.56-11.92c3.31-3.99,5.38-6.18,9.06-9.49l0.73-0.28h8.16l-1.65,1.82c-5.05,5.61-8.21,9.99-12.35,15.97 c-4.15,6-7.85,12.18-11.15,18.54l-1.03,1.98l-0.94-2.02c-1.74-3.73-3.82-7.15-6.3-10.21c-2.48-3.06-5.37-5.78-8.74-8.09 L35.44,58.28L35.44,58.28L35.44,58.28L35.44,58.28z"/></g></svg>
 						</div>
						 <div class="" style="position: absolute;white-space: nowrap;"><span>'.substr(@$skillname, 0, 50).$companies_id.'</span></div>
					</div>';
            })
            ->editColumn('Match', function ($row) {

                return '
				<div class="d-flex flex-column w-100 me-2" style="width: 150px !important; margin-left: 30px !important;">
							<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 111.81 122.88" style="enable-background:new 0 0 111.81 122.88; height:30px; width:30px;" xml:space="preserve"><style type="text/css"><![CDATA[
				.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#51B53C;}
				.st1{fill:#FFFFFF;}
				.st2{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
			]]></style><g>
			<path class="st0" d="M55.71,0c20.85,13.21,39.68,19.47,55.85,17.99c2.82,57.1-18.26,90.82-55.63,104.89 C19.84,109.71-1.5,77.44,0.08,17.12C19.06,18.12,37.67,14.01,55.71,0L55.71,0L55.71,0z"/>
			<path class="st2" d="M55.73,7.05c18.45,11.7,35.13,17.23,49.43,15.92c2.5,50.54-16.17,80.39-49.24,92.85 C23.98,104.16,5.09,75.6,6.49,22.21C23.29,23.09,39.77,19.46,55.73,7.05L55.73,7.05L55.73,7.05z"/>
			<path class="st0" d="M56.24,19.54c14.22,9.01,27.06,13.27,38.08,12.27c1.92,38.94-12.45,61.93-37.94,71.53 c-0.16-0.06-0.32-0.12-0.48-0.18c-0.16,0.06-0.32,0.12-0.48,0.18c-25.48-9.6-39.86-32.59-37.94-71.53 c11.02,1.01,23.87-3.26,38.08-12.27l0,0l0.33,0.25L56.24,19.54L56.24,19.54L56.24,19.54z"/>
			<path class="st1" d="M35.44,58.28l7.47-0.1l0.56,0.14c1.51,0.87,2.93,1.86,4.26,2.99c0.96,0.81,1.87,1.69,2.74,2.65 c2.68-4.31,5.54-8.28,8.56-11.92c3.31-3.99,5.38-6.18,9.06-9.49l0.73-0.28h8.16l-1.65,1.82c-5.05,5.61-8.21,9.99-12.35,15.97 c-4.15,6-7.85,12.18-11.15,18.54l-1.03,1.98l-0.94-2.02c-1.74-3.73-3.82-7.15-6.3-10.21c-2.48-3.06-5.37-5.78-8.74-8.09 L35.44,58.28L35.44,58.28L35.44,58.28L35.44,58.28z"/></g></svg>
           </div>';

            })
            ->editColumn('SimilarReq', function ($row) {
                return '<div class="d-flex flex-column" > <span class="text-dark fw-bolder fs-6">'.$row->SimilarReq.' </span> </div>';
            })
            ->editColumn('Profiles', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Profiles.'</span> </div>';
            })
            ->editColumn('HiringType', function ($row) {

                $empType = '';
                if (($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) && ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0)) {
                    $empType = 'Full-time/Contract';
                } elseif ($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) {
                    $empType = 'Full-time';
                } elseif ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0) {
                    $empType = 'Contract';
                }

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$empType.'</span> </div>';
            })
            ->editColumn('Location', function ($row) {
                if (! empty($row->city)) {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$row->address).'" >'.ucwords(substr(@$row->address, 0, 10)).'</span>
						<div class="d-flex align-items-center">
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->city, 0, 10)).'</span>
								&nbsp;|&nbsp;
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->country_name, 0, 10)).'</span>
							<div>
						  </div>
						</div>
					  </div>';
                } else {
                    return ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$row->address).'" >'.ucwords(substr(@$row->address, 0, 10)).'</span>
						<div class="d-flex align-items-center">
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->city_name, 0, 10)).'</span>
								&nbsp;|&nbsp;
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->country_name, 0, 10)).'</span>
							<div>
						  </div>
						</div>
					  </div>';
                }

            })
            ->editColumn('Status', function ($row) use ($company_id) {

                $regStatus = 'UnRegistered';
                $regStatusCss = 'text';
                if (! empty($row->from_company_id)) {
                    if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                        $regStatus = 'Registered';
                        $regStatusCss = 'text-success';
                    }
                }

                return '<div class="d-flex flex-column"> <span class=" '.@$regStatusCss.' fw-bolder fs-6">'.@$regStatus.'</div>';
            })
            ->editColumn('Action', function ($row) use ($jobId) {

                $source_company = auth()->user()->company_id;
                $staffing_company = $row->id;
                $current_job = $jobId;

                $recommendation_relation_cnt = 0;
                $recommendation_relation_cnt = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', '=', $source_company)
                    ->where('recommendation_relation.to_company_id', '=', $staffing_company)
                    ->where('recommendation_relation.final_status', '=', 0)
                    ->count();

                if ($recommendation_relation_cnt > 0) {

                    $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';

                } else {

                    if ($row->to_company_id == $staffing_company) {     // ###############    			  REGISTER NOT ASSIGN
                        $html = '<div class="d-flex flex-column"> 
					<a id="sp_list_'.$staffing_company.'" href="javascript:void(0);" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$source_company.'" data-row-tocompany="'.$staffing_company.'" class="btn btn-sm btn-light-primary w-130  w-130 send_only_job_invitation">
						Request Hire
					</a> 
				</div>';
                    } else { // ##########################################    							UNREGISTER
                        $html = '<div class="flex-column">
					<a id="sp_list_'.$staffing_company.'" href="javascript:void(0);" data-row-id="'.$staffing_company.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
						Request Hire
					</a> 
				</div>';
                    }

                }

                return $html;

            })
            ->rawColumns(['chkbox', 'Name', 'Match', 'SimilarReq', 'Profiles', 'HiringType', 'Status', 'Action', 'Location'])

            ->addIndexColumn()
            ->make(true);
    }

    // new recommented

    public function recommendedData(Request $request)
    {

        $searchTerms = request('cus_search');
        // dd($searchTerms);
        $custom_search = json_decode($searchTerms, true);
        // dd($custom_search);

        $searchTermsLocation = request('kt_tagify_location');
        // dd($searchTerms);
        // $custom_search_location = json_decode($searchTermsLocation, true);

        // abort_if(Gate::denies('view_jobs'), 403);	employment_type    0:Fulltime    1:Contract

        $employment_type = '';
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $jobId = $request->jobid;
        // JOB DETAIL
        $currentRowJob = Job::select(
            'title',
            'min_exp',
            'max_exp',
            'min_salary',
            'max_salary',
            'min_salary_currency',
            'max_salary_currency',
            'min_sal_mode',
            'max_sal_mode',
            'work_type',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();

        if (\request('app_int_outsource') != '') {
            $parent_employment_type = request('app_int_outsource');
        } else {
            $parent_employment_type = $currentRowJob->employment_type;
        }

        $commtype = request('app_int_outsource');

        // JOB SKILLS

        $currentJobSkill = DB::table('job_metronics_skills')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->pluck('job_metronics_skills.skill_name')
            ->toArray();

        if (is_array($custom_search) && ! empty($searchTerms)) {
            $currentJobSkill = array_column($custom_search, 'value');
            //  dd($currentJobSkill);
        } else {
            $jobTitle = $currentRowJob->title;
            array_push($currentJobSkill, $jobTitle);
        }

        $skillCaseStatements = '';
        foreach ($currentJobSkill as $skill) {
            $skillCaseStatements .= "CASE WHEN job_metronics_skills.skill_name LIKE '%{$skill}%' THEN 20 ELSE 0 END + ";
            $skillCaseStatements .= "CASE WHEN jobs.title LIKE '%{$skill}%' THEN 20 ELSE 0 END + ";
        }
        // Remove the trailing ' + '
        $skillCaseStatements = rtrim($skillCaseStatements, ' + ');
        $totalSkills = count($currentJobSkill);

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->where('client_location.country_id', $request->country)
            ->get();

        $currentJobLocationArray = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->where('client_location.country_id', $request->country)
            ->pluck('client_location.country_id')->toArray();

        if (! empty($searchTermsLocation)) {
            $country_id_job = 101;
            // $currentJobLocation = array_column($custom_search_location, 'value');
            $job_country_id = DB::table('client_city_jobs')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                ->select('countries.id')
                ->where('countries.country_name', '=', $searchTermsLocation)
                ->where('client_city_jobs.job_id', '=', $request->jobid)
                ->first();
            if ($job_country_id) {
                $country_id_job = $job_country_id->id;
            } else {
                $job_country_id = DB::table('countries')
                    ->select('countries.id')
                    ->where('countries.country_name', '=', $searchTermsLocation)
                    ->first();
                $country_id_job = $job_country_id->id;
            }
            //    dd($country_id_job);
        } else {
            $country_id_job = 101;
            $job_country_id = DB::table('client_city_jobs')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                ->select('countries.id')
                ->where('countries.id', '=', $country_id_job)
                ->first();
            if ($job_country_id) {
                $country_id_job = $job_country_id->id;
            } else {
                if (! empty($searchTermsLocation)) {
                    $job_country_id = DB::table('countries')
                        ->select('countries.id')
                        ->where('countries.country_name', '=', $searchTermsLocation)
                        ->first();
                    $country_id_job = $job_country_id->id;
                }
            }
        }

        //    dd($country_id_job);

        $clientMainLocation = GeneralMethod::companyMainAddress($company_id);

        // dd($currentJobLocationArray,$request->country);

        // dd($currentRowJob->title,$currentJobSkill,$currentJobLocation,$currentJobLocationArray);

        $jobMinExp = $currentRowJob->min_exp;
        $jobMaxExp = $currentRowJob->max_exp;
        $jobMinSalary = $currentRowJob->min_salary;
        $jobMaxSalary = $currentRowJob->max_salary;
        $jobSalaryCurrency = $currentRowJob->max_salary_currency;
        $jobSalaryMode = $currentRowJob->max_sal_mode;

        if (\request('min_exp') != '' && \request('max_exp')) {
            $jobMinExp = request('min_exp');
            $jobMaxExp = request('max_exp');
        }
        if (\request('min_sal') != '' && \request('max_sal')) {
            $jobMinSalary = request('min_sal');
            $jobMaxSalary = request('max_sal');
        }
        if (\request('min_salary_currency') != '' && \request('sal_mode') != '') {
            $jobSalaryCurrency = request('min_salary_currency');
            // dd($jobSalaryCurrency);
            $jobSalaryMode = request('sal_mode');
        }
        // dd($currentJobSkill);

        $boostkeywordcheck = '';
        foreach ($currentJobSkill as $skill) {
            $boostkeywordcheck .= "CASE WHEN package_active.boostkeywords_text LIKE '%{$skill}%' THEN 1 ELSE 0 END + ";
        }
        // Remove the trailing ' + '
        $boostkeywordcheck = rtrim($boostkeywordcheck, ' + ');

        $skillCaseStatements = implode(' + ', array_map(function ($skill) {
            return "CASE WHEN jobs.title LIKE '%".$skill."%' OR job_metronics_skills.skill_name LIKE '%".$skill."%' THEN 10 ELSE 0 END";
        }, $currentJobSkill));
        $skillCaseStatements = rtrim($skillCaseStatements, ' + ');
        // dd($skillCaseStatements);
        $skillsCount = count($currentJobSkill);
        $topRecordsCount = 20;
        $boostKeywordTopCount = 3;

        // Create other dynamic parts of the query
        $boostkeywordcheck = implode(' + ', array_map(function ($skill) {
            return "CASE WHEN package_active.boostkeywords_text LIKE '%".$skill."%' THEN 1 ELSE 0 END";
        }, $currentJobSkill));

        // dd($skillsList);

        $company_id = $this->user->company_id;

        $skillsList = array_filter($currentJobSkill, fn ($s) => $s !== 'skill');
        $skillsList = array_map('strtolower', $skillsList);
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 20);
        $from = ($page - 1) * $size;

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'jobid' => $request->jobid,
            'cus_search' => $skillsList,
            'kt_tagify_location' => $country_id_job ?? null,
            'min_sal' => $jobMinSalary,
            'max_sal' => $jobMaxSalary,
            'min_salary_currency' => $jobSalaryCurrency,
            'sal_mode' => $jobSalaryMode,
            'user_id' => $this->user->id,
            'app_int_outsource' => $parent_employment_type,
        ];

        $versionKey = 'allrecommendedagency_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allrecommendedagency:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        $etag = '"v3:recom:'.md5($cacheKeySet).'"';
        if (request()->header('If-None-Match') === $etag) {
            return response(null, 304)->header('ETag', $etag);
        }

        if (cache()->has($cacheKeySet)) {
            return response()->json(cache()->get($cacheKeySet))
                ->header('ETag', $etag)
                ->header('Cache-Control', 'private, max-age=0, must-revalidate');
        }

        //  dd("dkjdjdjd");

        $searchSkills = array_filter($currentJobSkill, fn ($s) => $s !== 'skill');
        $searchSkills = array_map('strtolower', $skillsList);

        /*
        |--------------------------------------------------------------------------
        | CLEAN SKILLS
        |--------------------------------------------------------------------------
        */

        $skills = array_map(function ($skill) {
            return strtolower(trim($skill));
        }, $searchSkills);
        $skills = array_values(array_unique(array_filter($skills)));

        /*
        |--------------------------------------------------------------------------
        | STEP 1
        |--------------------------------------------------------------------------
        | Find other companies having same skills
        |--------------------------------------------------------------------------
        */

        $companySkillMatchCount = [];
        $companyMatchedSkills = [];

        if (! empty($skills)) {

            /*
            |--------------------------------------------------------------------------
            | SKILL MATCH QUERY
            |--------------------------------------------------------------------------
            */

            $skillQueries = [];

            foreach ($skills as $skill) {

                $skill = strtolower(trim($skill));

                if (! empty($skill)) {

                    $skillQueries[] = [
                        'match_phrase_prefix' => [
                            'skill_name' => $skill,
                        ],
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ELASTIC SEARCH
            |--------------------------------------------------------------------------
            */

            $skillSearchParams = [

                'index' => ELASTIC_COMPANY_SKILLS,

                'body' => [

                    'size' => 10000,

                    '_source' => [
                        'company_id',
                        'skill_name',
                    ],

                    'query' => [

                        'bool' => [

                            'must' => [

                                [
                                    'bool' => [

                                        'should' => $skillQueries,

                                        'minimum_should_match' => 1,
                                    ],
                                ],
                            ],

                            'must_not' => [

                                [
                                    'term' => [
                                        'company_id' => $company_id,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $skillResponse = $this->elasticService->getClient()->search($skillSearchParams);

            $hits = $skillResponse['hits']['hits'] ?? [];

            // dd($hits);
            $SkillArrCheck = [];

            foreach ($hits as $hit) {

                $source = $hit['_source'];

                $matchedCompanyId = $source['company_id'];

                $skillName = strtolower(trim($source['skill_name']));
                if (! in_array($skillName, $SkillArrCheck)) {
                    $SkillArrCheck[] = $skillName;

                    /*
                    |--------------------------------------------------------------------------
                    | MATCH COUNT
                    |--------------------------------------------------------------------------
                    */

                    if (! isset($companySkillMatchCount[$matchedCompanyId])) {

                        $companySkillMatchCount[$matchedCompanyId] = 0;
                    }

                    $companySkillMatchCount[$matchedCompanyId]++;

                    /*
                    |--------------------------------------------------------------------------
                    | STORE MATCHED SKILLS
                    |--------------------------------------------------------------------------
                    */

                    $companyMatchedSkills[$matchedCompanyId][] = $skillName;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | REMOVE DUPLICATE SKILLS
            |--------------------------------------------------------------------------
            */

            foreach ($companyMatchedSkills as $companyId => $skillList) {

                $companyMatchedSkills[$companyId] = array_values(array_unique($skillList));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2
        |--------------------------------------------------------------------------
        | Dynamic score boost
        |--------------------------------------------------------------------------
        */

        // dd($companySkillMatchCount);

        $functions = [];

        foreach ($companySkillMatchCount as $matchedCompanyId => $matchedSkillCount) {
            //   dd($matchedCompanyId);
            $functions[] = [

                'filter' => [

                    'term' => [
                        'id' => $matchedCompanyId,
                    ],
                ],

                'weight' => ($matchedSkillCount * 100),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3
        |--------------------------------------------------------------------------
        | Fetch companies
        |--------------------------------------------------------------------------
        */

        $experience = request('experience');
        $industry = request('industry');

        $experienceFilter = [];
        $industryFilter = [];

        $currentYear = date('Y');

        if (! empty($experience)) {

            /*
            |--------------------------------------------------------------------------
            | Calculate experience from founded year
            |--------------------------------------------------------------------------
            */

            if ($experience == '0–2 years') {

                $minFoundedYear = $currentYear - 2;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'gte' => $minFoundedYear,
                        ],
                    ],
                ];
            } elseif ($experience == '3–5 years') {

                $maxFoundedYear = $currentYear - 3;
                $minFoundedYear = $currentYear - 5;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'gte' => $minFoundedYear,
                            'lte' => $maxFoundedYear,
                        ],
                    ],
                ];
            } elseif ($experience == '5–10 years') {

                $maxFoundedYear = $currentYear - 5;
                $minFoundedYear = $currentYear - 10;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'gte' => $minFoundedYear,
                            'lte' => $maxFoundedYear,
                        ],
                    ],
                ];
            } elseif ($experience == '10+ years') {

                $maxFoundedYear = $currentYear - 10;

                $experienceFilter[] = [
                    'range' => [
                        'founded' => [
                            'lte' => $maxFoundedYear,
                        ],
                    ],
                ];
            }
        }
        if (! empty($industry)) {

            $industryFilter[] = [

                'terms' => [
                    'industry.ind_id' => (array) $industry,
                ],
            ];
        }

        $commtype = request('app_int_outsource');
        $commercialFilter = [];

        if (! empty($commtype)) {
            $commercialFilter[] = [

                'nested' => [

                    'path' => 'commercial_nested',

                    'query' => [

                        'bool' => [

                            'must' => [

                                [
                                    'term' => [
                                        'commercial_nested.commtype' => (int) $commtype,
                                    ],
                                ],

                                [
                                    'term' => [
                                        'commercial_nested.status' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        $globalSearchFilter = [];
        //  dd($country_id_job);
        $companySearchParams = [

            'index' => ELASTIC_COMPANIES_INDEX,

            'body' => [

                'from' => $from,
                'size' => $size,
                'track_total_hits' => true,
                'track_scores' => true,
                'query' => [

                    'function_score' => [

                        'query' => [

                            'bool' => [
                                'must' => array_merge(

                                    [

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ACTIVE COMPANY
                                        |--------------------------------------------------------------------------
                                        */

                                        [
                                            'term' => [
                                                'status' => 'active',
                                            ],
                                        ],
                                        [
                                            'terms' => [
                                                'company_type' => [1, 2],
                                            ],
                                        ],

                                        [
                                            'term' => [
                                                'status' => 'active',
                                            ],
                                        ],

                                        [
                                            'term' => [
                                                'commericial_countries.country_id' => $country_id_job,
                                            ],
                                        ],

                                        /*
                                        |--------------------------------------------------------------------------
                                        | COMPANY COINS
                                        |--------------------------------------------------------------------------
                                        */

                                        [
                                            'nested' => [

                                                'path' => 'company_coins',

                                                'query' => [

                                                    'bool' => [

                                                        'must' => [

                                                            [
                                                                'term' => [
                                                                    'company_coins.event_type' => 'sales',
                                                                ],
                                                            ],

                                                            [
                                                                'range' => [
                                                                    'company_coins.coins' => [
                                                                        'gt' => 0,
                                                                    ],
                                                                ],
                                                            ],

                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],

                                    ],

                                    $commercialFilter,
                                    $experienceFilter,
                                    $industryFilter,
                                    $globalSearchFilter
                                ),

                                // EXCLUDE CURRENT COMPANY
                                'must_not' => [

                                    [
                                        'term' => [
                                            'id' => $company_id,
                                        ],
                                    ],
                                ],
                            ],
                        ],

                        'functions' => $functions,

                        'score_mode' => 'sum',

                        'boost_mode' => 'sum',
                    ],
                ],
                'sort' => [
                    ['_score' => ['order' => 'desc']],
                    ['leadCount' => ['order' => 'asc']], // stable sorting
                    ['id' => ['order' => 'desc']],
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | EXECUTE SEARCH
        |--------------------------------------------------------------------------
        */

        $companyResponse = $this->elasticService->getClient()->search($companySearchParams);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE DATA
        |--------------------------------------------------------------------------
        */

        $total = $companyResponse['hits']['total']['value'] ?? 0;

        $data = [];

        foreach ($companyResponse['hits']['hits'] as $hit) {

            $source = $hit['_source'];

            $matchedCompanyId = $source['id'];

            $matchedSkills = $companyMatchedSkills[$matchedCompanyId] ?? [];
            $commonSkills = array_values(array_intersect(

                array_map('strtolower', $searchSkills),
                array_map('strtolower', $matchedSkills)

            ));

            $source['matched_skills'] = $commonSkills;
            // $source['matched_skills'] = $companyMatchedSkills[$matchedCompanyId] ?? [];
            $source['search_skills'] = $searchSkills; // $companySkillMatchCount[$matchedCompanyId] ?? 0;

            $source['match_score'] = $hit['_score'];
            $source['matched_skill_count'] = $hit['_score'];

            $data[] = $source;
        }

        if (! empty($data)) {
            $agencyIds = collect($data)->pluck('id')->filter()->unique()->values()->toArray();

            if (! empty($agencyIds)) {
                // Batch 1: Fetch self-company client info for all recommended agencies
                $clientsMap = GeneralMethod::batchGetClientIdCompany($agencyIds);

                // Batch 2: Fetch relation data between employer company and recommended agencies
                $relationsMap = GeneralMethod::batchGetAgencyRelationData($company_id, $agencyIds);

                // Batch 3: User size counts via single SQL query
                // Intent: Single query counting users per agency company
                // Tenancy: Filtered by company_id in $agencyIds
                $userSizeCounts = DB::table('users')
                    ->select('company_id', DB::raw('count(*) as total'))
                    ->whereIn('company_id', $agencyIds)
                    ->groupBy('company_id')
                    ->pluck('total', 'company_id')
                    ->toArray();

                // Batch 4 & 5: Active jobs and candidates counts per agency
                $agencyJobsMap = GeneralMethod::batchAgencyJobsCount($agencyIds);
                $agencyCandidatesMap = GeneralMethod::batchAgencyJobsCandidatesCount($agencyIds);

                // Pre-bind batch properties onto $data array objects to eliminate Blade N+1
                foreach ($data as &$source) {
                    $cId = $source['id'] ?? null;
                    $source['_client_info'] = $clientsMap[$cId] ?? null;
                    $source['_relation_info'] = $relationsMap[$cId] ?? null;
                    $source['_user_size_count'] = $userSizeCounts[$cId] ?? 0;
                    $source['_agency_jobs_count'] = $agencyJobsMap[$cId] ?? (object) ['all_jobs' => 0];
                    $source['_agency_candidates_count'] = $agencyCandidatesMap[$cId] ?? (object) ['all_candidates' => 0, 'all_candidate_bench' => 0];
                }
                unset($source);
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $page_view = 'find_agency_recom_ajax';

        $userPermissions = $this->userPermissions;
        $app_int_outsource = request('app_int_outsource');
        $parent_employment_type = request('app_int_outsource');
        $hiringtype = $app_int_outsource;
        $jobid = $request->jobid;

        $responseData = [
            'html' => view('service_company.ajax_pages.'.$page_view.'', compact('data', 'userPermissions', 'app_int_outsource', 'jobid', 'currentJobSkill', 'hiringtype', 'parent_employment_type', 'company_id'))->render(),
            'total' => count($data),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, 3600);

        return response()->json($responseData)
            ->header('ETag', $etag)
            ->header('Cache-Control', 'private, max-age=0, must-revalidate');

        $commtypeArr = [1, 2];
        if (! empty($commtype)) {

            if ((int) $commtype == 0) {
                $commtypeArr = [2];
            } elseif ((int) $commtype == 1) {
                $commtypeArr = [1];
            } else {
                $commtypeArr = [1, 2];
            }
        }

        // dd("slsl"); // Send Invitation to {{__('messages.Agency')}}

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size, // usually 20
                'sort' => [
                    ['_score' => ['order' => 'desc']],
                    ['leadCount' => ['order' => 'asc']],
                    ['id' => ['order' => 'desc']],
                ],
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    [
                                        'nested' => [
                                            'path' => 'company_coins',
                                            'query' => [
                                                'bool' => [
                                                    'must' => [
                                                        ['term' => ['company_coins.event_type' => 'sales']],
                                                        ['range' => ['company_coins.coins' => ['gt' => 0]]],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'nested' => [
                                            'path' => 'commercial_nested',
                                            'query' => [
                                                'bool' => [
                                                    'must' => [

                                                        [
                                                            'term' => [
                                                                'commercial_nested.status' => 1,
                                                            ],
                                                        ],

                                                        [
                                                            'terms' => [
                                                                'commercial_nested.commtype' => $commtypeArr,
                                                            ],
                                                        ],

                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    ['terms' => ['company_type' => [1, 2]]],
                                    ['term' => ['status' => 'active']],
                                    ['term' => ['commericial_countries.country_id' => $country_id_job]],
                                ],

                                'must_not' => [
                                    ['term' => ['id' => $company_id]],
                                ],
                                'should' => [
                                    [
                                        'nested' => [
                                            'path' => 'job_metronics_skills',
                                            'score_mode' => 'sum',
                                            'query' => [
                                                'function_score' => [
                                                    'query' => [
                                                        'terms' => [
                                                            'job_metronics_skills.skill_name.keyword' => $skillsList,
                                                        ],
                                                    ],
                                                    'boost_mode' => 'sum',
                                                    'score_mode' => 'sum',
                                                    'functions' => [
                                                        ['weight' => 10],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                // 🚫 Don't set minimum_should_match
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                    ],
                ],
                'aggs' => [
                    'job_skill_match_count' => [
                        'nested' => ['path' => 'job_metronics_skills'],
                        'aggs' => [
                            'matched_jobs' => [
                                'filter' => [
                                    'terms' => [
                                        'job_metronics_skills.skill_name.keyword' => $skillsList,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'candidate_skill_match_count' => [
                        'nested' => ['path' => 'candidates'],
                        'aggs' => [
                            'matched_candidates' => [
                                'filter' => [
                                    'terms' => [
                                        'candidates.skills.keyword' => $skillsList,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'bench_candidates' => [
                        'nested' => ['path' => 'candidates'],
                        'aggs' => [
                            'bench' => [
                                'filter' => [
                                    'term' => ['candidates.type.keyword' => 'bench_condidate'],
                                ],
                            ],
                        ],
                    ],
                    'other_candidates' => [
                        'nested' => ['path' => 'candidates'],
                        'aggs' => [
                            'other' => [
                                'filter' => [
                                    'term' => ['candidates.type.keyword' => 'other'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $total = 0;
            $data = [];
        } else {
            // Execute search

            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;

            if ($currentRowJob->work_type == 'yes' && $total == 0) {

                $params = [
                    'index' => ELASTIC_COMPANIES_INDEX,
                    'body' => [
                        'from' => $from,
                        'size' => $size, // usually 20
                        'sort' => [
                            ['_score' => ['order' => 'desc']],
                            ['leadCount' => ['order' => 'asc']],
                            ['id' => ['order' => 'desc']],
                        ],
                        'query' => [
                            'function_score' => [
                                'query' => [
                                    'bool' => [
                                        'filter' => [
                                            [
                                                'nested' => [
                                                    'path' => 'company_coins',
                                                    'query' => [
                                                        'bool' => [
                                                            'must' => [
                                                                ['term' => ['company_coins.event_type' => 'sales']],
                                                                ['range' => ['company_coins.coins' => ['gt' => 0]]],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            [
                                                'nested' => [
                                                    'path' => 'commercial_nested',
                                                    'query' => [
                                                        'bool' => [
                                                            'must' => [

                                                                [
                                                                    'term' => [
                                                                        'commercial_nested.status' => 1,
                                                                    ],
                                                                ],

                                                                [
                                                                    'terms' => [
                                                                        'commercial_nested.commtype' => $commtypeArr,
                                                                    ],
                                                                ],

                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            ['terms' => ['company_type' => [1, 2]]],
                                            ['term' => ['status' => 'active']],
                                        ],
                                        'must_not' => [
                                            ['term' => ['id' => $company_id]],
                                        ],
                                        'should' => [
                                            [
                                                'nested' => [
                                                    'path' => 'job_metronics_skills',
                                                    'score_mode' => 'sum',
                                                    'query' => [
                                                        'function_score' => [
                                                            'query' => [
                                                                'terms' => [
                                                                    'job_metronics_skills.skill_name.keyword' => $skillsList,
                                                                ],
                                                            ],
                                                            'boost_mode' => 'sum',
                                                            'score_mode' => 'sum',
                                                            'functions' => [
                                                                ['weight' => 10],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        // 🚫 Don't set minimum_should_match
                                    ],
                                ],
                                'score_mode' => 'sum',
                                'boost_mode' => 'sum',
                            ],
                        ],
                        'aggs' => [
                            'job_skill_match_count' => [
                                'nested' => ['path' => 'job_metronics_skills'],
                                'aggs' => [
                                    'matched_jobs' => [
                                        'filter' => [
                                            'terms' => [
                                                'job_metronics_skills.skill_name.keyword' => $skillsList,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'candidate_skill_match_count' => [
                                'nested' => ['path' => 'candidates'],
                                'aggs' => [
                                    'matched_candidates' => [
                                        'filter' => [
                                            'terms' => [
                                                'candidates.skills.keyword' => $skillsList,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'bench_candidates' => [
                                'nested' => ['path' => 'candidates'],
                                'aggs' => [
                                    'bench' => [
                                        'filter' => [
                                            'term' => ['candidates.type.keyword' => 'bench_condidate'],
                                        ],
                                    ],
                                ],
                            ],
                            'other_candidates' => [
                                'nested' => ['path' => 'candidates'],
                                'aggs' => [
                                    'other' => [
                                        'filter' => [
                                            'term' => ['candidates.type.keyword' => 'other'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $response = $this->elasticService->getClient()->search($params);
                $total = $response['hits']['total']['value'] ?? 0;

            }

            // Extract and attach matched skills to result
            $data = array_map(function ($hit) use ($skillsList) {
                $source = $hit['_source'];

                $jobSkills = collect(data_get($source, 'job_metronics_skills', []))
                    ->pluck('skill_name')
                    ->filter()
                    ->unique()
                    ->toArray();

                $matchedSkills = array_values(array_intersect($jobSkills, $skillsList));
                $matchCount = count($matchedSkills);
                $totalSkills = count($skillsList);

                $matchPercent = $totalSkills > 0 ? round(($matchCount / $totalSkills) * 100, 1) : 0;

                // dd($matchCount);

                return array_merge($source, [
                    'matched_skill_count' => $matchCount,
                    'matched_skills' => $matchedSkills,
                    'skill_match_percentage' => $matchPercent, // Add numeric field
                ]);
            }, $response['hits']['hits']);

            // ✅ Sort the final result by percentage descending
            usort($data, function ($a, $b) {
                return $b['skill_match_percentage'] <=> $a['skill_match_percentage'];
            });

            //    dd($cacheKey,$data,$cacheTTL);
            // Cache::put($cacheKey, ['data' => $data,'total' => $total], $cacheTTL);
            // Cache::put($cacheKey, $data, $cacheTTL);
        }

        // }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        $page_view = 'find_agency_recom_ajax';

        $userPermissions = $this->userPermissions;
        $app_int_outsource = request('app_int_outsource');
        $parent_employment_type = request('app_int_outsource');
        $hiringtype = $app_int_outsource;
        $jobid = $request->jobid;

        $responseData = [
            'html' => view('service_company.ajax_pages.'.$page_view.'', compact('data', 'userPermissions', 'app_int_outsource', 'jobid', 'currentJobSkill', 'hiringtype', 'parent_employment_type', 'company_id'))->render(),
            'total' => count($data),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(ALL_RECOMMENDED_AGENCY_TIME));

        return response()->json($responseData);

        // Fetch vendors eligible for ranking
        // $vendorIds = DB::table('companies')
        // 	->where('company_type', '!=', 3)
        // 	->where('package_id', '>', 0)
        // 	->where('id', '!=', $company_id)
        // 	->pluck('id');

        // dd($skillsList);
        // 1. Cached vendor IDs
        $vendorIds = Cache::remember("vendor_ids_for_company_{$company_id}", 300, function () use ($company_id) {
            return DB::table('companies')
                ->where('company_type', '!=', 3)
                ->where('package_id', '>', 0)
                ->where('id', '!=', $company_id)
                ->pluck('id');
        });

        // 2. Precompute: SimilarReq jobs count
        $similarReqSub = DB::table('jobs as j')
            ->select(DB::raw('COUNT(DISTINCT j.id) as SimilarReq'), DB::raw('COALESCE(j.company_id, rr.to_company_id) as vendor_id'))
            ->leftJoin('job_metronics_skills as jms', 'jms.job_id', '=', 'j.id')
            ->leftJoin('recommendation_relation as rr', 'rr.job_id', '=', 'j.id')
            ->where('j.status', 'active');
        if (count($skillsList) > 0) {
            $similarReqSub = $similarReqSub->whereIn('jms.skill_name', $skillsList);
        }
        $similarReqSub = $similarReqSub->when($parent_employment_type === '1' || $parent_employment_type === '0', function ($q) use ($parent_employment_type) {
            $q->where('j.employment_type', $parent_employment_type);
        })
            ->groupBy('vendor_id');

        // 3. Precompute: Bench profiles count
        $benchProfilesSub = DB::table('job_applications')
            ->select(DB::raw('COUNT(DISTINCT job_applications.id) as benchProfilesCount'), 'job_applications.company_id as vendor_id')
            ->join('job_application_skills', 'job_application_skills.job_application_id', '=', 'job_applications.id')
            ->where('job_applications.status', 2)
            ->where('job_applications.condidate_type', 'bench_condidate');
        if (count($skillsList) > 0) {
            $benchProfilesSub = $benchProfilesSub->whereIn('job_application_skills.skills', $skillsList);
        }
        $benchProfilesSub = $benchProfilesSub->groupBy('job_applications.company_id');

        // 4. Precompute: Other profiles count
        $otherProfilesSub = DB::table('job_applications')
            ->select(DB::raw('COUNT(DISTINCT job_applications.id) as otherProfilesCount'), DB::raw('COALESCE(add_job_wise_condidates.company_id, add_job_wise_condidates.thirdparty_company_id) as vendor_id'))
            ->join('job_application_skills', 'job_application_skills.job_application_id', '=', 'job_applications.id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
            ->where('job_applications.condidate_type', 'other');
        if (count($skillsList) > 0) {
            $otherProfilesSub = $otherProfilesSub->whereIn('job_application_skills.skills', $skillsList);
        }
        $otherProfilesSub = $otherProfilesSub->where(function ($q) {
            $q->whereNotNull('add_job_wise_condidates.company_id')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('add_job_wise_condidates.thirdparty_company_id')
                        ->where('add_job_wise_condidates.master_status_id', '!=', 8);
                });
        })
            ->groupBy('vendor_id');

        // Fetch vendor details along with skill match in a single query
        $finalVendorsBooster = DB::table('companies')
            ->leftJoin('company_coins', function ($join) {
                $join->on('company_coins.company_id', '=', 'companies.id')
                    ->where('company_coins.event_type', '=', 'sales')
                    ->where('company_coins.coins', '>', 0);
            })
            ->leftJoin('commericial_countries', function ($join) use ($country_id_job) {
                $join->on('commericial_countries.company_id', '=', 'companies.id')
                    ->where('commericial_countries.country_id', '=', $country_id_job);
            })
            ->leftJoin('boostkeywords', function ($join) use ($currentJobSkill, $country_id_job, $parent_employment_type) {
                $join->on('boostkeywords.company_id', '=', 'companies.id')
                    ->whereIn('boostkeywords.skill_name', $currentJobSkill)
                    ->where('boostkeywords.country_id', $country_id_job)
                    ->where('boostkeywords.campaign_type', $parent_employment_type)
                    ->where('boostkeywords.status', 1);
            })
            ->leftJoin('staffing_company_relation', function ($join) {
                $join->on('staffing_company_relation.from_company_id', '=', 'companies.id')
                    ->orOn('staffing_company_relation.to_company_id', '=', 'companies.id');
            })
            ->leftJoin('recommendation_relation', function ($join) {
                $join->on('recommendation_relation.from_company_id', '=', 'companies.id');
            })
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->leftJoinSub($similarReqSub, 'similar_reqs', fn ($join) => $join->on('similar_reqs.vendor_id', '=', 'companies.id'))
            ->leftJoinSub($benchProfilesSub, 'bench_profiles', fn ($join) => $join->on('bench_profiles.vendor_id', '=', 'companies.id'))
            ->leftJoinSub($otherProfilesSub, 'other_profiles', fn ($join) => $join->on('other_profiles.vendor_id', '=', 'companies.id'))
            ->when($parent_employment_type == 0, function ($q) {
                $q->join('commericials', function ($join) {
                    $join->on('commericials.company_id', '=', 'companies.id')
                        ->where('commericials.rateType', '=', 2)
                        ->where('commericials.status', '=', 1);
                });
            })
            ->when($parent_employment_type == 1, function ($q) {
                $q->join('commericials', function ($join) {
                    $join->on('commericials.company_id', '=', 'companies.id')
                        ->where('commericials.rateType', '=', 1)
                        ->where('commericials.status', '=', 1);
                });
            })->where('commericial_countries.country_id', $country_id_job)
            ->whereIn('companies.id', $vendorIds)
            ->whereIn('boostkeywords.skill_name', $currentJobSkill)
            ->whereIn('boostkeywords.skill_name', $currentJobSkill)
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.logo',
                'companies.last_login',
                'companies.leadCount',
                'td_industries.ind_name',
                'staffing_company_relation.from_company_id',
                'staffing_company_relation.to_company_id',
                'staffing_company_relation.final_status',
                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.from_status as rel_from_status',
                'recommendation_relation.to_status as rel_to_status',
                'recommendation_relation.job_id as rel_job_id',
                'recommendation_relation.from_company_id as rel_from_company_id',
                'recommendation_relation.final_status as recommendation_final_status',
                DB::raw('COUNT(DISTINCT boostkeywords.skill_name) as skill_match_count'),
                DB::raw("GROUP_CONCAT(DISTINCT boostkeywords.skill_name ORDER BY boostkeywords.skill_name SEPARATOR ', ') as matched_skills"),
                DB::raw("GROUP_CONCAT(DISTINCT boostkeywords.id ORDER BY boostkeywords.id SEPARATOR ', ') as boost_ids"),
                'similar_reqs.SimilarReq',
                'bench_profiles.benchProfilesCount',
                'other_profiles.otherProfilesCount'
            )->groupBy('companies.id');

        // ->groupBy('companies.id', 'companies.company_name', 'companies.logo', 'companies.last_login', 'companies.leadCount')

        $finalVendorsBooster = $finalVendorsBooster->orderByDesc(DB::raw('(COUNT(DISTINCT boostkeywords.skill_name) / '.count($currentJobSkill).') * 100'));
        if ($parent_employment_type == 1) {
            $finalVendorsBooster = $finalVendorsBooster->orderByDesc('benchProfilesCount');
        }

        $finalVendorsBooster = $finalVendorsBooster->orderBy('companies.leadCount')
            ->orderByDesc('companies.id')
            ->limit(3)
            ->get()
            ->map(function ($vendor) use ($currentJobSkill) {
                $vendor->skill_match_percentage = $vendor->skill_match_count ? round(($vendor->skill_match_count / count($currentJobSkill)) * 100, 2) : 0;
                $vendor->boosterCom = 1;

                return $vendor;
            });

        $recIDFilter = $finalVendorsBooster->pluck('id');
        $recIDFilterArray = $recIDFilter->toArray();

        //  dd($recIDFilterArray);

        //   $vendorIds =  $filteredVendorIds = array_values(array_diff($vendorIds->toArray(), $recIDFilterArray));

        //   dd($vendorIds);

        // Fetch vendor details along with skill match in a single query
        $finalVendors = DB::table('companies')
            ->leftJoin('company_coins', function ($join) {
                $join->on('company_coins.company_id', '=', 'companies.id')
                    ->where('company_coins.event_type', '=', 'sales')
                    ->where('company_coins.coins', '>', 0);
            })
            ->leftJoin('commericial_countries', function ($join) use ($country_id_job) {
                $join->on('commericial_countries.company_id', '=', 'companies.id')
                    ->where('commericial_countries.country_id', '=', $country_id_job);
            })
            ->leftJoin('job_metronics_skills', function ($join) use ($currentJobSkill) {
                $join->on('job_metronics_skills.company_id', '=', 'companies.id')
                    ->whereIn('job_metronics_skills.skill_name', $currentJobSkill);
            })
            ->leftJoin('staffing_company_relation', function ($join) {
                $join->on('staffing_company_relation.from_company_id', '=', 'companies.id')
                    ->orOn('staffing_company_relation.to_company_id', '=', 'companies.id');
            })
            ->leftJoin('recommendation_relation', function ($join) {
                $join->on('recommendation_relation.from_company_id', '=', 'companies.id');
            })
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->leftJoinSub($similarReqSub, 'similar_reqs', fn ($join) => $join->on('similar_reqs.vendor_id', '=', 'companies.id'))
            ->leftJoinSub($benchProfilesSub, 'bench_profiles', fn ($join) => $join->on('bench_profiles.vendor_id', '=', 'companies.id'))
            ->leftJoinSub($otherProfilesSub, 'other_profiles', fn ($join) => $join->on('other_profiles.vendor_id', '=', 'companies.id'))
            ->when($parent_employment_type == 0, function ($q) {
                $q->join('commericials', function ($join) {
                    $join->on('commericials.company_id', '=', 'companies.id')
                        ->where('commericials.rateType', '=', 2)
                        ->where('commericials.status', '=', 1);
                });
            })
            ->when($parent_employment_type == 1, function ($q) {
                $q->join('commericials', function ($join) {
                    $join->on('commericials.company_id', '=', 'companies.id')
                        ->where('commericials.rateType', '=', 1)
                        ->where('commericials.status', '=', 1);
                });
            })->where('commericial_countries.country_id', $country_id_job)
            ->whereIn('companies.id', $vendorIds)
            ->whereNotIn('companies.id', $recIDFilterArray)
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.logo',
                'companies.last_login',
                'companies.leadCount',
                'td_industries.ind_name',
                'staffing_company_relation.from_company_id',
                'staffing_company_relation.to_company_id',
                'staffing_company_relation.final_status',
                'recommendation_relation.id as recommendation_relation_id',
                'recommendation_relation.from_status as rel_from_status',
                'recommendation_relation.to_status as rel_to_status',
                'recommendation_relation.to_status as rel_final_status',
                'recommendation_relation.job_id as rel_job_id',
                'recommendation_relation.from_company_id as rel_from_company_id',
                'recommendation_relation.final_status as recommendation_final_status',
                DB::raw('COUNT(DISTINCT job_metronics_skills.skill_name) as skill_match_count'),
                DB::raw("GROUP_CONCAT(DISTINCT job_metronics_skills.skill_name ORDER BY job_metronics_skills.skill_name SEPARATOR ', ') as matched_skills"),
                DB::raw("GROUP_CONCAT(DISTINCT job_metronics_skills.id ORDER BY job_metronics_skills.id SEPARATOR ', ') as boost_ids"),
                'similar_reqs.SimilarReq',
                'bench_profiles.benchProfilesCount',
                'other_profiles.otherProfilesCount'
            )->groupBy('companies.id');

        // ->groupBy('companies.id', 'companies.company_name', 'companies.logo', 'companies.last_login', 'companies.leadCount')

        $finalVendors = $finalVendors->orderByDesc(DB::raw('(COUNT(DISTINCT job_metronics_skills.skill_name) / '.count($currentJobSkill).') * 100'));
        if ($parent_employment_type == 1) {
            $finalVendors = $finalVendors->orderByDesc('benchProfilesCount');
        }
        $finalVendors = $finalVendors->orderBy('companies.leadCount')
            ->orderByDesc('companies.id')
            ->limit(20)
            ->get()
            ->map(function ($vendor) use ($currentJobSkill) {
                $vendor->skill_match_percentage = $vendor->skill_match_count ? round(($vendor->skill_match_count / count($currentJobSkill)) * 100, 2) : 0;
                $vendor->boosterCom = 0;

                return $vendor;
            });

        //  dd($finalVendors);

        $mergedUsers = $finalVendorsBooster->merge($finalVendors);

        //   dd($mergedUsers);

        $hiringtype = request('app_int_outsource');

        return DataTables::of($mergedUsers)
            ->editColumn('Name', function ($row) use ($currentJobSkill) {
                //    dd($row->company_name);
                $logo_path = str_replace('index.php', '', url('img/client-default.png'));
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = @$row->logo;
                }

                $percentage = 0;
                $companies_id = '';
                $companies_id = $row->id;

                $title = $skills = $experience = $salary = $location = $jobtype = $jskillset = 0;

                // SKILL COUNTER
                $recommended_Skill = DB::table('companies')
                    ->select('companies.id')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                    ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        $recommended_Skill = $recommended_Skill->orWhere('jobs.title', 'like', "%{$jskill}%");
                        $recommended_Skill = $recommended_Skill->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill}%");
                    }
                }
                $recommended_Skill = $recommended_Skill->where('jobs.company_id', $companies_id);
                $recommended_Skill = $recommended_Skill->count();
                // dd($recommended_Skill);
                if ($recommended_Skill > 0) {
                    $skills = 20;
                }

                $recommended_Skill_Name = DB::table('companies')
                    ->select('job_metronics_skills.skill_name')
                    ->join('jobs', 'jobs.company_id', 'companies.id')
                    ->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id')
                    ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');

                if (! empty($currentJobSkill)) {

                    foreach ($currentJobSkill as $jskill) {
                        // dd($jskill);
                        // $recommended_Skill_Name 	=	$recommended_Skill_Name->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                        $recommended_Skill_Name = $recommended_Skill_Name->whereIn('job_metronics_skills.skill_name', $currentJobSkill);
                        $jskillset .= $jskill;

                    }
                }
                $recommended_Skill_Name = $recommended_Skill_Name->where('jobs.company_id', $companies_id);
                $recommended_Skill_Name = $recommended_Skill_Name->get()->map(function ($qqq) {
                    return $qqq->skill_name;
                });
                $skillname = '';
                $scnt = 0;
                $tempArr = [];

                if ($row->boosterCom == 1) {
                    $recommended_match = DB::table('companies')
                        ->leftJoin('boostkeywords', 'boostkeywords.company_id', '=', 'companies.id')
                        ->where('boostkeywords.company_id', $row->id)
                        ->pluck('boostkeywords.skill_name')->toArray();
                } else {
                    $recommended_match = DB::table('companies')
                        ->join('jobs', 'jobs.company_id', 'companies.id')
                        ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                        ->where('job_metronics_skills.company_id', $row->id)
                        ->pluck('job_metronics_skills.skill_name')->toArray();
                }

                // dd($currentJobSkill,$recommended_match);
                $recommended_Skill = array_unique($recommended_Skill_Name->toArray());

                $result = array_intersect($currentJobSkill, $recommended_match);

                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $sname) {
                        $tempArr[] = $sname;
                        $scnt++;

                    }
                }

                if (! empty($tempArr)) {
                    $newArr = array_keys(array_flip($tempArr));
                    // $$tempArr	=	array_unique($tempArr);
                    $skillname = implode(',', $newArr);
                }
                $skillname = explode(',', $row->matched_skills);

                $skilltag = '';
                if (count($recommended_match) > 0) {
                    foreach ($skillname as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1">'.ucfirst($sval).'</span>  ';
                    }
                }

                $percentage = number_format($row->skill_match_percentage, 0); // number_format($row->match_percentage, 0, '.', ''); //$salary + $title + $jobtype + $experience + $skills + $location;
                $skillMatchSection = '';
                if ($percentage > 0) {
                    $skillMatchSection = '<div class="" style="position: absolute;bottom: 5px;left: 11px;white-space: nowrap;"><span>'.$percentage.'% | '.$skilltag.'</span></div>';
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style=" float:left;margin-left: 10px !important;"> 
								<a href="javascript:void(0);">
									<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
								</a> 
							</div>
							<div class="d-flex flex-column" style=" float:left;"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 30).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 27).'</a>
							</div>
							'.$skillMatchSection.'';
            })
            ->editColumn('SimilarReq', function ($row) use ($hiringtype) {
                $companies_id = $row->id;

                $Profiles = 0;
                $titleffc = 'Profiles';
                $SimilarReq = 0;
                if ($hiringtype === '1') {
                    if ($row->benchProfilesCount > 0) {
                        $Profiles = $row->benchProfilesCount;
                        $SimilarReq = $row->SimilarReq;
                        $titleffc = 'Bench';
                    } else {
                        $Profiles = $row->otherProfilesCount;
                        $SimilarReq = $row->SimilarReq;
                        $titleffc = 'Profiles';
                    }
                } elseif ($hiringtype === '0') {

                    $Profiles = $row->otherProfilesCount;
                    $SimilarReq = $row->SimilarReq;
                    $titleffc = 'Profiles';

                } else {
                    $Profiles = ($row->benchProfilesCount) + ($row->otherProfilesCount);
                    $SimilarReq = $row->SimilarReq;
                }

                return ' <div class="d-flex flex-column"><span class="text-dark fw-bolder fs-6">'.$SimilarReq.' Similar Jobs </span>
		<span class="text-muted fw-bold text-muted d-block fs-7">'.$Profiles.' '.$titleffc.' Available </span> </div>';
            })
            ->editColumn('RatesCal', function ($row) use ($jobMinExp, $jobMaxExp, $parent_employment_type, $jobSalaryMode, $jobSalaryCurrency) {
                if ($parent_employment_type == 1) {
                    $commtype = 1;
                } else {
                    $commtype = 2; // full time
                }
                $rates = '';
                $currency = GeneralMethod::currency();
                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currencySymbole();
                $minimumRate = DB::table('commericials')
                    ->join('commericial_details', 'commericial_details.commericial_id', '=', 'commericials.id')
                    ->where('commericials.commtype', '=', $commtype)
                    ->where('commericials.company_id', '=', $row->id)
                    // Intent: choose a rate only from active backend bands overlapping this job's experience requirement.
                    ->whereExists(function ($experienceBands) use ($jobMinExp, $jobMaxExp) {
                        $experienceBands->selectRaw('1')
                            ->from('commercial_experience_bands as experience_bands')
                            ->where('experience_bands.is_active', 1)
                            ->whereColumn('commericial_details.exp', '>=', 'experience_bands.min_experience')
                            ->whereColumn('commericial_details.exp', '<=', 'experience_bands.max_experience')
                            ->where('experience_bands.max_experience', '>=', $jobMinExp)
                            ->where('experience_bands.min_experience', '<=', $jobMaxExp);
                    })
                    ->min('commericial_details.rate');

                $minimumRateType = DB::table('commericials')
                    ->select('commericials.*')
                    ->where('commericials.commtype', '=', $commtype)
                    ->where('commericials.company_id', '=', $row->id)
                    ->first();
                // sal_mode /salary_currency

                if ($minimumRateType) {
                    if ($minimumRateType->commtype == 1) {
                        $current_cur = $currencySymbole[@$jobSalaryCurrency];
                        $current_mode = $salaryMode[@$jobSalaryMode];
                        $currencymain = GeneralMethod::currency();  // Currency code
                        // dd($currency);

                        $currency = $currencymain[@$minimumRateType->salary_currency];
                        // dd($currency);
                        // $current_cur = $currencySymbole[@$minimumRateType->salary_currency];
                        // $current_mode = $salaryMode[@$minimumRateType->sal_mode];

                        $convertedAmoun = GeneralMethod::conversionRates($currency, $minimumRate, @$minimumRateType->sal_mode);
                        // dd($convertedAmoun[$jobSalaryMode]);
                        $exchangeRates = [
                            'INR' => 1,
                            'EUR' => 90.33,  // Example exchange rate for EUR to INR
                            'JPY' => 0.68,   // Example exchange rate for JPY to INR
                            'GBP' => 103.70, // Example exchange rate for GBP to INR
                            'USD' => 83.63,   // Example exchange rate for USD to INR
                            'AED' => 20.38,   // Example exchange rate for AED to INR
                        ];
                        // dd($jobSalaryCurrency);
                        $currencydd = $currencymain[@$jobSalaryCurrency];
                        $exchangeRateInrToEur = 1 / $exchangeRates[$currencydd];

                        // Convert INR to EUR
                        $amount = $convertedAmoun[$jobSalaryMode]; // * $exchangeRateInrToEur;

                        $currencymain = GeneralMethod::currency();  // Currency code
                        $currency = $currencymain[@$jobSalaryCurrency];
                        $econvertedAmount = $minimumRate;
                        if ($minimumRateType->salary_currency > 0) {
                            $econvertedAmount = GeneralMethod::convertToINR($minimumRate, $minimumRateType->salary_currency);
                        }

                        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $econvertedAmount, $minimumRateType->sal_mode);
                        $minimumRate = $convertedAmounEctc[@$jobSalaryMode];

                        $salary = GeneralMethod::thousandsCurrencyFormat($minimumRate);

                        // $rates = $current_cur.''.$salary.'/'.$current_mode;
                        return '<div class="d-flex flex-column">
						<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.$current_cur.$salary.'/'.$current_mode.'</a>
						<span class="text-muted fw-bold text-muted d-block fs-7">Contract</span> </div>';
                    } else {
                        $rates = $minimumRate.'%';

                        return '<div class="d-flex flex-column">'.$rates.'</div>
					<span class="text-muted fw-bold text-muted d-block fs-7">full time</span> ';
                    }
                }

                // dd($minimumRate,$row->id);

            })
            ->editColumn('Sizecount', function ($row) use ($company_id) {

                $Sizecount = DB::table('users')->where('company_id', $row->id)->count();
                $regStatus = 'UnRegistered';
                $regStatusCss = 'text';
                if (! empty($row->from_company_id)) {
                    if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                        $regStatus = 'Registered';

                        $regStatusCss = 'text-success';
                    }
                }

                return '<div class="d-flex flex-column"><span class="text-dark fw-bolder fs-6">'.$regStatus.'</span> <span class="text-muted fw-bold text-muted d-block fs-7">'.$Sizecount.' Member</span></div>';
            })
            ->editColumn('Location', function ($row) {
                $clients = DB::table('clients')->where('company_id', '=', $row->id)->where('self_company', '=', 1)->first();
                $locInfo = '';
                if ($clients) {
                    $locInfo = DB::table('client_location')
                        ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                        ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                        ->select(
                            'client_location.id',
                            'client_location.city',
                            'client_location.address',
                            'client_location.postcode',
                            'client_location.country_id',
                            'city_list.city as city_name',
                            'state_list.state as state_name',
                            'countries.country_name'
                        )
                        ->where('client_id', $clients->id)
                        ->first();
                }

                if (! empty($locInfo)) {
                    return ' <div class="d-flex flex-column">
				<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$locInfo->city_name).'" >'.ucwords(substr(@$locInfo->city_name, 0, 10)).'</span>
				<div class="d-flex align-items-center">
					  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$locInfo->state_name, 0, 10)).'</span>
						&nbsp;|&nbsp;
					  <span class="fw-bold text-muted d-block fs-7">'.strtoupper(substr(@$locInfo->country_name, 0, 2)).'</span>
					<div>
				  </div>
				</div>
			  </div>';
                }

            })
            ->editColumn('LoginTime', function ($row) {

                return ' <div class="d-flex flex-column">
				<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="" >'.date('d M y', strtotime(@$row->last_login)).'</span>
				<div class="d-flex align-items-center">
					  <span class="fw-bold text-muted d-block fs-7">'.date('h:i A', strtotime(@$row->last_login)).'</span>
					<div>
				  </div>
				</div>
			  </div>';

            })
            ->editColumn('Action', function ($row) use ($jobId, $currentJobSkill) {
                $from_company = $company_id = auth()->user()->company_id;
                $id = '';
                $id = $row->id;

                $html = '';
                $recommendateCounter = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', $from_company)
                    ->where('recommendation_relation.to_company_id', $id)
                    ->where('recommendation_relation.job_id', $jobId)
                    // ->where('recommendation_relation.final_status', '=', 1)
                    ->count();

                $matchcount = DB::table('package_active');
                if (! empty($currentJobSkill)) {
                    foreach ($currentJobSkill as $jskill) {
                        $matchcount = $matchcount->orWhere('package_active.boostkeywords_text', 'LIKE', "%{$jskill}%");

                    }
                }
                $matchcount = 0; // $row->boostkeywordCount; //$matchcount->where('package_active.company_id','=',$row->id)->count();

                if ($recommendateCounter == 1) {

                    $recommendateRow = DB::table('recommendation_relation')
                        ->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
                        ->where('recommendation_relation.from_company_id', $from_company)
                        ->where('recommendation_relation.to_company_id', $id)
                        ->where('recommendation_relation.job_id', $jobId)
                        // ->where('recommendation_relation.final_status', '=', 1)
                        ->first();

                    if (($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '')) {
                        $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
                    } elseif ($recommendateRow->final_status == -1) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-matchcount="'.$matchcount.'" data-row-jobid="'.@$jobId.'" data-row-ename="ignore" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } elseif ($recommendateRow->final_status == 2) {
                        $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-matchcount="'.$matchcount.'" data-row-jobid="'.@$jobId.'" data-row-ename="remove" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                    } else {
                        $html = '<div class="d-flex flex-column"> 
				<a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="'.$jobId.'" data-matchcount="'.$matchcount.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->to_company_id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
				Remove
				</a>
				</div>';
                    }

                } else {
                    if ($row->to_company_id == $id) {     // ###############    						   REGISTER NOT ASSIGN
                        $html = '<div class="d-flex flex-column"> 
				<a href="javascript:void(0);" id="sp_list_'.$id.'" data-boosterCom="'.$row->boosterCom.'" data-boost-ids="'.$row->boost_ids.'" data-row-jobid="'.$jobId.'"  data-matchcount="'.$matchcount.'" data-row-fromcompany="'.$from_company.'" data-row-tocompany="'.$id.'" class="btn btn-sm btn-secondary  w-130 send_only_job_invitation">
				Assign Job
				</a> 
				</div>';
                    } else { // ##########################################    							UNREGISTER
                        $html = '<div class="flex-column">
				<a href="javascript:void(0);" id="sp_list_'.$id.'" data-boosterCom="'.$row->boosterCom.'" data-boost-ids="'.$row->boost_ids.'" data-row-id="'.$id.'" data-matchcount="'.$matchcount.'" data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
				Send Job Request
				</a> 
				<span class="text-muted fw-bold text-muted d-block fs-7">Invite Agency</span>
				</div>';
                    }

                }

                return $html;

            })
            ->rawColumns(['Name', 'SimilarReq', 'RatesCal', 'Sizecount', 'Location', 'LoginTime', 'Action'])
            ->addIndexColumn()
            ->make(true);

    }

    public function boostkeywordsAndrecommendedData(Request $request, $id)
    {

        $employment_type = '';
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();

        $this->job_id = $id;
        $jobId = $this->job_id;
        // JOB DETAIL
        $currentRowJob = Job::select(
            'title',
            'min_exp',
            'max_exp',
            'min_salary',
            'max_salary',
            'min_salary_currency',
            'max_salary_currency',
            'min_sal_mode',
            'max_sal_mode',
            'employment_type'
        )
            ->where('id', $this->job_id)
            ->first();
        $parent_employment_type = $currentRowJob->employment_type;

        // JOB SKILLS
        $currentJobSkill = DB::table('job_metronics_skills')
            ->select('job_metronics_skills.skill_name')
            ->where('job_metronics_skills.job_id', $this->job_id)
            ->get();

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $this->job_id)
            ->get();
        $recommendedDataList = DB::table('companies')->select(
            'companies.id',
            'companies.company_name',
            'companies.logo',
            'companies.industries',
            'jobs.title',
            DB::raw('count(DISTINCT job_applications.job_id) as SimilarReq'),
            DB::raw('count(DISTINCT job_applications.id) as Profiles'),
            'jobs.employment_type',
            'job_metronics_skills.skill_name',
            'td_industries.ind_name',
            'staffing_company_relation.from_company_id',
            'staffing_company_relation.to_company_id',
            'staffing_company_relation.final_status',
            'recommendation_relation.from_status as rel_from_status',
            'recommendation_relation.to_status as rel_to_status',
            'recommendation_relation.to_status as rel_final_status',
            'recommendation_relation.job_id as rel_job_id',
            'recommendation_relation.from_company_id as rel_from_company_id',
            'package_active.newjobrequest_status',
            'package_active.newjobrequest_balance',
            'package_active.newclientrequest_status',
            'package_active.newclientrequest_balance',
            'package_active.boostkeywords_status',
            'city_list.city as city_name',
            'state_list.state',
            'countries.country_name',
            'client_location.city',
            'client_location.address'
        );
        $recommendedDataList = $recommendedDataList->join('jobs', function ($join) use ($company_id) {
            $join->on('jobs.company_id', '=', 'companies.id')
                ->on('jobs.company_id', '!=', DB::raw("'".$company_id."'"));
        });
        // ->join('package_active', 'package_active.company_id', '=', 'companies.id')
        // New Client Request 		:: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD
        // New Job Request	   		:: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD
        if ($parent_employment_type == 0) {				// 0 : Fulltime(In job)      New Job Request :: Fulltime Recruitment Leads  FULLTIME_RECRUITMENT_LEAD(In package_active)
            $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newjobrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newjobrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('0'));

            });
        } elseif ($parent_employment_type == 1) {			// 1	: Contract(In job)      New Client Request :: Contract Recruitment Lead   CONTRACT_RECRUITMENT_LEAD(In package_active)
            $recommendedDataList = $recommendedDataList->join('package_active', function ($join) {
                $join->on('package_active.company_id', '=', 'jobs.company_id')
                    ->on('package_active.newclientrequest_balance', '>', DB::raw('0'))
                    ->on('package_active.newclientrequest_status', '=', DB::raw('1'))
                    ->on('package_active.boostkeywords_status', '=', DB::raw('0'));

            });
        }
        $recommendedDataList = $recommendedDataList->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id');
        $recommendedDataList = $recommendedDataList->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
        $recommendedDataList = $recommendedDataList->leftJoin('countries', 'countries.id', '=', 'client_location.country_id');
        $recommendedDataList = $recommendedDataList->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id');
        $recommendedDataList = $recommendedDataList->leftJoin('state_list', 'state_list.id', '=', 'client_location.state');
        $recommendedDataList = $recommendedDataList->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
            $join->on('staffing_company_relation.to_company_id', '=', 'companies.id');
            $join->on('staffing_company_relation.from_company_id', '=', DB::raw("'".$company_id."'"));
        });
        $recommendedDataList = $recommendedDataList->leftJoin('recommendation_relation', function ($join) use ($jobId) {
            $join->on('recommendation_relation.to_company_id', '=', 'companies.id');
            $join->on('recommendation_relation.final_status', '=', DB::raw("'1'"));
            $join->on('recommendation_relation.job_id', '=', DB::raw("'".$jobId."'"));
        });
        $recommendedDataList = $recommendedDataList->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries');
        $recommendedDataList = $recommendedDataList->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');

        // $recommendedDataList 		=	$recommendedDataList->whereNotIn('package_active.boostkeywords_status', [1] );

        // ################################################################################# ------------- TITLE MATCH
        $findstring = explode(' ', $currentRowJob->title);
        foreach ($findstring as $value) {
            $recommendedDataList = $recommendedDataList->orWhere('jobs.title', 'like', "%{$value}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        }
        if (! empty($currentJobSkill)) {
            foreach ($currentJobSkill as $jskill) {
                $recommendedDataList = $recommendedDataList->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
                $recommendedDataList = $recommendedDataList->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
            }
        }

        // ################################################################################# ------------- EXPERIENCE MATCH
        if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
            $recommendedDataList = $recommendedDataList->orWhereBetween('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp])->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
            $recommendedDataList = $recommendedDataList->orWhereBetween('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp])->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        }

        // ################################################################################# ------------ JOB TYPE
        if ($currentRowJob->employment_type) {
            $recommendedDataList = $recommendedDataList->orWhere('jobs.employment_type', $currentRowJob->employment_type)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        }

        // ################################################################################# ------------  LOCATION
        if (! empty($currentJobLocation)) {
            foreach ($currentJobLocation as $cloc) {
                if ($cloc->country_id == 101) {
                    $recommendedDataList = $recommendedDataList->orWhere('client_location.city_id', $cloc->city_id)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
                } else {
                    $recommendedDataList = $recommendedDataList->orWhere('client_location.city', 'like', "%{$cloc->city}%")->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
                }
            }
        }

        $recommendedDataList = $recommendedDataList->where('companies.id', '>', 0)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        $recommendedDataList = $recommendedDataList->where(function ($query) {
            return $query->where('companies.id', '!=', auth()->user()->company_id)->whereIn('companies.company_type', [1, 2])->whereNull('recommendation_relation.final_status');
        });

        // ->on('package_active.boostkeywords_status', '!=',  DB::raw('1') )
        // $recommendedDataList 	=	$recommendedDataList->where('package_active.boostkeywords_status', '!=',  1);

        $recommendedDataList = $recommendedDataList->where('companies.id', '!=', auth()->user()->company_id)
            ->where('recommendation_relation.final_status', '!=', 1)
            ->whereIn('companies.company_type', [1, 2]);

        $recommendedDataList = $recommendedDataList->groupBy('companies.id');
        $recommendedDataList = $recommendedDataList->orderBy(DB::raw('RAND()'));
        $this->recommendedDataList = $recommendedDataList->get();

        $records = [];

        foreach ($this->recommendedDataList as $row) {

            $logo_path = url('img/client-default.png');
            if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = url('public/user-uploads/company-logo/'.@$row->logo);
            }

            $percentage = 0;
            $companies_id = '';
            $companies_id = $row->id;

            $title = $skills = $experience = $salary = $location = $jobtype = $jskillset = 0;

            // ######################################### ------------- SALARY COUNTER
            $recommended_Salary = DB::table('jobs');
            $recommended_Salary = $recommended_Salary->where('jobs.min_salary_currency', $currentRowJob->min_salary_currency);
            $recommended_Salary = $recommended_Salary->where('jobs.max_salary_currency', $currentRowJob->max_salary_currency);
            $recommended_Salary = $recommended_Salary->where('jobs.min_sal_mode', $currentRowJob->min_sal_mode);
            $recommended_Salary = $recommended_Salary->where('jobs.max_sal_mode', $currentRowJob->max_sal_mode);
            $recommended_Salary = $recommended_Salary->whereBetween('jobs.min_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
            $recommended_Salary = $recommended_Salary->whereBetween('jobs.max_salary', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
            $recommended_Salary = $recommended_Salary->where('jobs.company_id', $companies_id);
            $recommended_Salary = $recommended_Salary->count();
            if ($recommended_Salary > 0) {
                $salary = 15;
            }

            // ######################################### ------------- TITLE COUNTER
            $recommended_Title = DB::table('jobs');
            $findstring = explode(' ', $currentRowJob->title);
            foreach ($findstring as $value) { // Title match
                if ($value != '') {
                    $recommended_Title = $recommended_Title->orWhere('jobs.title', 'like', "%{$value}%");
                }
            }
            $recommended_Title = $recommended_Title->where('jobs.company_id', $companies_id);
            $recommended_Title = $recommended_Title->count();
            if ($recommended_Title > 0) {
                $title = 30;
            }

            // ######################################### ------------- JOBTYPE
            $recommended_Jobtype = DB::table('jobs');
            $recommended_Jobtype = $recommended_Jobtype->where('jobs.company_id', $companies_id);
            $recommended_Jobtype = $recommended_Jobtype->Where('jobs.employment_type', $currentRowJob->employment_type);
            $recommended_Jobtype = $recommended_Jobtype->count();
            if ($recommended_Jobtype > 0) {
                $jobtype = 10;
            }

            // ######################################### ------------- EXPERIENCE MATCH
            $recommended_Experience = DB::table('jobs');
            $recommended_Experience = $recommended_Experience->where('jobs.company_id', $companies_id);
            if ($currentRowJob->min_exp >= 0 && $currentRowJob->max_exp >= 0) {
                $recommended_Experience = $recommended_Experience->Where('jobs.min_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
                $recommended_Experience = $recommended_Experience->Where('jobs.max_exp', [$currentRowJob->min_exp, $currentRowJob->max_exp]);
            }
            $recommended_Experience = $recommended_Experience->count();
            if ($recommended_Experience > 0) {
                $experience = 15;
            }

            // SKILL COUNTER
            $recommended_Skill = DB::table('companies')
                ->select('companies.id')
                ->join('jobs', 'jobs.company_id', 'companies.id')
                ->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id')
                ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');
            if (! empty($currentJobSkill)) {
                foreach ($currentJobSkill as $jskill) {
                    $recommended_Skill = $recommended_Skill->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                    $recommended_Skill = $recommended_Skill->orWhere('job_metronics_skills.skill_name', 'like', "%{$jskill->skill_name}%");

                }
            }
            $recommended_Skill = $recommended_Skill->where('jobs.company_id', $companies_id);
            $recommended_Skill = $recommended_Skill->count();
            if ($recommended_Skill > 0) {
                $skills = 20;
            }

            $recommended_Skill_Name = DB::table('companies')
                ->select('job_metronics_skills.skill_name')
                ->join('jobs', 'jobs.company_id', 'companies.id')
                ->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id')
                ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id');

            if (! empty($currentJobSkill)) {

                foreach ($currentJobSkill as $jskill) {
                    // $recommended_Skill_Name 	=	$recommended_Skill_Name->orWhere('jobs.title', 'like', "%{$jskill->skill_name}%");
                    $recommended_Skill_Name = $recommended_Skill_Name->orWhere('job_metronics_skills.skill_name', $jskill->skill_name);
                    $jskillset .= $jskill->skill_name;

                }
            }
            $recommended_Skill_Name = $recommended_Skill_Name->where('jobs.company_id', $companies_id);
            $recommended_Skill_Name = $recommended_Skill_Name->get();
            $skillname = '';
            $scnt = 0;
            $tempArr = [];
            if (! empty($recommended_Skill_Name)) {
                foreach ($recommended_Skill_Name as $sname) {
                    $tempArr[] = $sname->skill_name;
                    $scnt++;

                }
            }
            if (! empty($tempArr)) {
                $newArr = array_keys(array_flip($tempArr));
                // $$tempArr	=	array_unique($tempArr);
                $skillname = implode(',', $newArr);
            }
            // ################################################################################# ------------  LOCATION
            $recommended_Location = DB::table('companies')
                ->select('client_location.id')
                ->join('jobs', 'jobs.company_id', 'companies.id')
                ->join('add_job_wise_condidates as job_applications', 'job_applications.job_id', 'jobs.id')
                ->leftJoin('job_metronics_skills', 'job_metronics_skills.job_id', '=', 'jobs.id')
                ->leftJoin('client_location_jobs', 'client_location_jobs.job_id', '=', 'jobs.id')
                ->leftJoin('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id');

            if (! empty($currentJobLocation)) {
                foreach ($currentJobLocation as $cloc) {
                    if ($cloc->country_id == 101) {
                        $recommended_Location = $recommended_Location->orWhere('client_location.city_id', $cloc->city_id);
                    } else {
                        $recommended_Location = $recommended_Location->orWhere('client_location.city', 'like', "%{$cloc->city}%");
                    }
                }
            }

            $recommended_Location = $recommended_Location->where('jobs.company_id', $companies_id);
            $recommended_Location = $recommended_Location->count();
            if ($recommended_Location > 0) {
                $location = 10;
            }

            $percentage = $salary + $title + $jobtype + $experience + $skills + $location;

            $empType = '';
            if (($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) && ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0)) {
                $empType = 'Full-time/Contract';
            } elseif ($row->newjobrequest_status == 1 && $row->newjobrequest_balance > 0) {
                $empType = 'Full-time';
            } elseif ($row->newclientrequest_status == 1 && $row->newclientrequest_balance > 0) {
                $empType = 'Contract';
            }

            $regStatus = 'UnRegistered';
            $regStatusCss = 'text';
            if (! empty($row->from_company_id)) {
                if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
                    $regStatus = 'Registered';

                    $regStatusCss = 'text-success';
                }
            }

            $from_company = $company_id = auth()->user()->company_id;
            $id = '';
            $id = $row->id;

            $html = '';
            $address = '';
            $recommendateCounter = DB::table('recommendation_relation')
                ->select('recommendation_relation.id')
                ->where('recommendation_relation.from_company_id', $from_company)
                ->where('recommendation_relation.to_company_id', $id)
                ->where('recommendation_relation.job_id', $jobId)
                // ->where('recommendation_relation.final_status', '=', 1)
                ->count();

            if ($recommendateCounter == 1) {

                $recommendateRow = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
                    ->where('recommendation_relation.from_company_id', $from_company)
                    ->where('recommendation_relation.to_company_id', $id)
                    ->where('recommendation_relation.job_id', $jobId)
                    // ->where('recommendation_relation.final_status', '=', 1)
                    ->first();

                if (($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '')) {
                    $html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
                } elseif ($recommendateRow->final_status == -1) {
                    $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="ignore" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Assign Job</a>';
                } elseif ($recommendateRow->final_status == 2) {
                    $html = '<a href="javascript:void(0);" data-row-id="'.$id.'" data-row-jobid="'.@$jobId.'" data-row-ename="remove" class="btn btn-sm btn-light-success w-130 resend_job_recommanded_request">Re-Assign Job</a>';
                } else {
                    $html = '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->to_company_id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
						Remove
						</a>
						</div>';
                }

            } else {
                if ($row->to_company_id == $id) {     // ###############    						   REGISTER NOT ASSIGN
                    $html = '<div class="d-flex flex-column"> 
					<a href="javascript:void(0);" id="sp_list_'.$id.'"  data-row-jobid="'.$jobId.'" data-row-fromcompany="'.$from_company.'" data-row-tocompany="'.$id.'" class="btn btn-sm btn-secondary  w-130 send_only_job_invitation">
					Assign Job
					</a> 
					</div>';
                } else { // ##########################################    							UNREGISTER
                    $html = '<div class="flex-column">
					<a href="javascript:void(0);" id="sp_list_'.$id.'"  data-row-id="'.$id.'"  data-row-jobid="'.$jobId.'" class="btn btn-sm btn-light-primary w-130 send_job_recommanded_request">
					Request Hire
					</a> 
					</div>';
                }

            }

            if (! empty($row->city)) {
                $address = '<div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$row->address).'" >'.ucwords(substr(@$row->address, 0, 10)).'</span>
						<div class="d-flex align-items-center">
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->city, 0, 10)).'</span>
								&nbsp;|&nbsp;
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->country_name, 0, 10)).'</span>
							<div>
						  </div>
						</div>
					  </div>';
            } else {
                $address = ' <div class="d-flex flex-column">
						<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="'.ucwords(@$row->address).'" >'.ucwords(substr(@$row->address, 0, 10)).'</span>
						<div class="d-flex align-items-center">
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->city_name, 0, 10)).'</span>
								&nbsp;|&nbsp;
							  <span class="fw-bold text-muted d-block fs-7">'.ucwords(substr(@$row->country_name, 0, 10)).'</span>
							<div>
						  </div>
						</div>
					  </div>';
            }

            $records[] = [
                'chkbox' => '<div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div>',

                'Name' => '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style=" float:left;margin-left: 10px !important;"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column" style=" float:left; width: 100px !important;"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 20).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>',
                'Skills' => '<div class="" style=""><span>'.$percentage.'% | '.substr(@$skillname, 0, 50).'</span></div>',
                'SimilarReq' => '<div class="d-flex flex-column" style="width: 90px !important; margin-left: 10px !important;"> <span class="text-dark fw-bolder fs-6">'.$row->SimilarReq.' </span> </div>',
                'Profiles' => '<div class="d-flex flex-column" style="width: 60px !important; margin-left: 0px !important;"> <span class="text-dark fw-bolder fs-6">'.$row->Profiles.'</span> </div>',
                'Location' => $address,

                'HiringType' => '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$empType.'</span> </div>',

                'Status' => '<div class="d-flex flex-column"> <span class=" '.@$regStatusCss.' fw-bolder fs-6">'.@$regStatus.'</div>',
                'Action' => $html,
            ];

        }
        $this->records = $records;
        // dd($this->records);

        $view = view('service_company.jobs.spartnerlistingview', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function recommendedAssignList(Request $request)
    {

        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();

        $job_status = GeneralMethod::job_status();
        $salaryMode = GeneralMethod::salaryMode();
        $currencySymbole = GeneralMethod::currencySymbole();

        $company_id = auth()->user()->company_id;
        $employmentType = GeneralMethod::employmentType();
        $searchTerms = request('cus_search');
        $custom_search = $searchTerms; // json_decode($searchTerms, true);

        $cachePayload = [
            'custom_search' => $custom_search,
            'company' => $company_id,
            'user_id' => $this->user->id,
            'custom_search' => $custom_search,
            'jobid' => $request->jobid,
        ];

        $versionKey = 'allassignedagency_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allassignedagency:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        if (config('feature.use_three_drawers_v3')) {
            // V3 Step 7: ETag check — 304 skips Redis lookup + ES search + view render entirely.
            // ETag changes whenever the version key bumps (i.e. after any team-assignment write).
            $etag = '"v3:team:'.md5($cacheKeySet).'"';
            if (request()->header('If-None-Match') === $etag) {
                return response(null, 304)->header('ETag', $etag);
            }

            // V3 Step 3: Redis cache guard — skip ES search + view render on cache hit.
            if (cache()->has($cacheKeySet)) {
                return response()->json(cache()->get($cacheKeySet))
                    ->header('ETag', $etag)
                    ->header('Cache-Control', 'private, max-age=0, must-revalidate');
            }
        }

        $contHtml = '<div class="symbol-group symbol-hover" style="float:left">';
        $contactQuery = DB::table('users')
            ->join('job_assign', 'job_assign.user_id', 'users.id')
            ->select('users.name', 'users.image')
            ->where('users.company_id', $company_id)
            ->where('job_assign.company_id', $company_id)
            ->where('job_assign.job_id', $request->jobid)
            ->get();
        if (! empty($contactQuery)) {
            foreach ($contactQuery as $cont) {
                $logo_path = str_replace('index.php', '', url('public/avatar.png'));
                if (! empty(@$cont->image) && strlen(@$cont->image) > 10) {
                    $logo_path = @$cont->image;
                }
                $contHtml .= '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.$cont->name.'"> <img alt="One" src="'.$logo_path.'"></div>';
            }
        }
        $contHtml .= '</div>';

        // dd($request->jobid);
        $jobId = $request->jobId;

        $job_id = $request->jobid;

        $searchTerms = request('cus_search');
        $custom_search = $searchTerms;

        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 10); // Default 20 results per page
        $from = ($page - 1) * $size;
        $valCheck = $request->input('sortingfilter');
        $mstatus_id = $request->input('mstatus_id');
        $sort = [];
        $country = '';
        $filters = [];

        // Apply fallback sort by ID descending
        $sort[] = ['final_status' => ['order' => 'asc', 'unmapped_type' => 'long']];

        // if (!empty($custom_search)) {
        // 	$filters[] = [
        // 		'bool' => [
        // 			'should' => [
        // 				[
        // 					'match_phrase_prefix' => [
        // 						'company_name_to' => $custom_search
        // 					]
        // 				]
        // 			],
        // 		]
        // 	];
        // }

        $boolQuery = [
            'bool' => [
                'should' => [
                    // First condition
                    [
                        'bool' => [
                            'must' => array_merge([
                                ['term' => ['from_company_id' => $company_id]],
                                ['term' => ['job_id' => $job_id]],
                                ['term' => ['final_status' => 1]],
                            ]),
                        ],
                    ],
                ],
                'minimum_should_match' => 1,
                'filter' => $filters, // We'll add all filters here
            ],
        ];

        // Build the final query
        $params = [
            'index' => ELASTIC_JOB_REQUEST,
            'request_cache' => true,
            'body' => [
                'from' => $from,
                'size' => $size,
                'sort' => $sort,
                'query' => $boolQuery,
            ],
        ];

        if (! $this->elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
            $total = 0;
            $data = [];
        } else {

            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;
            $data = array_map(function ($hit) {
                return $hit['_source'];
            }, $response['hits']['hits']);
        }

        if (! empty($data)) {
            $agencyIds = collect($data)->pluck('to_company_id')->filter()->unique()->values()->toArray();

            if (! empty($agencyIds)) {
                // Batch 1: Fetch self-company client info for all assigned agencies
                $clientsMap = GeneralMethod::batchGetClientIdCompany($agencyIds);

                // Batch 2: Fetch contact user for assigned job across all agencies
                // Intent: Single SQL query to retrieve primary contact details per assigned agency
                // Tenancy: Filtered by users.company_id in $agencyIds and job_assign.job_id = $job_id
                $contactsList = DB::table('users')
                    ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
                    ->select('users.name', 'users.image', 'users.mobile', 'users.email', 'users.company_id')
                    ->whereIn('users.company_id', $agencyIds)
                    ->where('job_assign.job_id', $job_id)
                    ->get();

                $contactsMap = [];
                foreach ($contactsList as $cnt) {
                    if (! isset($contactsMap[$cnt->company_id])) {
                        $contactsMap[$cnt->company_id] = $cnt;
                    }
                }

                // Batch 3: Fetch performance metrics (Upload, Interview, Joined) for all assigned agencies
                $perfMap = GeneralMethod::batchAgencyCandidatesCount($agencyIds, $job_id);

                // Pre-bind batch properties to eliminate Blade loop N+1 queries
                foreach ($data as &$rowArr) {
                    $toComId = $rowArr['to_company_id'] ?? null;
                    $rowArr['_client_info'] = $clientsMap[$toComId] ?? null;
                    $rowArr['_contact_info'] = $contactsMap[$toComId] ?? null;
                    $rowArr['_perf_info'] = $perfMap[$toComId] ?? (object) ['Upload' => 0, 'Interview' => 0, 'Joined' => 0];
                }
                unset($rowArr);
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'per_page' => $size,
            'listType' => 'allcandidate',
        ];

        $userPermissions = $this->userPermissions;
        $responseData = [
            'html' => view('service_company.ajax_pages.assigned_gency_ajax', compact('data', 'userPermissions'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        // V3 Step 3 / V2 Agency Step 5: TTL extended from 3 min → 60 min. Assigned agency
        // list changes only when an agency is added/removed (write path purges the key).
        // N+1 DB queries inside the view are eliminated on any warm cache hit.
        if (config('feature.use_three_drawers_v3')) {
            cache()->put($cacheKeySet, $responseData, 3600);
        }

        // V3 Step 7: attach ETag to 200 so browser can revalidate on the next open.
        if (config('feature.use_three_drawers_v3') && isset($etag)) {
            return response()->json($responseData)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'private, max-age=0, must-revalidate');
        }

        return response()->json($responseData);

        $responseData = DataTables::of($recommendedDataList)
            ->editColumn('Name', function ($row) {

                $logo_path = str_replace('index.php', '', url('img/client-default.png'));
                if (! empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = @$row->logo;
                }

                return '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
								<a href="javascript:void(0);">
                      				<div class="symbol-label border-radius-5"> <img src="'.$logo_path.'" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      			</a> 
							</div>
							<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">'.substr(ucwords(@$row->company_name), 0, 15).'</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">'.substr(ucwords(@$row->ind_name), 0, 15).'</a>
							</div>';
            })
            ->editColumn('ContactPerson', function ($row) use ($job_id) {
                $company_id = $row->id;
                $contHtml = '';
                // dd($job_id);
                $contHtml = '<div class="symbol-group symbol-hover" style="float:left">';
                $contactQuery = DB::table('users')
                    ->join('job_assign', 'job_assign.user_id', 'users.id')
                    ->select('users.name', 'users.image')
                    ->where('users.company_id', $company_id)
                    ->where('job_assign.company_id', $company_id)
                    ->where('job_assign.job_id', $job_id)
                    ->skip(0)->take(10)
                    ->get();
                // dd($contactQuery,$company_id,$job_id);
                if (! empty($contactQuery)) {
                    foreach ($contactQuery as $cont) {
                        $bgTextDecore = GeneralMethod::icoinText(@$cont->name);
                        $logo_path = '';
                        if (! empty(@$cont->image)) {
                            $logo_path = @$cont->image;
                            $contHtml .= '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.$cont->name.'"> <img alt="One" src="'.$logo_path.'"></div>';
                        } else {
                            $contHtml .=
                            '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="'.ucwords(@$cont->name).'" >
								<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
								<a href="javascript:void(0);" data-row-id="11" class="border-radius-100" id="kt_activities3_toggle">
									<div class="symbol-label fs-8 fs-8 '.$bgTextDecore.' border-radius-100">'.substr($cont->name, 0, 1).'</div>
								</a>		 
								</div>
								</div>';
                        }

                    }
                }
                $contHtml .= '</div>';
                // dd($contHtml);

                return $contHtml;
            })
            ->editColumn('UploadProfile', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Upload.'</span> </div>';
            })
            ->editColumn('Interview', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Interview.'</span> </div>';
            })
            ->editColumn('Joinings', function ($row) {
                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$row->Joined.'</span> </div>';
            })
            ->editColumn('RatesCal', function ($row) {
                return '<div class="d-flex flex-column"> 2.33%</div>';
            })
            ->editColumn('SuccessCus', function ($row) {
                return '<div class="d-flex flex-column"> 60% Success</div>';
            })
            ->editColumn('Sizecount', function ($row) {
                $Sizecount = DB::table('users')->where('company_id', $row->id)->count();

                return '<div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">'.$Sizecount.' Member</span></div>';
            })
            ->editColumn('Action', function ($row) use ($company_id) {
                $jobid = $row->jobid;

                return '<div class="flex-column"> 
		<a href="javascript:void(0);" data-row-tabname="Assigned"  data-row-ename="remove"  data-row-jobid="'.$jobid.'" data-row-fromcompany="'.$company_id.'" data-row-tocompany="'.$row->id.'" class="btn btn-sm btn-light-danger w-130 remove_job">
			Remove
		</a> 
		</div>';
            })
            ->rawColumns(['Name', 'ContactPerson', 'UploadProfile', 'Interview', 'Joinings', 'Action'])
            ->addIndexColumn()
            ->make(true);

        cache()->put($cacheKeySet, $responseData, now()->addMinutes(3));

        return $responseData;

    }

    public function staffingPartnersRegister(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $chkAdminRole = $this->user->hasRole('admin');
        // $chkAdminRole = DB::table('roles')->select('roles.id')->where('id', $this->user->role->role_id)->where('name', 'admin')->count();
        $accountCompanyType = GeneralMethod::accountCompanyType();
        $jobId = $request->jobid;

        $hiringtype = request('hiringtype');
        $country = request('country');

        $currentRowJob = Job::select(
            'title',
            'min_exp',
            'max_exp',
            'min_salary',
            'max_salary',
            'min_salary_currency',
            'max_salary_currency',
            'min_sal_mode',
            'max_sal_mode',
            'company_id',
            'employment_type'
        )
            ->where('id', $request->jobid)
            ->first();
        $parent_employment_type = $currentRowJob->employment_type;

        $jobMinExp = $currentRowJob->min_exp;
        $jobMaxExp = $currentRowJob->max_exp;
        $jobMinSalary = $currentRowJob->min_salary;
        $jobMaxSalary = $currentRowJob->max_salary;
        $jobSalaryCurrency = $currentRowJob->max_salary_currency;
        $jobSalaryMode = $currentRowJob->max_sal_mode;

        $currentJobSkill = DB::table('job_metronics_skills')
            ->where('job_metronics_skills.job_id', $request->jobid)
            ->where('job_metronics_skills.company_id', '=', $currentRowJob->company_id)
            ->pluck('job_metronics_skills.skill_name')
            ->toArray();

        // JOB LOCATION QUERY
        $currentJobLocation = DB::table('client_location_jobs')
            ->select('client_location.id', 'client_location.city', 'client_location.city_id', 'client_location.country_id')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->get();

        $currentJobLocationArray = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->where('client_location_jobs.job_id', $request->jobid)
            ->pluck('client_location.country_id')->toArray();

        $country_id_job = $country;
        if ($country == 'Show All') {
            $country_id_job = '';
        }

        $skillCaseStatements = '';
        // dd($currentJobSkill);
        foreach ($currentJobSkill as $skill) {
            $skillCaseStatements .= "CASE WHEN job_metronics_skills.skill_name LIKE '%{$skill}%' THEN 20 ELSE 0 END + ";
            $skillCaseStatements .= "CASE WHEN jobs.title LIKE '%{$skill}%' THEN 20 ELSE 0 END + ";
        }
        // Remove the trailing ' + '
        $skillCaseStatements = rtrim($skillCaseStatements, ' + ');
        $totalSkills = count($currentJobSkill);

        $skillsList = array_filter($currentJobSkill, fn ($s) => $s !== 'skill');

        $skillsList = array_map('strtolower', $skillsList);
        $searchQuery = $request->input('q');
        $page = max((int) $request->input('page', 1), 1);
        $size = (int) $request->input('size', 20);
        $from = ($page - 1) * $size;

        $cachePayload = [
            'page' => $page,
            'size' => $size,
            'q' => $searchQuery,
            'company' => $company_id,
            'hiringtype' => $hiringtype,
            'country' => $country_id_job,
            'user_id' => $this->user->id,
            'cus_search' => request('cus_search'),
        ];
        // dd("sdlkf");
        $versionKey = 'allregisteredagency_version_user_'.$this->user->id;
        $version = cache()->get($versionKey, 1);
        $cacheKeySet = 'ats:allregisteredagency:'.$this->user->id.':'.$version.':'.md5(json_encode($cachePayload));
        /* ----------------------------
        ⚡ Check DragonflyDB first
        ----------------------------- */
        // V3 Step 7: ETag check first — 304 skips Redis + ES relation scan + agency search + render.
        // ETag changes when version key bumps after any agency-assignment write.
        $etag = '"v3:agency:'.md5($cacheKeySet).'"';
        if (config('feature.use_three_drawers_v3') && request()->header('If-None-Match') === $etag) {
            return response(null, 304)->header('ETag', $etag);
        }

        if (cache()->has($cacheKeySet)) {
            $cachedResponse = response()->json(cache()->get($cacheKeySet));
            if (config('feature.use_three_drawers_v3')) {
                $cachedResponse = $cachedResponse
                    ->header('ETag', $etag)
                    ->header('Cache-Control', 'private, max-age=0, must-revalidate');
            }

            return $cachedResponse;
        }

        // V3 Step 4: cap from 10,000 → 500. At 10k concurrent users the unrestricted scan
        // consumes ~200 MB of ES heap per request. 500 covers all real-world companies;
        // a Log::warning fires if a tenant actually exceeds this so operators can review.
        $relationScanSize = config('feature.use_three_drawers_v3') ? 500 : 10000;

        $relationIds = $this->elasticService->getClient()->search([
            'index' => ELASTIC_CLIENT_AND_AGENCY_RELATION_INDEX,
            'body' => [
                'size' => $relationScanSize,
                'track_total_hits' => true,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['from_company_id' => $company_id]],
                            ['term' => ['final_status' => 1]],
                        ],
                    ],
                ],
                '_source' => ['to_company_id'],
            ],
        ]);

        // Extract company IDs
        $companyIds = collect($relationIds['hits']['hits'])
            ->pluck('_source.to_company_id')
            ->unique()
            ->values()
            ->toArray();

        if (config('feature.use_three_drawers_v3') && ($relationIds['hits']['total']['value'] ?? 0) > 500) {
            Log::warning('v3:staffingPartnersRegister: company exceeded 500-relation cap; some agencies may be hidden in the Add Agency drawer.', [
                'company_id' => $company_id,
                'total_relations' => $relationIds['hits']['total']['value'],
            ]);
        }

        $mustConditions = [];

        if ($search = request('cus_search')) {

            $mustConditions[] = [
                'bool' => [
                    'should' => [
                        ['match_phrase_prefix' => ['company_name' => strtolower($search)]],
                        ['wildcard' => ['company_name.keyword' => '*'.strtolower($search).'*']],
                    ],
                    'minimum_should_match' => 1,
                ],
            ];
        }

        $params = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body' => [
                'from' => $from,
                'size' => $size,
                'track_total_hits' => true,
                'sort' => [
                    ['_score' => ['order' => 'desc']],
                    ['leadCount' => ['order' => 'asc']],
                ],
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    ['terms' => ['id' => $companyIds]],
                                    ['term' => ['status' => 'active']],
                                ],
                                'must' => $mustConditions,
                                'must_not' => [
                                    ['term' => ['id' => $company_id]],
                                ],
                            ],
                        ],
                        'score_mode' => 'sum',
                        'boost_mode' => 'sum',
                        'functions' => [
                            [
                                'script_score' => [
                                    'script' => [
                                        'lang' => 'painless',
                                        'source' => '
												int count = 0;
												if (doc.containsKey("job_metronics_skills.skill_name.keyword")) {
													for (s in params.skills) {
														if (doc["job_metronics_skills.skill_name.keyword"].contains(s)) {
															count += 1;
														}
													}
												}
												return count;
											',
                                        'params' => [
                                            'skills' => array_values($skillsList),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'aggs' => [
                    'job_skill_match_count' => [
                        'nested' => ['path' => 'job_metronics_skills'],
                        'aggs' => [
                            'matched_jobs' => [
                                'filter' => [
                                    'terms' => [
                                        'job_metronics_skills.skill_name.keyword' => $skillsList,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'candidate_skill_match_count' => [
                        'nested' => ['path' => 'candidates'],
                        'aggs' => [
                            'matched_candidates' => [
                                'filter' => [
                                    'terms' => [
                                        'candidates.skills.keyword' => $skillsList,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'bench_candidates' => [
                        'nested' => ['path' => 'candidates'],
                        'aggs' => [
                            'bench' => [
                                'filter' => [
                                    'term' => ['candidates.type.keyword' => 'bench_condidate'],
                                ],
                            ],
                        ],
                    ],
                    'other_candidates' => [
                        'nested' => ['path' => 'candidates'],
                        'aggs' => [
                            'other' => [
                                'filter' => [
                                    'term' => ['candidates.type.keyword' => 'other'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // V3 Step 4: the 4 nested aggregations (job_skill_match_count, candidate_skill_match_count,
        // bench_candidates, other_candidates) are computed but their results are never referenced
        // in $responseData — $response['aggregations'] is never read below. Dropping them saves
        // significant ES heap and computation time on every cache-miss search.
        if (config('feature.use_three_drawers_v3')) {
            unset($params['body']['aggs']);
        }

        if (! $this->elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $total = 0;
            $data = [];
        } else {
            // Execute search

            $response = $this->elasticService->getClient()->search($params);
            $total = $response['hits']['total']['value'] ?? 0;

            // Extract and attach matched skills to result
            $data = array_map(function ($hit) use ($skillsList) {
                $source = $hit['_source'];

                $jobSkills = collect(data_get($source, 'job_metronics_skills', []))
                    ->pluck('skill_name')
                    ->filter()
                    ->unique()
                    ->toArray();

                $matchedSkills = array_values(array_intersect($jobSkills, $skillsList));
                $matchCount = count($matchedSkills);

                // dd($matchCount);

                return array_merge($source, [
                    'matched_skill_count' => $matchCount,
                    'matched_skills' => $matchedSkills,
                ]);
            }, $response['hits']['hits']);
        }

        if (! empty($data)) {
            $agencyIds = collect($data)->pluck('id')->filter()->unique()->values()->toArray();

            if (! empty($agencyIds)) {
                // Batch 1: Fetch self-company client info for all registered agencies
                $clientsMap = GeneralMethod::batchGetClientIdCompany($agencyIds);

                // Batch 2: Fetch relation data between employer company and registered agencies
                $relationsMap = GeneralMethod::batchGetAgencyRelationData($company_id, $agencyIds);

                // Batch 3: User size counts via single SQL query
                // Intent: Single query counting users per agency company
                // Tenancy: Filtered by company_id in $agencyIds
                $userSizeCounts = DB::table('users')
                    ->select('company_id', DB::raw('count(*) as total'))
                    ->whereIn('company_id', $agencyIds)
                    ->groupBy('company_id')
                    ->pluck('total', 'company_id')
                    ->toArray();

                // Batch 4 & 5: Active jobs and candidates counts per agency
                $agencyJobsMap = GeneralMethod::batchAgencyJobsCount($agencyIds);
                $agencyCandidatesMap = GeneralMethod::batchAgencyJobsCandidatesCount($agencyIds);

                // Pre-bind batch properties onto $data array objects to eliminate Blade N+1
                foreach ($data as &$source) {
                    $cId = $source['id'] ?? null;
                    $source['_client_info'] = $clientsMap[$cId] ?? null;
                    $source['_relation_info'] = $relationsMap[$cId] ?? null;
                    $source['_user_size_count'] = $userSizeCounts[$cId] ?? 0;
                    $source['_agency_jobs_count'] = $agencyJobsMap[$cId] ?? (object) ['all_jobs' => 0];
                    $source['_agency_candidates_count'] = $agencyCandidatesMap[$cId] ?? (object) ['all_candidates' => 0, 'all_candidate_bench' => 0];
                }
                unset($source);
            }
        }

        $pagination = [
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $size),
            'listType' => 'allcandidate',
        ];

        // dd($pagination);

        // dd($data);

        $userPermissions = $this->userPermissions;
        $app_int_outsource = request('app_int_outsource');
        $parent_employment_type = request('app_int_outsource');
        // $hiringtype = $app_int_outsource ;
        $jobid = $request->jobid;

        // return response()->json([
        // 	'html' => view('service_company.ajax_pages.'.$page_view.'', compact('data','userPermissions','app_int_outsource', 'jobid', 'currentJobSkill', 'hiringtype', 'parent_employment_type', 'company_id'))->render(),
        // 	'total' => $total,
        // 	'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        // ]);

        $responseData = [
            'html' => view('service_company.ajax_pages.rec_register_agency_ajax', compact('data', 'userPermissions', 'company_id', 'currentRowJob', 'currentJobSkill', 'currentJobLocation', 'hiringtype', 'jobMinExp', 'jobMaxExp', 'jobSalaryMode', 'jobSalaryCurrency', 'jobId'))->render(),
            'total' => $total,
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ];

        // V2 Agency Step 5: extend TTL from 3 min → 60 min. Agency lists change only when
        // an invitation is sent (which purges the key via SendAgencyInvitationAsyncJob).
        // 3-min TTL caused constant cache churn with no benefit.
        cache()->put($cacheKeySet, $responseData, 3600);

        // V3 Step 7: attach ETag to 200 so browser can revalidate on the next open.
        if (config('feature.use_three_drawers_v3')) {
            return response()->json($responseData)
                ->header('ETag', $etag)
                ->header('Cache-Control', 'private, max-age=0, must-revalidate');
        }

        return response()->json($responseData);

        // 1. Precompute: SimilarReq jobs count
        $similarReqSub = DB::table('jobs as j')
            ->select(DB::raw('COUNT(DISTINCT j.id) as SimilarReq'), DB::raw('COALESCE(j.company_id, rr.to_company_id) as vendor_id'))
            ->leftJoin('job_metronics_skills as jms', 'jms.job_id', '=', 'j.id')
            ->leftJoin('recommendation_relation as rr', 'rr.job_id', '=', 'j.id')
            ->where('j.status', 'active');
        if (count($skillsList) > 0) {
            $similarReqSub = $similarReqSub->whereIn('jms.skill_name', $skillsList);
        }
        $similarReqSub = $similarReqSub->when($parent_employment_type === '1' || $parent_employment_type === '0', function ($q) use ($parent_employment_type) {
            $q->where('j.employment_type', $parent_employment_type);
        })
            ->groupBy('vendor_id');

        // 2. Precompute: Bench profiles count
        $benchProfilesSub = DB::table('job_applications')
            ->select(DB::raw('COUNT(DISTINCT job_applications.id) as benchProfilesCount'), 'job_applications.company_id as vendor_id')
            ->join('job_application_skills', 'job_application_skills.job_application_id', '=', 'job_applications.id')
            ->where('job_applications.status', 2)
            ->where('job_applications.condidate_type', 'bench_condidate');
        if (count($skillsList) > 0) {
            $benchProfilesSub = $benchProfilesSub->whereIn('job_application_skills.skills', $skillsList);
        }
        $benchProfilesSub = $benchProfilesSub->groupBy('job_applications.company_id');

        // 3. Precompute: Other profiles count
        $otherProfilesSub = DB::table('job_applications')
            ->select(DB::raw('COUNT(DISTINCT job_applications.id) as otherProfilesCount'), DB::raw('COALESCE(add_job_wise_condidates.company_id, add_job_wise_condidates.thirdparty_company_id) as vendor_id'))
            ->join('job_application_skills', 'job_application_skills.job_application_id', '=', 'job_applications.id')
            ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_application_id', '=', 'job_applications.id')
            ->where('job_applications.condidate_type', 'other');
        if (count($skillsList) > 0) {
            $otherProfilesSub = $otherProfilesSub->whereIn('job_application_skills.skills', $skillsList);
        }
        $otherProfilesSub = $otherProfilesSub->where(function ($q) {
            $q->whereNotNull('add_job_wise_condidates.company_id')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('add_job_wise_condidates.thirdparty_company_id')
                        ->where('add_job_wise_condidates.master_status_id', '!=', 8);
                });
        })
            ->groupBy('vendor_id');

        // Step 1: Prebuild subquery for skill matching
        $skillMatchSub = DB::table('job_metronics_skills')
            ->select(
                'company_id',
                DB::raw('COUNT(DISTINCT skill_name) as skill_match_count'),
                DB::raw("GROUP_CONCAT(DISTINCT skill_name ORDER BY skill_name SEPARATOR ', ') as matched_skills"),
                DB::raw("GROUP_CONCAT(DISTINCT id ORDER BY id SEPARATOR ', ') as boost_ids")
            )
            ->whereIn('skill_name', $currentJobSkill)
            ->groupBy('company_id');

        // Step 2: Paginate company IDs based on relation + filters
        $baseQuery = DB::table('companies')
            ->join('staffing_company_relation', function ($join) use ($company_id) {
                $join->on('staffing_company_relation.to_company_id', '=', 'companies.id')
                    ->where('staffing_company_relation.from_company_id', '=', $company_id);
            })
            ->leftJoin('commericial_countries', 'commericial_countries.company_id', 'companies.id');

        if (! empty($country_id_job)) {
            $baseQuery->where('commericial_countries.country_id', $country_id_job);
        }

        if ($search = request('cus_search')) {
            $baseQuery->where('companies.company_name', 'LIKE', "%{$search}%");
        }

        $baseQuery->where('companies.id', '!=', auth()->user()->company_id)
            ->where('staffing_company_relation.final_status', '=', 1)
            ->groupBy('companies.id');

        // Paginate only company IDs
        $perPage = 10;
        $page = request()->get('page', 1);
        $companyIdsPage = $baseQuery->select('companies.id')->paginate($perPage);

        // Step 3: Fetch full data for paginated company IDs
        $recommendedDataList = DB::table('companies')
            ->select(
                'companies.id',
                'companies.company_name',
                'companies.last_login',
                'companies.company_type',
                'companies.logo',
                'td_industries.ind_name',
                'companies.industries',
                DB::raw('COALESCE(skill_match_data.skill_match_count, 0) as skill_match_count'),
                DB::raw('COALESCE(skill_match_data.matched_skills, "") as matched_skills'),
                DB::raw('COALESCE(skill_match_data.boost_ids, "") as boost_ids'),
                'similar_reqs.SimilarReq',
                'bench_profiles.benchProfilesCount',
                'other_profiles.otherProfilesCount',
                'staffing_company_relation.to_company_id',
                'staffing_company_relation.final_status'
            )
            ->leftJoin('staffing_company_relation', function ($join) use ($company_id) {
                $join->on('staffing_company_relation.to_company_id', '=', 'companies.id')
                    ->where('staffing_company_relation.from_company_id', '=', $company_id);
            })
            ->leftJoin('td_industries', 'td_industries.ind_id', '=', 'companies.industries')
            ->leftJoinSub($skillMatchSub, 'skill_match_data', function ($join) {
                $join->on('skill_match_data.company_id', '=', 'companies.id');
            })
            ->leftJoinSub($similarReqSub, 'similar_reqs', fn ($join) => $join->on('similar_reqs.vendor_id', '=', 'companies.id'))
            ->leftJoinSub($benchProfilesSub, 'bench_profiles', fn ($join) => $join->on('bench_profiles.vendor_id', '=', 'companies.id'))
            ->leftJoinSub($otherProfilesSub, 'other_profiles', fn ($join) => $join->on('other_profiles.vendor_id', '=', 'companies.id'))
            ->whereIn('companies.id', $companyIdsPage->pluck('id'))
            ->orderByDesc(DB::raw('(COALESCE(skill_match_data.skill_match_count, 0) / '.count($currentJobSkill).') * 100'))
            ->get()
            ->map(function ($vendor) use ($currentJobSkill) {
                $vendor->skill_match_percentage = $vendor->skill_match_count
                    ? round(($vendor->skill_match_count / count($currentJobSkill)) * 100, 2)
                    : 0;
                $vendor->boosterCom = 0;

                return $vendor;
            });

        // Step 4: Final pagination structure
        $data = new \Illuminate\Pagination\LengthAwarePaginator(
            $recommendedDataList,
            $companyIdsPage->total(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $pagination = [
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'listType' => 'allcandidate',
        ];

        // dd($pagination,$data);
        $userPermissions = $this->userPermissions;

        return response()->json([
            'html' => view('service_company.ajax_pages.rec_register_agency_ajax', compact('data', 'userPermissions', 'company_id', 'currentRowJob', 'currentJobSkill', 'currentJobLocation', 'hiringtype', 'jobMinExp', 'jobMaxExp', 'jobSalaryMode', 'jobSalaryCurrency', 'jobId'))->render(),
            'total' => $data->total(),
            'pagination' => view('service_company.ajax_pages.pagination', compact('pagination'))->render(),
        ]);

    }

    public function resend_job_recommanded_request(Request $request, $id)
    {

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;
        $to_company_id = $request->to_company_id;

        $jobid = $request->current_jobid;
        // dd($request->to_company_id);

        if (Job::where('id', '=', $jobid)->where('status', '=', 'inactive')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is closed. Please change the job status to active.
			"]);
        } elseif (Job::where('id', '=', $jobid)->where('status', '=', 'hold')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is hold. Please change the job status to active."]);
        }

        //  $searchTermsLocation = request('kt_tagify_location');
        //  $custom_search_location = json_decode($searchTermsLocation, true);
        $currentJobLocation = 101; // array_column($custom_search_location, 'value');
        //    dd($currentJobLocation);
        $job_country_id = DB::table('countries')
            ->select('countries.id')
            ->where('countries.id', '=', $currentJobLocation)
            ->first();
        $country_id_job = $job_country_id->id;
        $clientMainLocation = GeneralMethod::companyMainAddress($from_company_id);
        // dd($clientMainLocation==$country_id_job);

        $jobid = $_REQUEST['current_jobid'];
        $clientRow = DB::table('clients')->select('clients.id')->where('clients.company_id', '=', $from_company_id)->where('clients.self_company', '=', 1)->first();
        // $jobRow = DB::table('jobs')->select('jobs.id', 'jobs.employment_type')->where('jobs.id', '=', $jobid)->first();

        $jobRow = DB::table('jobs')->join('job_countries', 'job_countries.job_id', '=', 'jobs.id')->select('jobs.id', 'jobs.created_at', 'jobs.status', 'jobs.total_positions', 'jobs.employment_type', 'job_countries.country_id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.employment_type', 'jobs.max_salary_currency', 'jobs.max_sal_mode', 'jobs.title', 'jobs.company_id')->where('jobs.id', '=', $jobid)->first();

        $resendallow = DB::table('features')->select('features.*')->where('id', '=', 3)->orderBy('features.id', 'ASC')->first();
        if ($resendallow) {
            if ($resendallow->status) {
                // if($_REQUEST['matchcount']){
                // 	if ($clientMainLocation == $country_id_job) {

                // 		$boostKeyWork_Auth = DB::table('package_active')
                // 			->select('package_active.id')
                // 			->where('package_active.BoostKeywordsIndia_status', '=', 1)
                // 			->where('package_active.BoostKeywordsIndia_balance', '>', 0)
                // 			->where('package_active.company_id', '=', $to_company_id)
                // 			->count();

                // 		if ($boostKeyWork_Auth > 0) {
                // 			$packageStatus = '';
                // 			$packageStatus = GeneralMethod::validateClientPackage('BoostKeywordsIndia', $to_company_id);

                // 			if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) 		// BOOST STAFFING PARTNER
                // 			{

                // 				//DEDUCTION BOOST PROFILE
                // 				if (@$jobid > 0) {
                // 					$source_from_company_id = $to_company_id;
                // 					$event_name = 'BoostKeywordsIndia';
                // 					$event_table_name = 'jobs';
                // 					$event_table_id = @$jobid;
                // 					$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 				}

                // 			} else {
                // 				return Reply::dataOnly(["status" => "warning", "message" => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // 			}

                // 		}

                // 	} else {

                // 		$boostKeyWork_Auth = DB::table('package_active')
                // 			->select('package_active.id')
                // 			->where('package_active.BoostKeywordsInternational_status', '=', 1)
                // 			->where('package_active.BoostKeywordsInternational_balance', '>', 0)
                // 			->where('package_active.company_id', '=', $to_company_id)
                // 			->count();

                // 		if ($boostKeyWork_Auth > 0) {
                // 			$packageStatus = '';
                // 			$packageStatus = GeneralMethod::validateClientPackage('BoostKeywordsInternational', $to_company_id);

                // 			if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) 		// BOOST STAFFING PARTNER
                // 			{

                // 				//DEDUCTION BOOST PROFILE
                // 				if (@$jobid > 0) {
                // 					$source_from_company_id = $to_company_id;
                // 					$event_name = 'BoostKeywordsInternational';
                // 					$event_table_name = 'jobs';
                // 					$event_table_id = @$jobid;
                // 					$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 				}

                // 			} else {
                // 				return Reply::dataOnly(["status" => "warning", "message" => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // 			}

                // 		}

                // 	}

                // }else{

                if ($jobRow->employment_type == 1) {
                    //	CONTRACT_RECRUITMENT_LEAD   employment_type = 1
                    if ($clientMainLocation == $country_id_job) {

                        $eventId = 21;
                        $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        if ($coinsDeductions->status) {
                            return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                        } else {
                            GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        }

                        // $packageStatus = GeneralMethod::validateClientPackage('ContractRecruitmentLeadIndia', $to_company_id);
                        // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                        // 	//DEDUCTION CONTRACT_RECRUITMENT_LEAD
                        // 	if (@$jobid > 0) {
                        // 		$source_from_company_id = $to_company_id;
                        // 		$event_name = 'ContractRecruitmentLeadIndia';
                        // 		$event_table_name = 'jobs';
                        // 		$event_table_id = @$jobid;
                        // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                        // 	}
                        // } else {
                        // 	return Reply::dataOnly(["status" => "warning", "message" => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                        // }

                    } else {

                        $eventId = 22;
                        $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        if ($coinsDeductions->status) {
                            return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                        } else {
                            GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        }

                        // $packageStatus = GeneralMethod::validateClientPackage('ContractRecruitmentLeadInternational', $to_company_id);
                        // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                        // 	//DEDUCTION CONTRACT_RECRUITMENT_LEAD
                        // 	if (@$jobid > 0) {
                        // 		$source_from_company_id = $to_company_id;
                        // 		$event_name = 'ContractRecruitmentLeadInternational';
                        // 		$event_table_name = 'jobs';
                        // 		$event_table_id = @$jobid;
                        // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                        // 	}
                        // } else {
                        // 	return Reply::dataOnly(["status" => "warning", "message" => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                        // }

                    }

                } elseif ($jobRow->employment_type == 0) {	//	FULLTIME_RECRUITMENT_LEAD   employment_type = 0
                    if ($clientMainLocation == $country_id_job) {

                        $eventId = 19;
                        $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        if ($coinsDeductions->status) {
                            return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                        } else {
                            GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        }

                        // $packageStatus = GeneralMethod::validateClientPackage('PermamentRecruitmentLeadIndia', $to_company_id);
                        // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                        // 	//DEDUCTION FULLTIME_RECRUITMENT_LEAD
                        // 	if (@$jobid > 0) {
                        // 		$source_from_company_id = $to_company_id;
                        // 		$event_name = 'PermamentRecruitmentLeadIndia';
                        // 		$event_table_name = 'jobs';
                        // 		$event_table_id = @$jobid;
                        // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                        // 	}
                        // } else {
                        // 	return Reply::dataOnly(["status" => "warning", "message" => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                        // }

                    } else {

                        $eventId = 20;
                        $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        if ($coinsDeductions->status) {
                            return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                        } else {
                            GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                        }

                        // 	$packageStatus = GeneralMethod::validateClientPackage('PermamentRecruitmentLeadInternational', $to_company_id);
                        // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                        // 	//DEDUCTION FULLTIME_RECRUITMENT_LEAD
                        // 	if (@$jobid > 0) {
                        // 		$source_from_company_id = $to_company_id;
                        // 		$event_name = 'PermamentRecruitmentLeadInternational';
                        // 		$event_table_name = 'jobs';
                        // 		$event_table_id = @$jobid;
                        // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                        // 	}
                        // } else {
                        // 	return Reply::dataOnly(["status" => "warning", "message" => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                        // }

                    }

                }

                // }
            }
        }
        $insertArr = [
            'final_status' => 0,
            'to_status' => 0,

        ];

        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
        $attachments = [];

        $cc = [];
        $checkMailPermision = GeneralMethod::mailPermission(17);
        if ($checkMailPermision) {

            $mailTypeSubject = GeneralMethod::mailTypeSubject(17, 2);
            $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $to_company_id);
            $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
            $subject = str_replace(['[{JOB_TITLE}]', '[{COMPANY_NAME}]'], [$jobRow->title, $detailsVendorSide->client_company], $mailTypeSubject->subject);
            $employment_type = '';
            if ($jobRow->employment_type == 1) {
                $employment_type = 'CONTRACT';
            } else {
                $employment_type = 'FULLTIME';
            }

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currency();
            $sal_mode = $min_salary = $salary_currency = $max_salary = '';
            $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
            $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
            $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
            $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
            $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);
            $status = ucwords($jobRow->status);

            if ($jobRow->status == 'active') {
                $status = 'Hiring';
            } elseif ($jobRow->status == 'inactive') {
                $status = 'Closed';
            } else {
                $status = 'Hold';
            }

            $recommendation_text = __('messages.internal_tag');
            $ulink =
            '<a href="javascript:void(0);" class="fs-6 '.
            @$class.
            ' text-hover-success">'.
            $status.
            '</a>';
            $statusAndId = '<div class="d-flex flex-column">
										'.
                        $ulink.
                        '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                        @$jobRow->id.
                        ' -'.
                        $recommendation_text.
                        '</span>
										</div>';

            $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

            $TERMS_FIXED = '';

            $total_positions = '';
            $total_positions = number_format($jobRow->total_positions);

            $created_at = '';
            $created_at = $jobRow->created_at;
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
            $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                $newDate.
                '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                $total_positions.
                ' Opening</span>
							</div>';

            $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';
            $oprnlink = url('service_company/jobs/job_request/0');

            $jonTermsterms = "<h3><strong>Steps to Accept and Process the Job Request:</strong></h3>
								<ol>
									<li><strong>Log in</strong> to Jero.works using your email and password.</li>
									<li><strong>View the job request</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;Job Requests&quot;</strong> tab after logging in.</li>
									<li>Click <strong>&quot;Accept Job&quot;</strong> to confirm the request.</li>
									<li>Go to the <strong>&quot;All Jobs&quot;</strong> tab, locate the accepted job, and click on it.</li>
									<li>Click the <strong>&quot;Add Candidate&quot;</strong> button, upload resumes, and fill in the required details.</li>
									<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
									<li>Click on the below link to directly open the job request page<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
								</ol>

								<h3><strong>Important Note:</strong></h3>

								<ul>
									<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
									<li>If a candidate remains in <strong>&quot;In Review&quot; status, the profile will only be visible to you and your team</strong>, not the client.</li>
									<li>Ensure that you <strong>change the status to &quot;Sourcing&quot;</strong> for the client to review and take further action on the profiles.</li>
								</ul>
								';

            $template_description = str_replace(['[{VENDOR_NAME}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{HR_PHONE}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, @$jonTermsterms, $TERMS_FIXED, $statusAndId, $dateAdd], $template_description);

            $message = $template_description;

            $notificationemail = GeneralMethod::notification($to_company_id, 'Sales'); // Recuitment Sales
            $defailsEmail = DB::table('companies')->where('id', $to_company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
            //    dd($to_Mail_Id);
            $data = [
                'message' => $message,
            ];

            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.common.index',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $to_company_id
            )->onConnection('emails');

            // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);
        }

        $record = DB::table('recommendation_relation')->where('job_id', $request->current_jobid)->where('to_company_id', $request->to_company_id)->first();
        if ($record) {
            DB::table('recommendation_relation')
                ->where('id', $record->id)
                ->update($insertArr);

            $updatedId = $record->id;

            GeneralMethod::jobRequestIndexElastic($updatedId);
        }

        // DB::table('recommendation_relation')->where('job_id', $request->current_jobid)->where('to_company_id', $request->to_company_id)->update($insertArr);

        return Reply::dataOnly(['status' => 'success']);

    }

    public function send_job_recommanded_request($id)
    {
        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;
        $to_company_id = $id;

        $jobidGet = $_REQUEST['current_jobid'];

        $boostId = '';
        if (@$request->boostercom == 1) {
            $boostIdArr = explode(',', $request->boostids);
            $boostId = $boostIdArr[0];
            $boostkeywords = DB::table('boostkeywords')->where('id', $boostId)->first();
            $clickCount = $boostkeywords->clickCount + 1;
            $boostkeywords = DB::table('boostkeywords')->where('id', $boostId)->update(['clickCount' => $clickCount]);
        }

        if (Job::where('id', '=', $jobidGet)->where('status', '=', 'inactive')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is closed. Please change the job status to active.
			"]);
        } elseif (Job::where('id', '=', $jobidGet)->where('status', '=', 'hold')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is hold. Please change the job status to active."]);
        }

        $searchTermsLocation = request('kt_tagify_location');
        $custom_search_location = json_decode($searchTermsLocation, true);
        $currentJobLocation = array_column($custom_search_location, 'value');
        $job_country_id = DB::table('countries')
            ->select('countries.id')
            ->where('countries.country_name', '=', $currentJobLocation[0])
            ->first();
        $country_id_job = $job_country_id->id;
        $clientMainLocation = GeneralMethod::companyMainAddress($from_company_id);
        // dd($clientMainLocation==$country_id_job);

        $jobid = $_REQUEST['current_jobid'];
        $clientRow = DB::table('clients')->select('clients.id')->where('clients.company_id', '=', $from_company_id)->first();
        // dd($clientRow);
        // $jobRow = DB::table('jobs')->select('jobs.id', 'jobs.employment_type')->where('jobs.id', '=', $jobid)->first();
        $jobRow = DB::table('jobs')->join('job_countries', 'job_countries.job_id', '=', 'jobs.id')->select('jobs.id', 'jobs.created_at', 'jobs.status', 'jobs.total_positions', 'jobs.employment_type', 'job_countries.country_id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.employment_type', 'jobs.max_salary_currency', 'jobs.max_sal_mode', 'jobs.title', 'jobs.company_id')->where('jobs.id', '=', $jobid)->first();

        // $boostKeyWork_Auth = DB::table('package_active')
        // 	->select('package_active.id')
        // 	->where('package_active.boostkeywords_status', '=', 1)
        // 	->where('package_active.boostkeywords_balance', '>', 0)
        // 	->where('package_active.company_id', '=', $to_company_id)
        // 	->count();

        // if ($boostKeyWork_Auth > 0) {
        // 	$packageStatus = '';
        // 	$packageStatus = GeneralMethod::validateClientPackage('BOOSTKEYWORDS', $to_company_id);

        // 	if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0))
        // 	{

        // 		if (@$jobid > 0) {
        // 			$source_from_company_id = $to_company_id;
        // 			$event_name = 'BOOSTKEYWORDS';
        // 			$event_table_name = 'jobs';
        // 			$event_table_id = @$jobid;
        // 			$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
        // 		}

        // 	} else {
        // 		return Reply::dataOnly(["status" => "warning", "message" => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
        // 	}

        // }
        //   if($_REQUEST['matchcount']){
        // 	if ($clientMainLocation == $country_id_job) {

        // 		$boostKeyWork_Auth = DB::table('package_active')
        // 			->select('package_active.id')
        // 			->where('package_active.BoostKeywordsIndia_status', '=', 1)
        // 			->where('package_active.BoostKeywordsIndia_balance', '>', 0)
        // 			->where('package_active.company_id', '=', $to_company_id)
        // 			->count();

        // 		if ($boostKeyWork_Auth > 0) {
        // 			$packageStatus = '';
        // 			$packageStatus = GeneralMethod::validateClientPackage('BoostKeywordsIndia', $to_company_id);

        // 			if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) 		// BOOST STAFFING PARTNER
        // 			{

        // 				//DEDUCTION BOOST PROFILE
        // 				if (@$jobid > 0) {
        // 					$source_from_company_id = $to_company_id;
        // 					$event_name = 'BoostKeywordsIndia';
        // 					$event_table_name = 'jobs';
        // 					$event_table_id = @$jobid;
        // 					$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
        // 				}

        // 			} else {
        // 				return Reply::dataOnly(["status" => "warning", "message" => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
        // 			}

        // 		}

        // 	} else {

        // 		$boostKeyWork_Auth = DB::table('package_active')
        // 			->select('package_active.id')
        // 			->where('package_active.BoostKeywordsInternational_status', '=', 1)
        // 			->where('package_active.BoostKeywordsInternational_balance', '>', 0)
        // 			->where('package_active.company_id', '=', $to_company_id)
        // 			->count();

        // 		if ($boostKeyWork_Auth > 0) {
        // 			$packageStatus = '';
        // 			$packageStatus = GeneralMethod::validateClientPackage('BoostKeywordsInternational', $to_company_id);

        // 			if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) 		// BOOST STAFFING PARTNER
        // 			{

        // 				//DEDUCTION BOOST PROFILE
        // 				if (@$jobid > 0) {
        // 					$source_from_company_id = $to_company_id;
        // 					$event_name = 'BoostKeywordsInternational';
        // 					$event_table_name = 'jobs';
        // 					$event_table_id = @$jobid;
        // 					$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
        // 				}

        // 			} else {
        // 				return Reply::dataOnly(["status" => "warning", "message" => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
        // 			}

        // 		}

        // 	}

        //  }else{

        if ($jobRow->employment_type == 1) {
            //	CONTRACT_RECRUITMENT_LEAD   employment_type = 1
            if ($clientMainLocation == $country_id_job) {

                $eventId = 21;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

                // $packageStatus = GeneralMethod::validateClientPackage('ContractRecruitmentLeadIndia', $to_company_id);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION CONTRACT_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $to_company_id;
                // 		$event_name = 'ContractRecruitmentLeadIndia';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }

            } else {

                $eventId = 22;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

                // $packageStatus = GeneralMethod::validateClientPackage('ContractRecruitmentLeadInternational', $to_company_id);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION CONTRACT_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $to_company_id;
                // 		$event_name = 'ContractRecruitmentLeadInternational';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }

            }

        } elseif ($jobRow->employment_type == 0) {	//	FULLTIME_RECRUITMENT_LEAD   employment_type = 0
            if ($clientMainLocation == $country_id_job) {

                $eventId = 19;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

                // $packageStatus = GeneralMethod::validateClientPackage('PermamentRecruitmentLeadIndia', $to_company_id);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION FULLTIME_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $to_company_id;
                // 		$event_name = 'PermamentRecruitmentLeadIndia';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }

            } else {

                $eventId = 20;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $to_company_id, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

                // $packageStatus = GeneralMethod::validateClientPackage('PermamentRecruitmentLeadInternational', $to_company_id);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION FULLTIME_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $to_company_id;
                // 		$event_name = 'PermamentRecruitmentLeadInternational';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }

            }

        }

        //  }

        // ###################################################         START CLIENT INVITATION

        // CHECK CLIENT INVITATION SENT OR NOT
        $clientInvitationCounter = DB::table('staffing_company_invitation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('to_invitation_status', '!=', -1)
            ->count();
        if ($clientInvitationCounter == 0) {		// INVITATION SENT :- IN CASE NO INVITATION AVAIL
            // GET COMPANY DETAILS
            $company_row = DB::table('companies')->select('companies.company_email')->where('companies.id', '=', $to_company_id)->first();
            // dd($company_row);
            // INVITATION
            // staffing_company_invitation
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_client_id' => $clientRow->id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_email' => $company_row->company_email,
                'to_status' => 0,
                'job_id' => $jobid,
                'to_invitation_status' => 0,
            ];
            // dd($insertArr);

            DB::table('staffing_company_invitation')->insert($insertArr);
            $invitationId = DB::getPdo()->lastInsertId();
            GeneralMethod::clientRequestIndexElastic($invitationId);

            $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $to_company_id);
            $companyClientAddress = GeneralMethod::companyClientAddress($from_company_id);
            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];

            $cc = [];
            $checkMailPermision = GeneralMethod::mailPermission(19);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(19, 2);

                $subject = str_replace(['[{CLIENT_COMPANY_NAME}]'], [$detailsVendorSide->client_company], $mailTypeSubject->subject);

                $template_description = $mailTypeSubject->body_description;

                $template_description = str_replace(['[{CLIENT_COMPANY_NAME}]', '[{VENDOR_COMPANY_NAME}]', '[{HR_NAME}]', '[{HR_EMAILl}]', '[{HR_PHONE}]', '[{CITY_NAME}]'], [$detailsVendorSide->client_company, $detailsVendorSide->vendor_company, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, $companyClientAddress], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($to_company_id, 'Sales'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $to_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //    dd($to_Mail_Id);
                $data = [
                    'message' => $message,
                ];
                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $to_company_id
                )->onConnection('emails');

                // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);
            }

        }

        // ################################################             END CLIENT INVITATION

        // ###############################################				CHECK ACCOUNT REGISTER WITH US OR NOT

        $fromCounter = DB::table('staffing_company_relation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('final_status', '!=', -1)
            ->count();
        if ($fromCounter == 0) {
            // NOW REGISTER REQUEST SEND
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_client_id' => $clientRow->id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_client_id' => 0,
                'to_user_id' => 0,
                'to_status' => 0,
                'to_created_at' => date('Y-m-d'),

                'final_status' => 0,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),

            ];
            // DB:: table('staffing_company_relation')->insert($insertArr);
        }

        // ###############################################				END CHECK ACCOUNT REGISTER WITH US OR NOT

        // ###############################################				SEND JOB INVITATION

        $jobCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $from_company_id)
            ->where('to_company_id', $to_company_id)
            ->where('job_id', $jobid)
            ->where('final_status', '!=', -1)
            ->count();
        if ($jobCounter == 0) {
            // INSERT
            $insertArr = [
                'from_company_id' => $from_company_id,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $to_company_id,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobid,

            ];
            DB::table('recommendation_relation')->insert($insertArr);
            $relationId = DB::getPdo()->lastInsertId();
            GeneralMethod::jobRequestIndexElastic($relationId);
        }

        $basecompany = GeneralMethod::baseCompany();
        $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
        $attachments = [];

        $cc = [];
        $checkMailPermision = GeneralMethod::mailPermission(17);
        if ($checkMailPermision) {

            $mailTypeSubject = GeneralMethod::mailTypeSubject(17, 2);

            $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $to_company_id);
            $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
            $subject = str_replace(['[{JOB_TITLE}]', '[{COMPANY_NAME}]'], [$jobRow->title, $detailsVendorSide->client_company], $mailTypeSubject->subject);
            $employment_type = '';
            if ($jobRow->employment_type == 1) {
                $employment_type = 'CONTRACT';
            } else {
                $employment_type = 'FULLTIME';
            }

            $salaryMode = GeneralMethod::salaryMode();
            $currencySymbole = GeneralMethod::currency();
            $sal_mode = $min_salary = $salary_currency = $max_salary = '';
            $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
            $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
            $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
            $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
            $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

            $status = ucwords($jobRow->status);

            if ($jobRow->status == 'active') {
                $status = 'Hiring';
            } elseif ($jobRow->status == 'inactive') {
                $status = 'Closed';
            } else {
                $status = 'Hold';
            }

            // dd($status);
            $recommendation_text = __('messages.internal_tag');
            $ulink =
            '<a href="javascript:void(0);" class="fs-6 '.
            @$class.
            ' text-hover-success">'.
            $status.
            '</a>';
            $statusAndId = '<div class="d-flex flex-column">
										'.
                        $ulink.
                        '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                        @$jobRow->id.
                        ' -'.
                        $recommendation_text.
                        '</span>
										</div>';

            $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

            $TERMS_FIXED = '';

            $total_positions = '';
            $total_positions = number_format($jobRow->total_positions);

            $created_at = '';
            $created_at = $jobRow->created_at;
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
            $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                $newDate.
                '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                $total_positions.
                ' Opening</span>
							</div>';

            $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';
            $oprnlink = url('service_company/jobs/job_request/0');

            $jonTermsterms = "<h3><strong>Steps to Accept and Process the Job Request:</strong></h3>
								<ol>
									<li><strong>Log in</strong> to Jero.works using your email and password.</li>
									<li><strong>View the job request</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;Job Requests&quot;</strong> tab after logging in.</li>
									<li>Click <strong>&quot;Accept Job&quot;</strong> to confirm the request.</li>
									<li>Go to the <strong>&quot;All Jobs&quot;</strong> tab, locate the accepted job, and click on it.</li>
									<li>Click the <strong>&quot;Add Candidate&quot;</strong> button, upload resumes, and fill in the required details.</li>
									<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
									<li>Click on the below link to directly open the job request page<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
								</ol>

								<h3><strong>Important Note:</strong></h3>

								<ul>
									<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
									<li>If a candidate remains in <strong>&quot;In Review&quot; status, the profile will only be visible to you and your team</strong>, not the client.</li>
									<li>Ensure that you <strong>change the status to &quot;Sourcing&quot;</strong> for the client to review and take further action on the profiles.</li>
								</ul>
								';

            $template_description = str_replace(['[{VENDOR_NAME}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{HR_PHONE}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, @$jonTermsterms, $TERMS_FIXED, $statusAndId, $dateAdd], $template_description);

            // $jonTermssssss = @$jonTerms->terms;

            // $pdf = app('dompdf.wrapper');
            // $pdf->loadView('term_gen_pdf', compact('jonTermssssss'));

            // // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
            // $pdfContent = $pdf->output();
            // $filename = "terms-and-conditions-".$jobRow->id.".pdf";
            // // Option 1: Save PDF to storage and attach file path
            // $pdfPath = storage_path('app/public/termConditionsSendRequest/'.$filename.'');
            // file_put_contents($pdfPath, $pdfContent);
            // $hashname = str_replace('index.php','',url('storage/app/public/termConditionsSendRequest/'.$filename.''));

            // $attachments[] = [
            // 	'path' => $hashname,
            // 	'options' => []
            // ];

            $message = $template_description;

            $notificationemail = GeneralMethod::notification($to_company_id, 'Sales'); // Recuitment Sales
            $defailsEmail = DB::table('companies')->where('id', $to_company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
            //    dd($to_Mail_Id);
            $data = [
                'message' => $message,
            ];

            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.common.index',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $to_company_id
            )->onConnection('emails');

            // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);

        }

        // ###############################################				END  SEND JOB INVITATION

        return Reply::dataOnly(['status' => 'success']);

    }

    public function send_only_job_invitation(Request $request)
    {
        $jobid = $fromcompany = $tocompany = '';
        $boostId = '';
        if (@$request->boostercom == 1) {
            $boostIdArr = explode(',', $request->boostids);
            $boostId = $boostIdArr[0];
            $boostkeywords = DB::table('boostkeywords')->where('id', $boostId)->first();
            $clickCount = $boostkeywords->clickCount + 1;
            $boostkeywords = DB::table('boostkeywords')->where('id', $boostId)->update(['clickCount' => $clickCount]);
        }

        $from_company_id = auth()->user()->company_id;
        $from_user_id = $this->user->id;

        $jobid = $request->jobid;
        $fromcompany = $request->fromcompany;
        $tocompany = $request->tocompany;

        if (Job::where('id', '=', $jobid)->where('status', '=', 'inactive')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is closed. Please change the job status to active."]);
        } elseif (Job::where('id', '=', $jobid)->where('status', '=', 'hold')->exists()) {
            return Reply::dataOnly(['status' => 'closeJob', 'message' => "You can't invite job work because the job status is hold. Please change the job status to active."]);
        }

        $searchTermsLocation = request('kt_tagify_location');
        //    $custom_search_location = json_decode($searchTermsLocation, true);
        //    $currentJobLocation = array_column($custom_search_location, 'value');
        //    $job_country_id = DB::table('countries')
        // 						->select('countries.id')
        // 						->where('countries.id','=', $currentJobLocation[0])
        // 						->first();
        $country_id_job = (int) $searchTermsLocation;
        $clientMainLocation = GeneralMethod::companyMainAddress($from_company_id);
        // dd($clientMainLocation==$country_id_job);

        // 	if($clientMainLocation==$country_id_job){

        // 		$boostKeyWork_Auth = DB::table('package_active')
        // 	->select('package_active.id')
        // 	->where('package_active.BoostKeywordsIndia_status', '=', 1)
        // 	->where('package_active.BoostKeywordsIndia_balance', '>', 0)
        // 	->where('package_active.company_id', '=', $tocompany)
        // 	->count();

        // if ($boostKeyWork_Auth > 0) {
        // 	$packageStatus = '';
        // 	$packageStatus = GeneralMethod::validateClientPackage('BoostKeywordsIndia', $tocompany);

        // 	if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) 		// BOOST STAFFING PARTNER
        // 	{

        // 		//DEDUCTION BOOST PROFILE
        // 		if (@$jobid > 0) {
        // 			$source_from_company_id = $tocompany;
        // 			$event_name = 'BoostKeywordsIndia';
        // 			$event_table_name = 'jobs';
        // 			$event_table_id = @$jobid;
        // 			$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
        // 		}

        // 	} else {
        // 		return Reply::dataOnly(["status" => "warning", "message" => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
        // 	}

        // }

        // 	}else{

        // 		$boostKeyWork_Auth = DB::table('package_active')
        // 	->select('package_active.id')
        // 	->where('package_active.BoostKeywordsInternational_status', '=', 1)
        // 	->where('package_active.BoostKeywordsInternational_balance', '>', 0)
        // 	->where('package_active.company_id', '=', $tocompany)
        // 	->count();

        // if ($boostKeyWork_Auth > 0) {
        // 	$packageStatus = '';
        // 	$packageStatus = GeneralMethod::validateClientPackage('BoostKeywordsInternational', $tocompany);

        // 	if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) 		// BOOST STAFFING PARTNER
        // 	{

        // 		//DEDUCTION BOOST PROFILE
        // 		if (@$jobid > 0) {
        // 			$source_from_company_id = $tocompany;
        // 			$event_name = 'BoostKeywordsInternational';
        // 			$event_table_name = 'jobs';
        // 			$event_table_id = @$jobid;
        // 			$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
        // 		}

        // 	} else {
        // 		return Reply::dataOnly(["status" => "warning", "message" => "Boost keywords can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
        // 	}

        // }

        // 	}

        $jobRow = DB::table('jobs')->join('job_countries', 'job_countries.job_id', '=', 'jobs.id')->select('jobs.id', 'jobs.created_at', 'jobs.status', 'jobs.total_positions', 'jobs.employment_type', 'job_countries.country_id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.employment_type', 'jobs.max_salary_currency', 'jobs.max_sal_mode', 'jobs.title', 'jobs.company_id')->where('jobs.id', '=', $jobid)->first();

        // dd($jobRow);
        if ($jobRow->employment_type == 1) {		//	CONTRACT_RECRUITMENT_LEAD   employment_type = 1

            if ($clientMainLocation == $country_id_job) {

                $eventId = 21;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

                // $packageStatus = GeneralMethod::validateClientPackage('ContractRecruitmentLeadIndia', $tocompany);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION CONTRACT_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $tocompany;
                // 		$event_name = 'ContractRecruitmentLeadIndia';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }
            } else {
                $eventId = 22;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                }
                // $packageStatus = GeneralMethod::validateClientPackage('ContractRecruitmentLeadInternational', $tocompany);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION CONTRACT_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $tocompany;
                // 		$event_name = 'ContractRecruitmentLeadInternational';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }
            }

            // $packageStatus = GeneralMethod::validateClientPackage('CONTRACT_RECRUITMENT_LEAD', $tocompany);
            // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {

            // 	if (@$jobid > 0) {
            // 		$source_from_company_id = $tocompany;
            // 		$event_name = 'CONTRACT_RECRUITMENT_LEAD';
            // 		$event_table_name = 'jobs';
            // 		$event_table_id = @$jobid;
            // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
            // 	}
            // } else {
            // 	return Reply::dataOnly(["status" => "warning", "message" => "Contract Recruitment Lead can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            // }

        } elseif ($jobRow->employment_type == 0) {	//	FULLTIME_RECRUITMENT_LEAD   employment_type = 0

            if ($clientMainLocation == $country_id_job) {

                $eventId = 19;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

                // $packageStatus = GeneralMethod::validateClientPackage('PermamentRecruitmentLeadIndia', $tocompany);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION FULLTIME_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $tocompany;
                // 		$event_name = 'PermamentRecruitmentLeadIndia';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }

            } else {

                $eventId = 20;
                $coinsDeductions = GeneralMethod::coinsValidations((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                } else {
                    GeneralMethod::coinsDeductions((int) $tocompany, $eventId, auth()->user()->id, $from_company_id, '', '');
                }

                // $packageStatus = GeneralMethod::validateClientPackage('PermamentRecruitmentLeadInternational', $tocompany);
                // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {
                // 	//DEDUCTION FULLTIME_RECRUITMENT_LEAD
                // 	if (@$jobid > 0) {
                // 		$source_from_company_id = $tocompany;
                // 		$event_name = 'PermamentRecruitmentLeadInternational';
                // 		$event_table_name = 'jobs';
                // 		$event_table_id = @$jobid;
                // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
                // 	}
                // } else {
                // 	return Reply::dataOnly(["status" => "warning", "message" => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
                // }

            }

            // $packageStatus = GeneralMethod::validateClientPackage('FULLTIME_RECRUITMENT_LEAD', $tocompany);
            // if (($packageStatus['STATUS'] == 1) && ($packageStatus['EXPIRE'] == 'false') && ($packageStatus['BALANCE'] > 0)) {

            // 	if (@$jobid > 0) {
            // 		$source_from_company_id = $tocompany;
            // 		$event_name = 'FULLTIME_RECRUITMENT_LEAD';
            // 		$event_table_name = 'jobs';
            // 		$event_table_id = @$jobid;
            // 		$packageStatus = GeneralMethod::clientPackageRedeem($event_name, $event_table_name, $event_table_id, $source_from_company_id);
            // 	}
            // } else {
            // 	return Reply::dataOnly(["status" => "warning", "message" => "Fulltime Recruitment Leads can't access because Staffing partne PACKAGE EXPIRED , please contact to Administrator."]);
            // }
        }

        // dd("kdk");
        $fromCounter = DB::table('recommendation_relation')->select('*')
            ->where('from_company_id', $fromcompany)
            ->where('to_company_id', $tocompany)
            ->where('job_id', $jobid)
            ->count();

        if (($fromCounter == 0)) {
            // INSERT
            $insertArr = [
                'from_company_id' => $fromcompany,
                'from_user_id' => $from_user_id,
                'from_status' => 1,
                'from_created_at' => date('Y-m-d'),

                'to_company_id' => $tocompany,
                'to_status' => 0,
                'final_status' => 0,
                'job_id' => $jobid,

            ];

            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];

            $cc = [];
            $checkMailPermision = GeneralMethod::mailPermission(17);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(17, 2);

                $detailsVendorSide = GeneralMethod::companyAsVendor($fromcompany, $tocompany);
                $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
                $subject = str_replace(['[{JOB_TITLE}]', '[{COMPANY_NAME}]'], [$jobRow->title, $detailsVendorSide->client_company], $mailTypeSubject->subject);
                $employment_type = '';
                if ($jobRow->employment_type == 1) {
                    $employment_type = 'CONTRACT';
                } else {
                    $employment_type = 'FULLTIME';
                }

                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currency();
                $sal_mode = $min_salary = $salary_currency = $max_salary = '';
                $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
                $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
                $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
                $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
                $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

                $status = ucwords($jobRow->status);

                if ($jobRow->status == 'active') {
                    $status = 'Hiring';
                } elseif ($jobRow->status == 'inactive') {
                    $status = 'Closed';
                } else {
                    $status = 'Hold';
                }

                // dd($status);
                $recommendation_text = __('messages.internal_tag');
                $ulink =
                '<a href="javascript:void(0);" class="fs-6 '.
                @$class.
                ' text-hover-success">'.
                $status.
                '</a>';
                $statusAndId = '<div class="d-flex flex-column">
										'.
                            $ulink.
                            '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                            @$jobRow->id.
                            ' -'.
                            $recommendation_text.
                            '</span>
										</div>';

                $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

                $TERMS_FIXED = '';

                $total_positions = '';
                $total_positions = number_format($jobRow->total_positions);

                $created_at = '';
                $created_at = $jobRow->created_at;
                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
                $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                    $newDate.
                    '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                    $total_positions.
                    ' Opening</span>
							</div>';

                $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';
                $oprnlink = url('service_company/jobs/job_request/0');

                $jonTermsterms = "<h3><strong>Steps to Accept and Process the Job Request:</strong></h3>
								<ol>
									<li><strong>Log in</strong> to Jero.works using your email and password.</li>
									<li><strong>View the job request</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;Job Requests&quot;</strong> tab after logging in.</li>
									<li>Click <strong>&quot;Accept Job&quot;</strong> to confirm the request.</li>
									<li>Go to the <strong>&quot;All Jobs&quot;</strong> tab, locate the accepted job, and click on it.</li>
									<li>Click the <strong>&quot;Add Candidate&quot;</strong> button, upload resumes, and fill in the required details.</li>
									<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
									<li>Click on the below link to directly open the job request page<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
								</ol>

								<h3><strong>Important Note:</strong></h3>

								<ul>
									<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
									<li>If a candidate remains in <strong>&quot;In Review&quot; status, the profile will only be visible to you and your team</strong>, not the client.</li>
									<li>Ensure that you <strong>change the status to &quot;Sourcing&quot;</strong> for the client to review and take further action on the profiles.</li>
								</ul>
								';

                $template_description = str_replace(['[{VENDOR_NAME}]', '[{CLIENT_NAME}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{HR_NAME}]', '[{HR_EMAIL}]', '[{HR_PHONE}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $detailsVendorSide->client_name, $detailsVendorSide->client_email, $detailsVendorSide->client_phone, @$jonTermsterms, $TERMS_FIXED, $statusAndId, $dateAdd], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($tocompany, 'Sales'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $tocompany)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //    dd($to_Mail_Id);
                $data = [
                    'message' => $message,
                ];

                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $tocompany
                )->onConnection('emails');

                // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);
            }

            DB::table('recommendation_relation')->insert($insertArr);
            $relationId = DB::getPdo()->lastInsertId();
            GeneralMethod::jobRequestIndexElastic($relationId);
            $msg = 'Recommended request sent successfully !';

            return Reply::dataOnly(['status' => 'success', 'message' => $msg]);

        } else {
            $msg = 'Recommended request already sent !';

            return Reply::dataOnly(['status' => 'warning', 'message' => $msg]);
        }

    }

    public function requestapproval(Request $request)
    {

        // dd($request->all());
        $jobId = '';
        $jobId = $request->jobId;

        $rec_relation_id = (int) request()->rec_relation_id;
        $id = auth()->user()->company_id;
        $jobTerms = Job::findOrFail($jobId);
        $companyTerms = Company::withoutGlobalScope('company')->findOrFail($jobTerms->company_id);
        // dd($companyTerms,$jobTerms->company_id);
        $global_settings = DB::table('global_settings')->first();
        $companiesDetails = DB::table('recommendation_relation')
            ->select('vendor_company.company_name as vendor_company_name', 'client_company.company_name as client_company_name', 'vendor_company.address as vendor_company_address', 'client_company.address as client_company_address')
            ->join('companies as vendor_company', 'recommendation_relation.to_company_id', '=', 'vendor_company.id')
            ->join('companies as client_company', 'recommendation_relation.from_company_id', '=', 'client_company.id')
            ->where('recommendation_relation.id', '=', $rec_relation_id)
            ->get();
        // dd($companiesDetails);
        $terms = '';
        // dd(count($companiesDetails));
        if (count($companiesDetails) > 0) {
            // dd("d");
            $terms .= "By clicking the accept button, {$companiesDetails[0]->vendor_company_name} will accept the rates, terms, and conditions outlined below for the job requirements of {$companiesDetails[0]->client_company_name}.";
        }

        $terms .= '<p>&nbsp;</p>';
        $terms .= ($companyTerms->terms) ? $companyTerms->terms : $global_settings->terms;

        $termsfinal = str_replace(['[TODAY_DATE]', '[AGENCY_NAME]', '[AGENCY_ADDRESS]', '[COMPANY_NAME]', '[COMPANY_ADDRESS]'], [date('d M, Y h:i:s'), $companiesDetails[0]->vendor_company_name, $companiesDetails[0]->vendor_company_address, $companiesDetails[0]->client_company_name, $companiesDetails[0]->client_company_address], $terms);

        // dd($termsfinal);

        $rec_relation_id = '';
        $status = '';

        $rec_relation_id = $request->rec_relation_id;
        $status = $request->status;
        $current_company_id = auth()->user()->company_id;

        $clientRow = DB::table('clients')
            ->select('clients.id')
            ->where('clients.company_id', '=', $current_company_id)
            // ->where('clients.self_company', '=', 1)
            ->first();
        // dd($clientRow,$current_company_id);

        if ($clientRow == null) {
            $msg = 'ignoreClient';

            return Reply::dataOnly(['status' => $msg, 'dataCnt' => 0]);

        }

        // dd($rec_relation_id);
        $recommendation_relationRow = DB::table('recommendation_relation')
            ->select('recommendation_relation.from_company_id', 'recommendation_relation.job_id', 'recommendation_relation.from_user_id')
            ->where('recommendation_relation.id', '=', $rec_relation_id)
            // ->where('clients.self_company', '=', 1)
            ->first();
        // dd($recommendation_relationRow);
        if (empty($recommendation_relationRow)) {

            $clientExistingDoc = $this->elasticService->getClient()->get([
                'index' => ELASTIC_JOB_REQUEST,
                'id' => $rec_relation_id,
            ]);
            $recommendation_relationRow = (object) $clientExistingDoc['_source'];
            // dd($recommendation_relationRow);
        }

        $jobid = $recommendation_relationRow->job_id;
        $jobRow = DB::table('jobs')->join('job_countries', 'job_countries.job_id', '=', 'jobs.id')->select('jobs.id', 'jobs.created_at', 'jobs.status', 'jobs.total_positions', 'jobs.employment_type', 'job_countries.country_id', 'jobs.title', 'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary', 'jobs.employment_type', 'jobs.max_salary_currency', 'jobs.max_sal_mode', 'jobs.title', 'jobs.company_id')->where('jobs.id', '=', $jobid)->first();

        $data = [];
        if ($status == 'ignore') {
            $data['to_status'] = -1;
            $data['final_status'] = -1;
            $msg = 'fail';

            DB::table('recommendation_relation')->where('id', @$rec_relation_id)->delete();

            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_JOB_REQUEST,
                'id' => $rec_relation_id,
            ]);

            if ($exists) {

                // 2. Prepare the update timestamp
                $updateTime = now()->toIso8601String();

                // 3. Execute update with debugging
                $updateParams = [
                    'index' => ELASTIC_JOB_REQUEST,
                    'id' => $rec_relation_id,
                    'body' => [
                        'doc' => [
                            'to_status' => -1,
                            'final_status' => -1,
                            'to_user_id' => $this->user->id,
                            'to_created_at' => $updateTime,
                            'updated_at' => $updateTime,
                        ],
                        'doc_as_upsert' => false, // Changed to false since we verified existence
                    ],
                    'refresh' => true, // Force immediate refresh
                ];
                $this->elasticService->getClient()->update($updateParams);

            }

            $from_company_id = $recommendation_relationRow->from_company_id;
            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];

            $cc = [];
            $checkMailPermision = GeneralMethod::mailPermission(25);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(25, 1);

                // $jobid = $recommendation_relationRow->job_id;

                // $jobRow = DB::table('jobs')->join('job_countries','job_countries.job_id','=','jobs.id')->select('jobs.id', 'jobs.employment_type','job_countries.country_id','jobs.title','jobs.min_exp','jobs.max_exp','jobs.min_salary','jobs.max_salary','jobs.employment_type','jobs.max_salary_currency','jobs.max_sal_mode','jobs.title','jobs.company_id')->where('jobs.id', '=', $jobid)->first();

                $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $current_company_id);
                $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);

                $subject = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]'], [$detailsVendorSide->vendor_company, $jobRow->title, $jobRow->id], $mailTypeSubject->subject);
                $employment_type = '';
                if ($jobRow->employment_type == 1) {
                    $employment_type = 'CONTRACT';
                } else {
                    $employment_type = 'FULLTIME';
                }

                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currencySymbole();
                $sal_mode = $min_salary = $salary_currency = $max_salary = '';
                $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
                $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
                $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
                $salaryRange = $salary_currency.$min_salary.' - '.$max_salary.'/'.$sal_mode;

                $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

                $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

                $TERMS_FIXED = '';

                $template_description = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{CLIENT_COMPANY_NAME}]', '[{CLIENT_NAME}]', '[{CLIENT_PHONE}]', '[{CLIENT_EMAIL}]', '[{TERMS}]', '[{TERMS_FIXED}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, @$jonTerms->terms, $TERMS_FIXED], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($from_company_id, 'Recuitment'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $from_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //    dd($to_Mail_Id);
                $data = [
                    'message' => $message,
                ];

                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $from_company_id
                )->onConnection('emails');

                // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);
            }

        } elseif ($status == 'accept') {

            $from_company_id = $recommendation_relationRow->from_company_id;

            // dd($from_company_id);
            // $eventId = 26;
            // $coinsDeductions =  GeneralMethod::coinsValidations((int) $from_company_id,$eventId,auth()->user()->id,$current_company_id,'','');
            // // dd($coinsDeductions);
            // if($coinsDeductions->status){
            // 	return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message,'company_coins'=> $coinsDeductions->company_coins]);
            // }

            //  dd("accept",$recommendation_relationRow->from_company_id);

            $companyData = DB::table('companies')
                ->select('companies.*')
                ->where('companies.id', '=', $current_company_id)
                ->first();
            if ($companyData) {
                // dd($companyData);
                $leadCount = $companyData->leadCount + 1;
                DB::table('companies')->where('id', '=', $companyData->id)->update(['leadCount' => $leadCount]);
            }

            $client = DB::table('clients')->select('clients.id')->join('companies', 'companies.id', '=', 'clients.company_id')->where('clients.company_id', $current_company_id)->where('clients.self_company', 1)->first();

            //    if($client){
            // 	 GeneralMethod::clientStaffingPartner($client->id);
            //     }

            // dd($from_company_id);

            $job_metronics_skills = DB::table('job_metronics_skills')->select('job_metronics_skills.skill_name')->where('job_id', '=', $jobId)->where('company_id', '=', $from_company_id)->get();
            if ($job_metronics_skills) {
                $job = Job::findOrFail($jobId);
                foreach ($job_metronics_skills as $skills) {
                    $insertArr = ['company_id' => $current_company_id, 'job_id' => $jobId, 'skill_name' => $skills->skill_name];
                    if (! DB::table('job_metronics_skills')->where('company_id', '=', $current_company_id)->where('skill_name', '=', $skills->skill_name)->where('job_id', '=', $jobId)->exists()) {
                        DB::table('job_metronics_skills')->insert($insertArr);
                        $rowId = DB::getPdo()->lastInsertId();

                        $skill_name = strtolower(trim($skills->skill_name));
                        $documentId = md5($current_company_id.'_'.$skill_name);
                        $response = $this->elasticService->getClient()->update([
                            'index' => ELASTIC_COMPANY_SKILLS,
                            'id' => $documentId,
                            'retry_on_conflict' => 5,
                            'body' => [
                                'doc' => [
                                    'id' => $rowId,
                                    'company_id' => $current_company_id,
                                    'job_id' => $job->id,
                                    'skill_name' => $skill_name,
                                    'created_at' => (@$row->created_at) ? Carbon::parse(@$row->created_at)->toIso8601String() : null,
                                    'updated_at' => (@$row->updated_at) ? Carbon::parse(@$row->updated_at)->toIso8601String() : null,
                                ],
                                'doc_as_upsert' => true,
                                'detect_noop' => true,
                            ],
                        ]);

                    }
                }

            }

            $client_city_jobs = DB::table('client_city_jobs')->select('client_city_jobs.city_id', 'client_city_jobs.country_id')->where('job_id', '=', $jobId)->where('company_id', '=', $from_company_id)->get();
            if ($client_city_jobs) {
                foreach ($client_city_jobs as $city_jobs) {
                    $insertArrCity = ['company_id' => $current_company_id, 'city_id' => $city_jobs->city_id, 'country_id' => $city_jobs->country_id, 'job_id' => $jobId];
                    if (! DB::table('client_city_jobs')->where('company_id', '=', $current_company_id)->where('city_id', '=', $city_jobs->city_id)->exists()) {
                        DB::table('client_city_jobs')->insert($insertArrCity);
                    }

                }
            }

            // UPDATE INVITATION
            $invitationArr = [
                'to_status' => 1,
                'to_invitation_status' => 1,
            ];
            DB::table('staffing_company_invitation')
                ->where('staffing_company_invitation.from_company_id', $from_company_id)
                ->where('staffing_company_invitation.to_company_id', @$current_company_id)
                ->limit(1)
                ->update($invitationArr);

            // COMPANY RELATION
            $company_relation = DB::table('staffing_company_relation')
                ->select('recommendation_relation.from_company_id')
                ->where('staffing_company_relation.from_company_id', '=', $from_company_id)
                ->where('staffing_company_relation.to_company_id', '=', $current_company_id)
                // ->where('clients.self_company', '=', 1)
                ->count();

            if ($company_relation == 0) {
                $insertArr = [
                    'from_company_id' => $from_company_id,
                    'to_company_id' => $current_company_id,
                    'from_status' => 1,
                    'to_status' => 1,
                    'final_status' => 1,
                ];
                DB::table('staffing_company_relation')->insert($insertArr);
                $relationId = DB::getPdo()->lastInsertId();
                GeneralMethod::appendClientNestedData($relationId);

            }

            $data['to_status'] = 1;
            $data['final_status'] = 1;
            $msg = 'success';
            // NOW REGISTER REQUEST SEND
            $companyRelationArr = [
                'to_client_id' => $clientRow->id,
                'to_user_id' => $this->user->id,
                'to_status' => 1,
                'to_created_at' => date('Y-m-d'),
                'final_status' => 1,

                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ];
            DB::table('staffing_company_relation')
                ->where('staffing_company_relation.from_company_id', $recommendation_relationRow->from_company_id)
                ->where('staffing_company_relation.to_company_id', @$current_company_id)
                ->where('staffing_company_relation.to_status', 0)
                ->where('staffing_company_relation.final_status', 0)
                ->limit(1)
                ->update($companyRelationArr);

            $data['to_user_id'] = $this->user->id;
            $data['to_created_at'] = date('Y-m-d');
            $data['accepterms'] = $termsfinal;

            // dd($data,@$rec_relation_id);
            DB::table('recommendation_relation')->where('id', @$rec_relation_id)->limit(1)->update($data);

            $exists = $this->elasticService->getClient()->exists([
                'index' => ELASTIC_JOB_REQUEST,
                'id' => $rec_relation_id,
            ]);

            if ($exists) {

                // 2. Prepare the update timestamp
                $updateTime = now()->toIso8601String();

                // 3. Execute update with debugging
                $updateParams = [
                    'index' => ELASTIC_JOB_REQUEST,
                    'id' => $rec_relation_id,
                    'body' => [
                        'doc' => [
                            'to_status' => 1,
                            'final_status' => 1,
                            'to_user_id' => $this->user->id,
                            'to_created_at' => $updateTime,
                            'accepterms' => $termsfinal,
                            'updated_at' => $updateTime,
                        ],
                        'doc_as_upsert' => false, // Changed to false since we verified existence
                    ],
                    'refresh' => true, // Force immediate refresh
                ];
                $this->elasticService->getClient()->update($updateParams);

            }
            // Vendor Add Job
            $teamRankElastic = [
                'company_id' => $recommendation_relationRow->from_company_id,
                'user_id' => $recommendation_relationRow->from_user_id,
                'candidate_id' => 0,
                'profileUpload' => 0,
                'interviewScheduled' => 0,
                'interviewNoShow' => 0,
                'offerReleased' => 0,
                'candidateJoined' => 0,
                'jobsPosted' => 0,
                'feedbackGivenOnCVs' => 0,
                'postJobToVendorMarket' => 0,
                'outsourceJobsToVendors' => 5,
                'listBenchCandidates' => 0,
                'respondToBenchRequests' => 0,
            ];
            GeneralMethod::teamRankElastic($teamRankElastic);

            GeneralMethod::addUpdateCompanyStaffingRecommendationRelations($current_company_id);

            // ElasticCompanyIndex::dispatch($current_company_id)->onConnection('emails');

            GeneralMethod::elasticJobsAddEditUpdateIndex($jobid);
            GeneralMethod::clientStaffingPartner($client->id);
            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];

            $to_notificationemail = GeneralMethod::notification($current_company_id, 'Recuitment'); // Recuitment Sales
            $to_defailsEmail = DB::table('companies')->where('id', $current_company_id)->first()->company_email;
            $com_to_Mail_Id = $to_notificationemail->email ?? $to_defailsEmail ?? '';

            $checkMailPermision = GeneralMethod::mailPermission(45);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(45, 1);
                $cc = [];

                $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $current_company_id);
                $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
                $companyAsdress = GeneralMethod::companyClientAddress($from_company_id);
                $partnerAddess = GeneralMethod::companyClientAddress($current_company_id);

                $subject = str_replace(['[{CLIENT_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]'], [$detailsVendorSide->client_company, $jobRow->title, $jobRow->id], $mailTypeSubject->subject);
                $employment_type = '';
                if ($jobRow->employment_type == 1) {
                    $employment_type = 'CONTRACT';
                } else {
                    $employment_type = 'FULLTIME';
                }

                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currency();
                $sal_mode = $min_salary = $salary_currency = $max_salary = '';
                $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
                $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
                $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
                $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
                $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

                $status = ucwords($jobRow->status);

                if ($jobRow->status == 'active') {
                    $status = 'Hiring';
                } elseif ($jobRow->status == 'inactive') {
                    $status = 'Closed';
                } else {
                    $status = 'Hold';
                }

                $recommendation_text = __('messages.internal_tag');
                $ulink =
                '<a href="javascript:void(0);" class="fs-6 '.
                @$class.
                ' text-hover-success">'.
                $status.
                '</a>';
                $statusAndId = '<div class="d-flex flex-column">
										'.
                                $ulink.
                                '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                                @$jobRow->id.
                                ' -'.
                                $recommendation_text.
                                '</span>
										</div>';

                // $template_description = $mailTypeSubject->body_description;
                $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

                $TERMS_FIXED = "{$detailsVendorSide->vendor_company} has accepted the rates, terms, and conditions outlined below for the job requirements of  {$detailsVendorSide->client_company}";

                $total_positions = '';
                $total_positions = number_format($jobRow->total_positions);

                $created_at = '';
                $created_at = $jobRow->created_at;
                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
                $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                    $newDate.
                    '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                    $total_positions.
                    ' Opening</span>
							</div>';

                $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';
                $id = encrypt($jobRow->id);
                $oprnlink = url('/service_company/mail_jobs_ajax/'.$id.'/');

                // $oprnlink =  url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($jobRow->id) . '/all');

                $afterAcceptJobsteps = "<h3><strong>Steps to Add Candidates/Profiles to a Job:</strong></h3>
										<ol>
											<li><strong>Log in</strong> to Jero.works using your email and password.</li>
											<li><strong>View the job</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;All Jobs&quot;</strong> tab after logging in.</li>
											<li>Click on <strong>&quot;Add Candidate&quot;</strong>, upload resumes, and fill in the required details.</li>
											<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
											<li>Track scheduled interviews for your candidates in the <strong>&quot;Interview Tracker&quot;</strong> tab from the menu.</li>
											<li>Click on the below link to directly open the job page to upload the candidate profile into the job<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
										</ol>

										<h3><strong>Important Notes:</strong></h3>

										<ul>
											<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
											<li>If a candidate remains in <strong>&quot;In Review&quot;</strong> status, the profile will only be visible to you and your team, <strong>not the client</strong>.</li>
											<li>Ensure you <strong>change the status to &quot;Sourcing&quot;</strong> so the client can review and proceed with the profiles.</li>
										</ul>
										';

                $partnersData = DB::table('job_assign')
                    ->join('users', 'users.id', '=', 'job_assign.user_id')
                    ->select('users.mobile as phone', 'users.name', 'users.email')
                    ->where('job_assign.spok_user_id', '!=', '')
                    ->where('users.company_id', '=', $from_company_id)
                    ->first();

                $template_description = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{CLIENT_COMPANY_NAME}]', '[{CAPTAIN_NAME}]', '[{CAPTAIN_PHONE}]', '[{CAPTAIN_EMAIL}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $partnersData->name, $partnersData->phone, $partnersData->email, $afterAcceptJobsteps, $TERMS_FIXED, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $statusAndId, $dateAdd], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($from_company_id, 'Recuitment'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $from_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //    dd($to_Mail_Id);
                $data = [
                    'message' => $message,
                ];
                $jonTermssssss = str_replace(['[TODAY_DATE]', '[AGENCY_NAME]', '[COMPANY_NAME]', '[COMPANY_ADDRESS]', '[AGENCY_ADDRESS]'], [date('d M y'), $detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $companyAsdress, $partnerAddess], $jonTerms->terms);
                $pdf = app('dompdf.wrapper');
                $pdf->loadView('term_gen_pdf', compact('jonTermssssss'));

                // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
                $pdfContent = $pdf->output();
                $filename = 'terms-and-conditions-'.$jobRow->id.'.pdf';
                // Option 1: Save PDF to storage and attach file path
                Storage::disk('public')->makeDirectory('termConditionsSendRequest');
                $pdfPath = storage_path('app/public/termConditionsSendRequest/'.$filename.'');
                file_put_contents($pdfPath, $pdfContent);
                $hashname = str_replace('index.php', '', url('storage/app/public/termConditionsSendRequest/'.$filename.''));

                $attachments[] = [
                    'path' => $hashname,
                    'options' => [],
                ];

                QueueNotificationMailService::dispatch(
                    $com_to_Mail_Id,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $from_company_id
                )->onConnection('emails');

            }

            $cc = [];

            $checkMailPermision = GeneralMethod::mailPermission(18);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(18, 1);

                $detailsVendorSide = GeneralMethod::companyAsVendor($from_company_id, $current_company_id);
                $jobCitySelectedTag = GeneralMethod::JobcitiesTags($jobid);
                $companyAsdress = GeneralMethod::companyClientAddress($from_company_id);
                $partnerAddess = GeneralMethod::companyClientAddress($current_company_id);

                $subject = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]'], [$detailsVendorSide->vendor_company, $jobRow->title, $jobRow->id], $mailTypeSubject->subject);
                $employment_type = '';
                if ($jobRow->employment_type == 1) {
                    $employment_type = 'CONTRACT';
                } else {
                    $employment_type = 'FULLTIME';
                }

                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currency();
                $sal_mode = $min_salary = $salary_currency = $max_salary = '';
                $sal_mode = strtoupper(substr($salaryMode[$jobRow->max_sal_mode], 0, 1));
                $salary_currency = $currencySymbole[@$jobRow->max_salary_currency];
                $max_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($jobRow->min_salary);
                $salaryRange = $salary_currency.' '.$min_salary.'/'.$sal_mode.'<br> '.$salary_currency.' '.$max_salary.'/'.$sal_mode;
                $jonTerms = Company::withoutGlobalScope('company')->findOrFail($jobRow->company_id);

                $status = ucwords($jobRow->status);

                if ($jobRow->status == 'active') {
                    $status = 'Hiring';
                } elseif ($jobRow->status == 'inactive') {
                    $status = 'Closed';
                } else {
                    $status = 'Hold';
                }

                $recommendation_text = __('messages.internal_tag');
                $ulink =
                '<a href="javascript:void(0);" class="fs-6 '.
                @$class.
                ' text-hover-success">'.
                $status.
                '</a>';
                $statusAndId = '<div class="d-flex flex-column">
										'.
                                $ulink.
                                '
										<br><span class="fw-bold text-muted d-block fs-7">R'.
                                @$jobRow->id.
                                ' -'.
                                $recommendation_text.
                                '</span>
										</div>';

                // $template_description = $mailTypeSubject->body_description;
                $template_description = str_replace('[{TERMS}]', '<br>[{TERMS_FIXED}] [{TERMS}]', $mailTypeSubject->body_description);

                $TERMS_FIXED = "{$detailsVendorSide->vendor_company} has accepted the rates, terms, and conditions outlined below for the job requirements of  {$detailsVendorSide->client_company}";

                $total_positions = '';
                $total_positions = number_format($jobRow->total_positions);

                $created_at = '';
                $created_at = $jobRow->created_at;
                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); // date("d M, Y", strtotime($created_at));
                $dateAdd = '<div class="d-flex flex-column">
								<span class="text-dark fw-bolder fs-6">'.
                    $newDate.
                    '</span><br>
								<span class="text-muted fw-bold d-block fs-7">'.
                    $total_positions.
                    ' Opening</span>
							</div>';

                $exp = $jobRow->min_exp.' Y <br>'.$jobRow->max_exp.' Y';

                $id = encrypt($jobRow->id);
                $oprnlink = url('/service_company/mail_jobs_ajax/'.$id.'/');

                // $oprnlink =  url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($jobRow->id) . '/all');

                $afterAcceptJobsteps = "<h3><strong>Steps to Add Candidates/Profiles to a Job:</strong></h3>
										<ol>
											<li><strong>Log in</strong> to Jero.works using your email and password.</li>
											<li><strong>View the job</strong> from the link <a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>(Click Here)</a> or navigate to the <strong>&quot;All Jobs&quot;</strong> tab after logging in.</li>
											<li>Click on <strong>&quot;Add Candidate&quot;</strong>, upload resumes, and fill in the required details.</li>
											<li>Change the <strong>candidate status from &quot;In Review&quot; to &quot;Sourcing.&quot;</strong></li>
											<li>Track scheduled interviews for your candidates in the <strong>&quot;Interview Tracker&quot;</strong> tab from the menu.</li>
											<li>Click on the below link to directly open the job page to upload the candidate profile into the job<br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a></li>
										</ol>

										<h3><strong>Important Notes:</strong></h3>

										<ul>
											<li>The <strong>client will only have access to candidate profiles once the status is changed to &quot;Sourcing.&quot;</strong></li>
											<li>If a candidate remains in <strong>&quot;In Review&quot;</strong> status, the profile will only be visible to you and your team, <strong>not the client</strong>.</li>
											<li>Ensure you <strong>change the status to &quot;Sourcing&quot;</strong> so the client can review and proceed with the profiles.</li>
										</ul>
										';

                $template_description = str_replace(['[{VENDOR_COMPANY_NAME}]', '[{CLIENT_COMPANY_NAME}]', '[{CLIENT_NAME}]', '[{CLIENT_PHONE}]', '[{CLIENT_EMAIL}]', '[{TERMS}]', '[{TERMS_FIXED}]', '[{JOB_TITLE}]', '[{EXPERIENCE_RANGE}]', '[{SALARY_RANGE}]', '[{JOB_TYPE}]', '[{CITY_NAME}]', '[{STATUS}]', '[{DATEADD}]'], [$detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, $afterAcceptJobsteps, $TERMS_FIXED, $jobRow->title, $exp, $salaryRange, $employment_type, $jobCitySelectedTag, $statusAndId, $dateAdd], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($from_company_id, 'Recuitment'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $from_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //    dd($to_Mail_Id);
                $data = [
                    'message' => $message,
                ];
                $jonTermssssss = str_replace(['[TODAY_DATE]', '[AGENCY_NAME]', '[COMPANY_NAME]', '[COMPANY_ADDRESS]', '[AGENCY_ADDRESS]'], [date('d M y'), $detailsVendorSide->vendor_company, $detailsVendorSide->client_company, $companyAsdress, $partnerAddess], $jonTerms->terms);
                $pdf = app('dompdf.wrapper');
                $pdf->loadView('term_gen_pdf', compact('jonTermssssss'));

                // Save PDF to a temporary file or generate the raw content signature-pad.blade.php
                $pdfContent = $pdf->output();
                $filename = 'terms-and-conditions-'.$jobRow->id.'.pdf';
                // Option 1: Save PDF to storage and attach file path
                Storage::disk('public')->makeDirectory('termConditionsSendRequest');
                $pdfPath = storage_path('app/public/termConditionsSendRequest/'.$filename.'');
                file_put_contents($pdfPath, $pdfContent);
                $hashname = str_replace('index.php', '', url('storage/app/public/termConditionsSendRequest/'.$filename.''));

                $attachments[] = [
                    'path' => $hashname,
                    'options' => [],
                ];

                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.common.index',
                    (object) $data,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $from_company_id
                )->onConnection('emails');

                $teamsEmail = DB::table('users')->select('users.*')->where('company_id', $current_company_id)->where('root_user', 1)->first();
                $values = [
                    'company_id' => $current_company_id,
                    'user_id' => $teamsEmail->id,
                    'job_id' => $jobRow->id,
                    'spok_user_id' => $teamsEmail->id,
                ];
                DB::table('job_assign')->insert($values);

                /** Start send mail add job accept request */
                $job = Job::find($jobId);
                $employmentType = GeneralMethod::employmentType();
                $employment_type = (int) $job->employment_type;
                $employment_type = $employmentType[$employment_type];

                $strAdded = DB::table('clients')->select('companies.company_name', 'clients.name', 'clients.self_company')->join('companies', 'companies.id', '=', 'clients.company_id')->where('clients.id', $job->client_id)->first();

                $contact_person_name = '';
                if ($strAdded->self_company == 1) {
                    $contact_person_name = $strAdded->company_name;
                } else {
                    $contact_person_name = $strAdded->company_name;
                    if ($current_company_id == $job->company_id) {
                        $contact_person_name = $strAdded->name;
                    }

                }
                $currency = GeneralMethod::currencySymbole();
                $currencyGet = GeneralMethod::currencySymboleforexcell();
                $salaryMode = GeneralMethod::salaryMode();
                $max_salary = $min_salary = $max_sal_mode = $min_sal_mode = '';
                $max_salary = GeneralMethod::thousandsCurrencyFormat($job->max_salary);
                $min_salary = GeneralMethod::thousandsCurrencyFormat($job->min_salary);

                $sal_mode = strtoupper(mb_substr($salaryMode[$job->max_sal_mode], 0, 1));
                $salary_currency = $currency[@$job->max_salary_currency];

                $work_type = 'Work From Office';
                if ($job->work_type == 'no') {
                    $work_type = 'Work From Office';
                } elseif ($job->work_type == 'yes') {
                    $work_type = 'Remote';
                } elseif ($job->work_type == 'other') {
                    $work_type = 'Hybrid';
                }

                $client_loc = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')->where('client_location_jobs.job_id', @$jobId)->first();

                $client_loc = DB::table('client_city_jobs')
                    ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                    ->join('state_list', 'state_list.id', '=', 'city_list.state_id')
                    ->join('countries', 'countries.id', '=', 'state_list.country_id')
                    ->select('city_list.city', 'countries.id as country_id', 'countries.country_name', 'city_list.city as city_name', 'state_list.state as state_name')
                    ->where('client_city_jobs.job_id', $jobId)
                    ->get();

                // dd($client_loc);

                // $country_id = @$client_loc->country_id;
                $location = '';

                if (count($client_loc) > 0) {
                    // $country_name = GeneralMethod::getCountryName($country_id);
                    foreach ($client_loc as $key => $cval) {
                        $location .= (++$key).') '.$cval->city_name.' | '.$cval->state_name.' | '.$cval->country_name.'<br>';
                        // $country_name = ucwords($cval['sortname']);
                    }
                }

                $jobAdditional = Job::Select('clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path', 'job_categories.name as cat_name', 'qualification_master.name as qualification_name')
                    ->join('clients', 'clients.id', '=', 'jobs.client_id')
                    ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
                    ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
                    ->findOrFail(@$jobId);

                $recruiter = DB::table('users')
                    ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
                    ->where('job_assign.job_id', $jobId)->pluck('users.name')->toArray();

                $recruiterdata = (implode(',', $recruiter));

                $hiring_namager = DB::table('client_contact_info')
                    ->join('client_contact_info_jobs', 'client_contact_info_jobs.client_contact_info_id', '=', 'client_contact_info.id')
                    ->where('client_contact_info_jobs.job_id', $jobId)->pluck('client_contact_info.contact_person_name')->toArray();
                $hiring_namagerdata = (implode(',', $hiring_namager));

                $cc = [];

                $basecompany = GeneralMethod::baseCompany();
                $senderName = (auth()->user()->name) ? auth()->user()->name : '';
                $fromAddress = (auth()->user()->email) ? auth()->user()->email : '';
                $attachments = [];
                $subject = 'New Job Added';
                $checkMailPermision = GeneralMethod::mailPermission(10);
                if ($checkMailPermision) {
                    $company_id = auth()->user()->company_id;
                    $mailTypeSubject = GeneralMethod::mailTypeSubject(10, 1);
                    $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');

                    if ($job->status == 'active') {
                        $status_job = "<strong><span style='color:#27ae60'>Hiring</span></strong>";
                    } elseif ($job->status == 'inactive') {
                        $status_job = "<strong><span style='color:#ff0202'>Closed</span></strong>";
                    } else {
                        $status_job = "<strong><span style='color:#ffab00'>Hold</span></strong>";
                    }

                    $subject = str_replace(['[{JOB_TITLE}]', '[{ClientCompany}]', '[{Location}]', '[{JobID}]'], [@$job->title, @$contact_person_name, @$location, @$job->id], $mailTypeSubject->subject);

                    $template_description = str_replace(['[{status}]', '[{clientCompany}]', '[{job_id}]', '[{job_title}]', '[{experience}]', '[{salaryrange}]', '[{NoofOpenings}]', '[{location}]', '[{remote}]', '[{JobType}]', '[{Department}]', '[{Qualification}]', '[{hiringManager}]', '[{recruiter}]', '[{addedby}]', '[{JobDescriptions}]'], [@$status_job, @$contact_person_name, @$job->id, @$job->title, @$job->min_exp.'-'.@$job->max_exp.' Years', $currencyGet[@$job->min_salary_currency].' '.@$job->min_salary.' to '.@$job->max_salary.' '.$sal_mode,  @$job->total_positions, @$location, @$work_type, @$employment_type, $jobAdditional->cat_name, $jobAdditional->qualification_name, @$hiring_namagerdata, $recruiterdata, @$request->user()->name, @$job->job_description], $mailTypeSubject->body_description);

                    $data = [
                        'message' => $template_description,
                    ];

                    $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
                    $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

                    QueueNotificationMailService::dispatch(
                        $teamsEmail->email,
                        $subject,
                        'email.common.index',
                        (object) $data,
                        $attachments,
                        $fromAddress,
                        $senderName,
                        $cc,
                        $company_id
                    )->onConnection('emails');
                }

                /** End send mail add job accept request */

                // dd("k",$to_Mail_Id);

                // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $data, $attachments, $fromAddress, $senderName,$cc);
            }

            $cachekeys = [
                'alljobslist_version_user_'.$this->user->id,
                'alljobslist_version_company_'.$current_company_id,
                'allclientrequisition_version_user_'.$this->user->id,
                'allclientrequisition_version_company_'.$current_company_id,
                'alljobrequest_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

        }

        GeneralMethod::elasticJobsAddEditUpdateIndex($jobid);

        $dataCnt = $this->dataCnt();

        return Reply::dataOnly(['status' => $msg, 'dataCnt' => $dataCnt]);

    }

    public function clienthmlisting($id)
    {
        // dd( $userPermissions);
        $this->currentJobId = $id;
        $this->recruiter = [];
        $company_id = auth()->user()->company_id;
        $clientCompanyId = DB::table('jobs')->select('jobs.company_id')->where('jobs.id', '=', $this->currentJobId)->first();
        $this->team = DB::table('users')
            ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.from_user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.mobile',
                'job_assign.id as job_assign_id',
                'job_assign.job_id',
                'job_assign.spok_user_id',
                'roles.display_name',
                'recommendation_relation.from_user_id',
                'users_profile.designation'
            )
            ->where('users.company_id', $clientCompanyId->company_id)
            ->where('job_assign.job_id', $this->currentJobId)
            ->groupBy('users.id')
            ->get();
        $company_or_client = GeneralMethod::companyDetailsByClient($this->currentJobId);
        $this->client_types = DB::table('clients')->where('company_id', $clientCompanyId->company_id)->first();
        $this->logo = $this->client_types->logo ?? null;
        // dd($this->logo);
        // dd($this->company_or_client);
        $view = view('service_company.jobs.clienthmlisting', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'company_or_client' => $company_or_client, 'logo' => $this->logo]);
    }

    public function btlisting(Request $request, $id)
    {
        // dd( $userPermissions);
        $this->currentJobId = $id;
        $this->recruiter = [];
        $company_id = auth()->user()->company_id;
        $clientCompanyId = DB::table('jobs')->select('jobs.company_id')->where('jobs.id', '=', $this->currentJobId)->first();
        $this->team = DB::table('users')
            ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('recommendation_relation', 'recommendation_relation.from_user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.mobile',
                'job_assign.id as job_assign_id',
                'job_assign.job_id',
                'job_assign.spok_user_id',
                'roles.display_name',
                'recommendation_relation.from_user_id',
                'users_profile.designation'
            )
            ->where('users.company_id', $clientCompanyId->company_id)
            ->where('job_assign.job_id', $this->currentJobId)
            ->groupBy('users.id')
            ->get();

        $this->job = Job::find($id);

        $this->jobCitySelected = DB::table('client_city_jobs')
            ->where('client_city_jobs.company_id', $this->job->company_id)
            ->where('client_city_jobs.job_id', $this->job->id)
            ->pluck('client_city_jobs.city_id')
            ->toArray();

        $this->default_loc = GeneralMethod::allCitiesSelected($this->jobCitySelected);

        $gdFilterData = GeneralMethod::gdFilterData($this->job->id);
        // dd($gdFilterData);
        $this->min_exp = ($gdFilterData->min_exp >= 1) ? $gdFilterData->min_exp - 1 : $gdFilterData->min_exp;
        $this->max_exp = $gdFilterData->max_exp + 1;
        $this->min_salary_currency = $request->cur;
        $this->min_salary = $gdFilterData->min_salary;
        $this->max_salary = $gdFilterData->max_salary;
        $this->min_sal_mode = $request->mod;
        $this->default_skill = $gdFilterData->default_skill;

        $company_or_client = GeneralMethod::companyDetailsByClient($this->currentJobId);
        $this->client_types = DB::table('clients')->where('company_id', $clientCompanyId->company_id)->first();
        $this->logo = $this->client_types->logo ?? null;
        $this->cities = GeneralMethod::allCities();
        // dd($this->cities);
        // dd($this->company_or_client);
        $view = view('service_company.cvtracker.bench-talents', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'company_or_client' => $company_or_client, 'logo' => $this->logo]);
    }

    public function termshmlisting(Request $request, $id)
    {
        $recommendation_relation_id = (int) request()->recommendation_relation_id;
        $this->currentJobId = $id;
        $jobTerms = Job::findOrFail($id);
        $companyTerms = Company::withoutGlobalScope('company')->findOrFail($jobTerms->company_id);
        $global_settings = DB::table('global_settings')->first();
        $companiesDetails = DB::table('recommendation_relation')
            ->select('vendor_company.company_name as vendor_company_name', 'client_company.company_name as client_company_name', 'recommendation_relation.accepterms')
            ->join('companies as vendor_company', 'recommendation_relation.to_company_id', '=', 'vendor_company.id')
            ->join('companies as client_company', 'recommendation_relation.from_company_id', '=', 'client_company.id')
            ->where('recommendation_relation.id', '=', $recommendation_relation_id)
            ->first();
        // dd($companiesDetails);
        $terms = '';
        // dd(count($companiesDetails));
        if ($companiesDetails) {
            // dd("d");
            $terms .= "By clicking the accept button, {$companiesDetails->vendor_company_name} will accept the rates, terms, and conditions outlined below for the job requirements of {$companiesDetails->client_company_name}.";
            $companyTerms->terms = $companiesDetails->accepterms ?? null;
        }

        $terms .= '<p>&nbsp;</p>';
        $terms .= ($companyTerms->terms) ? $companyTerms->terms : $global_settings->terms;

        return Reply::dataOnly(['status' => 'success', 'terms' => $terms]);
        // dd($jobTerms->company_id,$companyTerms->terms);
    }

    public function termshmlistingaccept($id)
    {
        $companiesDetails = DB::table('recommendation_relation')
            ->select('recommendation_relation.accepterms')
            ->where('recommendation_relation.id', '=', $id)
            ->first();
        // dd($companiesDetails);
        $terms = ($companiesDetails) ? $companiesDetails->accepterms : '';
        // dd(count($companiesDetails));

        return Reply::dataOnly(['status' => 'success', 'terms' => $terms]);
        // dd($jobTerms->company_id,$companyTerms->terms);
    }

    public function currentteamlisting($id)
    {
        $this->currentJobId = $id;
        $this->recruiter = [];
        $company_id = auth()->user()->company_id;
        $clientCompanyId = DB::table('jobs')->select('jobs.company_id')->where('jobs.id', '=', $this->currentJobId)->first();
        $this->team = DB::table('users')
            ->leftJoin('job_assign', function ($join) use ($id, $company_id) {
                $join->on('job_assign.user_id', '=', 'users.id');
                $join->on('job_assign.job_id', '=', DB::raw("'".$id."'"));
                $join->on('job_assign.company_id', '=', DB::raw("'".$company_id."'"));
            })
            ->leftjoin('role_user', 'role_user.user_id', 'users.id')
            ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
            ->leftjoin('roles', 'roles.id', 'role_user.role_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.working_status',
                'job_assign.id as job_assign_id',
                'job_assign.job_id',
                'job_assign.spok_user_id',
                'roles.display_name',
                'users_profile.designation'
            )
            ->where('users.company_id', $company_id)
            ->where('users.status', 'active')
            ->groupBy('users.id')
            ->get();
        $view = view('service_company.jobs.currentteamlisting', $this->data)->render();
        $this->client_types = DB::table('clients')->where('company_id', $company_id)->first();
        $this->logo = $this->client_types->logo ?? null;

        return Reply::dataOnly(['status' => 'success', 'view' => $view, 'logo' => $this->logo]);
    }

    public function auction_send_job_request(Request $request)
    {

        // dd("hii");

        $jobidGet = $request->current_jobid;
        $global_settings = DB::table('global_settings')->select('global_settings.*')->first();

        $company_id = auth()->user()->company_id;

        $companyTerms = Company::withoutGlobalScope('company')->findOrFail($company_id);

        $terms = '';
        // dd(count($companiesDetails));

        $cachekeys = [
            'allsendinvitationtoagency_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        $terms .= '<p>&nbsp;</p>';
        $terms .= ($companyTerms->terms) ? $companyTerms->terms : $global_settings->terms;

        if ($global_settings) {

            $job = Job::findOrFail($jobidGet);

            if ($job->status == 'inactive' || $job->status == 'hold') {
                $msg = "Please update the job status from 'Closed/Hold' to 'Hiring' to enable posting for agency partners";

                return Reply::dataOnly(['status' => 'closeJob', 'message' => $msg]);
            }

            if ($request->job_cancel == 1) {
                $text_change = 'Publish to Marketplace';
                $color_change = 'btn-light-danger';
                $job->public_market_status = 6;
                $msg = 'Your job Cancel for partners Job !';
                $job->save();
                \App\Services\MarketJobV2Service::invalidateGlobalCache();

                return Reply::dataOnly(['status' => 'closeJob', 'message' => $msg, 'text_change' => $text_change, 'color_change' => $color_change]);
            }
            if ($global_settings->public_market_job_status) {
                $job->public_market_status = 2;
                $msg = 'Your job public for partners';
                $text_change = 'Unpublish Job';
                $color_change = 'btn-light-danger';
            } else {
                $job->public_market_status = 1;
                $msg = 'Request sent for approval to an administrator';
                $text_change = 'Approval Pending';
                $color_change = 'btn-light-warning';
                $this->class_send = 'post_to_public';
            }
            $job->job_requirement = $terms;
            $job->save();
            \App\Services\MarketJobV2Service::invalidateGlobalCache();

            $teamRankElastic = [
                'company_id' => $this->user->company_id,
                'user_id' => $this->user->id,
                'candidate_id' => 0,
                'profileUpload' => 0,
                'interviewScheduled' => 0,
                'interviewNoShow' => 0,
                'offerReleased' => 0,
                'candidateJoined' => 0,
                'jobsPosted' => 0,
                'feedbackGivenOnCVs' => 0,
                'postJobToVendorMarket' => 2,
                'outsourceJobsToVendors' => 0,
                'listBenchCandidates' => 0,
                'respondToBenchRequests' => 0,
            ];
            GeneralMethod::teamRankElastic($teamRankElastic);

            GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);

            DB::table('market_job_added_count')->where('job_id', '=', $jobidGet)->where('status', '=', 0)->update(['status' => 1]);

            return Reply::dataOnly(['status' => 'success', 'message' => $msg, 'text_change' => $text_change, 'color_change' => $color_change]);
        } else {
            $msg = 'Recommended request already sent !';

            return Reply::dataOnly(['status' => 'warning', 'message' => $msg]);
        }

    }

    /**
     * Render the Edit Requisition form with prefilled job details.
     *
     * Intent: Load job requisition details for editing with Redis caching for tenant reference data and consolidated single-pass queries.
     * Tenancy Note: Scoped strictly by company_id and job_id to prevent cross-tenant data leaks.
     *
     * @param  string  $id  Encrypted Job ID
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $this->pageTitle = __('Edit Requisition');
        abort_if(Gate::denies('edit_jobs'), 403);
        $id = GeneralMethod::value_decrypt($id);

        $userId = (int) auth()->user()->id;
        $company_id = (int) $this->user->company_id;

        // 1. User + company profile — cached 1 hour per user
        $userCompanyProfile = Cache::remember('user_profile:'.$userId, 3600, function () use ($userId) {
            return User::join('companies', 'companies.id', '=', 'users.company_id')
                ->leftJoin('users_profile', 'users_profile.user_id', '=', 'users.id')
                ->select('users_profile.country', 'companies.logo', 'companies.company_name')
                ->where('users.id', '=', $userId)
                ->first();
        });

        $this->country = @$userCompanyProfile->country;
        $this->companylogo = @$userCompanyProfile->logo;
        $this->company_name = @$userCompanyProfile->company_name;

        // 2. Primary Job Model load
        $this->job = Job::where('company_id', $company_id)->findOrFail($id);

        // 3. Tenant-scoped categories and clients — cached 5 min
        $this->categories = Cache::remember('tenant:'.$company_id.':categories', 300, function () use ($company_id) {
            return JobCategory::where('company_id', $company_id)->get();
        });

        $this->client = Cache::remember('tenant:'.$company_id.':clients', 300, function () use ($company_id) {
            return Client::where('company_id', $company_id)->orderBy('clients.name', 'ASC')->get();
        });

        // 4. Placeholders for view properties unused on edit form
        $this->locations = collect();
        $this->skills = collect();
        $this->questions = collect();
        $this->jobQuestion = [];

        $this->consultancy = DB::table('users')->select('id', 'name')->where('type', 1)->where('company_id', $company_id)->where('status', 'active')->get();
        $this->team = DB::table('users')->select('id', 'name')->where('type', 0)->where('company_id', $company_id)->where('status', 'active')->get();

        $this->lArr = '';
        $this->uArr = '';
        if ($this->job->client_id > 0) {
            $this->lArr = DB::table('client_location')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->join('clients', 'clients.id', '=', 'client_location.client_id')
                ->select('client_location.id', 'client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location.client_id', $this->job->client_id)
                ->get();

            $this->uArr = DB::table('client_contact_info')
                ->join('clients', 'clients.id', '=', 'client_contact_info.client_id')
                ->select('client_contact_info.id', 'client_contact_info.contact_person_name')
                ->where('clients.company_id', $this->job->client_id)
                ->get();
        }

        $this->employmentType = GeneralMethod::employmentType();
        $this->priority = GeneralMethod::priority();

        // 5. Consolidated Qualification Master query (single execution + memory tag map)
        $this->qualification = Cache::remember('global_qualification_master', 86400, function () {
            return DB::table('qualification_master')
                ->select('*')
                ->whereNotIn('id', [60])
                ->orderBy('qualification_master.display_order', 'ASC')
                ->get();
        });
        $this->qualificationTag = $this->qualification->pluck('name');

        $this->jobAssign = DB::table('job_assign')
            ->select('user_id')
            ->where('company_id', $company_id)
            ->where('job_id', $id)
            ->get();

        // 6. Consolidated Job Additional Detail query (eliminate duplicate count query)
        $this->additionalDetail = DB::table('job_additional_detail')
            ->select('*')
            ->where('company_id', $company_id)
            ->where('job_id', $id)
            ->get()
            ->toArray();
        $this->additionalDetailCount = count($this->additionalDetail);

        // 7. Contact person list & recruiter list
        $this->contactPersonList = DB::table('client_contact_info_jobs')
            ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
            ->select('client_contact_info.contact_person_name')
            ->where('client_contact_info_jobs.job_id', $id)
            ->get();

        $this->recruiterlist = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.name', 'users.id')
            ->where('job_assign.company_id', $company_id)
            ->where('job_assign.job_id', $id)
            ->where('users.type', '=', 0)
            ->get();

        $this->job_metronics_skills = DB::table('job_metronics_skills')
            ->select('skill_name')
            ->where('company_id', $company_id)
            ->where('job_id', $id)
            ->get();

        $this->page_event = 'EDIT';
        $this->currency = GeneralMethod::currency();
        $this->salaryMode = GeneralMethod::salaryMode();

        $this->jobLocation = DB::table('client_location_jobs')
            ->where('client_location_jobs.company_id', $company_id)
            ->where('client_location_jobs.job_id', $id)
            ->pluck('client_location_jobs.client_location_id')
            ->toArray();

        $this->cities = '';

        $this->countries = Cache::rememberForever('global_countries_list', function () {
            return DB::table('countries')->get();
        });

        $this->valSelected = DB::table('job_countries')
            ->where('job_countries.company_id', $company_id)
            ->where('job_countries.job_id', $id)
            ->pluck('job_countries.country_id')
            ->toArray();

        $this->jobCitySelected = DB::table('client_city_jobs')
            ->where('client_city_jobs.company_id', $company_id)
            ->where('client_city_jobs.job_id', $id)
            ->pluck('client_city_jobs.city_id')
            ->toArray();

        $this->default_loc = GeneralMethod::allCitiesSelected($this->jobCitySelected);
        $this->default_qualification = GeneralMethod::allQualificationSelected($id);

        // 8. Coin rates & company balance — cached 5 min / 1 min
        $this->job_coins = Cache::remember('coin_event:operational:company:'.$company_id.':event:2', 300, function () use ($company_id) {
            return DB::table('coin_order_events')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $company_id)
                ->where('coin_event_id', '=', 2)
                ->orderBy('id', 'DESC')
                ->first();
        });
        $this->candidates_coins = Cache::remember('coin_event:operational:company:'.$company_id.':event:4', 300, function () use ($company_id) {
            return DB::table('coin_order_events')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $company_id)
                ->where('coin_event_id', '=', 4)
                ->orderBy('id', 'DESC')
                ->first();
        });
        $this->company_coins = Cache::remember('company_coins:company:'.$company_id, 60, function () use ($company_id) {
            return DB::table('company_coins')
                ->where('event_type', '=', 'operational')
                ->where('company_id', '=', $company_id)
                ->orderBy('id', 'DESC')
                ->first();
        });

        return view('service_company.jobs.create', $this->data);
    }

    /**
     * Update an existing Job Requisition.
     *
     * Intent: Delegates job requisition update, batch pivot synchronization, and async ES indexing to JobService.
     * Tenancy Note: Scoped strictly by company_id via authenticated user context.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreJob $request, $id, JobService $jobService)
    {
        abort_if(Gate::denies('edit_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $job = Job::where('company_id', $company_id)->findOrFail($id);

        try {
            $jobService->updateJob($job, $request->all(), auth()->user());

            return Reply::redirect(route('service_company.jobs.index'), __('menu.jobs').' '.__('messages.updatedSuccessfully'));
        } catch (\Throwable $e) {
            \Log::error('jobs update() transaction failed: '.$e->getMessage(), ['exception' => $e]);

            return Reply::dataOnly(['status' => 'error', 'msgType' => 'danger', 'message' => 'Update failed. Please try again.']);
        }
    }

    public function editJobStatus($id)
    {
        $this->id = $id;
        if ($this->id) {
            if (config('feature.use_three_drawers_v3')) {
                // V3 Step 2: job query cached; view rendered fresh so CSRF token is never stale.
                $this->job = Cache::remember("v3:status_job:{$id}", 3600, function () use ($id) {
                    return DB::table('jobs')->where('jobs.id', '=', $id)->first();
                });

                // V3 Step 7: ETag from job status+updated_at.
                // 304 skips the lastStatusChange lookup + view render entirely.
                // ETag auto-changes when newUpdateJobstatus busts the v3:status_job:{id} cache key.
                if ($this->job) {
                    $etag = '"v3:status:'.$id.':'.md5($this->job->status.$this->job->updated_at).'"';
                    if (request()->header('If-None-Match') === $etag) {
                        return response(null, 304)->header('ETag', $etag);
                    }
                }

                $this->lastStatusChange = Cache::remember("v3:status_log:{$id}", 3600, function () use ($id) {
                    return DB::table('job_status_logs')
                        ->leftJoin('users', 'users.id', '=', 'job_status_logs.changed_by')
                        ->select('job_status_logs.created_at', 'users.name as changed_by_name')
                        ->where('job_status_logs.job_id', $id)
                        ->orderByDesc('job_status_logs.created_at')
                        ->first();
                });
            } else {
                $this->job = DB::table('jobs')
                    ->where('jobs.id', '=', $this->id)
                    ->first();

                // "Last updated by" footer — most recent status change of any kind, for attribution only.
                $this->lastStatusChange = DB::table('job_status_logs')
                    ->leftJoin('users', 'users.id', '=', 'job_status_logs.changed_by')
                    ->select('job_status_logs.created_at', 'users.name as changed_by_name')
                    ->where('job_status_logs.job_id', $this->id)
                    ->orderByDesc('job_status_logs.created_at')
                    ->first();
            }
        }
        $view = view('service_company.jobs.status', $this->data)->render();
        // dd($view);
        // V3 Step 7: attach ETag to 200 so the browser can validate on the next open.
        if (config('feature.use_three_drawers_v3') && isset($etag)) {
            return response()->json(['status' => 'success', 'view' => $view])
                ->header('ETag', $etag)
                ->header('Cache-Control', 'private, max-age=0, must-revalidate');
        }

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function newUpdateJobstatus(Request $request)
    {

        $company_id = '';
        $company_id = auth()->user()->company_id;
        $job_id = $_REQUEST['job_id'];
        $status = $_REQUEST['status'];
        $rawToStatus = $status; // raw enum value, kept before $status gets swapped for the "Hiring" display label below

        $data = [];

        $data['status'] = $status;

        $job = Job::find($job_id);

        // A job posted by another company and merely recommended/assigned to
        // this agency (a "Direct Client" job on the Client Jobs tab) is not
        // this company's to change the status of — only the company that
        // owns the job (jobs.company_id) may transition it. Without this,
        // any agency with access to the status drawer could flip a client's
        // own job to Closed/Hold/Hiring.
        if (! $job || (int) $job->company_id !== (int) $company_id) {
            return Reply::dataOnly(['status' => 'error', 'message' => 'Only the company that owns this job can change its status.']);
        }

        if ($status == 'active') {

            $today = Carbon::today();
            $auto_job_coins_jobs = Carbon::parse($job->auto_date_job);
            $diffInDaysJobs = abs($today->diffInDays($auto_job_coins_jobs));

            if ($diffInDaysJobs > 0) {
                $coinsRequ = 1;
                $companyId = auth()->user()->company_id;
                $eventId = 32;
                $coinsDeductions = GeneralMethod::coinsValidationsAllcompany($companyId, $eventId, $coinsRequ);
                if ($coinsDeductions->status) {
                    return Reply::dataOnly(['status' => 'coinCheck', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
                }
            }
            // dd($coinsDeductions);

            if ($job->closeType == 1) {
                $start_date = Carbon::parse($job->start_date);
                $end_date = Carbon::parse($job->end_date);

                $daysDifference = $start_date->diffInDays($end_date);

                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d', strtotime($start_date.' + '.@$daysDifference.' days'));
                $job->start_date = $start_date;

                $data['auto_date_job'] = $start_date;
                // start_date must be persisted too, not just set on the in-memory $job object —
                // otherwise it stays frozen at the original creation date forever while end_date
                // keeps advancing each cycle, so daysDifference (and the auto-close duration
                // derived from it) grows longer every time the job is reopened instead of staying
                // fixed at the originally-selected N days.
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                // Keep closeType=1 (auto-close still configured) so this recompute runs again on every
                // future close -> reopen cycle, not just the first one. Previously reset to 0 here, which
                // silently disabled the "auto-close in N days" behaviour after a single reactivation.
                $data['closeType'] = 1;
            }

            $data['auto_date_job'] = Carbon::today();

            if ($diffInDaysJobs > 0) {
                $eventId = 32;
                GeneralMethod::coinsDeductions($companyId, $eventId, auth()->user()->id, $company_id, $job->id, '');

            }

        }
        if ($status == 'inactive') {
            $data['public_market_status'] = 0;
        }

        DB::table('jobs')->where('id', @$job_id)->limit(1)->update($data);

        // V3 Step 9 (drift prevention): invalidate immediately after the DB write and BEFORE
        // anything that can throw (JobStatusLog, mail). If we invalidated after and an exception
        // occurred mid-method, the V3 cache would silently serve the old status for up to 1 hour.
        if (config('feature.use_three_drawers_v3')) {
            Cache::forget("v3:status_job:{$job_id}");
            Cache::forget("v3:status_log:{$job_id}");
        }

        // Status Timeline audit row for the Job Status drawer — one append-only entry per transition.
        JobStatusLog::create([
            'job_id' => $job_id,
            'company_id' => $company_id,
            'from_status' => $job->status,
            'to_status' => $rawToStatus,
            'changed_by' => auth()->id(),
            'created_at' => Carbon::now(),
        ]);

        $this->activeJobCounter = DB::table('jobs')->select('id')->where('status', '=', 'active')->where('company_id', '=', $company_id)->count();
        $this->inactiveJobCounter = DB::table('jobs')->select('id')->where('status', '=', 'inactive')->where('company_id', '=', $company_id)->count();
        $this->holdJobCounter = DB::table('jobs')->select('id')->where('status', '=', 'hold')->where('company_id', '=', $company_id)->count();

        if ($job->status == 'active') {
            $jobStatus = 'Hiring';
        } elseif ($job->status == 'inactive') {
            $jobStatus = 'Closed';
        } else {
            $jobStatus = 'Hold';
        }
        if ($status == 'active') {
            $status = 'Hiring';
        }

        $client_loc = DB::table('client_location_jobs')
            ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
            ->select('client_location.address', 'client_location.city', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
            ->where('client_location_jobs.job_id', @$job_id)
            ->first();

        $Company = Company::find(auth()->user()->company_id);

        $companyOrClientName = GeneralMethod::companyOrClientName($job->client_id);

        $subject = "Status Change | $jobStatus to $status  | $job->title | $companyOrClientName->companyOrclientName | $job->id ";

        $data = [
            'username' => auth()->user()->name,
            'status_pr' => $jobStatus,
            'status_curr' => $status,
            'job_title' => @$job->title,
            'job_id' => 'J'.@$job->id,
            'companyName' => $Company->company_name,
            'location' => @$client_loc->city_name,
            'companyOrclientName' => @$companyOrClientName->companyOrclientName,
            'selfCompany' => @$companyOrClientName->selfCompany,
            'NoofOpenings' => @$job->total_positions,
        ];
        // dd($data);

        $assignTeamemail = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->where('users.company_id', $company_id)
            ->where('job_assign.job_id', $job->id)
            ->pluck('users.email')->toArray();

        // dd($assignTeamemail,$job->id);

        $basecompany = GeneralMethod::baseCompany();
        // $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
        // $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
        $senderName = (auth()->user()->name) ? auth()->user()->name : '';
        $fromAddress = (auth()->user()->email) ? auth()->user()->email : '';
        $attachments = [];
        $cc = $assignTeamemail;

        $checkMailPermision = GeneralMethod::mailPermission(11);
        if ($checkMailPermision) {
            $company_id = auth()->user()->company_id;
            $mailTypeSubject = GeneralMethod::mailTypeSubject(11, 1);
            $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
            $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';

            QueueNotificationMailService::dispatch(
                $to_Mail_Id,
                $subject,
                'email.requisitions.requisition-update-status',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $company_id
            )->onConnection('emails');

            // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.requisitions.requisition-update-status',(object) $data, $attachments, $fromAddress, $senderName,$cc);
        }

        $m_n_id = 0;
        if ($status == 'active') {
            $m_n_id = 44;
        } elseif ($status == 'hold') {
            $m_n_id = 42;
        } elseif ($status == 'inactive') {
            $m_n_id = 43;
        }

        $checkMailPermision = GeneralMethod::mailPermission($m_n_id);

        if ($checkMailPermision && $m_n_id > 0) {

            $company_id = auth()->user()->company_id;
            $mailTypeSubject = GeneralMethod::mailTypeSubject($m_n_id, 1);
            $notificationemail = GeneralMethod::notification($company_id, 'Recuitment');
            $currentJobId = $job_id;
            $currentUserId = auth()->user()->id;
            $max_salary = $min_salary = $max_sal_mode = $min_sal_mode = '';
            $job = Job::find($currentJobId);
            $employmentType = GeneralMethod::employmentType();
            $employment_type = (int) $job->employment_type;
            $employment_type = $employmentType[$employment_type];
            $currency = GeneralMethod::currencySymbole();
            $currencyGet = GeneralMethod::currencySymboleforexcell();
            $salaryMode = GeneralMethod::salaryMode();

            $sal_mode = strtoupper(mb_substr($salaryMode[$job->max_sal_mode], 0, 1));
            $salary_currency = $currency[@$job->max_salary_currency];

            $recruiter = DB::table('users')
                ->join('job_assign', 'job_assign.user_id', '=', 'users.id')
                ->where('job_assign.job_id', $currentJobId)->pluck('users.name')->toArray();

            $recruiterdata = (implode(',', $recruiter));

            $hiring_namager = DB::table('client_contact_info')
                ->join('client_contact_info_jobs', 'client_contact_info_jobs.client_contact_info_id', '=', 'client_contact_info.id')
                ->where('client_contact_info_jobs.job_id', $currentJobId)->pluck('client_contact_info.contact_person_name')->toArray();
            $hiring_namagerdata = (implode(',', $hiring_namager));

            $strAdded = DB::table('clients')->select('companies.company_name', 'clients.name', 'clients.self_company')->join('companies', 'companies.id', '=', 'clients.company_id')->where('clients.id', $job->client_id)->first();
            $contact_person_name = '';
            if ($strAdded->self_company == 1) {
                $contact_person_name = $strAdded->company_name;
            } else {
                $contact_person_name = $strAdded->company_name;
                if ($company_id == $job->company_id) {
                    $contact_person_name = $strAdded->name;
                }

            }

            $jobAdditional = Job::Select('clients.name as client_name', 'clients.description as client_description', 'clients.logo_logical_path', 'job_categories.name as cat_name', 'qualification_master.name as qualification_name')
                ->join('clients', 'clients.id', '=', 'jobs.client_id')
                ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
                ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
                ->findOrFail($job->id);

            $oprnlink = url('/service_company/apptracker/clientapp/'.GeneralMethod::value_encrypt($job->id).'/all');

            $subject = str_replace(['[{JOB_TITLE}]', '[{ClientCompany}]', '[{Location}]', '[{JobID}]'], [@$job->title, @$contact_person_name, @$job->id], $mailTypeSubject->subject);

            $maillink = "You may access and manage the job posting using the link below:<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>Click here to open the job</a> <br><br> If the above link does not open directly, you may copy and paste the below link it into your browser.<br><br><a href='".$oprnlink."' style='color: #080feb; font-weight: bold;'>".$oprnlink.'</a>';

            if ($job->status == 'active') {
                $status_job = "<strong><span style='color:#27ae60'>Hiring</span></strong>";
            } elseif ($job->status == 'inactive') {
                $status_job = "<strong><span style='color:#ff0202'>Closed</span></strong>";
            } else {
                $status_job = "<strong><span style='color:#ffab00'>Hold</span></strong>";
            }

            $template_description = str_replace(['[{status}]', '[{clientCompany}]', '[{job_id}]', '[{job_title}]', '[{experience}]', '[{salaryrange}]', '[{NoofOpenings}]', '[{location}]', '[{remote}]', '[{JobType}]', '[{Department}]', '[{Qualification}]', '[{hiringManager}]', '[{recruiter}]', '[{addedby}]', '[{JobDescriptions}]', '[{maillink}]'], [@$status_job, @$contact_person_name, @$job->id, @$job->title, @$job->min_exp.'-'.@$job->max_exp.' Years', $currencyGet[@$job->min_salary_currency].' '.@$job->min_salary.' to '.@$job->max_salary.' '.$sal_mode,  @$job->total_positions, @$client_loc->city_name, @$work_type, @$employment_type, $jobAdditional->cat_name, $jobAdditional->qualification_name, @$hiring_namagerdata, $recruiterdata, @$request->user()->name, @$job->job_description, $maillink], $mailTypeSubject->body_description);

            $data = [
                'message' => $template_description,
            ];

            $defailsEmail = DB::table('companies')->where('id', $company_id)->first()->company_email;
            $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
            $teamsEmail = DB::table('users')->where('users.id', $currentUserId)->where('company_id', $company_id)->first();

            QueueNotificationMailService::dispatch(
                $teamsEmail->email,
                $subject,
                'email.common.index',
                (object) $data,
                $attachments,
                $fromAddress,
                $senderName,
                $cc,
                $currentUserId
            )->onConnection('emails');
        }

        // Mail::to(auth()->user()->email)->send(new RequisitionUpdateStatusMail((object)$data,$subject));

        // GeneralMethod::elasticJobsUpdateIndex($job_id);

        // ElasticCompanyIndex::dispatch($company_id)->onConnection('emails');
        GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);

        $cachekeys = [
            'alljobslist_version_user_'.$this->user->id,
            'jobDetails_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }

        return Reply::dataOnly([
            'status' => 'success',
            'activeJobs' => $this->activeJobCounter,
            'inactiveJobs' => $this->inactiveJobCounter,
            'holdJobs' => $this->holdJobCounter,
        ]);

    }

    public function clientviewmore($id)
    {

        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->employmentType = GeneralMethod::employmentType();
        $this->job = DB::table('jobs')
            ->Select(
                'jobs.id',
                'jobs.title',
                'jobs.work_type',
                'jobs.slug',
                'jobs.company_id',
                'jobs.job_description',
                'jobs.job_requirement',
                'jobs.total_positions',
                'jobs.work_shifts',
                'jobs.location_id',
                'jobs.category_id',
                'jobs.start_date',
                'jobs.end_date',
                'jobs.status',
                'jobs.required_columns',
                'jobs.meta_details',
                'jobs.section_visibility',
                'jobs.created_at',
                'jobs.updated_at',
                'jobs.priority',
                'jobs.client_id',
                'jobs.min_exp',
                'jobs.max_exp',
                'jobs.min_salary',
                'jobs.max_salary',
                'jobs.qualification',
                'jobs.employment_type',
                'jobs.min_salary_currency',
                'jobs.max_salary_currency',
                'jobs.min_sal_mode',
                'jobs.max_sal_mode',
                'jobs.carrier_website',
                'clients.name as client_name',
                'clients.description as client_description',
                'clients.logo_logical_path',
                'job_categories.name as cat_name',
                'qualification_master.name as qualification_name'
            )
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->where('jobs.id', '=', $id)
            ->first();

        $this->skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();
        // Company Data
        $company_arr = DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
        $this->company_name = @$company_arr->company_name;
        $this->logo_path = url('img/client-default.png');
        if (! empty(@$company_arr->logo) && strlen(@$company_arr->logo) > 10) {
            $this->logo_path = url('public/user-uploads/company-logo/'.@$company_arr->logo);
        }
        $this->category_name = 'N/A';
        $this->jobLocation = '';

        if ($this->job->id > 0) {

            $this->jobLocation = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                // ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->first();

            $this->jobLocationCount = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->join('countries', 'countries.id', '=', 'client_location.country_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'countries.country_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->count();

            $this->clientContactPersonListing = DB::table('client_contact_info_jobs')
                ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                ->select('client_contact_info.contact_person_name')
                ->where('client_contact_info_jobs.job_id', $this->job->id)
                ->get();

        }

        $this->assignTeamCount = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->count();

        // RECRUITER LIST
        $this->recruiterlist = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->join('role_user', 'role_user.user_id', 'users.id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('add_job_wise_condidates', function ($join) {
                $join->on('add_job_wise_condidates.job_id', '=', 'job_assign.job_id')
                    ->on('add_job_wise_condidates.submitted_by', '=', 'users.id');
            })
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.image',
                'users.mobile',
                'roles.display_name',
                DB::raw('count(add_job_wise_condidates.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')
            )
            ->where('users.company_id', $company_id)
            ->where('job_assign.job_id', $this->job->id)
            ->where('users.type', '=', 0)
            ->groupBy('users.id')
            ->orderBy('users.name', 'DESC')
            ->get();
        // dd($this->recruiterlist);

        $companiesDetails = DB::table('recommendation_relation')
            ->select('recommendation_relation.accepterms')
            ->where('recommendation_relation.to_company_id', auth()->user()->company_id)
            ->where('recommendation_relation.job_id', '=', $this->job->id)
            ->first();
        // dd($companiesDetails);
        $this->terms = ($companiesDetails) ? $companiesDetails->accepterms : '';

        $this->conactPersonListing = DB::table('job_assign')
            ->join('jobs', 'jobs.id', '=', 'job_assign.job_id')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'job_assign.user_id')
                    ->on('users.company_id', '=', 'jobs.company_id');
            })
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->jobCitySel = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->where('client_city_jobs.job_id', $this->job->id)
            ->pluck('city_list.city')
            ->toArray();

        if ($this->job->work_type == 'yes') {
            $this->jobCitySelected = 'Remote';
        } elseif ($this->job->work_type == 'no') {
            $this->jobCitySelected = implode(',', $this->jobCitySel);
        } elseif ($this->job->work_type == 'any') {
            $this->jobCitySelected = 'Remote/'.implode(',', $this->jobCitySel);
        } elseif ($this->job->work_type == 'other') {
            $this->jobCitySelected = ''.implode(',', $this->jobCitySel);
        }

        $view = view('service_company.jobs.clientviewmore', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    // Full-page job details for market-jobs "View Requirement" link.
    // Same data as clientviewmore() but returns a full Blade page instead of JSON.
    public function marketJobDetail(Request $request, $id)
    {
        abort_if(Gate::denies('view_jobs'), 403);

        $company_id = auth()->user()->company_id;
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->employmentType = GeneralMethod::employmentType();
        $this->job = DB::table('jobs')
            ->Select(
                'jobs.id', 'jobs.title', 'jobs.work_type', 'jobs.slug', 'jobs.company_id',
                'jobs.job_description', 'jobs.job_requirement', 'jobs.total_positions',
                'jobs.work_shifts', 'jobs.location_id', 'jobs.category_id',
                'jobs.start_date', 'jobs.end_date', 'jobs.status',
                'jobs.required_columns', 'jobs.meta_details', 'jobs.section_visibility',
                'jobs.created_at', 'jobs.updated_at', 'jobs.priority', 'jobs.client_id',
                'jobs.min_exp', 'jobs.max_exp', 'jobs.min_salary', 'jobs.max_salary',
                'jobs.qualification', 'jobs.employment_type',
                'jobs.min_salary_currency', 'jobs.max_salary_currency',
                'jobs.min_sal_mode', 'jobs.max_sal_mode', 'jobs.carrier_website',
                'clients.name as client_name', 'clients.description as client_description',
                'clients.logo_logical_path', 'job_categories.name as cat_name',
                'qualification_master.name as qualification_name'
            )
            ->join('clients', 'clients.id', '=', 'jobs.client_id')
            ->leftJoin('job_categories', 'job_categories.id', '=', 'jobs.category_id')
            ->leftJoin('qualification_master', 'qualification_master.id', '=', 'jobs.qualification')
            ->where('jobs.id', '=', $id)
            ->first();

        abort_if(! $this->job, 404);

        $this->skillArr = DB::table('job_metronics_skills')->select('*')->where('job_id', $this->job->id)->get();

        $company_arr = DB::table('companies')->select('*')->where('id', $this->job->company_id)->first();
        $this->company_name = @$company_arr->company_name;
        $this->logo_path = url('img/client-default.png');
        if (! empty(@$company_arr->logo) && strlen(@$company_arr->logo) > 10) {
            $this->logo_path = url('public/user-uploads/company-logo/'.@$company_arr->logo);
        }

        $this->jobLocation = '';
        if ($this->job->id > 0) {
            $this->jobLocation = DB::table('client_location_jobs')
                ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                ->select('client_location.city', 'client_location.address', 'client_location.postcode', 'client_location.country_id', 'city_list.city as city_name')
                ->where('client_location_jobs.job_id', $this->job->id)
                ->orderBy('client_location_jobs.id', 'DESC')
                ->first();

            $this->clientContactPersonListing = DB::table('client_contact_info_jobs')
                ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                ->select('client_contact_info.contact_person_name')
                ->where('client_contact_info_jobs.job_id', $this->job->id)
                ->get();
        }

        $this->recruiterlist = DB::table('job_assign')
            ->join('users', 'users.id', '=', 'job_assign.user_id')
            ->join('role_user', 'role_user.user_id', 'users.id')
            ->join('roles', 'roles.id', 'role_user.role_id')
            ->leftJoin('add_job_wise_condidates', function ($join) {
                $join->on('add_job_wise_condidates.job_id', '=', 'job_assign.job_id')
                    ->on('add_job_wise_condidates.submitted_by', '=', 'users.id');
            })
            ->select(
                'users.id', 'users.name', 'users.email', 'users.image', 'users.mobile',
                'roles.display_name',
                DB::raw('count(add_job_wise_condidates.id) as allCandidate'),
                DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 3 THEN 1 ELSE 0 END) AS Interview'),
                DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')
            )
            ->where('users.company_id', $company_id)
            ->where('job_assign.job_id', $this->job->id)
            ->where('users.type', '=', 0)
            ->groupBy('users.id')
            ->orderBy('users.name', 'DESC')
            ->get();

        $companiesDetails = DB::table('recommendation_relation')
            ->select('recommendation_relation.accepterms')
            ->where('recommendation_relation.to_company_id', $company_id)
            ->where('recommendation_relation.job_id', '=', $this->job->id)
            ->first();
        $this->terms = ($companiesDetails) ? $companiesDetails->accepterms : '';

        $this->conactPersonListing = DB::table('job_assign')
            ->join('jobs', 'jobs.id', '=', 'job_assign.job_id')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'job_assign.user_id')
                    ->on('users.company_id', '=', 'jobs.company_id');
            })
            ->select('users.id', 'users.name', 'users.email')
            ->where('job_assign.job_id', $this->job->id)
            ->orderBy('users.name', 'DESC')
            ->get();

        $this->jobCitySel = DB::table('client_city_jobs')
            ->join('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
            ->where('client_city_jobs.job_id', $this->job->id)
            ->pluck('city_list.city')
            ->toArray();

        if ($this->job->work_type == 'yes') {
            $this->jobCitySelected = 'Remote';
        } elseif ($this->job->work_type == 'no') {
            $this->jobCitySelected = implode(', ', $this->jobCitySel);
        } elseif ($this->job->work_type == 'any') {
            $this->jobCitySelected = 'Remote / '.implode(', ', $this->jobCitySel);
        } else {
            $this->jobCitySelected = implode(', ', $this->jobCitySel);
        }

        return view('service_company.jobs.market-job-detail', $this->data);
    }

    public function removeJob(Request $request)
    {

        $company_id = $jobid = $fromcompany = $tocompany = '';
        $company_id = auth()->user()->company_id;
        $jobid = $request->jobid;
        $fromcompany = $request->fromcompany;
        $tocompany = $request->tocompany;

        if (! empty($jobid) && ! empty($company_id) && ! empty($tocompany)) {
            $data = [];
            $data['final_status'] = 2;
            $data['from_status'] = 2;
            DB::table('recommendation_relation')->where('from_company_id', @$fromcompany)->where('to_company_id', @$tocompany)->where('job_id', @$jobid)->where('final_status', 1)->limit(1)->update($data);

            $recommendation_relation = DB::table('recommendation_relation')->where('from_company_id', @$fromcompany)->where('to_company_id', @$tocompany)->where('job_id', @$jobid)->where('final_status', 2)->first();

            GeneralMethod::jobRequestIndexElastic($recommendation_relation->id);
            GeneralMethod::elasticJobsAddEditUpdateIndex($jobid);

            $cachekeys = [
                'alljobslist_version_user_'.$this->user->id,
                'allclientrequisition_version_user_'.$this->user->id,
                'alljobrequest_version_user_'.$this->user->id,
            ];
            foreach ($cachekeys as $key) {
                GeneralMethod::cacheClean($key);
            }

        }

        return Reply::dataOnly(['status' => 'success']);

    }

    public function add_to_list(Request $request)
    {

        // dd($request->all());

        $jobId = $request->jobId;
        $recomndedId = $request->recomndedId;
        $condidateId = $request->condidateId;
        // dd($condidateId);

        // $query = DB::table('job_applications')->where('id',$condidateId)->update(['job_id'=>$jobId]);

        $jobs = JobApplication::join('city_list', 'city_list.city_id', '=', 'job_applications.city_id')->select('job_applications.*', 'city_list.city')->where('job_applications.id', $condidateId)->first();

        $rec_candidate_assign = DB::table('rec_candidate_assign')
            ->where('rec_candidate_assign.id', $recomndedId)
            ->limit(1)
            ->first();

        $eventId = 26;
        $coinsDeductions = GeneralMethod::coinsValidations((int) $rec_candidate_assign->source_from_company_id, $eventId, auth()->user()->id, $rec_candidate_assign->source_company_id, '', '');

        if ($coinsDeductions->status) {
            return Reply::dataOnly(['status' => 'info', 'message' => $coinsDeductions->message, 'company_coins' => $coinsDeductions->company_coins]);
        }

        // dd($coinsDeductions);

        $company_id = auth()->user()->company_id;
        $user_id = auth()->user()->id;
        $note_text = '';
        if ((int) $jobs->company_id == (int) $company_id) {
            $master_status_id = 8;
            $note_text = 'Recommend Request =>Internal Bench Talent';
        } else {
            $master_status_id = 1;
            $note_text = 'Recommend Request =>Agency Bench Talent';
        }

        // dd($jobs); company_id 	submitted_by

        $jobRow = Job::findOrFail($jobId);
        if ((int) $jobs->company_id != (int) $company_id) {
            $insertArr = ['job_id' => $jobId, 'job_application_id' => $condidateId, 'rec_candidate_assign_id' => $recomndedId, 'status_id' => 2, 'master_status_id' => $master_status_id, 'thirdparty_company_id' => $jobs->company_id, 'child_status_id' => 0, 'company_id' => $company_id, 'submitted_by' => $user_id];
        } else {
            $insertArr = ['job_id' => $jobId, 'job_application_id' => $condidateId, 'rec_candidate_assign_id' => $recomndedId, 'status_id' => 2, 'master_status_id' => $master_status_id, 'child_status_id' => 0, 'company_id' => $company_id, 'submitted_by' => $user_id];

        }

        //  dd($recomndedId);

        // dd($company_id,$jobRow->company_id);

        $query_update = DB::table('add_job_wise_condidates')->insert($insertArr);

        $cachekeys = [
            'allcandidatesrequest_version_user_'.$this->user->id,
        ];
        foreach ($cachekeys as $key) {
            GeneralMethod::cacheClean($key);
        }
        $lastWiseIs = DB::getPdo()->lastInsertId();
        if ($query_update) {
            GeneralMethod::JobApplicationsAddEditUpload($lastWiseIs);
            GeneralMethod::elasticBenchCandidateAddEditIndex($condidateId);
        }
        $row_data = DB::table('job_applications')->select('*')->where('id', @$condidateId)->first();
        $note = $note_text;
        $timelineArr = [
            'event_id' => 1,
            'event_by_company_id' => auth()->user()->company_id,
            'event_by_user_id' => auth()->user()->id,
            'master_process_name' => 'Sourcing',
            // 'child_process_name'	=>
            'application_id' => $condidateId,
            'application_email' => $row_data ? @$row_data->email : '',
            'job_id' => $jobId,
            'note' => $note,
        ];
        GeneralMethod::UpdateTimeline($timelineArr);

        $eventId = 26;
        $coinsDeductions = GeneralMethod::coinsDeductions((int) $rec_candidate_assign->source_from_company_id, $eventId, auth()->user()->id, $rec_candidate_assign->source_company_id, '', '');

        DB::table('rec_candidate_assign')
            ->where('rec_candidate_assign.id', $recomndedId)
            ->limit(1)
            ->update(['ac_status' => 1]);

        $exists = $this->elasticService->getClient()->exists([
            'index' => ELASTIC_CANDIDATES_REQUEST,
            'id' => $recomndedId,
        ]);

        if ($exists) {

            // 2. Prepare the update timestamp
            $updateTime = now()->toIso8601String();

            // 3. Execute update with debugging
            $updateParams = [
                'index' => ELASTIC_CANDIDATES_REQUEST,
                'id' => $recomndedId,
                'body' => [
                    'doc' => [
                        'ac_status' => 1,
                        'updated_at' => $updateTime,
                    ],
                    'doc_as_upsert' => false, // Changed to false since we verified existence
                ],
                'refresh' => true, // Force immediate refresh
            ];

            $this->elasticService->getClient()->update($updateParams);

            $teamRankElastic = [
                'company_id' => $this->user->company_id,
                'user_id' => $this->user->id,
                'candidate_id' => 0,
                'profileUpload' => 0,
                'interviewScheduled' => 0,
                'interviewNoShow' => 0,
                'offerReleased' => 0,
                'candidateJoined' => 0,
                'jobsPosted' => 0,
                'feedbackGivenOnCVs' => 0,
                'postJobToVendorMarket' => 0,
                'outsourceJobsToVendors' => 0,
                'listBenchCandidates' => 0,
                'respondToBenchRequests' => 4,
            ];
            GeneralMethod::teamRankElastic($teamRankElastic);

        }

        if ($query_update) {

            $queryget = DB::table('rec_candidate_assign')->where('id', $recomndedId)->first();
            $detailsVendorSide = GeneralMethod::companyAsVendor($jobRow->company_id, $queryget->source_from_company_id);
            $basecompany = GeneralMethod::baseCompany();
            $senderName = ($basecompany->companyName) ? $basecompany->companyName : '';
            $fromAddress = ($basecompany->companyEmail) ? $basecompany->companyEmail : '';
            $attachments = [];

            $cc = [];
            $checkMailPermision = GeneralMethod::mailPermission(22);
            if ($checkMailPermision) {

                $mailTypeSubject = GeneralMethod::mailTypeSubject(22, 2);

                $salaryMode = GeneralMethod::salaryMode();
                $currencySymbole = GeneralMethod::currencySymbole();
                $bill_rate_mode = $bill_rate_currency = $bill_rate_amount = '';
                $bill_rate_mode = strtoupper(substr($salaryMode[$jobs->bill_rate_mode], 0, 1));
                $bill_rate_currency = $currencySymbole[@$jobs->bill_rate_currency];
                $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($jobs->bill_rate_amount);
                $salaryRange = $bill_rate_currency.$bill_rate_amount.'/'.$bill_rate_mode;

                $subject = str_replace(['[{BENCH_CANDIDATE_NAME}]', '[{BENCH_ID}]'], [$jobs->full_name, $jobs->id], $mailTypeSubject->subject);
                $template_description = $mailTypeSubject->body_description;
                $template_description = str_replace(['[{CLIENT_COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_ID}]', '[{VENDOR_COMPANY_NAME}]', '[{BENCH_CANDIDATE_NAME}]', '[{BILL_RATE}]', '[{EXPERENCE}]', '[{LOCATION}]', '[{CONTACT_PERSON_NAME}]', '[{PHONE}]', '[{EMAIL}]', '[{SKILL}]'], [$detailsVendorSide->client_company, $jobRow->title, $jobRow->id, $detailsVendorSide->vendor_company, $jobs->full_name, $salaryRange, $jobs->total_exp, $jobs->city, $detailsVendorSide->vendor_name, $detailsVendorSide->vendor_phone, $detailsVendorSide->vendor_email, $jobs->skills], $template_description);

                $message = $template_description;

                $notificationemail = GeneralMethod::notification($queryget->source_from_company_id, 'Sales'); // Recuitment Sales
                $defailsEmail = DB::table('companies')->where('id', $queryget->source_from_company_id)->first()->company_email;
                $to_Mail_Id = $notificationemail->email ?? $defailsEmail ?? '';
                //    dd($to_Mail_Id);
                $datamail = [
                    'message' => $message,
                ];

                QueueNotificationMailService::dispatch(
                    $to_Mail_Id,
                    $subject,
                    'email.common.index',
                    (object) $datamail,
                    $attachments,
                    $fromAddress,
                    $senderName,
                    $cc,
                    $queryget->source_from_company_id
                )->onConnection('emails');

                // $result = $this->notificationMailService->sendEmail($to_Mail_Id, $subject,'email.common.index', (object) $datamail, $attachments, $fromAddress, $senderName,$cc);
                // dd($to_Mail_Id,$result);
            }

            $res = [
                'status' => true,
            ];

        } else {
            $res = [
                'status' => false,
            ];

        }

        return $res;

    }

    public function jobServices()
    {
        $this->pageTitle = '';

        return view('service_company.jobs.services', $this->data);
    }

    public function serviceviewmore(Request $request, $id)
    {
        // dd($id);
        abort_if(Gate::denies('view_jobs'), 403);
        $company_id = auth()->user()->company_id;
        $this->salaryMode = GeneralMethod::salaryMode();
        $this->currency = GeneralMethod::currency();
        $this->replacementPeriods = self::COMMERCIAL_REPLACEMENT_PERIODS;
        $this->replacementTypes = self::COMMERCIAL_REPLACEMENT_TYPES;
        $this->replacementEligibility = self::COMMERCIAL_REPLACEMENT_ELIGIBILITY;
        $this->billingFrequencies = self::COMMERCIAL_BILLING_FREQUENCIES;
        $this->billingTriggers = self::COMMERCIAL_BILLING_TRIGGERS;
        $this->paymentTerms = self::COMMERCIAL_PAYMENT_TERMS;
        $this->employmentType = GeneralMethod::employmentType();
        // Intent: load the global range master from cache, replacing an old empty cache entry left by a pre-master deployment.
        $commercialExperienceBands = Cache::get('commercial_experience_bands:v1');
        if (collect($commercialExperienceBands)->isEmpty()) {
            $commercialExperienceBands = DB::table('commercial_experience_bands')
                ->select(['id', 'code', 'label', 'min_experience', 'max_experience', 'sort_order'])
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get();

            Cache::put('commercial_experience_bands:v1', $commercialExperienceBands, 3600);
        }
        $this->commercialExperienceBands = $commercialExperienceBands;

        $commericials = DB::table('commericials')
            ->where('commtype', $id)
            ->where('company_id', $company_id)
            ->orderBy('commtype', 'asc')
            ->get()
            ->map(function ($commericial) {
                $commericial_details = DB::table('commericial_details')
                    ->where('commericial_id', $commericial->id)
                    ->get()
                    ->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'commericial_id' => $detail->commericial_id,
                            'exp' => $detail->exp,
                            'rate' => $detail->rate,
                            'company_id' => $detail->company_id,
                            'commtype' => $detail->commtype,
                        ];
                    })
                    ->toArray();

                //  dd($commericial, $commericial_details);

                $commericial_countries = DB::table('commericial_countries')
                    ->where('commericial_id', $commericial->id)
                    ->pluck('country_id')
                    ->toArray();

                return [
                    'id' => $commericial->id,
                    'company_id' => $commericial->company_id,
                    'rateType' => $commericial->rateType,
                    'commtype' => $commericial->commtype,
                    'status' => $commericial->status,
                    'sal_mode' => $commericial->sal_mode,
                    'salary_currency' => $commericial->salary_currency,
                    'replacement_period_days' => $commericial->replacement_period_days,
                    'replacement_type' => $commericial->replacement_type,
                    'replacement_eligibility' => $commericial->replacement_eligibility,
                    'billing_frequency' => $commericial->billing_frequency,
                    'billing_trigger' => $commericial->billing_trigger,
                    'payment_terms_days' => $commericial->payment_terms_days,
                    'commericial_details' => $commericial_details,
                    'commericial_countries' => $commericial_countries,
                ];
            });

        $this->commericials = $commericials;
        //    dd($commericials[0]['sal_mode']);
        // return response()->json($commericials); salary_currency status

        // dd($commericials);

        $this->industries = DB::table('td_industries')->get();
        $this->countries = DB::table('countries')->get();
        $this->default_rates = DB::table('default_rates')->first();
        $this->sectionCheck = $id;

        // dd($this->industries);

        //    dd($this->default_rates);

        $view = view('service_company.jobs.services_view', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);

    }

    public function jobServicesAddUpdate(Request $request)
    {

        $company_id = auth()->user()->company_id;
        $rateType = $request->rateTypeClass;
        //  dd($rateType);
        $commType = $request->commType;
        $sal_mode = $request->sal_mode;
        $main_status = ($request->main_status) ? $request->main_status : 0;
        $salary_currency = $request->salary_currency;

        // Intent: accept only the controller-defined commercial policy values for this tenant's service record.
        $replacementPeriod = array_key_exists((int) $request->replacement_period_days, self::COMMERCIAL_REPLACEMENT_PERIODS) ? (int) $request->replacement_period_days : 90;
        $replacementType = array_key_exists((string) $request->replacement_type, self::COMMERCIAL_REPLACEMENT_TYPES) ? (string) $request->replacement_type : 'free_replacement';
        $replacementEligibility = array_key_exists((string) $request->replacement_eligibility, self::COMMERCIAL_REPLACEMENT_ELIGIBILITY) ? (string) $request->replacement_eligibility : 'early_exit';
        $billingFrequency = array_key_exists((string) $request->billing_frequency, self::COMMERCIAL_BILLING_FREQUENCIES) ? (string) $request->billing_frequency : 'one_time';
        $billingTrigger = array_key_exists((string) $request->billing_trigger, self::COMMERCIAL_BILLING_TRIGGERS) ? (string) $request->billing_trigger : 'candidate_joined';
        $paymentTermsDays = array_key_exists((int) $request->payment_terms_days, self::COMMERCIAL_PAYMENT_TERMS) ? (int) $request->payment_terms_days : 30;
        $policyFields = ['replacement_period_days' => $replacementPeriod, 'replacement_type' => $replacementType, 'replacement_eligibility' => $replacementEligibility, 'billing_frequency' => $billingFrequency, 'billing_trigger' => $billingTrigger, 'payment_terms_days' => $paymentTermsDays];

        $company = Company::withoutGlobalScope('company')->findOrFail($company_id);

        if ($commType == 1) {
            $status = 0;
            if ($company->company_type == 1) {
                $status = 1;
            } elseif ($company->company_type == 2) {
                $status = 1; // IT Service Company
            } else {
                $status = 0; // Product Company
            }

            $value = array_merge(['company_id' => $company_id, 'rateType' => $rateType, 'commtype' => $commType, 'sal_mode' => $sal_mode, 'salary_currency' => $salary_currency, 'status' => $status], $policyFields);
        } else {
            $status = 0;
            if ($company->company_type == 1) {
                $status = 1;   // Staffing Company
            } elseif ($company->company_type == 2) {
                $status = 1; // IT Service Company
            } else {
                $status = 0; // Product Company
            }

            $value = array_merge(['company_id' => $company_id, 'rateType' => $rateType, 'commtype' => $commType, 'sal_mode' => $sal_mode, 'salary_currency' => $salary_currency, 'status' => $status], $policyFields);
        }
        // Intent: persist rates against the active global range-master indexes so agency rate cards and this editor share one definition.
        $experienceIndexes = Cache::remember('commercial_experience_bands:v1', 3600, function () {
            return DB::table('commercial_experience_bands')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get(['min_experience', 'max_experience']);
        })->flatMap(function ($band) {
            return range((int) $band->min_experience, (int) $band->max_experience);
        })->unique()->values()->all();

        DB::table('commericials')->where('company_id', $company_id)->where('commtype', $commType)->delete();
        $commericialId = DB::table('commericials')->insertGetId($value);
        if (count($request->rate_val) > 0 && count($experienceIndexes) > 0) {
            DB::table('commericial_details')->where('company_id', $company_id)->where('commtype', $commType)->delete();
            foreach ($experienceIndexes as $position => $experienceIndex) {
                if (! array_key_exists($position, $request->rate_val)) {
                    continue;
                }

                $value = ['company_id' => $company_id, 'commericial_id' => $commericialId, 'commtype' => $commType, 'exp' => $experienceIndex, 'rate' => $request->rate_val[$position]];
                DB::table('commericial_details')->insertGetId($value);
            }
        }

        $countryIds = $request->input('arrCountry', []);

        // Intent: replace this tenant's complete country selection, including a valid empty selection.
        DB::table('commericial_countries')->where('company_id', $company_id)->where('commtype', $commType)->delete();
        if (count($countryIds) > 0) {
            for ($i = 0; $i < count($countryIds); $i++) {
                $value = ['company_id' => $company_id, 'commericial_id' => $commericialId, 'commtype' => $commType, 'country_id' => $countryIds[$i]];
                DB::table('commericial_countries')->insertGetId($value);
            }
        }

        // Intent: prevent the account Commercials tab from serving pre-save rates, countries, or status from its tenant cache.
        Cache::forget("account_settings:company:{$company_id}");

        // Intent: asynchronously refresh only this company's ES document after its MySQL commercial countries are replaced.
        ElasticCompanyIndex::dispatch($company_id);

        // dd( $commericialId);
        return response()->json(['status' => 'success']);
    }

    public function updateserviceviewmore(Request $request)
    {
        $request->validate([
            'id' => ['required', 'string'],
            'changeStatus' => ['required', 'boolean'],
        ]);

        $id = decrypt($request->id);
        $company_id = auth()->user()->company_id;
        $status = (int) $request->boolean('changeStatus');

        // Intent: update only the authenticated tenant's selected commercial service status.
        // Tenancy: the record ID is always constrained by company_id to prevent cross-company updates.
        $commercial = DB::table('commericials')
            ->where('id', $id)
            ->where('company_id', $company_id)
            ->first(['id']);

        if (! $commercial) {
            return response()->json(['status' => 'error', 'message' => 'Commercial service not found.'], 404);
        }

        DB::table('commericials')
            ->where('id', $commercial->id)
            ->where('company_id', $company_id)
            ->update(['status' => $status]);

        // Intent: invalidate the tenant-only account payload so refreshes always read the persisted service status.
        Cache::forget("account_settings:company:{$company_id}");

        // Intent: keep agency search/recommendation data synchronized without delaying this status-toggle response.
        ElasticCompanyIndex::dispatch($company_id);

        return response()->json(['status' => 'success', 'serviceStatus' => $status]);

    }

    public function demoView()
    {
        $this->pageTitle = '';

        return view('service_company.job-applications.demo', $this->data);
    }

    // return response()->json($commericials); resources\views\service_company\job-applications
    // return response()->json('status'=>'success');

}
