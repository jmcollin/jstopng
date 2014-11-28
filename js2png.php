<?php

function p($obj)
{
	echo '<pre>';
	print_r($obj);
	echo '<pre>';
}

function allInOne($js_files)
{
	$content = '';
	ksort($js_files);
	foreach ($js_files as $js_file)
	{
		if (!file_get_contents($js_file))
			die("whate da fuck ".$js_file);

		$content .= file_get_contents($js_file);
	}
	unset($js_files, $js_file);

	file_put_contents('oneJSForRuleThemAll.js', $content);
}

function combineJS($path)
{
	$js_files = array();
	$is_dot = array ('.', '..');
	if (is_dir($path))
	{
		if (version_compare(phpversion(), '5.3', '<'))
		{
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($path),
				RecursiveIteratorIterator::SELF_FIRST
			);
		}
		else
		{
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
				RecursiveIteratorIterator::CHILD_FIRST
			);
		}

		foreach ($iterator as $pathname => $file)
		{
			if (version_compare(phpversion(), '5.2.17', '<='))
			{
				if (in_array($file->getBasename(), $is_dot))
					continue;
			}
			elseif (version_compare(phpversion(), '5.3', '<'))
			{
				if ($file->isDot())
					continue;
			}

			if ($file->getExtension() === 'js')
				$js_files[] = $file->getPathname();
		}

		allInOne($js_files);

		unset($iterator, $file);
	}
}

function convertJS($filename)
{
	if (file_exists($filename))
	{
		$iFileSize = filesize($filename);
		$iWidth = ceil(sqrt($iFileSize / 1));
		$iHeight = $iWidth;
		$im = imagecreatetruecolor($iWidth, $iHeight);
		$fs = fopen($filename, 'r');
		$data = fread($fs, $iFileSize);
		fclose($fs);
		$i = 0;
		for ($y=0; $y < $iHeight; $y++)
		{
			for ($x=0; $x < $iWidth; $x++)
			{
				$ord = ord(@$data[$i]);
				imagesetpixel($im,
					$x, $y,
					imagecolorallocate($im,
						$ord,
						$ord,
						$ord
					)
				);
				$i++;
			}
		}
		imagepng($im, 'oneJSForRuleThemAll.png');
		imagedestroy($im);
		p('Image created oneJSForRuleThemAll.png');
	}
}


function JSToPng($dir)
{
	combineJS($dir);
	convertJS('oneJSForRuleThemAll.js');
}

JSToPng('./js/');