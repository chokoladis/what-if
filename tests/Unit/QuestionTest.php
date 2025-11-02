<?php

namespace Tests\Unit;

use App\Http\Controllers\QuestionController;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Question;
use App\Services\QuestionService;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class QuestionTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_store(): void
    {
//        $controller = new QuestionController;

//        $controllerReflection = new \ReflectionClass($controller);
//        $storeMethod = $controllerReflection->getMethod('store');
//        $storeMethod->setAccessible(true);

//         var 1
//        $question = Question::factory()->create();
//        $this->actingAs($question);
//
//        $response = $this->post(route('questions.store'), [
//            'category' => '0',
//            'title' => '1',
//            'h-captcha-response' => 'fake',
//        ]);
//
//        $saveRequest = $this->createMock(StoreRequest::class);

//        $mock = \Mockery::mock(StoreRequest::class)->makePartial();
//        $mock->shouldReceive('validated')->andReturn([
//            'category' => '0',
//            'title' => '1',
//            'h-captcha-response' => '1232',
//        ]);

//        $requestData = StoreRequest::create('/questions', 'POST', [
//            'category' => '0',
//            'title' => '1',
//            'h-captcha-response' => '1232',
//        ]);
//
////
//        $requestData->setContainer(app());

//        $requestData->validateResolved(); // вручную вызвать валидацию
//
//        $requestData = app()->make(StoreRequest::class);
//        $requestData->request->add([
////            'h-captcha-response' => '1232',
//            'category' => '0',
//            'title' => '1',
////            'img' => null
//        ]);
        $service = new QuestionService();

//        $questionMock = Mockery::mock('alias:App\Models\Question');

        // Мокаем FormRequest и подсовываем данные
        $mock = \Mockery::mock(StoreRequest::class);
        $mock->shouldReceive('validated')->andReturn([
            'category' => '0',
            'title' => '1',
            'h-captcha-response' => '1232',
        ]);

        $mock->shouldReceive('get')->with('h-captcha-response')->andReturn('1232');
        $mock->shouldReceive('hasFile')->andReturn(false);
        $mock->shouldReceive('user')->andReturn(
            Mockery::mock()->shouldReceive('can')->with('isAdmin', Mockery::any())->andReturn(false)->getMock()
        );


        // Если в контроллере вызывается $request->user()
//        $mock->shouldReceive('user')->andReturn((object)['id' => 1]);

        $result = $service->store($mock);

//        $result = $controller->store($requestData);

//        $result = $storeMethod->invokeArgs($controller, [$mock]);

        $this->assertTrue(true, $result);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }
}
