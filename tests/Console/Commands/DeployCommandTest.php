<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\SplFileInfo;
use GitS3\Console\Application;
use GitS3\Console\Commands\DeployCommand;
use GitS3\Wrapper\Config;
use GitS3\Wrapper\History;
use Mockery as m;

class SubDeployCommand extends DeployCommand
{
  public function proxyExecute(InputInterface $input, OutputInterface $output) {
    $this->execute($input, $output);
  }
}

class DeployCommandTest extends PHPUnit_Framework_TestCase {
  private $application;
  private $fileMock;
  private $inputMock;
  private $outputMock;
  private $tester;

  public function setUp() {
    $this->application = m::mock('GitS3\Console\Application');
    $this->configMock = m::mock('GitS3\Wrapper\Config');
    $this->historyMock = m::mock('GitS3\Wrapper\History');
    $this->fileMock = m::mock('File');
    $this->inputMock = m::mock('Symfony\Component\Console\Input\InputInterface');
    $this->outputMock = m::mock('Symfony\Component\Console\Output\OutputInterface');
    $this->deployMock = m::mock('DeployCommand');
    $this->helperMock = m::mock('Symfony\Component\Console\Helper\HelperSet');
    $this->commandMock = m::mock('Symfony\Component\Console\Tester\CommandTester');
    $this->fileMock = m::mock('Symfony\Component\Finder\SplFileInfo');

    $application = new Application($this->configMock,
                                   $this->historyMock);

    $this->tester = m::mock('SubDeployCommand[getApplication,hasNotBeenInitialized]');
  }

  public function tearDown()
  {
      m::close();
  }

  public function testExecute() {
    $this->inputMock->shouldNotReceive('writeln');
    $this->tester->shouldReceive('getApplication')->once()->andReturn($this->application);
    $this->application->shouldReceive('getBucket')->once()->andReturn(null);
    $this->application->shouldReceive('getConfig')->once()->andReturn($this->configMock);
    $this->configMock->shouldReceive('getPath')->once()->andReturn('something');

    $this->setExpectedException('InvalidArgumentException');


    $this->tester->proxyExecute($this->inputMock, $this->outputMock);
  }
}


?>
