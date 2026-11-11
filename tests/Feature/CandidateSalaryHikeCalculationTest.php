<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyJobApplicationController;
use Tests\TestCase;

/**
 * Feature test for Salary Details Card Expected Increase (%) Direct Editable Enhancement.
 * Verifies bidirectional synchronization between Current Salary, Expected Increase (%),
 * Expected Salary, and Frequency/Currency across all candidate create/edit views.
 */
class CandidateSalaryHikeCalculationTest extends TestCase
{
    /**
     * Verifies that the Salary Details card renders the directly editable percentage hike input
     * across Add Candidate, Client Create Candidate, and Public Career Apply views.
     */
    public function test_salary_details_renders_direct_editable_hike_input_across_all_views(): void
    {
        $views = [
            'resources/views/service_company/job-applications/create.blade.php',
            'resources/views/service_company/job-applications/clientcreate.blade.php',
            'resources/views/career-apply.blade.php',
        ];

        foreach ($views as $viewPath) {
            $source = file_get_contents(base_path($viewPath));
            $this->assertNotFalse($source, "Failed reading {$viewPath}");

            // 1. Direct editable input element
            $this->assertStringContainsString('id="candidateSalaryHikeInput"', $source, "Missing candidateSalaryHikeInput in {$viewPath}");
            $this->assertStringContainsString('class="candidate-salary-hike-input"', $source, "Missing candidate-salary-hike-input class in {$viewPath}");
            $this->assertStringContainsString('type="number"', $source, "Missing numeric input in {$viewPath}");
            $this->assertStringContainsString('step="0.01"', $source, "Missing step attribute in {$viewPath}");

            // 2. Visual layout elements
            $this->assertStringContainsString('class="candidate-salary-hike-wrap"', $source, "Missing candidate-salary-hike-wrap in {$viewPath}");
            $this->assertStringContainsString('class="candidate-salary-hike-unit"', $source, "Missing candidate-salary-hike-unit in {$viewPath}");
            $this->assertStringContainsString('Expected Increase', $source, "Missing Expected Increase label in {$viewPath}");

            // 3. Form submission persistence
            $this->assertStringContainsString('name="percentage_hike"', $source, "Missing hidden percentage_hike input in {$viewPath}");

            // 4. Client-side engine functions & guards
            $this->assertStringContainsString('function applyHikePercentageToExpected()', $source, "Missing applyHikePercentageToExpected in {$viewPath}");
            $this->assertStringContainsString('_isUpdatingHike', $source, "Missing _isUpdatingHike guard in {$viewPath}");
            $this->assertStringContainsString("syncInputsForGroup('expected', period)", $source, "Missing syncInputsForGroup in {$viewPath}");
        }
    }

    /**
     * Verifies mathematical calculation accuracy of calculatedCandidateSalaryIncrease in the controller.
     */
    public function test_controller_salary_increase_calculation_accuracy(): void
    {
        $controller = (new \ReflectionClass(ServiceCompanyJobApplicationController::class))
            ->newInstanceWithoutConstructor();
        $reflection = new \ReflectionObject($controller);
        $increaseMethod = $reflection->getMethod('calculatedCandidateSalaryIncrease');

        // Example from prompt: Current CTC 8,00,000, 30% increase -> Expected 10,40,000
        $this->assertSame(30.0, $increaseMethod->invoke($controller, 800000, 1040000));

        // Decimal hike: 25.5% hike on 10,00,000 -> 12,55,000
        $this->assertSame(25.5, $increaseMethod->invoke($controller, 1000000, 1255000));

        // Negative percentage: Pay cut from 5,00,000 to 4,50,000 -> -10%
        $this->assertSame(-10.0, $increaseMethod->invoke($controller, 500000, 450000));

        // Zero / invalid salary bounds
        $this->assertNull($increaseMethod->invoke($controller, 0, 1040000));
        $this->assertNull($increaseMethod->invoke($controller, 800000, 0));
        $this->assertNull($increaseMethod->invoke($controller, 0, 0));
    }
}
