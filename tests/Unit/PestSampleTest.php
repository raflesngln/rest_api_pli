<?php

namespace Tests\Unit;



use Tests\TestCase;
use function Pest\Faker\faker;
use App\Models\User;


function sum($a, $b){
    return $a + $b;
}

test('sum Must Be True', function () {
   $result = sum(1, 2);
   expect($result)->toBe(3);
});

test('asserts that true is true in PEST Testing', function () {
   $result = true;
   expect($result)->toBeTrue();
});


test('MUST Contain', function () {
   $result ='lorem ipsum dolor';

   expect($result)->toContain('ipsum');
});

test('Multi testing category', function () {
    $value = sum(1, 2);
    expect($value)
        ->toBeInt()
        ->toBe(3)
        ->not->toBeString() // Not to be string...
        ->not->toBe(5); // Not to be 4...
});
test('collection expectations', function () {
    $value = sum(3, 2);
    $data_user = array('name' => 'rafles', 'alamat' => 'jakarta','is_active' => true, 'phone'=>['home','office']);
    $user = (object) $data_user;

      // expect($user)->toHaveMethod('getFullname');
    // expect($user)->toHaveMethods(['getFullname', 'isAuthenticated']);

    expect('')->toBeEmpty();
    expect([])->toBeEmpty();
    expect(null)->toBeEmpty();
    expect(1)->toBeTruthy();
    expect('1')->toEqual(1);
    expect(14)->toEqualWithDelta(10,4);
    expect('1')->toBeTruthy(); //0  tobe false
    expect($value)->toBeGreaterThan(2);
    expect($value)->toBeGreaterThanOrEqual(5);
    expect($value)->toBeLessThan(100);
    expect($value)->toBeLessThanOrEqual(5);
    expect(['Nuno', 'Luke', 'Alex', 'Dan'])->toHaveCount(4);

    expect('lorem ipsum dolor sit amet')->toContain('ipsum');
    expect('lorem ipsum dolor sit amet')->toContain('ipsum', 'sit', 'amet');
    expect([1, 2, 3, 4])->toContain(2, 4);

    expect($user)->toHaveProperty('name');
    expect($user)->toHaveProperty('name', 'rafles');
    expect($user)->toHaveProperty('is_active', true);
    expect($user)->toHaveProperties(['name', 'alamat']);

    expect($user)->toMatchArray(['name' => 'rafles','alamat' => 'jakarta']);
    expect($user)->toMatchObject(['name' => 'rafles','alamat' => 'jakarta']);
    // expect($user->phone)->toBeIn(['home', 'office']);
    expect($data_user )->toBeArray();
    expect($user )
            ->toHaveProperty('name')
            ->toHaveProperty('name', 'rafles')
            ->toHaveProperty('name', 'rafles')
            ->toHaveProperty('is_active', true);
    expect(10)->toBeNumeric();
    expect('{"hello":"world"}')->toBeJson();
    expect($user)->toHaveKey('name');
    expect($user)->toHaveKey('name','rafles');
    expect(['user' => ['name' => 'Nuno', 'surname' => 'Maduro']])->toHaveKey('user.name');

});



test('Expectations condition', function () {
    // expect(fn() => throw new Exception('Something happened.'))->toThrow('Something happened.');
    expect('Hello World')->toMatch('/^hello wo.*$/i');
    expect('Hello World')->toEndWith('World');

    expect([1, 2, 3])->each->toBeInt();
    expect([1, 2, 3])->each->not->toBeString();
    expect([1, 2, 3,5])->each(fn ($number) => $number->toBeLessThan(6));

 });

test('JSON,Sequence condition', function () {
    $data_user = array('name' => 'rafles', 'alamat' => 'jakarta','is_active' => true,'star'=>5,'phone'=>'12121212');
    $user = (object) $data_user;

    expect('{"name":"rafles","credit":1000.00}')
            ->json()
            ->toHaveCount(2)
            ->not->toBeString()
            ->name->toBe('rafles')
            ->credit->toBeFloat();

    expect([1, 2, 3])->sequence(
                fn ($number) => $number->toBe(1),
                fn ($number) => $number->toBe(2),
                fn ($number) => $number->toBe(3),
            );

            expect($data_user)->sequence(
                fn ($value, $key) => $value->toEqual('rafles'),
                fn ($value, $key) => $key->toEqual('alamat'),
                fn ($value, $key) => $value->toBeTrue(),
                fn ($value, $key) => $value->toBeInt(),
                fn ($value, $key) => $value->toBeString(),
            );

    expect($user)
            ->when($user->is_active === true, fn ($user) =>$user
            ->star->toBeGreaterThan(2))
            ->alamat->not->toBeEmpty()
            ->phone->not->toBeEmpty()
            ->star->toBeInt(2)
            ->star->toBeLessThan(100);
 });



// it('can handle a form submission', function () {
//     // $url = route('api.v1.auth.login');
//     $url = route('login');

//     $values = [
//         'email' => 'raflesngln@gmail.com',
//         'password' => '12345678',
//     ];

//     post($url, $values)->assertSuccessful();

//     assertDatabaseHas(User::class, $values);
// });
