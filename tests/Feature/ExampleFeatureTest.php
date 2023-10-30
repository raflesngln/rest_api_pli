<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExampleFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_access_get(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function calculate($number1, $number2, $operator)
    {
        switch ($operator) {
            case '+':
                $result = $number1 + $number2;
                break;
            case '-':
                $result = $number1 - $number2;
                break;
            case '*':
                $result = $number1 * $number2;
                break;
            case '/':
                if ($number2 != 0) {
                    $result = $number1 / $number2;
                } else {
                    throw new \Exception("Cannot divide by zero");
                }
                break;
            default:
                throw new \Exception("Invalid operator");
        }

        return [
            'result' => $result,
        ];
    }

    public function testCalculate()
    {
        $result = $this->calculate(5, 3, '+');
        // var_dump($result);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertEquals(8, $result['result']);
    }

}
