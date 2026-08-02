<?php

declare(strict_types=1);

final class Validator
{
	public function validate(string $file): void
	{
		echo "Validate {$file}\n";
	}
}
final class Transcoder
{
	public function to720p(string $file): string
	{
		echo "Transcode\n";
		return 'output.mp4';
	}
}
final class Storage
{
	public function upload(string $file): string
	{
		echo "Upload\n";
		return 'https://cdn/video.mp4';
	}
}
final class VideoProcessingFacade
{
	public function __construct(private Validator $validator, private Transcoder $transcoder, private Storage $storage)
	{
	}
	public function process(string $file): string
	{
		$this->validator->validate($file);
		return $this->storage->upload($this->transcoder->to720p($file));
	}
}
echo (new VideoProcessingFacade(new Validator(), new Transcoder(), new Storage()))->process('input.mov') . PHP_EOL;
