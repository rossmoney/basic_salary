<?php
namespace tests\Command;
 
use App\Command\CreatePayDatesCSVCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
 
class CreatePayDatesCSVCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;
 
    protected function setUp() : void
    {
        $application = new Application();
        $application->add(new CreatePayDatesCSVCommand());
        $command = $application->find('app:create-pay-dates-csv');
        $this->commandTester = new CommandTester($command);
    }
 
    protected function tearDown() : void
    {
        $this->commandTester = null;
    }
 
    public function testExecute()
    {
        $this->commandTester->execute([]);
 
        $this->assertStringContainsString('dates.csv was saved', trim($this->commandTester->getDisplay()));
    }
}
