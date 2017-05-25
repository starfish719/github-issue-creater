<?php

$filePath = '/.trach/issues.csv';
if (!is_file($filePath)) {
    error_log('CSVファイルが存在しません', 0);
    return;
}

// 各種設定
$owner = 'owner name';
$repository = 'repository name';
$auth = 'username:access token';

// csvのトラッカーからGitHubのラベルのマッピング
$convTrackerList = array(
    'error' => 'bug',
);

// https://github domain/api/v3/repos/【owner】/【repos】/milestones で取得できるマイルストーンのnumberを指定する
// CSVの優先度からGitHubのマイルストーンのマッピング
$convMilestoneList = array(
    'csv milestone' => 1,
);

$apiUrl = 'curl --request POST -k https://github domain/api/v3/repos/' . $owner . '/' . $repository . '/issues -u ' . $auth . ' -H "Accept: application/json" -H "Content-type: application/json" -d ';

// 文字コードのチェックと変換
$fileData = file_get_contents($filePath);
$fileEncoding = mb_detect_encoding($fileData, 'UTF-8,SJIS');
if ($fileEncoding !== 'UTF-8') {
    $fileData = mb_convert_encoding($fileData, 'UTF-8', $fileEncoding);
}
$handle = fopen($filePath, 'w');
fwrite($handle, $fileData);
fclose($handle);

$fileObject = new SplFileObject($filePath);
$fileObject->setFlags(SplFileObject::READ_CSV);
foreach ($fileObject as $line) {

    try {
        if (count($line) < 6 || !is_numeric($line[0])) {
            continue;
        }

        $postData = array(
            'title' => $line[3],
            'body' => $line[5] . (!empty($line[4]) ? ("\r\n\r\n【備考欄】" . $line[4]) : ''),
        );

        if (isset($convMilestoneList[$line[2]])) {
            $postData['milestone'] = $convMilestoneList[$line[2]];
        }

        if (isset($convTrackerList[$line[1]])) {
            $postData['labels'][] = $convTrackerList[$line[1]];
        }

        $postCmd = $apiUrl . "'" . (json_encode($postData)) . "'";
        exec($postCmd);
    } catch (Exception $e) {
        error_log($line[0], 0);
        error_log($e->getMessage(), 0);
    }
}
