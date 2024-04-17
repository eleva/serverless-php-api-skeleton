<?php declare(strict_types=1);

use App\HelloHandler;
use PHPUnit\Framework\TestCase;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;

final class HelloTest extends TestCase
{
    /**
     * @var HelloHandler
     */
    private HelloHandler $handler;
    /**
     * @var Psr17Factory
     */
    private Psr17Factory $psr17Factory;
    /**
     * @var ServerRequestCreator
     */
    private ServerRequestCreator $creator;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        require_once('src/function/hello/index.php');
    }

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->handler = new HelloHandler();
        $this->psr17Factory = new Psr17Factory();

        $this->creator = new ServerRequestCreator(
            $this->psr17Factory, // ServerRequestFactory
            $this->psr17Factory, // UriFactory
            $this->psr17Factory, // UploadedFileFactory
            $this->psr17Factory  // StreamFactory
        );
    }

    /**
     * @return void
     * @throws ValidationFailed
     */
    public function testHello(): void
    {
        $event = $this->creator->fromGlobals();
        $response = $this->handler->handle($event,null);
        $response = new Response(200, ["Content-Type"=>'application/json'], $response['body']);
        $responseJson = json_decode($response->getBody()->getContents(),true);

        $yamlFile = "doc/build/openapi.yaml";

        $validator = (new ValidatorBuilder)->fromYamlFile($yamlFile)->getResponseValidator();
        $operation = new OperationAddress('/hello', 'get') ;

        $validator->validate($operation, $response);

        $this->assertSame('Bref! Your function executed successfully!', $responseJson['message']);
    }
}
