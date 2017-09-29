<?php
$template = '<?php' . chr(10) . '$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'realurl\'] = unserialize(\'|\');';
$sourceFile = 'realurlconf.php';
$targetFile = 'realurlconf-s.php';

echo '<div style="color: green;">Trying to create a back-up of "' . $targetFile . '"</div>';
if (is_file($targetFile)) {
	$backupFile = $targetFile . '-backup-' . time() . '.php';
	$copyStatus = copy($targetFile, $backupFile);
	if ($copyStatus) {
		echo '<div style="color: green;">Back-up created: "' . $backupFile . '"</div>';
		echo '<div style="color: green;">Read file "' . $sourceFile . '"</div>';
		require_once($sourceFile);
		echo '<div style="color: green;">Checking variable $REALURL_CONF</div>';
		if (isset($REALURL_CONF) && is_array($REALURL_CONF) && !empty($REALURL_CONF)) {
			echo '<div style="color: green;">File "' . $targetFile . '" will be written</div>';
			$content = serialize($REALURL_CONF);
			file_put_contents($targetFile, str_replace('|', $content, $template), LOCK_EX);
			echo '<div style="color: green;"><strong>Finished</strong></div>';
		} else {
			echo '<div style="color: red;">$REALURL_CONF has unexpected format</div>';
		}
	} else {
		echo '<div style="color: red;">Back-up could not be created. Something went wrong while copying the file "' . $targetFile . '"</div>';
	}
} else {
	echo('<div style="color: red;">"' . $targetFile . '" does not exist. Stopping here. "' . $targetFile . '" was not overwritten</div>');
}