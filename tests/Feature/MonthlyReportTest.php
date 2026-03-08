<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MonthlyReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test successful monthly report generation.
     *
     * @return void
     */
    public function test_can_generate_monthly_report_with_valid_dates()
    {
        $this->actingAs($this->user);

        $mockService = $this->mock(MonthlyReportService::class);
        $mockService->shouldReceive('generateMonthlyReport')
            ->once()
            ->with($this->user->id, '2023-01-01', '2023-01-31')
            ->andReturn([
                'income_total' => 1500.50,
                'outcome_total' => 750.25,
            ]);

        $response = $this->getJson('/api/v2/movimentacoes/report/monthly?data_inicial=2023-01-01&data_final=2023-01-31');

        $response->assertStatus(200)
            ->assertJson([
                'income_total' => 1500.50,
                'outcome_total' => 750.25,
            ]);
    }

    /**
     * Test validation for missing data_inicial.
     *
     * @return void
     */
    public function test_requires_data_inicial()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v2/movimentacoes/report/monthly?data_final=2023-01-31');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['data_inicial']);
    }

    /**
     * Test validation for missing data_final.
     *
     * @return void
     */
    public function test_requires_data_final()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v2/movimentacoes/report/monthly?data_inicial=2023-01-01');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['data_final']);
    }

    /**
     * Test validation for invalid date format for data_inicial.
     *
     * @return void
     */
    public function test_data_inicial_has_valid_format()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v2/movimentacoes/report/monthly?data_inicial=01-01-2023&data_final=2023-01-31');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['data_inicial']);
    }

    /**
     * Test validation for invalid date format for data_final.
     *
     * @return void
     */
    public function test_data_final_has_valid_format()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v2/movimentacoes/report/monthly?data_inicial=2023-01-01&data_final=31/01/2023');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['data_final']);
    }

    /**
     * Test validation for data_final being before data_inicial.
     *
     * @return void
     */
    public function test_data_final_is_after_or_equal_to_data_inicial()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/v2/movimentacoes/report/monthly?data_inicial=2023-01-31&data_final=2023-01-01');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['data_final']);
    }

    /**
     * Test unauthenticated access to the monthly report endpoint.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_access_report()
    {
        $response = $this->getJson('/api/v2/movimentacoes/report/monthly?data_inicial=2023-01-01&data_final=2023-01-31');

        $response->assertStatus(401);
    }

    /**
     * Test the MonthlyReportService logic directly.
     *
     * @return void
     */
    public function test_monthly_report_service_calculates_totals_correctly()
    {
        // Create some transactions for the user
        Movimentacao::factory()->create([
            'user_id' => $this->user->id,
            'valor' => 100.00,
            'data_transacao' => '2023-01-15',
        ]);
        Movimentacao::factory()->create([
            'user_id' => $this->user->id,
            'valor' => 200.50,
            'data_transacao' => '2023-01-20',
        ]);
        Movimentacao::factory()->create([
            'user_id' => $this->user->id,
            'valor' => -50.25,
            'data_transacao' => '2023-01-25',
        ]);
        // Transaction outside the date range
        Movimentacao::factory()->create([
            'user_id' => $this->user->id,
            'valor' => 500.00,
            'data_transacao' => '2023-02-01',
        ]);
        // Transaction for another user
        Movimentacao::factory()->create([
            'user_id' => User::factory()->create()->id,
            'valor' => 1000.00,
            'data_transacao' => '2023-01-10',
        ]);

        $service = new MonthlyReportService();
        $report = $service->generateMonthlyReport($this->user->id, '2023-01-01', '2023-01-31');

        $this->assertEquals(300.50, $report['income_total']);
        $this->assertEquals(50.25, $report['outcome_total']);
    }
}
