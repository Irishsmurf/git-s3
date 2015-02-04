<?php namespace GitS3\Wrapper;

use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;
use Symfony\Component\Finder\SplFileInfo as File;
use Aws\Common\Exception\InstanceProfileCredentialsException;
use Aws\S3\Exception\InvalidAccessKeyIdException;
use \Exception;
class Bucket
{

	private $client;
	private $name;
	
	public function __construct($key, $secret, $name)
	{
		$this->name = $name;
		
		//Check for valid keys
		$validKey = preg_match("/^(?<![A-Z0-9])[A-Z0-9]{20}(?![A-Z0-9])$/", $key);
		$validSecret = preg_match("/^(?<![A-Za-z0-9\/+=])[A-Za-z0-9\/+=]{40}(?![A-Za-z0-9\/+=])$/", $secret);

		//If keys are not valid, try a role.
		if(!$validKey || !$validSecret)
			$this->client = S3Client::factory(array());
		else
			$this->client = S3Client::factory(array(
				'key'		=> $key,
				'secret'	=> $secret,
			));
	}

	public function upload(File $file)
	{
		try
		{
			$this->client->putObject(array(
				'Bucket'		=> $this->name,
				'Key'    		=> $file->getRelativePathname(),
				'SourceFile' 	=> $file->getRealpath(),
				'ACL'   		=> CannedAcl::PUBLIC_READ,
				));
		}
		catch(Exception $e)
		{
			throw new InvalidAccessKeyIdException("The AWS Access Key Id you provided does not exist in our records.");
		}
	}

	public function delete($fileName)
	{
		try
		{
			$this->client->deleteObject(array(
				'Bucket' => $this->name,
				'Key'    => $fileName,
				));
		}
		catch(Exception $e)
		{
			throw new InvalidAccessKeyIdException("The AWS Access Key Id you provided does not exist in our records.");
		}
	}
}
