<?php
namespace Test\Kohkimakimoto\BackgroundProcess;

use Kohkimakimoto\BackgroundProcess\BackgroundProcess;
use Kohkimakimoto\BackgroundProcess\BackgroundProcessManager;

class BackgroundProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        if (file_exists("/tmp/BackgroundProcess_t1.sh.output")) {
          unlink("/tmp/BackgroundProcess_t1.sh.output");
        }

        $process = new BackgroundProcess("sh ".__DIR__."/BackgroundProcessTest/t1.sh");
        $process->run();

        sleep(4);

        if (!file_exists($process->getExecutablePHPFilePath())) {
          // File has not deleted. It's fail.
          $this->assertEquals(true, true);
        } else {
          $this->assertEquals(true, false);
        }

        $retVal = file_get_contents("/tmp/BackgroundProcess_t1.sh.output");
        $this->assertEquals("aaaa\naaaa\naaaa\n", $retVal);
    }

    /**
     * Test of BackgroundProcess Option configurations.
     */
    public function testRun2()
    {
      if (file_exists("/tmp/BackgroundProcess_t1.sh.output")) {
        unlink("/tmp/BackgroundProcess_t1.sh.output");
      }

      $process = new BackgroundProcess("sh ".__DIR__."/BackgroundProcessTest/t1.sh", array(
        'working_directory' => '/var/tmp/php/background_process',
        'key_prefix'        => 'abc.',
        'error_log'         => 'error_foo.log',
      ));
      $process->run();
    }

    /**
     * Test of command raise error.
     */
    public function testRun3()
    {
      if (file_exists("/tmp/php/background_process/err.log")) {
        unlink("/tmp/php/background_process/err.log");
      }

      $process = new BackgroundProcess("sh ".__DIR__."/BackgroundProcessTest/t2.sh");
      $process->run();

      sleep(1);

      if (file_exists("/tmp/php/background_process/err.log")) {
        // File has not deleted. It's fail.
        $this->assertEquals(true, true);
      } else {
        $this->assertEquals(true, false);
      }


    }

    public function testAccessor()
    {
      $process = new BackgroundProcess("sh ".__DIR__."/BackgroundProcessTest/t1.sh");

      $process->setKey("aaaaaa");
      $this->assertEquals("aaaaaa", $process->getKey());

      $process->setCommandline("ls -ltr");
      $this->assertEquals("ls -ltr", $process->getCommandline());

      $manager = new BackgroundProcessManager();
      $process->setManager($manager);
      $this->assertEquals($manager, $process->getManager());

      $manager = new BackgroundProcessManager();
      $process->setMeta(array('commandline' => "ls"));
      $this->assertEquals(array('commandline' => "ls"), $process->getMeta());

    }



}