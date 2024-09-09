<?php
// エラー表示
ini_set("display_errors", 1);

// Qiita APIのアクセストークン
$accessToken = '587ed145f873fd8c3436325feee2486355278979';

// Qiita APIのエンドポイント
$apiUrl = "https://qiita.com/api/v2/items?query=title:初心者&per_page=1";

// cURLを使用してQiita APIからデータを取得
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken
]);
$response = curl_exec($ch);
curl_close($ch);

// APIレスポンスを確認
if ($response === false) {
    exit('cURL Error: ' . curl_error($ch));
}
//echo "API Response: " . $response . "\n";

// JSONデータを配列に変換
$articles = json_decode($response, true);

// デコード結果を確認
if (json_last_error() !== JSON_ERROR_NONE) {
    exit('JSON Decode Error: ' . json_last_error_msg());
}
//echo "Decoded JSON: " . print_r($articles, true) . "\n";

// DB接続
try {
    $pdo = new PDO('mysql:dbname=gs_db;charset=utf8;host=localhost', 'root', '');
} catch (PDOException $e) {
    exit('DBerror:' . $e->getMessage());
}

// データベースに記事を保存
foreach ($articles as $article) {
    $title = $article['title'];
    $url = $article['url'];
    $sql = "INSERT INTO qiita_table (title, url, bookmark) VALUES (:title, :url, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':url', $url, PDO::PARAM_STR);
    $status = $stmt->execute();

    if ($status == false) {
        $error = $stmt->errorInfo();
        exit("SQLError:" . $error[2]);
    }
}

echo "データベースに記事を保存しました。";
?>